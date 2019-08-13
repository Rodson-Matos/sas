<?php

//<fileHeader>
  
//</fileHeader>

class CursoHorario extends TRecord
{
    const TABLENAME  = 'curso_horario';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}
    
    
    private $curso;
    private $horarios;
    
    //<classProperties>
  
    //</classProperties>
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('horarios_id');
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
    /**
     * Method set_horarios
     * Sample of usage: $var->horarios = $object;
     * @param $object Instance of Horarios
     */
    public function set_horarios(Horarios $object)
    {
        $this->horarios = $object;
        $this->horarios_id = $object->horarios_id;
    }
    
    /**
     * Method get_horarios
     * Sample of usage: $var->horarios->attribute;
     * @returns Horarios instance
     */
    public function get_horarios()
    {
        
        // loads the associated object
        if (empty($this->horarios))
            $this->horarios = new Horarios($this->horarios_id);
        
        // returns the associated object
        return $this->horarios;
    }
    
    
    //<userCustomFunctions>
  
    //</userCustomFunctions>
}


