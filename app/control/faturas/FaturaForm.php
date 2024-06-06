<?php
/**
 * FaturaForm
 *
 * @version    1.0
 * @package    erphouse
 * @subpackage control
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class FaturaForm extends TWindow
{
    protected $form; // form
    protected $fieldlist;
    
    /**
     * Class constructor
     * Creates the page and the registration form
     */
    function __construct($param)
    {
        parent::__construct($param);
        parent::setSize(0.8, null);
        parent::setMinWidth(0.9, 1000);
        parent::removePadding();
        parent::removeTitleBar();
        //parent::disableEscape();
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_Fatura');
        $this->form->setFormTitle('Fatura');
        
        // master fields
        $id = new TEntry('id');
        $cliente_id = new TDBUniqueSearch('cliente_id', 'erphouse', 'Pessoa', 'id', 'nome_fantasia');
        $dt_fatura = new TDate('dt_fatura');

        // sizes
        $id->setSize('100%');
        $cliente_id->setSize('100%');
        $dt_fatura->setSize('100%');
        
        $cliente_id->addValidation('Cliente', new TRequiredValidator);
        $dt_fatura->addValidation('Dt Fatura', new TRequiredValidator);

        $id->setEditable(FALSE);
        $cliente_id->setMinLength(0);
        
        $dt_fatura->setMask('dd/mm/yyyy');
        $dt_fatura->setDatabaseMask('yyyy-mm-dd');
        
        // add form fields to the form
        $this->form->addFields( [new TLabel('Id')], [$id] );
        $this->form->addFields( [new TLabel('Cliente')], [$cliente_id] );
        $this->form->addFields( [new TLabel('Dt Fatura')], [$dt_fatura] );
        
        
        // detail fields
        $this->fieldlist = new TFieldList;
        $this->fieldlist-> width = '100%';
        $this->fieldlist->enableSorting();
        
        $servico_id = new TDBUniqueSearch('list_servico_id[]', 'erphouse', 'Servico', 'id', 'nome', null, TCriteria::create( ['ativo' => 'Y'] ));
        $valor = new TNumeric('list_valor[]', 2, ',', '.');
        $quantidade = new TNumeric('list_quantidade[]', 2, ',', '.');
        
        $servico_id->setSize('100%');
        $valor->setSize('100%');
        $quantidade->setSize('100%');
        $servico_id->setMinLength(0);

        $this->fieldlist->addField( '<b>Servico</b>', $servico_id, ['width' => '40%']);
        $this->fieldlist->addField( '<b>Valor</b>', $valor, ['width' => '30%']);
        $this->fieldlist->addField( '<b>Quantidade</b>', $quantidade, ['width' => '30%']);

        $this->form->addField($servico_id);
        $this->form->addField($valor);
        $this->form->addField($quantidade);
        
        $detail_wrapper = new TElement('div');
        $detail_wrapper->add($this->fieldlist);
        $detail_wrapper->style = 'overflow-x:auto';
        
        $this->form->addContent( [ TElement::tag('h5', 'Itens da fatura', [ 'style'=>'background: whitesmoke; padding: 5px; border-radius: 5px; margin-top: 5px'] ) ] );
        $this->form->addContent( [ $detail_wrapper ] );
        
        // create actions
        $this->form->addAction( _t('Save'),  new TAction( [$this, 'onSave'] ),  'fa:save green' );
        $this->form->addAction( _t('Clear'), new TAction( [$this, 'onClear'] ), 'fa:eraser red' );
        $this->form->addHeaderActionLink( _t('Close'), new TAction([$this, 'onClose']), 'fa:times red');
        
        // create the page container
        $container = new TVBox;
        $container->style = 'width: 100%';
        //$container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        parent::add($container);
    }
    
    /**
     * Executed whenever the user clicks at the edit button da datagrid
     */
    function onEdit($param)
    {
        try
        {
            TTransaction::open('erphouse');
            
            if (isset($param['key']))
            {
                $key = $param['key'];
                
                $object = new Fatura($key);
                $this->form->setData($object);
                
                $items  = FaturaItem::where('fatura_id', '=', $key)->load();
                
                if ($items)
                {
                    $this->fieldlist->addHeader();
                    foreach($items  as $item )
                    {
                        $detail = new stdClass;
                        $detail->list_servico_id = $item->servico_id;
                        $detail->list_valor = $item->valor;
                        $detail->list_quantidade = $item->quantidade;
                        $this->fieldlist->addDetail($detail);
                    }
                    
                    $this->fieldlist->addCloneAction();
                }
                else
                {
                    $this->onClear($param);
                }
                
                TTransaction::close(); // close transaction
	    }
	    else
            {
                $this->onClear($param);
            }
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
    
    /**
     * Clear form
     */
    public function onClear($param)
    {
        $this->fieldlist->addHeader();
        $this->fieldlist->addDetail( new stdClass );
        $this->fieldlist->addCloneAction();
    }
    
    /**
     * Save the Fatura and the FaturaItem's
     */
    public static function onSave($param)
    {
        try
        {
            TTransaction::open('erphouse');
            
            $id = (int) $param['id'];
            $master = new Fatura;
            $master->fromArray( $param);
            $master->dt_fatura = TDateTime::convertToMask($param['dt_fatura'], 'dd/mm/yyyy', 'yyyy-mm-dd');
            $master->mes = TDateTime::convertToMask($param['dt_fatura'], 'dd/mm/yyyy', 'mm');
            $master->ano = TDateTime::convertToMask($param['dt_fatura'], 'dd/mm/yyyy', 'yyyy');
            
            if (empty($master->id))
            {
                $master->ativo = 'Y';
                $master->financeiro_gerado = 'N';
            }
            
            $master->store(); // save master object
            
            // delete details
            FaturaItem::where('fatura_id', '=', $master->id)->delete();
            
            if( !empty($param['list_servico_id']) AND is_array($param['list_servico_id']) )
            {
                $total = 0;
                foreach( $param['list_servico_id'] as $row => $servico_id)
                {
                    if (!empty($servico_id))
                    {
                        $detail = new FaturaItem;
                        $detail->fatura_id = $master->id;
                        $detail->servico_id = $param['list_servico_id'][$row];
                        $detail->valor =      (float) str_replace(['.',','], ['','.'], $param['list_valor'][$row]);
                        $detail->quantidade = (float) str_replace(['.',','], ['','.'], $param['list_quantidade'][$row]);
                        $detail->total = round($detail->valor * $detail->quantidade, 2);
                        $detail->store();
                        
                        $total += $detail->total;
                    }
                }
                $master->total = $total;
                $master->store(); // save master object
            }
            
            $data = new stdClass;
            $data->id = $master->id;
            TForm::sendData('form_Fatura', $data);
            TTransaction::close(); // close the transaction
            
            $pos_action = new TAction(['FaturaList', 'onReload']);
            new TMessage('info', AdiantiCoreTranslator::translate('Record saved'), $pos_action);
            parent::closeWindow();
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
    
    /**
     * Close
     */
    public static function onClose($param)
    {
        parent::closeWindow();
    }   
}
