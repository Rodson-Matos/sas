<?php

class TurmaList extends TPage
{
    private $form; // form
    private $datagrid; // listing
    private $pageNavigation;
    private $loaded;
    private static $database = 'sas';
    private static $activeRecord = 'Turma';
    private static $primaryKey = 'turma_id';
    private static $formName = 'formList_Turma';

    /**
     * Class constructor
     * Creates the page, the form and the listing
     */
    public function __construct()
    {
        parent::__construct();
        // creates the form
        $this->form = new BootstrapFormBuilder(self::$formName);

        // define the form title
        $this->form->setFormTitle('Listagem de Turmas');


        $turma_id = new TEntry('turma_id');
        $n_alunos = new TEntry('n_alunos');
        $tipo_turma = new TDBCombo('tipo_turma', 'sas', 'Turma', 'tipo_turma', '{tipo_turma}','turma_id asc'  );
        $curso_id = new TDBCombo('curso_id', 'sas', 'Curso', 'curso_id', '{nome_curso}','curso_id asc'  );

        $turma_id->setSize(100);
        $n_alunos->setSize('70%');
        $curso_id->setSize('70%');
        $tipo_turma->setSize('70%');

        $row1 = $this->form->addFields([new TLabel('ID:', null, '14px', null)],[$turma_id]);
        $row2 = $this->form->addFields([new TLabel('Número de alunos:', null, '14px', null)],[$n_alunos]);
        $row3 = $this->form->addFields([new TLabel('Tipo da turma:', null, '14px', null)],[$tipo_turma]);
        $row4 = $this->form->addFields([new TLabel('Curso:', null, '14px', null)],[$curso_id]);

        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue(__CLASS__.'_filter_data') );

        $btn_onsearch = $this->form->addAction('Buscar', new TAction([$this, 'onSearch']), 'fa:search #ffffff');
        $btn_onsearch->addStyleClass('btn-primary'); 

        $btn_onexportcsv = $this->form->addAction('Exportar como CSV', new TAction([$this, 'onExportCsv']), 'fa:file-text-o #000000');

        $btn_onshow = $this->form->addAction('Cadastrar', new TAction(['TurmaForm', 'onShow']), 'fa:plus #69aa46');

        // creates a Datagrid
        $this->datagrid = new TDataGrid;
        $this->datagrid = new BootstrapDatagridWrapper($this->datagrid);

        $this->datagrid->style = 'width: 100%';
        $this->datagrid->setHeight(320);

        $column_turma_id = new TDataGridColumn('turma_id', 'Turma id', 'center' , '70px');
        $column_tipo_turma = new TDataGridColumn('tipo_turma', 'Tipo da turma', 'left');
        $column_n_alunos = new TDataGridColumn('n_alunos', 'Número de alunos', 'left');
        $column_curso_id = new TDataGridColumn('curso_id', 'Curso id', 'left');

        $order_turma_id = new TAction(array($this, 'onReload'));
        $order_turma_id->setParameter('order', 'turma_id');
        $column_turma_id->setAction($order_turma_id);

        $this->datagrid->addColumn($column_turma_id);
        $this->datagrid->addColumn($column_tipo_turma);
        $this->datagrid->addColumn($column_n_alunos);
        $this->datagrid->addColumn($column_curso_id);

        $action_onEdit = new TDataGridAction(array('TurmaForm', 'onEdit'));
        $action_onEdit->setUseButton(false);
        $action_onEdit->setButtonClass('btn btn-default btn-sm');
        $action_onEdit->setLabel('Editar');
        $action_onEdit->setImage('fa:pencil-square-o #478fca');
        $action_onEdit->setField(self::$primaryKey);

        $this->datagrid->addAction($action_onEdit);

        $action_onDelete = new TDataGridAction(array('TurmaList', 'onDelete'));
        $action_onDelete->setUseButton(false);
        $action_onDelete->setButtonClass('btn btn-default btn-sm');
        $action_onDelete->setLabel('Excluir');
        $action_onDelete->setImage('fa:trash-o #dd5a43');
        $action_onDelete->setField(self::$primaryKey);

        $this->datagrid->addAction($action_onDelete);

