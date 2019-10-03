<?php

class DatabaseInstall extends TPage
{
    private $datagrid;
    /**
     * método construtor
     * Cria a página e o formulário de cadastro
     */
     
    static $msgs = []; 
    
    function __construct()
    {
        parent::__construct();
    
        try 
        {
            $this->adianti_target_container = 'adianti_div_content';
            
            $configs = [];
            $configs['permission'] = parse_ini_file('app/config/permission.ini');
            $configs['communication'] = parse_ini_file('app/config/communication.ini');
            $configs['log'] = parse_ini_file('app/config/log.ini');
            
            $myDatabases = [];
            $myDatabases['permission'] = 'app/database/permission.sql';
            $myDatabases['communication'] = 'app/database/comnunication.sql';
            $myDatabases['log'] = 'app/database/log.sql';
            
            $installIni = parse_ini_file('app/config/install.ini');
            $installedIni = parse_ini_file('app/config/installed.ini');
            
            if(isset($installIni['installed']) && $installIni['installed'])
            {
                throw new Exception(_t('Databases have already been installed').'.<br>'._t('If you want to reinstall edit the file app/config/install.ini and change installed = 1 to installed = 0. Erase the content in app/config/installed.ini too').'.');
            }
            
            foreach ($installIni['databases'] as $database) 
            {
                $iniFile = 'app/config/'.$database.'.ini';
                if(!is_readable($iniFile))
                {
                    new TMessage('error', _t("In order to continue with the installation you must grant read permission to the file:"."<b>{$iniFile}</b>"));
                    return false;   
                }
                if(!is_writable($iniFile))
                {
                    new TMessage('error', _t("In order to continue with the installation you must grant write permission to the file:"."<b>{$iniFile}</b>"));
                    return false;   
                }
                
                $configs[$database] = parse_ini_file($iniFile);
                
                $myDatabases[$database] = "app/database/{$database}-{$configs[$database]['type']}";
            }

            $this->form = new BootstrapFormBuilder('form-download-step-1');
            $this->form->setFormTitle(_t('Installing your application'));
            
            $tstep = new TStep();
            $tstep->addItem(_t('PHP verification'), true, true);
            $tstep->addItem(_t('Directory and files verification'), true, true);
            $tstep->addItem('<b>'._t('Database configuration/creation').'</b>', true, false);
            
            $this->form->addContent([$tstep]);
            
            $database_types = ['mysql'=>'MySql', 'pgsql'=> 'Postgres', 'oracle' => 'Oracle', 'mssql' => 'SQL Server', 'sqlite' => 'SQLite', 'fbird' => 'Firebird'];
            
            if(isset($installIni['template_databases']) && $installIni['template_databases'])
            {
                foreach ($installIni['template_databases'] as $templateDb => $db) 
                {
                    unset($myDatabases[$templateDb]);
                }
            }
            
            foreach ($myDatabases as $databaseName => $database) 
            {
                $portValue = isset($configs[$databaseName]['port']) ? $configs[$databaseName]['port'] : '';
                
                $name           = new TEntry("name[]");
                $port           = new TEntry("port[]");
                $host           = new TEntry("host[]");
                $username       = new TEntry("username[]");
                $root_user      = new TEntry("root_user[]");
                $password       = new TEntry("password[]");
                $root_password  = new TEntry("root_password[]");
                $databaseType   = new TCombo("database_type[]");
                $database_name   = new THidden('database_name[]');
                
                $fields = [$name,$port,$host,$username,$root_user,$password,$root_password,$databaseType,$database_name];

                $databaseType->addItems($database_types);
                
                $databaseType->setValue($configs[$databaseName]['type']);
                $port->setValue($portValue);
                $host->setValue($configs[$databaseName]['host']);
                $username->setValue($configs[$databaseName]['user']);
                $password->setValue($configs[$databaseName]['pass']);
                $name->setValue($configs[$databaseName]['name']);
                $database_name->setValue($databaseName);
                
                $databaseType->setChangeAction(new TAction([$this,'changeDBType']));
                
                $databaseInstalled = '';
                if(isset($installedIni[$databaseName.'_'.$configs[$databaseName]['name']]) && $installedIni[$databaseName.'_'.$configs[$databaseName]['name']] == '1')
                {
                    $databaseInstalled = " - <span class='gren'>"._t('Installed').'</span> <i class="fa fa-check-circle green" aria-hidden="true"></i>';
                }
                
                $this->form->addContent([new TFormSeparator(_t('Database').": {$databaseName}".$databaseInstalled)]);
                $this->form->addFields([new TLabel(_t('Database type').':','#FF0000;')], [$databaseType],[new TLabel(_t('Database name').':','#FF0000;')],[$name]);
                $this->form->addFields([new TLabel(_t('Admin user').':')], [$root_user],[new TLabel(_t('Admin password').':')],[$root_password]);
                $this->form->addFields([new TLabel('Host:')], [$host],[new TLabel(_t('Port').':')], [$port]);
                $this->form->addFields([new TLabel(_t('User').':')], [$username],[new TLabel(_t('Password').':')], [$password]);
                $this->form->addFields([$database_name]);
                

                $uniqid = uniqid();
                foreach ($fields as $field)
                {
                    $field->setId(str_replace(['[',']','_'],'',$field->getName()).'_'.$uniqid);
                }
                
                self::changeDBType(['_field_id'=>$databaseType->getId(),'_field_value'=>$databaseType->getValue()]);
                
            }
            
            TTransaction::close();
            
            $this->form->addAction(_t('Back'), new TAction([$this, 'lastStep']), 'fa:arrow-left red');
            $this->form->addAction(_t('Install'), new TAction([$this, 'install']), 'fa:cogs green');
            $container = new TElement('div');
            $container->class = 'container formBuilderContainer';
            
            $container->add($this->form);
            
            parent::add($container);
        } 
        catch (Exception $e) 
        {
            TTransaction::rollback();
            new TMessage('error', $e->getMessage());
        }
    }
    
