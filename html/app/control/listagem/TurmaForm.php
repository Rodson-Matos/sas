<?php

class TurmaForm extends TPage
{
    protected $form;
    private $formFields = [];
    private static $database = 'sas';
    private static $activeRecord = 'Turma';
    private static $primaryKey = 'turma_id';
    private static $formName = 'form_Turma';

    /**
     * Form constructor
     * @param $param Request
     */
    public function __construct( $param )
    {
        parent::__construct();

        // creates the form
        $this->form = new BootstrapFormBuilder(self::$formName);
        // define the form title
        $this->form->setFormTitle("Cadastro de turma");


        $turma_id = new TEntry('turma_id');
        $n_alunos = new TEntry('n_alunos');
        $tipo_turma = new TCombo('tipo_turma');
        $curso_id = new TDBUniqueSearch('curso_id', 'sas', 'Curso', 'curso_id', 'curso_id','curso_id asc'  );

        $n_alunos->addValidation("Número de alunos", new TRequiredValidator()); 
        $tipo_turma->addValidation("Tipo da turma", new TRequiredValidator()); 
        $curso_id->addValidation("Curso id", new TRequiredValidator()); 

        $turma_id->setEditable(false);
        $tipo_turma->addItems(['MED1'=>'1º ANO','MED2'=>'2º ANO','MED3'=>'3º ANO','MOD1'=>'1º MÓDULO','MOD2'=>'2º MÓDULO','MOD3'=>'3º MÓDULO','MOD4'=>'4º MÓDULO']);
        $curso_id->setMinLength(1);
        $curso_id->setMask('{nome_curso}');

        $turma_id->setSize(100);
        $n_alunos->setSize('70%');
        $curso_id->setSize('70%');
        $tipo_turma->setSize('70%');

        $row1 = $this->form->addFields([new TLabel("ID:", null, '14px', null)],[$turma_id]);
        $row2 = $this->form->addFields([new TLabel("Número de alunos:", '#ff0000', '14px', null)],[$n_alunos]);
        $row3 = $this->form->addFields([new TLabel("Tipo da turma:", '#ff0000', '14px', null)],[$tipo_turma]);
        $row4 = $this->form->addFields([new TLabel("Curso:", '#ff0000', '14px', null)],[$curso_id]);

        // create the form actions
        $btn_onsave = $this->form->addAction("Salvar", new TAction([$this, 'onSave']), 'fa:floppy-o #ffffff');
        $btn_onsave->addStyleClass('btn-primary'); 

        $btn_onclear = $this->form->addAction("Limpar formulário", new TAction([$this, 'onClear']), 'fa:eraser #dd5a43');

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->class = 'form-container';
        // $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);

        parent::add($container);

    }

    public function onSave($param = null) 
    {
        try
        {
            TTransaction::open(self::$database); // open a transaction

            /**
            // Enable Debug logger for SQL operations inside the transaction
            TTransaction::setLogger(new TLoggerSTD); // standard output
            TTransaction::setLogger(new TLoggerTXT('log.txt')); // file
            **/

            $messageAction = null;

            $this->form->validate(); // validate form data

            $object = new Turma(); // create an empty object 

            $data = $this->form->getData(); // get form data as array
            $object->fromArray( (array) $data); // load the object with data

            $object->store(); // save the object 

            // get the generated {PRIMARY_KEY}
            $data->turma_id = $object->turma_id; 

            $this->form->setData($data); // fill form data
            TTransaction::close(); // close the transaction

            /**
            // To define an action to be executed on the message close event:
            $messageAction = new TAction(['className', 'methodName']);
            **/

            new TMessage('info', AdiantiCoreTranslator::translate('Record saved'), $messageAction);

        }
        catch (Exception $e) // in case of exception
        {
            //</catchAutoCode> 

            new TMessage('error', $e->getMessage()); // shows the exception error message
            $this->form->setData( $this->form->getData() ); // keep form data
            TTransaction::rollback(); // undo all pending operations
        }
    }

    public function onEdit( $param )
    {
        try
        {
            if (isset($param['key']))
            {
                $key = $param['key'];  // get the parameter $key
                TTransaction::open(self::$database); // open a transaction

                $object = new Turma($key); // instantiates the Active Record 

                $this->form->setData($object); // fill the form 

                TTransaction::close(); // close the transaction 
            }
            else
            {
                $this->form->clear();
            }
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }
    }

    /**
     * Clear form data
     * @param $param Request
     */
    public function onClear( $param )
    {
        $this->form->clear(true);

    }

    public function onShow($param = null)
    {

    } 

}

