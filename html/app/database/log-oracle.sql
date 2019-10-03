CREATE TABLE system_change_log (
    id INTEGER PRIMARY KEY NOT NULL,
    logdate timestamp(0),
    login CLOB,
    tablename CLOB,
    primarykey CLOB,
    pkvalue CLOB,
    operation CLOB,
    columnname CLOB,
    oldvalue CLOB,
    newvalue CLOB);

CREATE TABLE system_sql_log (
    id INTEGER PRIMARY KEY NOT NULL,
    logdate timestamp(0),
    login CLOB,
    database_name CLOB,
    sql_command CLOB,
    statement_type CLOB);

CREATE TABLE system_access_log (
    id INTEGER PRIMARY KEY NOT NULL,
    sessionid CLOB,
    login CLOB,
    login_time timestamp(0),
    logout_time timestamp(0));