        // create the datagrid model
        $this->datagrid->createModel();

        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->enableCounters();
        $this->pageNavigation->setAction(new TAction(array($this, 'onReload')));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());

        $panel = new TPanelGroup;
        $panel->add($this->datagrid);

        $panel->addFooter($this->pageNavigation);

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add(TBreadCrumb::create(['Listagem','Turmas']));
        $container->add($this->form);
        $container->add($panel);

        parent::add($container);

    }

    public function onExportCsv($param = null) 
    {
        try
        {
            $this->onSearch();

            TTransaction::open(self::$database); // open a transaction
            $repository = new TRepository(self::$activeRecord); // creates a repository for Customer
            $criteria = new TCriteria; // creates a criteria

            if($filters = TSession::getValue(__CLASS__.'_filters'))
            {
                foreach ($filters as $filter) 
                {
                    $criteria->add($filter);       
                }
            }

            $records = $repository->load($criteria); // load the objects according to criteria
            if ($records)
            {
                $file = 'tmp/'.uniqid().'.csv';
                $handle = fopen($file, 'w');
                $columns = $this->datagrid->getColumns();

                $csvColumns = [];
                foreach($columns as $column)
                {
                    $csvColumns[] = $column->getLabel();
                }
                fputcsv($handle, $csvColumns, ';');

                foreach ($records as $record)
                {
                    $csvColumns = [];
                    foreach($columns as $column)
                    {
                        $name = $column->getName();
                        $csvColumns[] = $record->{$name};
                    }
                    fputcsv($handle, $csvColumns, ';');
                }
                fclose($handle);

                TPage::openFile($file);
            }
            else
            {
                new TMessage('info', _t('No records found'));       
            }

            TTransaction::close(); // close the transaction
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }
    }

    public function onDelete($param = null) 
    { 
        if(isset($param['delete']) && $param['delete'] == 1)
        {
            try
            {
                // get the paramseter $key
                $key = $param['key'];
                // open a transaction with database
                TTransaction::open(self::$database);

                // instantiates object
                $object = new Turma($key, FALSE); 

                // deletes the object from the database
                $object->delete();

                // close the transaction
                TTransaction::close();

                // reload the listing
                $this->onReload( $param );
                // shows the success message
                new TMessage('info', AdiantiCoreTranslator::translate('Record deleted'));
            }
            catch (Exception $e) // in case of exception
            {
                // shows the exception error message
                new TMessage('error', $e->getMessage());
                // undo all pending operations
                TTransaction::rollback();
            }
        }
        else
        {
            // define the delete action
            $action = new TAction(array($this, 'onDelete'));
            $action->setParameters($param); // pass the key paramseter ahead
            $action->setParameter('delete', 1);
            // shows a dialog to the user
            new TQuestion(AdiantiCoreTranslator::translate('Do you really want to delete ?'), $action);   
        }
    }

    /**
     * Register the filter in the session
     */
    public function onSearch()
    {
        // get the search form data
        $data = $this->form->getData();
        $filters = [];

        TSession::setValue(__CLASS__.'_filter_data', NULL);
        TSession::setValue(__CLASS__.'_filters', NULL);

        if (isset($data->turma_id) AND ( (is_scalar($data->turma_id) AND $data->turma_id !== '') OR (is_array($data->turma_id) AND (!empty($data->turma_id)) )) )
        {

            $filters[] = new TFilter('turma_id', '=', $data->turma_id);// create the filter 
        }

        if (isset($data->n_alunos) AND ( (is_scalar($data->n_alunos) AND $data->n_alunos !== '') OR (is_array($data->n_alunos) AND (!empty($data->n_alunos)) )) )
        {

            $filters[] = new TFilter('n_alunos', '=', $data->n_alunos);// create the filter 
        }

        if (isset($data->tipo_turma) AND ( (is_scalar($data->tipo_turma) AND $data->tipo_turma !== '') OR (is_array($data->tipo_turma) AND (!empty($data->tipo_turma)) )) )
        {

            $filters[] = new TFilter('tipo_turma', 'like', "%{$data->tipo_turma}%");// create the filter 
        }

        if (isset($data->curso_id) AND ( (is_scalar($data->curso_id) AND $data->curso_id !== '') OR (is_array($data->curso_id) AND (!empty($data->curso_id)) )) )
        {

            $filters[] = new TFilter('curso_id', '=', $data->curso_id);// create the filter 
        }

        $param = array();
        $param['offset']     = 0;
        $param['first_page'] = 1;

        // fill the form with data again
        $this->form->setData($data);

        // keep the search data in the session
        TSession::setValue(__CLASS__.'_filter_data', $data);
        TSession::setValue(__CLASS__.'_filters', $filters);

        $this->onReload($param);
    }

    /**
     * Load the datagrid with data
     */
    public function onReload($param = NULL)
    {
        try
        {
            // open a transaction with database 'sas'
            TTransaction::open(self::$database);

            // creates a repository for Turma
            $repository = new TRepository(self::$activeRecord);
            $limit = 20;
            // creates a criteria
            $criteria = new TCriteria;

            if (empty($param['order']))
            {
                $param['order'] = 'turma_id';    
            }

            if (empty($param['direction']))
            {
                $param['direction'] = 'desc';
            }

            $criteria->setProperties($param); // order, offset
            $criteria->setProperty('limit', $limit);

            if($filters = TSession::getValue(__CLASS__.'_filters'))
            {
                foreach ($filters as $filter) 
                {
                    $criteria->add($filter);       
                }
            }

            // load the objects according to criteria
            $objects = $repository->load($criteria, FALSE);

            $this->datagrid->clear();
            if ($objects)
            {
                // iterate the collection of active records
                foreach ($objects as $object)
                {
                    // add the object inside the datagrid

                    $this->datagrid->addItem($object);

                }
            }

            // reset the criteria for record count
            $criteria->resetProperties();
            $count= $repository->count($criteria);

            $this->pageNavigation->setCount($count); // count of records
            $this->pageNavigation->setProperties($param); // order, page
            $this->pageNavigation->setLimit($limit); // limit

            // close the transaction
            TTransaction::close();
            $this->loaded = true;
        }
        catch (Exception $e) // in case of exception
        {
            // shows the exception error message
            new TMessage('error', $e->getMessage());
            // undo all pending operations
            TTransaction::rollback();
        }
    }

    public function onShow($param = null)
    {

    }

    /**
     * method show()
     * Shows the page
     */
    public function show()
    {
        // check if the datagrid is already loaded
        if (!$this->loaded AND (!isset($_GET['method']) OR !(in_array($_GET['method'],  array('onReload', 'onSearch')))) )
        {
            if (func_num_args() > 0)
            {
                $this->onReload( func_get_arg(0) );
            }
            else
            {
                $this->onReload();
            }
        }
        parent::show();
    }

}

