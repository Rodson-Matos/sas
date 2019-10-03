<?php

class CursoDisciplina extends TRecord
{
    const TABLENAME  = 'curso_disciplina';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}

    private $disciplina;
    private $curso;

    

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('curso_id');
        parent::addAttribute('disciplina_id');
            
    }

    /**
     * Method set_disciplina
     * Sample of usage: $var->disciplina = $object;
     * @param $object Instance of Disciplina
     */
    public function set_disciplina(Disciplina $object)
    {
        $this->disciplina = $object;
        $this->disciplina_id = $object->disciplina_id;
    }

    /**
     * Method get_disciplina
     * Sample of usage: $var->disciplina->attribute;
     * @returns Disciplina instance
     */
    public function get_disciplina()
    {
    
        // loads the associated object
        if (empty($this->disciplina))
            $this->disciplina = new Disciplina($this->disciplina_id);
    
        // returns the associated object
        return $this->disciplina;
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

    
}

