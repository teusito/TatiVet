<?php
/**
 * ContratoDashboard
 *
 * @version    1.0
 * @package    erphouse
 * @subpackage control
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class ContratoDashboard extends TPage
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
            $total_ativos  = Contrato::where('ativo','=','Y')->count();
            $total_renovar = Contrato::where('ativo','=','Y')->where('dt_fim', '<=', date('Y-m-d'))->count();
            $contratos_grupo = ViewContratos::where('ativo','=','Y')->groupBy('nome_grupo')->sumBy('total');
            $contratos_tipo  = ViewContratos::where('ativo','=','Y')->groupBy('tipo_contrato')->sumBy('total');
            $top_clientes    = ViewContratos::where('ativo','=','Y')->groupBy('nome_cliente')->take(5)->orderBy('total', 'desc')->sumBy('total');
            $old_clientes    = ViewContratos::where('ativo','=','Y')->take(5)->orderBy('dt_inicio')->load();
            TTransaction::close();
            
            
            $indicator1 = new THtmlRenderer('app/resources/info-box.html');
            $indicator1->enableSection('main', ['title' => 'Contratos ativos', 'icon' => 'check-double', 'background' => 'green', 'value' => $total_ativos ] );
            
            
            $indicator2 = new THtmlRenderer('app/resources/info-box.html');
            $indicator2->enableSection('main', ['title' => 'Renovações pendentes', 'icon' => 'hourglass-start', 'background' => 'orange', 'value' => $total_renovar ] );
            
            $data = [];
            $data[] = [ 'Estado', 'Contratos' ];
            if ($contratos_grupo)
            {
                foreach($contratos_grupo as $contrato_grupo)
                {
                    $data[] = [ $contrato_grupo->nome_grupo, (float) $contrato_grupo->total ];
                }
            }
            $grafico1 = new THtmlRenderer('app/resources/google_pie_chart.html');
            $grafico1->enableSection('main', ['data'   => json_encode($data), 'width'  => '100%', 'height'  => '400px',
                                              'title'  => 'Contratos por grupo', 'ytitle' => 'Estado', 'xtitle' => 'Contratos', 'uniqid' => uniqid()]);
            
            
            
            $data = [];
            $data[] = [ 'Estado', 'Contratos' ];
            if ($contratos_tipo)
            {
                foreach($contratos_tipo as $contrato_tipo)
                {
                    $data[] = [ $contrato_tipo->tipo_contrato, (float) $contrato_tipo->total ];
                }
            }
            $grafico2 = new THtmlRenderer('app/resources/google_pie_chart.html');
            $grafico2->enableSection('main', ['data'   => json_encode($data), 'width'  => '100%', 'height'  => '400px',
                                              'title'  => 'Contratos por tipo', 'ytitle' => 'Tipo', 'xtitle' => 'Contratos', 'uniqid' => uniqid()]);
            
            
            
            $table1 = TTable::create( [ 'class' => 'table table-striped table-hover', 'style' => 'border-collapse:collapse' ] );
            $table1->addSection('thead');
            $table1->addRowSet('Cliente', 'Quantidade');
            
            if ($top_clientes)
            {
                $table1->addSection('tbody');
                foreach ($top_clientes as $top_cliente)
                {
                    $row = $table1->addRow();
                    $row->addCell($top_cliente->nome_cliente);
                    $row->addCell('R$&nbsp;' . number_format($top_cliente->total,2,',','.'))->style = 'text-align:right';
                }
            }
            
            
            $table2 = TTable::create( [ 'class' => 'table table-striped table-hover', 'style' => 'border-collapse:collapse' ] );
            $table2->addSection('thead');
            $table2->addRowSet('Cliente', 'Data');
            
            if ($old_clientes)
            {
                $table2->addSection('tbody');
                foreach ($old_clientes as $old_cliente)
                {
                    $row = $table2->addRow();
                    $row->addCell($old_cliente->nome_cliente);
                    $row->addCell(TDate::convertToMask($old_cliente->dt_inicio, 'yyyy-mm-dd', 'dd/mm/yyyy'));
                }
            }
            
            $div->add( TElement::tag('div', $indicator1, ['class' => 'col-sm-6']) );
            $div->add( TElement::tag('div', $indicator2, ['class' => 'col-sm-6']) );
            $div->add( TElement::tag('div', $grafico1,   ['class' => 'col-sm-6']) );
            $div->add( TElement::tag('div', $grafico2,   ['class' => 'col-sm-6']) );
            $div->add( TElement::tag('div', TPanelGroup::pack('Ranking TOP 5 Clientes', $table1),     ['class' => 'col-sm-6']) );
            $div->add( TElement::tag('div', TPanelGroup::pack('Raking 5 Clientes mais antigos', $table2),     ['class' => 'col-sm-6']) );
            
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
