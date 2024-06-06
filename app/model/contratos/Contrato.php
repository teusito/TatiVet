<?php
/**
 * Contrato Active Record
 * @author  <your-name-here>
 */
class Contrato extends TRecord
{
    const TABLENAME = 'contrato';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('cliente_id');
        parent::addAttribute('tipo_contrato_id');
        parent::addAttribute('ativo');
        parent::addAttribute('dt_inicio');
        parent::addAttribute('dt_fim');
        parent::addAttribute('obs');
    }

    public function get_tipo_contrato()
    {
        return TipoContrato::find($this->tipo_contrato_id);
    }

    public function get_cliente()
    {
        return Pessoa::find($this->cliente_id);
    }
    
    public function get_total()
    {
        return ContratoItem::where('contrato_id', '=', $this->id)->sumBy('total');
    }
    
    public function get_ultima_fatura()
    {
        return Fatura::where('cliente_id','=',$this->cliente_id)->where('total','=', $this->get_total())->orderBy('dt_fatura', 'desc')->first()->dt_fatura;
    }
}