    public static function lastStep($params = null)
    {
        $form = new PathInstall();
        $form->setIsWrapped(true);
        $form->show();
    }
    
    public static function changeDBType($param)
    {
        $field_data = explode('_',$param['_field_id']);
        $id = $field_data[1];
        $fields = ['rootuser','rootpassword','host','port','username','password'];
        
        $js = '';
        if ($param['_field_value'] == 'sqlite')
        {
            foreach ($fields as $field)
            {
                $js .= "
                    $('#{$field}_{$id}').attr('readonly', true);
                    $('#{$field}_{$id}').removeClass('tfield').addClass('tfield_disabled');
                    $('#{$field}_{$id}').val('');
                ";
            }
        }
        else
        {
             foreach ($fields as $field)
             {
                 $js .= "
                     $('#{$field}_{$id}').attr('readonly', false);
                     $('#{$field}_{$id}').removeClass('tfield_disabled').addClass('tfield');
                 ";
             }   
        }
        
        TScript::create($js);
    }
    
    public static function validate($post)
    {
        $obl = [];

        $obl['name']           = _t('Database name');
        $obl['host']           = 'Host';
        $obl['username']       = _t('User');
        $obl['root_user']      = _t('Admin user');
        $obl['password']       = _t('Password');
        $obl['root_password']  = _t('Admin password');
        $obl['database_type']  = _t('Database type');
        $obl['database_name']  = _t('Database name');
        
        $nao_obrigatorios = ['sqlite' => ['host'=>true,'username'=>true,'root_user'=>true,'password'=>true,'root_password'=>true] ];
        
        foreach ($obl as $field => $fieldName) 
        {
            foreach ($post[$field] as $key => $value) 
            {
                if(!trim($value) AND ! isset($nao_obrigatorios[$post['database_type'][$key]][$field]))
                {
                    throw new Exception(AdiantiCoreTranslator::translate('The field ^1 is required', '"'.$fieldName.'"').'. '._t('Of database:').$post['database_name'][$key]);
                }
            }    
        }
    }
    
