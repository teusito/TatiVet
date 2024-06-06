<?php
/**
 * KanbanFaseForm
 *
 * @version    1.0
 * @package    erphouse
 * @subpackage control
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class KanbanFaseForm extends TWindow
{
    protected $form; // form
    
    // trait with onSave, onClear, onEdit
    use Adianti\Base\AdiantiStandardFormTrait;
    
    /**
     * Class constructor
     * Creates the page and the registration form
     */
    function __construct()
    {
        parent::__construct();
        parent::setSize(400, null);
        parent::removePadding();
        parent::setTitle('Fase');
        
        $this->setDatabase('erphouse');    // defines the database
        $this->setActiveRecord('Fase');   // defines the active record
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_Fase');
        $this->form->setColumnClasses(2, ['col-sm-3','col-sm-9']);
        
        // create the form fields
        $id     = new THidden('id');
        $projeto_id = new THidden('projeto_id');
        $nome = new TEntry('nome');
        $ordem  = new THidden('ordem');
        $id->setEditable(FALSE);
        
        $ordem->setValue(999);
        $projeto_id->setValue( TSession::getValue('projeto_id') );
        
        // add the form fields
        $this->form->addFields( [$id, $projeto_id] );
        $this->form->addFields( [new TLabel('Nome', 'red')], [$nome] );
        $this->form->addFields( [$ordem] );
        
        // define the form action
        $this->form->addAction(_t('Save'), new TAction(array($this, 'onSave')), 'fa:save green');
        
        $this->setAfterSaveAction( new TAction( ['KanbanView', 'onLoad'] ) );
        $this->setUseMessages(FALSE);
        
        TScript::create('$("body").trigger("click")');
        TScript::create('$("[name=nome]").focus()');
        
        parent::add($this->form);
    }
}
