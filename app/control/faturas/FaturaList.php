<?php
/**
 * FaturaList
 *
 * @version    1.0
 * @package    erphouse
 * @subpackage control
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class FaturaList extends TPage
{
    protected $form;     // registration form
    protected $datagrid; // listing
    protected $pageNavigation;
    protected $formgrid;
    protected $deleteButton;
    
    use Adianti\base\AdiantiStandardListTrait;
    
    /**
     * Page constructor
     */
    public function __construct()
    {
        parent::__construct();
        
        $this->setDatabase('erphouse');            // defines the database
        $this->setActiveRecord('Fatura');   // defines the active record
        $this->setDefaultOrder('id', 'desc');         // defines the default order
        $this->setLimit(10);
        // $this->setCriteria($criteria) // define a standard filter

        $this->addFilterField('id', '=', 'id'); // filterField, operator, formField
        $this->addFilterField('cliente_id', 'like', 'cliente_id'); // filterField, operator, formField
        $this->addFilterField('financeiro_gerado', '=', 'financeiro_gerado'); // filterField, operator, formField
        $this->addFilterField('mes', '=', 'mes'); // filterField, operator, formField
        $this->addFilterField('ano', '=', 'ano'); // filterField, operator, formField
        $this->setOrderCommand('cliente->nome_fantasia', '(SELECT nome_fantasia FROM pessoa WHERE pessoa.id=fatura.cliente_id)');
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_search_Fatura');
        $this->form->setFormTitle('Fatura');
        

        // create the form fields
        $id = new TEntry('id');
        $cliente_id = new TDBUniqueSearch('cliente_id', 'erphouse', 'Pessoa', 'id', 'nome_fantasia');
        $financeiro_gerado = new TRadioGroup('financeiro_gerado');
        $mes = new TRadioGroup('mes');
        $ano = new TRadioGroup('ano');
        
        $current = (int) date('Y');
        $mes->addItems( ['01' => 'Jan', '02' => 'Fev', '03' => 'Mar', '04' => 'Abr', '05' => 'Mai', '06' => 'Jun', '07' => 'Jul', '08' => 'Ago', '09' => 'Set', '10' => 'Out', '11' => 'Nov', '12' => 'Dez'] );
        $ano->addItems( [ ($current -5) => ($current -5), ($current -4) => ($current -4), ($current -3) => ($current -3), ($current -2) => ($current -2), ($current -1) => ($current -1), $current => $current ] );
        
        $mes->setLayout('horizontal');
        $ano->setLayout('horizontal');
        $cliente_id->setMinLength(0);
        
        $financeiro_gerado->addItems( ['Y' => 'Sim', 'N' => 'Não', '' => 'Ambos'] );
        $financeiro_gerado->setLayout('horizontal');
        
        // add the fields
        $this->form->addFields( [ new TLabel('Id') ], [ $id ] );
        $this->form->addFields( [ new TLabel('Cliente') ], [ $cliente_id ] );
        $this->form->addFields( [ new TLabel('Financeiro Gerado') ], [ $financeiro_gerado ] );
        $this->form->addFields( [ new TLabel('Mes') ], [ $mes ] );
        $this->form->addFields( [ new TLabel('Ano') ], [ $ano ] );


        // set sizes
        $id->setSize('100%');
        $cliente_id->setSize('100%');
        $financeiro_gerado->setSize('100%');

        
        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue(__CLASS__.'_filter_data') );
        
        // add the search form actions
        $btn = $this->form->addAction(_t('Find'), new TAction([$this, 'onSearch']), 'fa:search');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addActionLink(_t('New'), new TAction(['FaturaForm', 'onEdit']), 'fa:plus green');
        
        // creates a Datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        //$this->datagrid->datatable = 'true';
        // $this->datagrid->enablePopover('Popover', 'Hi <b> {name} </b>');
        

        // creates the datagrid columns
        $column_id = new TDataGridColumn('id', 'Id', 'center');
        $column_cliente_id = new TDataGridColumn('cliente->nome_fantasia', 'Cliente', 'left');
        $column_dt_fatura = new TDataGridColumn('dt_fatura', 'Dt Fatura', 'center');
        $column_total = new TDataGridColumn('total', 'Total', 'right');
        $column_financeiro_gerado = new TDataGridColumn('financeiro_gerado', 'Financeiro', 'center');
        
        
        $column_dt_fatura->enableAutoHide(500);
        $column_total->enableAutoHide(500);
        $column_financeiro_gerado->enableAutoHide(500);
        
        $column_id->setTransformer( function ($value, $object, $row) {
            if ($object->ativo == 'N')
            {
                $row->style= 'color: silver';
            }
            
            return $value;
        });
        
        $column_dt_fatura->setTransformer( function($value) {
            return TDate::convertToMask($value, 'yyyy-mm-dd', 'dd/mm/yyyy');
        });
        
        $column_total->setTransformer( function($value) {
            if (is_numeric($value)) {
                return 'R$&nbsp;'.number_format($value, 2, ',', '.');
            }
            return $value;
        });
        
        $column_financeiro_gerado->setTransformer( function($value, $object) {
            if ($object->ativo == 'N')
            {
                return 'Cancelada';
            }
            
            if ($object->financeiro_gerado == 'N')
            {
                $value = 'Não gerado';
                $label = 'danger';
            }
            else if ($object->financeiro_gerado == 'Y')
            {
                $value = 'Aguardando';
                $label = 'warning';
                
                TTransaction::open('erphouse');
                $fcc = FaturaContaReceber::where('fatura_id', '=', $object->id)->first();
                if ($fcc)
                {
                    $conta_receber = ContaReceber::find($fcc->conta_receber_id);
                    if ($conta_receber)
                    {
                        if (!empty($conta_receber->dt_pagamento))
                        {
                            $value = 'Pago';
                            $label = 'success';
                        }
                    }
                }
                TTransaction::close();
            }
            
            $div = new TElement('span');
            $div->class="label label-" . $label;
            $div->style="text-shadow:none; font-size:12px";
            $div->add( $value );
            return $div;
        });
        
        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_cliente_id);
        $this->datagrid->addColumn($column_dt_fatura);
        $this->datagrid->addColumn($column_financeiro_gerado);
        $this->datagrid->addColumn($column_total);


        // creates the datagrid column actions
        $column_id->setAction(new TAction([$this, 'onReload']), ['order' => 'id']);
        $column_cliente_id->setAction(new TAction([$this, 'onReload']), ['order' => 'cliente->nome_fantasia']);
        $column_dt_fatura->setAction(new TAction([$this, 'onReload']), ['order' => 'dt_fatura']);

        
        $action1 = new TDataGridAction(['FaturaForm', 'onEdit'], ['id'=>'{id}']);
        $action2 = new TDataGridAction([$this, 'onCancel'], ['id'=>'{id}']);
        
        $this->datagrid->addAction($action1, _t('Edit'),   'far:edit blue');
        $this->datagrid->addAction($action2 ,_t('Cancel'), 'fa:power-off red');
        
        $action1->setDisplayCondition( function ($object) {
            return $object->ativo !== 'N';
        });
        
        $action2->setDisplayCondition( function ($object) {
            return $object->ativo !== 'N';
        });
        
        // create the datagrid model
        $this->datagrid->createModel();
        
        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction([$this, 'onReload']));
        
        $panel = new TPanelGroup('', 'white');
        $panel->add($this->datagrid);
        $panel->addFooter($this->pageNavigation);
        
        // header actions
        $dropdown = new TDropDown(_t('Export'), 'fa:list');
        $dropdown->setPullSide('right');
        $dropdown->setButtonClass('btn btn-default waves-effect dropdown-toggle');
        $dropdown->addAction( _t('Save as CSV'), new TAction([$this, 'onExportCSV'], ['register_state' => 'false', 'static'=>'1']), 'fa:table blue' );
        $dropdown->addAction( _t('Save as PDF'), new TAction([$this, 'onExportPDF'], ['register_state' => 'false', 'static'=>'1']), 'far:file-pdf red' );
        $panel->addHeaderWidget( $dropdown );
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        // $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        $container->add($panel);
        
        parent::add($container);
    }
    
    /**
     * Cancela fatura
     */
    public function onCancel($param)
    {
        try
        {
            TTransaction::open('erphouse');
            $fatura = new Fatura($param['id']);
            
            if ($fatura->ativo == 'Y')
            {
                $fcr = FaturaContaReceber::where('fatura_id', '=', $fatura->id)->first();
                
                if ($fcr)
                {
                    $contareceber = ContaReceber::find($fcr->conta_receber_id);
                    
                    if (!empty($contareceber->dt_pagamento))
                    {
                        throw new Exception('Conta a receber já quitada');
                    }
                    else
                    {
                        $contareceber->ativo = 'N';
                        $contareceber->store();
                    }
                }
                $fatura->ativo = 'N';
                $fatura->store();
                
                new TMessage('info', 'Fatura cancelada');
            }
            
            TTransaction::close();
            
            $this->onReload($param);
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }
    
    public function Delete($param)
    {
        new TMessage('error', 'Operação não permitida');
    }
}
