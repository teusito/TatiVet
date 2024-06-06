<?php
/**
 * ContaReceberForm
 *
 * @version    1.0
 * @package    erphouse
 * @subpackage control
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class ContaReceberForm extends TPage
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
        
        $this->setDatabase('erphouse');              // defines the database
        $this->setActiveRecord('ContaReceber');     // defines the active record
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_ContaReceber');
        $this->form->setFormTitle('Conta a Receber');
        $this->form->setClientValidation(true);
        $this->form->setColumnClasses( 2, ['col-sm-5 col-lg-4', 'col-sm-7 col-lg-8'] );

        // create the form fields
        $id = new TEntry('id');
        $dt_emissao = new TDate('dt_emissao');
        $dt_vencimento = new TDate('dt_vencimento');
        $dt_pagamento = new TDate('dt_pagamento');
        $pessoa_id = new TDBUniqueSearch('pessoa_id', 'erphouse', 'Pessoa', 'id', 'nome_fantasia');
        $valor = new TNumeric('valor', 2, ',', '.', true);
        $obs = new TText('obs');


        // add the fields
        $this->form->addFields( [ new TLabel('Id') ], [ $id ] );
        $this->form->addFields( [ new TLabel('Pessoa') ], [ $pessoa_id ] );
        $this->form->addFields( [ new TLabel('Dt Emissao') ], [ $dt_emissao ] );
        $this->form->addFields( [ new TLabel('Dt Vencimento') ], [ $dt_vencimento ] );
        $this->form->addFields( [ new TLabel('Dt Pagamento') ], [ $dt_pagamento ] );
        $this->form->addFields( [ new TLabel('Valor') ], [ $valor ] );
        $this->form->addFields( [ new TLabel('Obs') ], [ $obs ] );

        $dt_emissao->addValidation('Dt Emissao', new TRequiredValidator);
        $dt_vencimento->addValidation('Dt Vencimento', new TRequiredValidator);
        $pessoa_id->addValidation('Pessoa Id', new TRequiredValidator);


        // set sizes
        $id->setSize('100%');
        $dt_emissao->setSize('100%');
        $dt_vencimento->setSize('100%');
        $dt_pagamento->setSize('100%');
        $pessoa_id->setSize('100%');
        $valor->setSize('100%');
        $obs->setSize('100%');
        $dt_emissao->setMask('dd/mm/yyyy');
        $dt_vencimento->setMask('dd/mm/yyyy');
        $dt_pagamento->setMask('dd/mm/yyyy');
        $dt_emissao->setDatabaseMask('yyyy-mm-dd');
        $dt_vencimento->setDatabaseMask('yyyy-mm-dd');
        $dt_pagamento->setDatabaseMask('yyyy-mm-dd');
        $pessoa_id->setMinLength(0);
        
        $dt_emissao->setValue(date('Y-m-d'));
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
     * Save form data
     * @param $param Request
     */
    public function onSave( $param )
    {
        try
        {
            TTransaction::open('erphouse'); // open a transaction
            
            $this->form->validate(); // validate form data
            $data = $this->form->getData(); // get form data as array
            
            $object = new ContaReceber;  // create an empty object
            $object->fromArray( (array) $data); // load the object with data
            $object->mes = TDateTime::convertToMask($object->dt_vencimento, 'yyyy-mm-dd', 'mm');
            $object->ano = TDateTime::convertToMask($object->dt_vencimento, 'yyyy-mm-dd', 'yyyy');
            
            if (empty($object->id))
            {
                $object->ativo = 'Y';
            }
            
            $object->store(); // save the object
            
            // get the generated id
            $data->id = $object->id;
            
            $this->form->setData($data); // fill form data
            TTransaction::close(); // close the transaction
            
            $pos_action = new TAction(['ContaReceberList', 'onReload'], ['register_state' => 'true']);
                    
            new TMessage('info', AdiantiCoreTranslator::translate('Record saved'), $pos_action);
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            $this->form->setData( $this->form->getData() ); // keep form data
            TTransaction::rollback(); // undo all pending operations
        }
    }
    /**
     * Close side panel
     */
    public static function onClose($param)
    {
        TScript::create("Template.closeRightPanel()");
    }
}
