<?php
require_once 'init.php';

$conn = TTransaction::open('erphouse');
TTransaction::dump();

TDatabase::clearData($conn, 'evento');
for ($n=1; $n<= 300; $n++)
{
    $colors = ['#3a87ad', '#ffb800', '#0dc905', '#ff3232', '#00C9D2', '##FDFFBA'];
    $hour  = str_pad(my_rand(1,14), 2, '0', STR_PAD_LEFT);
    $month = str_pad(my_rand(1,12), 2, '0', STR_PAD_LEFT);
    $day   = str_pad(my_rand(1,29), 2, '0', STR_PAD_LEFT);
    
    $event = new Evento;
    $event->inicio = date("Y-$month-$day") . ' ' . $hour . ':00';
    $event->fim   = date("Y-$month-$day") . ' ' . str_pad($hour +4, 2, '0', STR_PAD_LEFT) . ':00';
    $event->cor = $colors[ my_rand(1, count($colors)) -1];
    $event->titulo = "Title for event #{$n}";
    $event->descricao = "Description for event #{$n}";
    $event->system_user_id = 1;
    $event->store();
}

function my_rand($from, $to)
{
    return round( mt_rand(0,100)/100 * ($to-$from), 0) + $from;
}

TTransaction::close();