    public static function install($params)
    {
        try 
        {
            self::validate($params);
            
            $installIni = parse_ini_file('app/config/install.ini');
            $installIniText = file_get_contents('app/config/install.ini');
            
            if(isset($installIni['installed']) && $installIni['installed'])
            {
                throw new Exception(_t('Databases have already been installed'));
            }

            foreach ($params['name'] as $key => $name) 
            {
                $params['name'][$key] = strtolower($params['name'][$key]);
                self::testConnection($params['host'][$key], $params['name'][$key], $params['root_user'][$key], $params['root_password'][$key], $params['database_type'][$key], $params['port'][$key]);
            }
            
            foreach ($params['name'] as $key => $name) 
            {
                $oldInserted = parse_ini_file('app/config/installed.ini');
                
                $ini = [
                    'host' => $params['host'][$key],
                    'user' => $params['root_user'][$key],
                    'pass' => $params['root_password'][$key],
                    'type' => $params['database_type'][$key],
                    'port' => $params['port'][$key]
                ];
                
                $databaseName = $params['database_name'][$key];
                $databaseType = $params['database_type'][$key];
                
                if($databaseType == 'pgsql')
                {
                    $ini['name'] = 'postgres';
                }
                elseif($databaseType == 'mysql')
                {
                    $ini['name'] = '';
                }
                elseif($databaseType == 'oracle')
                {
                    $ini['name'] = $name;
                }
                elseif($databaseType == 'mssql')
                {
                    $ini['name'] = '';
                }
                elseif($databaseType == 'fbird')
                {
                    $ini['name'] = $name;
                }
                
                if ($databaseType == 'sqlite')
                {
                    $conn = null;
                }
                else
                {
                    $conn = TConnection::openArray($ini);

                    if(self::userExists($params['username'][$key], $databaseType, $conn))
                    {
                        self::$msgs[$databaseName][] = 'Usuario ' . $params['username'][$key] . ' ja existente';
                    }
                    else
                    {                    
                        self::createUser($params['username'][$key], $params['password'][$key], $databaseType, $conn, $params['name'][$key], $params['host'][$key]);
                        self::$msgs[$databaseName][] = 'Usuario ' . $params['username'][$key]. ' criado';
                    }
                }
                
                $ini['name'] = $params['name'][$key];
                
                $exisits = self::databaseExists($ini);
                
                if($exisits === false)
                {
                    self::createDB($params['name'][$key], $params['username'][$key], $databaseType, $conn);
                    
                    self::$msgs[$databaseName][] = 'Banco ' . $params['name'][$key] . ' criado';                    
                }
                else
                {
                    self::$msgs[$databaseName][] = 'Banco ' . $params['name'][$key] . ' ja existente';
                }
                
                $ini['user'] = $params['username'][$key];
                $ini['pass'] = $params['password'][$key];
                $ini['name'] = $params['name'][$key];
                
                if($databaseType == 'sqlite')
                {
                    $ini['fkey'] = '0';
                }
                
                TTransaction::open(null, $ini);
                
                if($databaseType == 'sqlite')
                {
                    $conn = TTransaction::get();
                    $conn->query('PRAGMA foreign_keys = OFF');
                }
                
                if(file_exists("app/database/{$databaseName}-{$databaseType}.sql"))
                {   
                    $fks = self::createTables("app/database/{$databaseName}-{$databaseType}.sql", $name, $databaseType, $databaseName);
                }
                else
                {
                    $fks = self::createTables("app/database/{$databaseName}.sql", $name, $databaseType, $databaseName);
                }
                
                if (is_writable("app/config/{$databaseName}.ini"))
                {
                    self::updateIniFile("app/config/{$databaseName}.ini", $ini);
                }
                else
                {
                    throw new Exception("Sem permissão de escrita no arquivo app/config/{$databaseName}.ini");
                }
                
                if(isset($installIni['template_databases']) && $installIni['template_databases'])
                {
                    foreach ($installIni['template_databases'] as $templateDb => $db) 
                    {
                        if($db == $databaseName)
                        {
                            self::updateIniFile("app/config/{$templateDb}.ini", $ini);
                        }
                    }
                }
                
                if($databaseName == 'permission')
                {
                    self::insertPermissions($params['name'][$key], $databaseName);
                    self::$msgs[$databaseName][] = 'Dados inseridos';
                }
                
                if((isset($installIni['template_databases']) && isset($installIni['template_databases']['permission']) && $installIni['template_databases']['permission'] == $databaseName) || isset($installIni['template_databases']) && isset($installIni['template_databases']['communication']) && $installIni['template_databases']['communication'] == $databaseName)
                {
                    $inserted = self::insertDefaultData($params['name'][$key], $params['database_name'][$key], $params['database_type'][$key]);
                    
                    if($inserted)
                    {
                        $insertedTxt = '';
                        foreach ($inserted as $keyy => $value) 
                        {
                            $insertedTxt .= "{$keyy} = {$value} \n";
                        }
                        
                        file_put_contents('app/config/installed.ini', $insertedTxt);
                    }
                    
                    if(($databaseName == 'permission') || (isset($installIni['template_databases']['permission']) && $installIni['template_databases']['permission'] == $databaseName))
                    {
                        self::insertPermissions($params['name'][$key], $params['database_name'][$key]);
                    }
                    
                    self::$msgs[$databaseName][] = 'Dados inseridos';
                }
                elseif(is_file("app/database/{$params['database_name'][$key]}-inserts.sql") && $databaseName != 'permission' && $databaseName != 'communication')
                {
                    $inserted = self::insertDefaultData($params['name'][$key], $params['database_name'][$key], $params['database_type'][$key]);
                    
                    if($inserted)
                    {
                        $insertedTxt = '';
                        foreach ($inserted as $key => $value) 
                        {
                            $insertedTxt .= "{$key} = {$value} \n";
                        }
                        
                        file_put_contents('app/config/installed.ini', $insertedTxt);
                    }
                    
                    self::$msgs[$databaseName][] = 'Dados inseridos';
                }
                else
                {
                    $installedIni = parse_ini_file('app/config/installed.ini');
                    
                    $insertedTxt = '';
                    $installedIni["{$databaseName}_{$name}_inserts"] = '1';
                    
                    foreach ($installedIni as $key => $value) 
                    {
                        $insertedTxt .= "{$key} = {$value} \n";
                    }
                    
                    file_put_contents('app/config/installed.ini', $insertedTxt);
                }
                
                if(is_file("app/database/{$databaseName}-{$databaseType}-adjust-sequences.sql"))
                {
                    self::adjustSequences("app/database/{$databaseName}-{$databaseType}-adjust-sequences.sql");
                }

                if( $fks)
                {
                    self::createFKs( $fks, $name, $databaseType, TTransaction::get(), $databaseName );
                }
                
                TTransaction::close();
            }
            
            file_put_contents('app/config/install.ini', $installIniText."\ninstalled = 1");
            
            $summary = new SummaryDatabaseInstall();
            $summary->setIsWrapped(true);
            $summary->show();
            
            new TMessage('info', _t('Databases successfully installed'));
        } 
        catch (Exception $e) 
        {
            TTransaction::rollback();
            
            if(isset($oldInserted) && $oldInserted)
            {
                $insertedTxt = '';
                foreach ($oldInserted as $key => $value) 
                {
                    $insertedTxt .= "{$key} = {$value} \n";
                }
                
                file_put_contents('app/config/installed.ini', $insertedTxt);
            }
            
            
            new TMessage('error', $e->getMessage());    
        }
    }
    
