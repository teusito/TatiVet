<?php
/**
 * GeraFaturasList
 *
 * @version    1.0
 * @package    erphouse
 * @subpackage control
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class GeraFaturasList extends TPage
{
    private $form;
    
    public function __construct()
    {
        parent::__construct();
        
        $this->form = new BootstrapFormBuilder;
        $this->form->setFormTitle('Gerar faturas');
        
        $contrato_list = new TCheckList('contrato_list');
        
        $contrato_list->addColumn('id',          'Id',          'center',  '10%');
        $contrato_list->addColumn('cliente->nome_fantasia', 'Cliente', 'left',    '50%');
        $column_ultima_fatura = $contrato_list->addColumn('ultima_fatura', 'Última fatura', 'left',    '50%');
        $column_total = $contrato_list->addColumn('total',  'Valor',       'right',    '40%');
        
        $column_total->setTransformer( function($value) {
            if (is_numeric($value)) {
                return 'R$&nbsp;'.number_format($value, 2, ',', '.');
            }
            return $value;
        });
        
        $column_ultima_fatura->setTransformer( function($value) {
            $value_br = TDate::convertToMask($value, 'yyyy-mm-dd', 'dd/mm/yyyy');
            
            $month = substr($value,5,2);
            $year  = substr($value,0,4);
            
            $label = ( ($month == date('m') ) && ( $year==date('Y') ) ) ? 'success' : 'warning';
            
            if ($value)
            {
                $div = new TElement('span');
                $div->class="label label-" . $label;
                $div->style="text-shadow:none; font-size:12px";
                $div->add( $value_br );
                return $div;
            }
        });
        
        $input_search = new TEntry('search');
        $input_search->placeholder = _t('Search');
        $input_search->setSize('100%');
        $contrato_list->enableSearch($input_search, 'cliente->nome');
        
        $hbox = new THBox;
        $hbox->style = 'border-bottom: 1px solid gray;padding-bottom:10px';
        $hbox->add( new TLabel('Contratos') );
        $hbox->add( $input_search )->style = 'float:right;width:30%;';
        
        // load order items
        TTransaction::open('erphouse');
        $contratos = Contrato::where('ativo', '=', 'Y')->load();
        $contrato_list->addItems( $contratos );
        TTransaction::close();
        
        $this->form->addContent( [$hbox] );
        $this->form->addFields( [$contrato_list] );
        
        $this->form->addHeaderAction( 'Gerar', new TAction([$this, 'onGenerate']), 'fa:clipboard-check green');
        
        // wrap the page content using vertical box
        $vbox = new TVBox;
        $vbox->style = 'width: 100%';
        //$vbox->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $vbox->add($this->form);
        parent::add($vbox);
    }
    
    /**
     * Simulates an save button
     * Show the form content
     */
    public function onGenerate($param)
    {
        try
        {
            TTransaction::open('erphouse');
            
            $data = $this->form->getData();
            
            $contratos_ids = $data->contrato_list;
            
            if ($contratos_ids)
            {
                foreach ($contratos_ids as $contrato_id)
                {
                    $contrato = Contrato::find($contrato_id);
                    
                    if ($contrato)
                    {
                        $fatura = new Fatura;
                        $fatura->cliente_id        = $contrato->cliente_id;
                        $fatura->dt_fatura         = date('Y-m-d');
                        $fatura->mes               = date('m');
                        $fatura->ano               = date('Y');
                        $fatura->total             = $contrato->total;
                        $fatura->ativo             = 'Y';
                        $fatura->financeiro_gerado = 'N';
                        $fatura->store();
                        
                        $contrato_items = ContratoItem::where('contrato_id', '=', $contrato->id)->load();
                        
                        if ($contrato_items)
                        {
                            foreach ($contrato_items as $contrato_item)
                            {
                                $fatura_item = new FaturaItem;
                                $fatura_item->servico_id = $contrato_item->servico_id;
                                $fatura_item->fatura_id  = $fatura->id;
                                $fatura_item->valor      = $contrato_item->valor;
                                $fatura_item->quantidade = $contrato_item->quantidade;
                                $fatura_item->total      = $contrato_item->total;
                                $fatura_item->store();
                            }
                        }
                    }
                }
                
                new TMessage('info', 'Fatura geradas com sucesso');
            }
            
            // put the data back to the form
            $this->form->setData($data);
            
            TTransaction::close();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }
}
