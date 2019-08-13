<?php

//<fileHeader>
  
//</fileHeader>

class Turma extends TRecord
{
    const TABLENAME  = 'turma';
    const PRIMARYKEY = 'turma_id';
    const IDPOLICY   =  'serial'; // {max, serial}
    
    
    private $curso;
    
    //<classProperties>
  
    //</classProperties>
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('tipo_turma');
        parent::addAttribute('n_alunos');
        parent::addAttribute('curso_id');
        //<onAfterConstruct>
  
        //</onAfterConstruct>
    }

    /**
     * Method set_curso
     * Sample of usage: $var->curso = $object;
     * @param $object Instance of Curso
     */
    public function set_curso(Curso $object)
    {
        $this->curso = $object;
        $this->curso_id = $object->curso_id;
    }
    
    /**
     * Method get_curso
     * Sample of usage: $var->curso->attribute;
     * @returns Curso instance
     */
    public function get_curso()
    {
        
        // loads the associated object
        if (empty($this->curso))
            $this->curso = new Curso($this->curso_id);
        
        // returns the associated object
        return $this->curso;
    }
    
    
    //<userCustomFunctions>
  
    //</userCustomFunctions>
}

