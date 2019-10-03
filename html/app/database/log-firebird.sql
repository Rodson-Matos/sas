CREATE TABLE system_change_log (
    id INTEGER PRIMARY KEY NOT NULL,
    logdate timestamp,
    login blob sub_type 1,
    tablename blob sub_type 1,
    primarykey blob sub_type 1,
    pkvalue blob sub_type 1,
    operation blob sub_type 1,
    columnname blob sub_type 1,
    oldvalue blob sub_type 1,
    newvalue blob sub_type 1);

CREATE TABLE system_sql_log (
    id INTEGER PRIMARY KEY NOT NULL,
    logdate timestamp,
    login blob sub_type 1,
    database_name blob sub_type 1,
    sql_command blob sub_type 1,
    statement_type blob sub_type 1);

CREATE TABLE system_access_log (
    id INTEGER PRIMARY KEY NOT NULL,
    sessionid blob sub_type 1,
    login blob sub_type 1,
    login_time timestamp,
    logout_time timestamp);