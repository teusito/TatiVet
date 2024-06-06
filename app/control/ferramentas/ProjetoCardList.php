<?php
/**
 * ProjetoCardList
 *
 * @version    1.0
 * @package    erphouse
 * @subpackage control
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class ProjetoCardList extends TPage
{
    private $form;
    
    public function __construct()
    {
        parent::__construct();
        
        $cards = new TCardView;
        $cards->setUseButton();
        
        try
        {
            TTransaction::open('erphouse');
            $projetos_usuario = ProjetoUsuario::where('system_user_id','=',TSession::getValue('userid'))->getIndexedArray('projeto_id');
            $projetos = Projeto::where('id', 'IN', $projetos_usuario)->load();
            TTransaction::close();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
        
        foreach ($projetos as $projeto)
        {
            $cards->addItem($projeto);
        }
        
        //$cards->setTitleAttribute('nome');
        
        $cards->setItemTemplate('{nome}');
        $action   = new TAction([$this, 'onSelect'], ['id'=> '{id}']);
        $cards->addAction($action,   'Seleciona',   'fa:check blue');
        
        parent::add($cards);
    }
    
    /**
     * Item edit action
     */
    public static function onSelect($param = NULL)
    {
        TSession::setValue('projeto_id', $param['id']);
        TScript::create('$("body").addClass("ls-closed");');
        AdiantiCoreApplication::loadPage('KanbanView', 'onLoad');
    }
}