    public static function updateModels($dbType)
    {
        
    }
    
    public static function insertDefaultData($name, $databaseName, $databaseType)
    {
        try 
        {            
            $inserted = parse_ini_file('app/config/installed.ini');
            
            if(!isset($inserted["{$databaseName}_{$name}_inserts_default_data"]) || (isset($inserted["{$databaseName}_{$name}_inserts_default_data"]) && $inserted["{$databaseName}_{$name}_inserts_default_data"] == 0 ))
            {
                $sql = file_get_contents("app/database/{$databaseName}-inserts.sql");
                
                if($databaseType == 'sqlite')
                {
                    $sql = "PRAGMA foreign_keys=OFF;\n{$sql}";
                }
                elseif($databaseType == 'mysql')
                {
                    $sql = "SET FOREIGN_KEY_CHECKS = 0;\n{$sql}";   
                }
                
                $commands = explode(';', $sql);
                
                $conn = TTransaction::get();
                
                foreach ($commands as $sql) 
                {
                    if(trim($sql))
                    {
                        $conn->query("{$sql};");
                    }
                }
                
                $inserted["{$databaseName}_{$name}_inserts_default_data"] = 1;
            }
            
            return $inserted;
        } 
        catch (Exception $e) 
        {
            TTransaction::rollback();
            throw new Exception($e->getMessage());
        }
    }
    
