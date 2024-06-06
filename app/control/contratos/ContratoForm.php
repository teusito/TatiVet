<?php
/**
 * ContratoForm
 *
 * @version    1.0
 * @package    erphouse
 * @subpackage control
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class ContratoForm extends TWindow
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
        $this->form = new BootstrapFormBuilder('form_Contrato');
        $this->form->setFormTitle('Contrato');
        $this->form->setClientValidation(true);
        
        // master fields
        $id = new TEntry('id');
        $cliente_id = new TDBUniqueSearch('cliente_id', 'erphouse', 'Pessoa', 'id', 'nome_fantasia');
        $tipo_contrato_id = new TDBUniqueSearch('tipo_contrato_id', 'erphouse', 'TipoContrato', 'id', 'nome');
        $ativo = new TRadioGroup('ativo');
        $dt_inicio = new TDate('dt_inicio');
        $dt_fim = new TDate('dt_fim');
        $obs = new TText('obs');

        $cliente_id->setMinLength(0);
        $tipo_contrato_id->setMinLength(0);
        $ativo->addItems( ['Y' => 'Sim', 'N' => 'Não'] );
        $ativo->setLayout('horizontal');
        
        $dt_inicio->setMask('dd/mm/yyyy');
        $dt_fim->setMask('dd/mm/yyyy');
        $dt_inicio->setDatabaseMask('yyyy-mm-dd');
        $dt_fim->setDatabaseMask('yyyy-mm-dd');
        $dt_inicio->setSize('100%');
        $dt_fim->setSize('100%');
        $obs->setSize('100%', 60);
        $ativo->setValue('Y');
        
        $cliente_id->addValidation('Cliente', new TRequiredValidator);
        $tipo_contrato_id->addValidation('Tipo contrato', new TRequiredValidator);
        $ativo->addValidation('Ativo', new TRequiredValidator);
        $dt_inicio->addValidation('Data início', new TRequiredValidator);
        $dt_fim->addValidation('Data fim', new TRequiredValidator);

        // sizes
        $id->setSize('100%');
        $cliente_id->setSize('100%');
        $tipo_contrato_id->setSize('100%');
        $ativo->setSize('100%');
        $dt_inicio->setSize('100%');
        $dt_fim->setSize('100%');
        $id->setEditable(FALSE);
        
        // add form fields to the form
        $this->form->addFields( [new TLabel('Id')], [$id] );
        $this->form->addFields( [new TLabel('Cliente')], [$cliente_id] );
        $this->form->addFields( [new TLabel('Tipo Contrato')], [$tipo_contrato_id] );
        $this->form->addFields( [new TLabel('Ativo')], [$ativo] );
        $this->form->addFields( [new TLabel('Dt Inicio')], [$dt_inicio], [new TLabel('Dt Fim')], [$dt_fim] );
        $this->form->addFields( [new TLabel('Obs')], [$obs] );
        
        
        // detail fields
        $this->fieldlist = new TFieldList;
        $this->fieldlist-> width = '100%';
        $this->fieldlist->enableSorting();

        $servico_id = new TDBUniqueSearch('list_servico_id[]', 'erphouse', 'Servico', 'id', 'nome', null, TCriteria::create( ['ativo' => 'Y'] ));
        $valor = new TNumeric('list_valor[]', 2, ',', '.');
        $quantidade = new TNumeric('list_quantidade[]', 2, ',', '.');
        
        $servico_id->setChangeAction(new TAction(array($this, 'onChangeServico')));
        
        $servico_id->setSize('100%');
        $servico_id->setMinLength(0);
        $valor->setSize('100%');
        $quantidade->setSize('100%');

        $this->fieldlist->addField( '<b>Servico</b>', $servico_id, ['width' => '40%']);
        $this->fieldlist->addField( '<b>Valor</b>', $valor, ['width' => '30%']);
        $this->fieldlist->addField( '<b>Quantidade</b>', $quantidade, ['width' => '30%']);

        $this->form->addField($servico_id);
        $this->form->addField($valor);
        $this->form->addField($quantidade);
        
        $detail_wrapper = new TElement('div');
        $detail_wrapper->add($this->fieldlist);
        $detail_wrapper->style = 'overflow-x:auto';
        
        $this->form->addContent( [ TElement::tag('h5', 'Itens do contrato', [ 'style'=>'background: whitesmoke; padding: 5px; border-radius: 5px; margin-top: 5px'] ) ] );
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
                
                $object = new Contrato($key);
                $this->form->setData($object);
                
                $items  = ContratoItem::where('contrato_id', '=', $key)->load();
                
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
     * Save the Contrato and the ContratoItem's
     */
    public static function onSave($param)
    {
        try
        {
            TTransaction::open('erphouse');
            
            $id = (int) $param['id'];
            $master = new Contrato;
            $master->fromArray( $param);
            $master->dt_inicio = TDateTime::convertToMask($param['dt_inicio'], 'dd/mm/yyyy', 'yyyy-mm-dd');
            $master->dt_fim    = TDateTime::convertToMask($param['dt_fim'],    'dd/mm/yyyy', 'yyyy-mm-dd');
            $master->store(); // save master object
            
            // delete details
            ContratoItem::where('contrato_id', '=', $master->id)->delete();
            
            if( !empty($param['list_servico_id']) AND is_array($param['list_servico_id']) )
            {
                foreach( $param['list_servico_id'] as $row => $servico_id)
                {
                    if (!empty($servico_id))
                    {
                        $detail = new ContratoItem;
                        $detail->contrato_id = $master->id;
                        $detail->servico_id = $param['list_servico_id'][$row];
                        $detail->valor =      (float) str_replace(['.',','], ['','.'], $param['list_valor'][$row]);
                        $detail->quantidade = (float) str_replace(['.',','], ['','.'], $param['list_quantidade'][$row]);
                        $detail->total = round($detail->valor * $detail->quantidade,2);
                        $detail->store();
                    }
                }
            }
            
            $data = new stdClass;
            $data->id = $master->id;
            TForm::sendData('form_Contrato', $data);
            TTransaction::close(); // close the transaction
            
            new TMessage('info', AdiantiCoreTranslator::translate('Record saved'));
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
    
    
    /**
     * Change servico
     */
    public static function onChangeServico($param)
    {
        $input_id = $param['_field_id'];
        $servico_id = $param['_field_value'];
        $input_pieces = explode('_', $input_id);
        $unique_id = end($input_pieces);
        
        if ($servico_id)
        {
            $response = new stdClass;
            
            try
            {
                TTransaction::open('erphouse');
                
                $servico = Servico::find($servico_id);
                $response->{'list_quantidade_'.$unique_id} = '1,00';
                $response->{'list_valor_'.$unique_id} = number_format($servico->valor,2,',', '.');
                
                TForm::sendData('form_Contrato', $response);
                TTransaction::close();
            }
            catch (Exception $e)
            {
                TTransaction::rollback();
            }
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
