<?php
/**
 * TipoServico Active Record
 * @author  <your-name-here>
 */
class TipoServico extends TRecord
{
    const TABLENAME = 'tipo_servico';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('nome');
    }


}