    public static function adjustSequences($fileName)
    {
        try 
        {            
            $sql = file_get_contents($fileName);
             
            $commands = explode(';', $sql);
            
            $conn = TTransaction::get();
            
            foreach ($commands as $sql) 
            {
                if(trim($sql))
                {
                    $conn->query("{$sql};");
                }
            }
        } 
        catch (Exception $e) 
        {
            TTransaction::rollback();
            throw new Exception($e->getMessage());
        }
    }
    
    public static function insertPermissions($name, $databaseName)
    {
        try 
        {            
            $inserted = parse_ini_file('app/config/installed.ini');
            
            if(!isset($inserted["{$databaseName}_{$name}_inserts_permission"]) || (isset($inserted["{$databaseName}_{$name}_inserts_permission"]) && $inserted["{$databaseName}_{$name}_inserts_permission"] == 0 ))
            {
                $sql = file_get_contents("app/database/inserts-permission.sql");
                $commands = explode(';', $sql);
                
                $conn = TTransaction::get();
                
                foreach ($commands as $sql) 
                {
                    if(trim($sql))
                    {
                        $conn->query("{$sql}");
                    }
                }
                
                $inserted["{$databaseName}_{$name}_inserts_permission"] = 1;
            }
            
            if($inserted)
            {
                $insertedTxt = '';
                foreach ($inserted as $key => $value) 
                {
                    $insertedTxt .= "{$key} = {$value} \n";
                }
                
                file_put_contents('app/config/installed.ini', $insertedTxt);
            }  
        } 
        catch (Exception $e) 
        {
            TTransaction::rollback();
            throw new Exception($e->getMessage());
        }
    }
    
