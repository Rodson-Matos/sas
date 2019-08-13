<?php

class SalasForm extends TPage
{
    protected $form;
    private $formFields = [];
    private static $database = 'sas';
    private static $activeRecord = 'Salas';
    private static $primaryKey = 'sala_id';
    private static $formName = 'form_Salas';

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
        $this->form->setFormTitle('Cadastro de salas');


        $sala_id = new TEntry('sala_id');
        $numero_sala = new TEntry('numero_sala');
        $capacidade_sala = new TEntry('capacidade_sala');
        $tipo_sala = new TCombo('tipo_sala');

        $numero_sala->addValidation('Número da sala', new TRequiredValidator()); 
        $capacidade_sala->addValidation('Capacidade da sala', new TRequiredValidator()); 

        $tipo_sala->addItems(['SALA'=>'SALA','LAB'=>'LABORATÓRIO','DES'=>'DESENHO']);
        $sala_id->setEditable(false);

        $sala_id->setSize(100);
        $tipo_sala->setSize('70%');
        $numero_sala->setSize('70%');
        $capacidade_sala->setSize('70%');

        $row1 = $this->form->addFields([new TLabel('ID:', null, '14px', null)],[$sala_id]);
        $row2 = $this->form->addFields([new TLabel('Número da sala:', '#ff0000', '14px', null)],[$numero_sala]);
        $row3 = $this->form->addFields([new TLabel('Capacidade da sala:', '#ff0000', '14px', null)],[$capacidade_sala]);
        $row4 = $this->form->addFields([new TLabel('Tipo de sala:', null, '14px', null)],[$tipo_sala]);

        // create the form actions
        $btn_onsave = $this->form->addAction('Salvar', new TAction([$this, 'onSave']), 'fa:floppy-o #ffffff');
        $btn_onsave->addStyleClass('btn-primary'); 

        $btn_onclear = $this->form->addAction('Limpar formulário', new TAction([$this, 'onClear']), 'fa:eraser #dd5a43');

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

            $object = new Salas(); // create an empty object 

            $data = $this->form->getData(); // get form data as array
            $object->fromArray( (array) $data); // load the object with data

            $object->store(); // save the object 

            // get the generated {PRIMARY_KEY}
            $data->sala_id = $object->sala_id; 

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

                $object = new Salas($key); // instantiates the Active Record 

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

