<?php

//<fileHeader>
  
//</fileHeader>

class Horarios extends TRecord
{
    const TABLENAME  = 'horarios';
    const PRIMARYKEY = 'horarios_id';
    const IDPOLICY   =  'serial'; // {max, serial}
    
    
    
    //<classProperties>
  
    //</classProperties>
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('horario');
        parent::addAttribute('dia_sem');
        //<onAfterConstruct>
  
        //</onAfterConstruct>
    }

    
    /**
     * Method getCursoHorarios
     */
    public function getCursoHorarios()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('horarios_id', '=', $this->horarios_id));
        return CursoHorario::getObjects( $criteria );
    }
    
    //<userCustomFunctions>
  
    //</userCustomFunctions>
}