    public static function updateConfig($params)
    {
        try 
        {
            self::verifyRequiredFields($params);
            
            $databaseName = $params['key'];
            if ($params["database_type_{$databaseName}"] == 'sqlite')
            {
                $ini = [
                        'name' => $params["name_{$databaseName}"],
                        'type' => $params["database_type_{$databaseName}"],
                    ];
            }
            else
            {
                $ini = [
                        'host' => $params["host_{$databaseName}"],
                        'name' => $params["name_{$databaseName}"],
                        'user' => $params["username_{$databaseName}"],
                        'pass' => $params["password_{$databaseName}"],
                        'type' => $params["database_type_{$databaseName}"],
                    ];
            }
            
            self::updateIniFile("app/config/{$databaseName}.ini", $ini);
            
            new TMessage('info', _t('Configuration file: ^1 updated successfully', "app/config/{$databaseName}.ini"));    
        } 
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());
        }
    }
    
    public static function updateIniFile($fileName, $ini)
    {
        $fileContents = '';
        $ini['prep'] = '1';
        
        foreach ($ini as $key => $value) 
        {
            $fileContents .= "{$key} = \"{$value}\" \n";
        }
        
        file_put_contents($fileName, $fileContents);
    }
    
    public static function createDB($name, $user, $databaseType, $conn)
    {   
        $sql = '';
        if($databaseType == 'pgsql')
        {
            $sql = "create database {$name} owner {$user};"; 
        }
        elseif($databaseType == 'mysql')
        {
            $sql  = "CREATE DATABASE IF NOT EXISTS {$name} DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
                     GRANT ALL PRIVILEGES ON {$name}.* TO '{$user}'@'%' WITH GRANT OPTION;
                    ";
        }
        elseif($databaseType == 'mssql')
        {
            $sql  = "CREATE DATABASE {$name};
            
                     ALTER DATABASE {$name} SET ANSI_NULL_DEFAULT ON;
                     
                     ALTER AUTHORIZATION ON DATABASE::{$name} TO {$user};
                     ";
        }
        elseif($databaseType == 'sqlite')
        {
            $conn = new PDO("sqlite:{$name}");
        }

        if ($sql)
            $result = $conn->query($sql); 
    }
    
    public static function createTables($file, $databaseName, $databaseType, $iniName)
    {
        
        $sql = file_get_contents($file);
        $commands = explode(';', $sql);
        
        foreach ($commands as $command) 
        {
            if(!$command)
                continue;
            
            if(preg_match_all( '!CREATE TABLE (.*+)!i', $command, $match ) > 0)
            {
                foreach ($match[1] as $key => $table) 
                {
                    $tables[trim(explode('(',$table)[0])] = $command;   
                }
            }
        }
        
        $conn = TTransaction::get();
        
        $fks = [];
        $inserts = [];
        $idxs = [];
        
        foreach ($commands as $command) 
        {
            if(!$command)
                continue;
            
            if(preg_match_all( '!ADD CONSTRAINT (.+[ ?])!i', $command, $match ) > 0)
            {
                foreach ($match[1] as $key => $table) 
                {
                    $fks[trim(explode(' ',$table)[0])] = $command;
                }
            }
            
            if(preg_match("/insert into/i", $command))
                $inserts[] = $command;
            
            if(preg_match_all( '!create index (.+[ ?])!i', $command, $match ) > 0)
            {
                foreach ($match[1] as $key => $table) 
                {
                    $idxs[trim(explode(' ',$table)[0])] = $command;
                }
            }
        }
             
        foreach ($tables as $tableName => $createTableSql) 
        {
            if(self::isTableCreated($tableName, $databaseName, $databaseType, $conn))
            {
                self::$msgs[$iniName][] = 'Tabela ' . $tableName . ' ja existente';
            }
            else
            {
                $result = $conn->query($createTableSql);
                self::$msgs[$iniName][] = 'Tabela ' . $tableName . ' criada';
            }
        }
        
        
        if($idxs)
        {
            foreach ($idxs as $idxName => $createIdxSql) 
            {
                if(self::isIndexCreated($idxName, $databaseName, $databaseType, $conn))
                {
                    self::$msgs[$iniName][] = 'Index ' . $idxName . ' ja existente';
                }
                else
                {
                    $result = $conn->query($createIdxSql);
                    self::$msgs[$iniName][] = 'Index ' . $idxName . ' criado';
                }
            }
        }
        
        $inserted = parse_ini_file('app/config/installed.ini');

        if($inserts)
        {
            if(!isset($inserted["{$iniName}_{$databaseName}"]) || (isset($inserted["{$iniName}_{$databaseName}"]) && $inserted["{$iniName}_{$databaseName}"] == 0))
            {
                foreach ($inserts as $insertSql) 
                {
                    if(trim($insertSql))
                    {
                        $result = $conn->query($insertSql);
                    }
                }
                
                $inserted["{$iniName}_{$databaseName}"] = '1';
            }
        }
        else
        {
            $inserted["{$iniName}_{$databaseName}"] = '1';
        }
         
        if($inserted)
        {
            $insertedTxt = '';
            foreach ($inserted as $key => $value) 
            {
                $insertedTxt .= "{$key} = {$value} \n";
            }
            
            file_put_contents('app/config/installed.ini', $insertedTxt);
        }

        if($fks)
        {
            return $fks;         
        }
    }
    
    public static function createFKs($fks, $databaseName, $databaseType, $conn, $iniName)
    {
        $conn = TTransaction::get();
        foreach ($fks as $fkName => $createFkSql) 
        {
            if(self::isFkCreated($fkName, $databaseName, $databaseType, $conn))
            {
                self::$msgs[$iniName][]= 'FK ' . $fkName . ' ja existente';
            }
            else
            {
                $result = $conn->query($createFkSql);
                self::$msgs[$iniName][] = 'FK ' . $fkName . ' criada';
            }
        }
    }

    public static function isIndexCreated($idxName, $databaseName, $databaseType, $conn)
    {
        if($databaseType == 'pgsql')
        {
            $sql = "select * from pg_catalog.pg_indexes where indexname = '{$idxName}';";
        }
        elseif($databaseType == 'mysql')
        {
            $sql = "select * from information_schema.statistics where table_schema = '{$databaseName}' and index_name = '{$idxName}'";
        }
        elseif($databaseType == 'oracle')
        {
            $sql = "select * from all_indexes where upper(substr(index_name,0,30)) = upper(substr('{$idxName}',0,30))";
        }
        elseif($databaseType == 'mssql')
        {
            $sql = "select * from sys.indexes where name = '{$idxName}'";
        }
        elseif($databaseType == 'sqlite')
        {
            $sql = "select * from sqlite_master where type = 'index' and name = '{$idxName}'";
        }
        elseif($databaseType == 'fbird')
        {
            $sql = 'SELECT * FROM RDB$INDICES where RDB$INDEX_NAME = '."upper('{$idxName}')";
        }

        if (! empty($sql))
        {
            $result = $conn->query($sql); 
            $objs = $result->fetchAll(PDO::FETCH_CLASS, "stdClass");
        
            if($objs)
            {
                return true;
            }
        }
        
        return false;
    }
    
    public static function isTableCreated($tableName, $databaseName, $databaseType, $conn)
    {
        if($databaseType == 'pgsql')
        {
            $sql = "select * from information_schema.tables where table_name = '{$tableName}' and table_catalog = '{$databaseName}';";    
        }
        elseif($databaseType == 'mysql')
        {
            $sql = "select * from information_schema.tables where table_name = '{$tableName}' and table_schema = '{$databaseName}'";
        }
        elseif($databaseType == 'oracle')
        {
            $sql = "select * from all_tables where upper(table_name) = upper('{$tableName}')";
        }
        elseif($databaseType == 'mssql')
        {
            $sql = "select * from information_schema.tables where table_name = '{$tableName}'";
        }
        elseif($databaseType == 'sqlite')
        {
            $sql = "select * FROM sqlite_master WHERE type='table' AND name = '{$tableName}'";
        }
        elseif($databaseType == 'fbird')
        {
            $sql = 'SELECT * FROM RDB$RELATIONS WHERE RDB$FLAGS=1 and RDB$RELATION_NAME='."upper('{$tableName}')";
        }
        
        $result = $conn->query($sql); 
        $objs = $result->fetchAll(PDO::FETCH_CLASS, "stdClass");
        
        if($objs)
        {
            return true;
        }
        
        return false;
    }
    
    public static function isFkCreated($fkName, $databaseName, $databaseType, $conn)
    {
        if($databaseType == 'pgsql')
        {
            $sql = "select * from information_schema.table_constraints where constraint_name = '{$fkName}'";
        }
        elseif($databaseType == 'mysql')
        {
            $sql = "select * from information_schema.table_constraints where constraint_name = '{$fkName}' and constraint_schema = '{$databaseName}';";
        }
        elseif($databaseType == 'oracle')
        {
            $sql = "select * from all_constraints where upper(constraint_name) = upper('{$fkName}')"; 
        }
        elseif($databaseType == 'mssql')
        {
            $sql = "select * from sys.foreign_keys where name = upper('{$fkName}')"; 
        }
        elseif($databaseType == 'fbird')
        {
            $sql = 'SELECT * FROM RDB$RELATION_CONSTRAINTS WHERE RDB$CONSTRAINT_NAME = ' . "upper('{$fkName}')"; 
        }
         
        $result = $conn->query($sql); 
        $objs = $result->fetchAll(PDO::FETCH_CLASS, "stdClass");
        
        if($objs)
        {
            return true;
        }
        
        return false;
    }
        
    public static function createUser($user, $password, $databaseType, $conn, $databaseName = null, $host = null)
    {
        if($databaseType == 'pgsql')
        {        
            $sql = "create user {$user} with encrypted password '{$password}';"; 
            $result = $conn->query($sql); 
        }
        elseif($databaseType == 'mysql')
        {
            $sql = "CREATE USER '{$user}'@'%' IDENTIFIED BY '{$password}';";
            
            $result = $conn->query($sql); 
        }
        elseif($databaseType == 'mssql')
        {
            $sql = "create login {$user} with password = '{$password}'
                    create user {$user} for login {$user}";
            
            $result = $conn->query($sql); 
        }
            
    }
    
    public static function userExists($user, $databaseType, $conn)
    {
        try 
        {
            if($databaseType == 'pgsql')
            {
                $sql = "select * from pg_roles where rolname = '{$user}' "; 
            }
            elseif($databaseType == 'mysql')
            {
                $sql = "SELECT * FROM mysql.user WHERE user = '{$user}' "; 
            }
            elseif($databaseType == 'mssql')
            {
                $sql = "select * from sys.sysusers Where name = '{$user}'";
            }
            elseif($databaseType == 'oracle')
            {
                return true;
            }
            elseif($databaseType == 'fbird')
            {
                return true;
            }
            
            if (! empty($sql))
            {
                $result = $conn->query($sql); 
                $objs = $result->fetchAll(PDO::FETCH_CLASS, "stdClass");
                
                if(!$objs)
                {
                    return false;
                }
                
                return true;
            }
        } 
        catch (Exception $e) 
        {
            return $e;
        }
    }
    
    public static function databaseExists($ini)
    {
        try 
        {
            if ($ini['type'] == 'sqlite')
            {
                return file_exists($ini['name']);
            }
            elseif ($ini['type'] == 'fbird')
            {
                return file_exists($ini['name']);
            }
            elseif ($ini['type'] == 'pgsql')
            {
                $name = $ini['name'];
                $ini['name'] = 'postgres';
                
                TTransaction::open(null, $ini);
                $sql = "SELECT 1 as exists FROM pg_database WHERE datname = '{$name}'";
                $conn = TTransaction::get();
                $result = $conn->query($sql); 
                $objs = $result->fetchAll(PDO::FETCH_CLASS, "stdClass");
                TTransaction::close();
                
                if($objs)
                {
                    return true;
                }
                else
                {
                    return false;
                }
                
            }
            elseif ($ini['type'] == 'mysql')
            {
                $name = $ini['name'];
                $ini['name'] = '';
                
                TTransaction::open(null, $ini); 
                
                $sql = "SELECT SCHEMA_NAME AS 'Database' FROM INFORMATION_SCHEMA.SCHEMATA where schema_name = '{$name}'";
                $conn = TTransaction::get();
                $result = $conn->query($sql); 
                $objs = $result->fetchAll(PDO::FETCH_CLASS, "stdClass");
            
                TTransaction::close();
                
                if ($objs)
                {
                    return true;
                }
                else
                {
                    return false;
                }
            }
            elseif ($ini['type'] == 'mssql')
            {
                $name = $ini['name'];
                $ini['name'] = '';
                
                TTransaction::open(null, $ini); 
                
                $sql = "SELECT * FROM sys.databases where name = '{$name}'";
                $conn = TTransaction::get();
                $result = $conn->query($sql); 
                $objs = $result->fetchAll(PDO::FETCH_CLASS, "stdClass");
            
                TTransaction::close();
                
                if ($objs)
                {
                    return true;
                }
                else
                {
                    return false;
                }
            }
            else
            {
                TTransaction::open(null, $ini); 
            
                TTransaction::close();
            }
            
            return true;
        } 
        catch (Exception $e) 
        {
            throw new Exception($e->getMessage());
        }
    }
    
    public static function testConnection($host, $name, $user, $pass, $type, $port)
    {
        try
        {
            $ini = [
                'host' => $host,
                'name' => $name,
                'user' => $user,
                'pass' => $pass,
                'type' => $type,
                'port' => $port
            ];
            
            if($ini['type'] == 'pgsql'){
                $ini['name'] = 'postgres';
            }
            elseif($ini['type'] == 'mysql'){
                $ini['name'] = '';
            }
            elseif($ini['type'] == 'mssql'){
                $ini['name'] = '';
            }
            elseif($ini['type'] == 'sqlite'){
                if (is_writable(pathinfo($name)['dirname'])){
                    return true;
                }
                else{
                    throw new Exception(_t('No write permission on file').' '. $name); 
                }
            }
            elseif($ini['type'] == 'fbird'){
                if (is_file($name)){
                    if (is_writable(pathinfo($name)['dirname'])){
                        return true;
                    }
                    else{
                        throw new Exception(_t('No write permission on file').' '. $name); 
                    }
                }
                else{
                    throw new Exception('Banco ' . $name . ' não encontrado. É necessário criá-lo antes de prosseguir');
                }
                
            }
            TTransaction::open(null, $ini);
            
            TTransaction::close();
            
            return true;
        } 
        catch (Exception $e) 
        {
            throw new Exception($e->getMessage());
        }
    }
    
    public function onShow()
    {
        
    }
}