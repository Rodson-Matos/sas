<?php

class HorariosForm extends TPage
{
    protected $form;
    private $formFields = [];
    private static $database = 'sas';
    private static $activeRecord = 'Horarios';
    private static $primaryKey = 'horarios_id';
    private static $formName = 'form_Horarios';

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
        $this->form->setFormTitle('Cadastro de Horários');


        $horarios_id = new TEntry('horarios_id');
        $horario = new TTime('horario');
        $dia_sem = new TCombo('dia_sem');

        $horario->addValidation('Horário', new TRequiredValidator()); 

        $dia_sem->addItems(['SEG'=>'SEGUNDA-FEIRA','TER'=>'TERÇA-FEIRA','QUA'=>'QUARTA-FEIRA','QUI'=>'QUINTA-FEIRA','SEX'=>'SEXTA-FEIRA','SAB'=>'SÁBADO']);
        $horarios_id->setEditable(false);

        $dia_sem->setSize(150);
        $horario->setSize(150);
        $horarios_id->setSize(100);

        $row1 = $this->form->addFields([new TLabel('ID:', null, '14px', null)],[$horarios_id]);
        $row2 = $this->form->addFields([new TLabel('Horário:', '#ff0000', '14px', null)],[$horario]);
        $row3 = $this->form->addFields([new TLabel('Dia da semana:', null, '14px', null)],[$dia_sem]);

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

            $object = new Horarios(); // create an empty object 

            $data = $this->form->getData(); // get form data as array
            $object->fromArray( (array) $data); // load the object with data

            $object->store(); // save the object 

            // get the generated {PRIMARY_KEY}
            $data->horarios_id = $object->horarios_id; 

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

                $object = new Horarios($key); // instantiates the Active Record 

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

