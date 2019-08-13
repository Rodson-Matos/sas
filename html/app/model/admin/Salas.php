<?php

//<fileHeader>
  
//</fileHeader>

class Salas extends TRecord
{
    const TABLENAME  = 'salas';
    const PRIMARYKEY = 'sala_id';
    const IDPOLICY   =  'serial'; // {max, serial}
    
    
    
    //<classProperties>
  
    //</classProperties>
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('numero_sala');
        parent::addAttribute('capacidade_sala');
        parent::addAttribute('tipo_sala');
        //<onAfterConstruct>
  
        //</onAfterConstruct>
    }

    
    
    //<userCustomFunctions>
  
    //</userCustomFunctions>
}


