<?php

class CursoForm extends TPage
{
    protected $form;
    private $formFields = [];
    private static $database = 'sas';
    private static $activeRecord = 'Curso';
    private static $primaryKey = 'curso_id';
    private static $formName = 'form_Curso';

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
        $this->form->setFormTitle("Cadastro de curso");


        $curso_id = new TEntry('curso_id');
        $nome_curso = new TEntry('nome_curso');
        $tipo_curso = new TCombo('tipo_curso');
        $modalidade = new TCombo('modalidade');
        $turno_curso = new TCombo('turno_curso');

        $nome_curso->addValidation("Nome do curso", new TRequiredValidator()); 
        $tipo_curso->addValidation("Tipo do curso", new TRequiredValidator()); 
        $modalidade->addValidation("Modalidade", new TRequiredValidator()); 
        $turno_curso->addValidation("Turno do curso", new TRequiredValidator()); 

        $curso_id->setEditable(false);

        $modalidade->addItems(['INT'=>'INTEGRADO','SUB'=>'SUBSEQUENTE']);
        $turno_curso->addItems(['MAT'=>'MATUTINO','VES'=>'VESPERTINO','NOT'=>'NOTURNO']);
        $tipo_curso->addItems(['TEC'=>'TÉCNICO','SUP'=>'SUPERIOR','MES'=>'MESTRADO','DOC'=>'DOUTORADO']);

        $curso_id->setSize(100);
        $nome_curso->setSize('70%');
        $tipo_curso->setSize('70%');
        $modalidade->setSize('70%');
        $turno_curso->setSize('70%');

        $row1 = $this->form->addFields([new TLabel("Curso id:", null, '14px', null)],[$curso_id]);
        $row2 = $this->form->addFields([new TLabel("Nome do curso:", '#ff0000', '14px', null)],[$nome_curso]);
        $row3 = $this->form->addFields([new TLabel("Tipo do curso:", '#ff0000', '14px', null)],[$tipo_curso]);
        $row4 = $this->form->addFields([new TLabel("Modalidade:", '#ff0000', '14px', null)],[$modalidade]);
        $row5 = $this->form->addFields([new TLabel("Turno do curso:", '#ff0000', '14px', null)],[$turno_curso]);

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

            $object = new Curso(); // create an empty object 

            $data = $this->form->getData(); // get form data as array
            $object->fromArray( (array) $data); // load the object with data

            $object->store(); // save the object 

            // get the generated {PRIMARY_KEY}
            $data->curso_id = $object->curso_id; 

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

                $object = new Curso($key); // instantiates the Active Record 

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

