<?php

class CursoList extends TPage
{
    private $form; // form
    private $datagrid; // listing
    private $pageNavigation;
    private $loaded;
    private static $database = 'sas';
    private static $activeRecord = 'Curso';
    private static $primaryKey = 'curso_id';
    private static $formName = 'formList_Curso';

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
        $this->form->setFormTitle('Listagem de Cursos');


        $curso_id = new TEntry('curso_id');
        $nome_curso = new TEntry('nome_curso');
        $tipo_curso = new TDBCombo('tipo_curso', 'sas', 'Curso', 'curso_id', '{tipo_curso}','curso_id asc'  );
        $modalidade = new TDBCombo('modalidade', 'sas', 'Curso', 'curso_id', '{modalidade}','curso_id asc'  );
        $turno_curso = new TDBCombo('turno_curso', 'sas', 'Curso', 'curso_id', '{turno_curso}','curso_id asc'  );

        $curso_id->setSize(100);
        $nome_curso->setSize('70%');
        $tipo_curso->setSize('70%');
        $modalidade->setSize('70%');
        $turno_curso->setSize('70%');

        $row1 = $this->form->addFields([new TLabel('ID:', null, '14px', null)],[$curso_id]);
        $row2 = $this->form->addFields([new TLabel('Nome do curso:', null, '14px', null)],[$nome_curso]);
        $row3 = $this->form->addFields([new TLabel('Tipo do curso:', null, '14px', null)],[$tipo_curso]);
        $row4 = $this->form->addFields([new TLabel('Modalidade:', null, '14px', null)],[$modalidade]);
        $row5 = $this->form->addFields([new TLabel('Turno do curso:', null, '14px', null)],[$turno_curso]);

        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue(__CLASS__.'_filter_data') );

        $btn_onsearch = $this->form->addAction('Buscar', new TAction([$this, 'onSearch']), 'fa:search #ffffff');
        $btn_onsearch->addStyleClass('btn-primary'); 

        $btn_onexportcsv = $this->form->addAction('Exportar como CSV', new TAction([$this, 'onExportCsv']), 'fa:file-text-o #000000');

        $btn_onshow = $this->form->addAction('Cadastrar', new TAction(['CursoForm', 'onShow']), 'fa:plus #69aa46');

        // creates a Datagrid
        $this->datagrid = new TDataGrid;
        $this->datagrid = new BootstrapDatagridWrapper($this->datagrid);

        $this->datagrid->style = 'width: 100%';
        $this->datagrid->setHeight(320);

        $column_curso_id = new TDataGridColumn('curso_id', 'Curso id', 'center' , '70px');
        $column_nome_curso = new TDataGridColumn('nome_curso', 'Nome do curso', 'left');
        $column_tipo_curso = new TDataGridColumn('tipo_curso', 'Tipo do curso', 'left');
        $column_modalidade = new TDataGridColumn('modalidade', 'Modalidade', 'left');
        $column_turno_curso = new TDataGridColumn('turno_curso', 'Turno do curso', 'left');

        $order_curso_id = new TAction(array($this, 'onReload'));
        $order_curso_id->setParameter('order', 'curso_id');
        $column_curso_id->setAction($order_curso_id);

        $this->datagrid->addColumn($column_curso_id);
        $this->datagrid->addColumn($column_nome_curso);
        $this->datagrid->addColumn($column_tipo_curso);
        $this->datagrid->addColumn($column_modalidade);
        $this->datagrid->addColumn($column_turno_curso);

        $action_onEdit = new TDataGridAction(array('CursoForm', 'onEdit'));
        $action_onEdit->setUseButton(false);
        $action_onEdit->setButtonClass('btn btn-default btn-sm');
        $action_onEdit->setLabel('Editar');
        $action_onEdit->setImage('fa:pencil-square-o #478fca');
        $action_onEdit->setField(self::$primaryKey);

        $this->datagrid->addAction($action_onEdit);

        $action_onDelete = new TDataGridAction(array('CursoList', 'onDelete'));
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
        $container->add(TBreadCrumb::create(['Listagem','Cursos']));
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
                $object = new Curso($key, FALSE); 

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

        if (isset($data->curso_id) AND ( (is_scalar($data->curso_id) AND $data->curso_id !== '') OR (is_array($data->curso_id) AND (!empty($data->curso_id)) )) )
        {

            $filters[] = new TFilter('curso_id', '=', $data->curso_id);// create the filter 
        }

        if (isset($data->nome_curso) AND ( (is_scalar($data->nome_curso) AND $data->nome_curso !== '') OR (is_array($data->nome_curso) AND (!empty($data->nome_curso)) )) )
        {

            $filters[] = new TFilter('nome_curso', 'like', "%{$data->nome_curso}%");// create the filter 
        }

        if (isset($data->tipo_curso) AND ( (is_scalar($data->tipo_curso) AND $data->tipo_curso !== '') OR (is_array($data->tipo_curso) AND (!empty($data->tipo_curso)) )) )
        {

            $filters[] = new TFilter('tipo_curso', 'like', "%{$data->tipo_curso}%");// create the filter 
        }

        if (isset($data->modalidade) AND ( (is_scalar($data->modalidade) AND $data->modalidade !== '') OR (is_array($data->modalidade) AND (!empty($data->modalidade)) )) )
        {

            $filters[] = new TFilter('modalidade', 'like', "%{$data->modalidade}%");// create the filter 
        }

        if (isset($data->turno_curso) AND ( (is_scalar($data->turno_curso) AND $data->turno_curso !== '') OR (is_array($data->turno_curso) AND (!empty($data->turno_curso)) )) )
        {

            $filters[] = new TFilter('turno_curso', 'like', "%{$data->turno_curso}%");// create the filter 
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

            // creates a repository for Curso
            $repository = new TRepository(self::$activeRecord);
            $limit = 20;
            // creates a criteria
            $criteria = new TCriteria;

            if (empty($param['order']))
            {
                $param['order'] = 'curso_id';    
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

