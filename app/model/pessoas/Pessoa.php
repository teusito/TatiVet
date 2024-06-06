<?php
/**
 * Pessoa Active Record
 * @author  <your-name-here>
 */
class Pessoa extends TRecord
{
    const TABLENAME = 'pessoa';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    const CREATEDAT = 'created_at';
    const UPDATEDAT = 'updated_at';
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('nome');
        parent::addAttribute('nome_fantasia');
        parent::addAttribute('tipo');
        parent::addAttribute('codigo_nacional');
        parent::addAttribute('codigo_estadual');
        parent::addAttribute('codigo_municipal');
        parent::addAttribute('fone');
        parent::addAttribute('email');
        parent::addAttribute('observacao');
        parent::addAttribute('cep');
        parent::addAttribute('logradouro');
        parent::addAttribute('numero');
        parent::addAttribute('complemento');
        parent::addAttribute('bairro');
        parent::addAttribute('cidade_id');
        parent::addAttribute('created_at');
        parent::addAttribute('updated_at');
        parent::addAttribute('grupo_id');
    }
    
    public function get_cidade()
    {
        return Cidade::find($this->cidade_id);
    }
    
    public function get_grupo()
    {
        return Grupo::find($this->grupo_id);
    }
    
    public function delete($id = null)
    {
        $id = isset($id) ? $id : $this->id;
        
        PessoaPapel::where('pessoa_id', '=', $this->id)->delete();
        parent::delete($id);
    }
}
