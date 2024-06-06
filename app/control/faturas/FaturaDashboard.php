<?php
/**
 * FaturaDashboard
 *
 * @version    1.0
 * @package    erphouse
 * @subpackage control
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class FaturaDashboard extends TPage
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
            $vendas_mes  = Fatura::where('ativo','=','Y')->where('ano','=', date('Y'))->where('mes','=', date('m'))->sumBy('total');
            $vendas_ano  = Fatura::where('ativo','=','Y')->where('ano','=', date('Y'))->sumBy('total');
            $vendas_por_mes  = Fatura::where('ativo','=','Y')->where('ano','=', date('Y'))->orderBy('mes')->groupBy('mes')->sumBy('total');
            $vendas_por_ano  = Fatura::where('ativo','=','Y')->groupBy('ano')->orderBy('ano')->sumBy('total');
            TTransaction::close();
            
            
            $indicator1 = new THtmlRenderer('app/resources/info-box.html');
            $indicator1->enableSection('main', ['title' => 'Vendas no mês', 'icon' => 'calendar-check', 'background' => 'green', 'value' => 'R$ '.number_format( (float) $vendas_mes,2,',','.') ] );
            
            $indicator2 = new THtmlRenderer('app/resources/info-box.html');
            $indicator2->enableSection('main', ['title' => 'Vendas no ano', 'icon' => 'calendar-check', 'background' => 'blue', 'value' => 'R$ '.number_format( (float) $vendas_ano,2,',','.') ] );
            
            
            
            $meses = ['01' => 'Jan', '02' => 'Fev', '03' => 'Mar', '04' => 'Abr', '05' => 'Mai', '06' => 'Jun', '07' => 'Jul', '08' => 'Ago', '09' => 'Set', '10' => 'Out', '11' => 'Nov', '12' => 'Dez'];
            
            $data = [];
            $data[] = [ 'Mês', 'Vendas' ];
            if ($vendas_por_mes)
            {
                foreach($vendas_por_mes as $venda_por_mes)
                {
                    $data[] = [ $meses[ $venda_por_mes->mes ], (float) $venda_por_mes->total ];
                }
            }
            $grafico1 = new THtmlRenderer('app/resources/google_column_chart.html');
            $grafico1->enableSection('main', ['data'   => json_encode($data), 'width'  => '100%', 'height'  => '350px',
                                              'title'  => 'Faturado por mês', 'ytitle' => 'Faturado', 'xtitle' => 'Mês', 'uniqid' => uniqid()]);
            
            
            $data = [];
            $data[] = [ 'Ano', 'Vendas' ];
            if ($vendas_por_ano)
            {
                foreach($vendas_por_ano as $venda_por_ano)
                {
                    $data[] = [ $venda_por_ano->ano, (float) $venda_por_ano->total ];
                }
            }
            $grafico2 = new THtmlRenderer('app/resources/google_column_chart.html');
            $grafico2->enableSection('main', ['data'   => json_encode($data), 'width'  => '100%', 'height'  => '350px',
                                              'title'  => 'Faturado por ano', 'ytitle' => 'Faturado', 'xtitle' => 'Ano', 'uniqid' => uniqid()]);
            
            $div->add( TElement::tag('div', $indicator1, ['class' => 'col-sm-6']) );
            $div->add( TElement::tag('div', $indicator2, ['class' => 'col-sm-6']) );
            $div->add( TElement::tag('div', $grafico1,   ['class' => 'col-sm-12']) );
            $div->add( TElement::tag('div', $grafico2,   ['class' => 'col-sm-12']) );
            
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
