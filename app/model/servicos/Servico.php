<?php
/**
 * Servico Active Record
 * @author  <your-name-here>
 */
class Servico extends TRecord
{
    const TABLENAME = 'servico';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('nome');
        parent::addAttribute('valor');
        parent::addAttribute('tipo_servico_id');
        parent::addAttribute('ativo');
    }

    public function get_tipo_servico()
    {
        return TipoServico::find($this->tipo_servico_id);
    }

}
