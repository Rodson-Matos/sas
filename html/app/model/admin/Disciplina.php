<?php

//<fileHeader>
  
//</fileHeader>

class Disciplina extends TRecord
{
    const TABLENAME  = 'disciplina';
    const PRIMARYKEY = 'disciplina_id';
    const IDPOLICY   =  'serial'; // {max, serial}
    
    
    
    //<classProperties>
  
    //</classProperties>
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('nome_disc');
        parent::addAttribute('carga_h');
        //<onAfterConstruct>
  
        //</onAfterConstruct>
    }

    
    /**
     * Method getCursoDisciplinas
     */
    public function getCursoDisciplinas()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('disciplina_id', '=', $this->disciplina_id));
        return CursoDisciplina::getObjects( $criteria );
    }
    
    //<userCustomFunctions>
  
    //</userCustomFunctions>
}


