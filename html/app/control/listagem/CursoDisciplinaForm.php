<?php

class CursoDisciplinaForm extends TPage
{
    protected $form;
    private $formFields = [];
    private static $database = 'sas';
    private static $activeRecord = 'CursoDisciplina';
    private static $primaryKey = 'id';
    private static $formName = 'form_CursoDisciplina';

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
        $this->form->setFormTitle('Disciplina ao Curso');


        $id = new TEntry('id');
        $curso_id = new TDBCombo('curso_id', 'sas', 'Curso', 'curso_id', '{nome_curso}','curso_id asc'  );
        $disciplina_id = new TDBCombo('disciplina_id', 'sas', 'Disciplina', 'disciplina_id', '{nome_disc}','disciplina_id asc'  );

        $curso_id->addValidation('Curso id', new TRequiredValidator()); 
        $disciplina_id->addValidation('Disciplina id', new TRequiredValidator()); 

        $id->setEditable(false);
        $id->setSize(100);
        $curso_id->setSize('70%');
        $disciplina_id->setSize('70%');

        $row1 = $this->form->addFields([new TLabel('ID:', null, '14px', null)],[$id]);
        $row2 = $this->form->addFields([new TLabel('Curso:', '#ff0000', '14px', null)],[$curso_id]);
        $row3 = $this->form->addFields([new TLabel('Disciplina:', '#ff0000', '14px', null)],[$disciplina_id]);

        // create the form actions
        $btn_onsave = $this->form->addAction('Salvar', new TAction([$this, 'onSave']), 'fa:floppy-o #ffffff');
        $btn_onsave->addStyleClass('btn-primary'); 

        $btn_onclear = $this->form->addAction('Limpar formulário', new TAction([$this, 'onClear']), 'fa:eraser #dd5a43');

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->class = 'form-container';
        $container->add(TBreadCrumb::create(['Listagem','Cadastro de Disciplinas do Curso']));
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

            $object = new CursoDisciplina(); // create an empty object 

            $data = $this->form->getData(); // get form data as array
            $object->fromArray( (array) $data); // load the object with data

            $object->store(); // save the object 

            // get the generated {PRIMARY_KEY}
            $data->id = $object->id; 

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

                $object = new CursoDisciplina($key); // instantiates the Active Record 

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

