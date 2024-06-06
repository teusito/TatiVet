<?php
/**
 * FinanceiroDashboard
 *
 * @version    1.0
 * @package    erphouse
 * @subpackage control
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class FinanceiroDashboard extends TPage
{
    /**
     * Class constructor
     * Creates the page
     */
    function __construct()
    {
        parent::__construct();
        
        $vbox = new TVBox;
        $vbox->style = 'width: 100%';
        
        $div = new TElement('div');
        $div->class = "row";
        
        try
        {
            TTransaction::open('erphouse');
            $previsto_ano  = ContaReceber::where('ativo','=','Y')->where('ano','=', date('Y'))->sumBy('valor');
            $previsto_mes  = ContaReceber::where('ativo','=','Y')->where('ano','=', date('Y'))->where('mes','=', date('m'))->sumBy('valor');
            $aberto_ano    = ContaReceber::where('ativo','=','Y')->where('ano','=', date('Y'))->where('dt_pagamento','IS',NULL)->sumBy('valor');
            $aberto_mes    = ContaReceber::where('ativo','=','Y')->where('ano','=', date('Y'))->where('mes','=', date('m'))->where('dt_pagamento','IS',NULL)->sumBy('valor');
            $receber_meses = ContaReceber::where('ativo','=','Y')->where('ano','=', date('Y'))->orderBy('mes')->groupBy('mes')->sumBy('valor');
            $pago_meses    = ContaReceber::where('ativo','=','Y')->where('ano','=', date('Y'))->where('dt_pagamento','IS NOT', NULL)->orderBy('mes')->groupBy('mes')->sumBy('valor');
            $top_clientes  = ContaReceber::where('ativo','=','Y')->where('dt_pagamento','IS', NULL)->groupBy('pessoa_id')->orderBy('valor', 'desc')->sumBy('valor');
            TTransaction::close();
            
            $indicator1 = new THtmlRenderer('app/resources/info-box.html');
            $indicator2 = new THtmlRenderer('app/resources/info-box.html');
            $indicator3 = new THtmlRenderer('app/resources/info-box.html');
            $indicator4 = new THtmlRenderer('app/resources/info-box.html');
            
            $indicator1->enableSection('main', ['title' => 'Previsto ano', 'icon' => 'money-bill', 'background' => 'blue',
                                                'value' => 'R$ ' . number_format( (float) $previsto_ano,2,',','.') ] );
                                                
            $indicator2->enableSection('main', ['title' => 'Previsto mês', 'icon' => 'money-bill', 'background' => 'blue',
                                               'value'  => 'R$ ' . number_format( (float) $previsto_mes,2,',','.') ] );
            
            $indicator3->enableSection('main', ['title' => 'Aberto ano', 'icon' => 'money-bill', 'background' => 'orange',
                                                'value' => 'R$ ' . number_format( (float) $aberto_ano,2,',','.') ] );                                    
            
            $indicator4->enableSection('main', ['title' => 'Aberto mês', 'icon' => 'money-bill', 'background' => 'orange',
                                               'value'  => 'R$ ' . number_format( (float) $aberto_mes,2,',','.') ] );
            
            $meses = ['01' => 'Jan', '02' => 'Fev', '03' => 'Mar', '04' => 'Abr', '05' => 'Mai', '06' => 'Jun', '07' => 'Jul', '08' => 'Ago', '09' => 'Set', '10' => 'Out', '11' => 'Nov', '12' => 'Dez'];
            
            
            $div->add( TElement::tag('div', $indicator1, ['class' => 'col-sm-6']) );
            $div->add( TElement::tag('div', $indicator2, ['class' => 'col-sm-6']) );
            $div->add( TElement::tag('div', $indicator3, ['class' => 'col-sm-6']) );
            $div->add( TElement::tag('div', $indicator4, ['class' => 'col-sm-6']) );
            
            
            $pago_por_mes = [];
            
            if ($pago_meses)
            {
                foreach ($pago_meses as $pago_mes)
                {
                    $pago_por_mes[ $pago_mes->mes ] = $pago_mes->valor;
                }
            }
            
            $table1 = TTable::create( [ 'class' => 'table table-striped table-hover', 'style' => 'border-collapse:collapse' ] );
            $table1->addSection('thead');
            $table1->addRowSet('Mês', 'Receber', 'Recebido', 'Saldo');
            
            if ($receber_meses)
            {
                $total_receber = 0;
                $total_pago    = 0;
                $total_saldo   = 0;
                
                $table1->addSection('tbody');
                foreach ($receber_meses as $receber_mes)
                {
                    $receber = $receber_mes->valor;
                    $pago    = $pago_por_mes[ $receber_mes->mes] ?? 0;
                    $saldo   = $receber - $pago;
                    $row = $table1->addRow();
                    $row->addCell( $meses[ $receber_mes->mes ]);
                    $row->addCell('R$&nbsp;' . number_format($receber,2,',','.'))->style = 'text-align:right';
                    $row->addCell('R$&nbsp;' . number_format($pago,2,',','.'))->style = 'text-align:right';
                    $row->addCell('R$&nbsp;' . number_format($saldo,2,',','.'))->style = 'text-align:right';
                    
                    $total_receber += $receber;
                    $total_pago += $pago;
                    $total_saldo += $saldo;
                }
                $table1->addSection('tfoot');
                $row = $table1->addRow();
                $row->addCell( 'Total' );
                $row->addCell('R$&nbsp;' . number_format($total_receber,2,',','.'))->style = 'text-align:right';
                $row->addCell('R$&nbsp;' . number_format($total_pago,2,',','.'))->style = 'text-align:right';
                $row->addCell('R$&nbsp;' . number_format($total_saldo,2,',','.'))->style = 'text-align:right';
            }
            $div->add( TElement::tag('div', TPanelGroup::pack('A receber por mês', $table1), ['class' => 'col-sm-6']) );
            
            
            
            $table2 = TTable::create( [ 'class' => 'table table-striped table-hover', 'style' => 'border-collapse:collapse' ] );
            $table2->addSection('thead');
            $table2->addRowSet('Ciente', 'Saldo');
            
            if ($top_clientes)
            {
                $table2->addSection('tbody');
                foreach ($top_clientes as $top_cliente)
                {
                    $row = $table2->addRow();
                    $row->addCell( Pessoa::findInTransaction('erphouse', $top_cliente->pessoa_id)->nome );
                    $row->addCell('R$&nbsp;' . number_format($top_cliente->valor,2,',','.'))->style = 'text-align:right';
                }
            }
            $div->add( TElement::tag('div', TPanelGroup::pack('Saldos em aberto por cliente', $table2), ['class' => 'col-sm-6']) );
            //$vbox->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
            $vbox->add($div);
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
        
        parent::add($vbox);
    }
}
