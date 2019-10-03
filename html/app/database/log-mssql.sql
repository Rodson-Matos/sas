CREATE TABLE system_change_log (
    id INTEGER PRIMARY KEY NOT NULL,
    logdate datetime2,
    login nvarchar(max),
    tablename nvarchar(max),
    primarykey nvarchar(max),
    pkvalue nvarchar(max),
    operation nvarchar(max),
    columnname nvarchar(max),
    oldvalue nvarchar(max),
    newvalue nvarchar(max));

CREATE TABLE system_sql_log (
    id INTEGER PRIMARY KEY NOT NULL,
    logdate datetime2,
    login nvarchar(max),
    database_name nvarchar(max),
    sql_command nvarchar(max),
    statement_type nvarchar(max));

CREATE TABLE system_access_log (
    id INTEGER PRIMARY KEY NOT NULL,
    sessionid nvarchar(max),
    login nvarchar(max),
    login_time datetime2,
    logout_time datetime2);