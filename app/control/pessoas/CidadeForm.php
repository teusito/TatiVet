<?php
/**
 * CidadeForm
 *
 * @version    1.0
 * @package    erphouse
 * @subpackage control
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class CidadeForm extends TPage
{
    protected $form; // form
    
    use Adianti\Base\AdiantiStandardFormTrait; // Standard form methods
    
    /**
     * Class constructor
     * Creates the page and the registration form
     */
    function __construct()
    {
        parent::__construct();
        
        parent::setTargetContainer('adianti_right_panel');
        $this->setAfterSaveAction( new TAction(['CidadeList', 'onReload'], ['register_state' => 'true']) );
        
        $this->setDatabase('erphouse');              // defines the database
        $this->setActiveRecord('Cidade');     // defines the active record
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_Cidade');
        $this->form->setFormTitle('Cidade');
        
        $this->form->setColumnClasses( 2, ['col-sm-5 col-lg-4', 'col-sm-7 col-lg-8'] );$this->form->setClientValidation(true);
        

        // create the form fields
        $id = new TEntry('id');
        $nome = new TEntry('nome');
        $codigo_ibge = new TEntry('codigo_ibge');
        $estado_id = new TDBUniqueSearch('estado_id', 'erphouse', 'Estado', 'id', 'nome');
        $estado_id->setMinLength(0);
        $estado_id->setMask('{nome} ({uf})');

        // add the fields
        $this->form->addFields( [ new TLabel('Id') ], [ $id ] );
        $this->form->addFields( [ new TLabel('Nome') ], [ $nome ] );
        $this->form->addFields( [ new TLabel('Codigo IBGE') ], [ $codigo_ibge ] );
        $this->form->addFields( [ new TLabel('Estado') ], [ $estado_id ] );

        $nome->addValidation('Nome', new TRequiredValidator);
        $codigo_ibge->addValidation('Codigo Ibge', new TRequiredValidator);
        $estado_id->addValidation('Estado Id', new TRequiredValidator);


        // set sizes
        $id->setSize('100%');
        $nome->setSize('100%');
        $codigo_ibge->setSize('100%');
        $estado_id->setSize('100%');


        $id->setEditable(FALSE);
        
        // create the form actions
        $btn = $this->form->addAction(_t('Save'), new TAction([$this, 'onSave']), 'fa:save');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addActionLink(_t('New'),  new TAction([$this, 'onEdit']), 'fa:eraser red');
        $this->form->addHeaderActionLink( _t('Close'), new TAction([$this, 'onClose']), 'fa:times red');
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        // $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        
        parent::add($container);
    }
    
    /**
     * Close side panel
     */
    public static function onClose($param)
    {
        TScript::create("Template.closeRightPanel()");
    }
}
