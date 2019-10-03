<?php

class Curso extends TRecord
{
    const TABLENAME  = 'curso';
    const PRIMARYKEY = 'curso_id';
    const IDPOLICY   =  'serial'; // {max, serial}

    

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('nome_curso');
        parent::addAttribute('tipo_curso');
        parent::addAttribute('modalidade');
        parent::addAttribute('turno_curso');
            
    }

    /**
     * Method getCursoDisciplinas
     */
    public function getCursoDisciplinas()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('curso_id', '=', $this->curso_id));
        return CursoDisciplina::getObjects( $criteria );
    }
    /**
     * Method getCursoHorarios
     */
    public function getCursoHorarios()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('curso_id', '=', $this->curso_id));
        return CursoHorario::getObjects( $criteria );
    }
    /**
     * Method getTurmas
     */
    public function getTurmas()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('curso_id', '=', $this->curso_id));
        return Turma::getObjects( $criteria );
    }

    
}

