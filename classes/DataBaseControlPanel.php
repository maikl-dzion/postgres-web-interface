<?php


class DataBaseControlPanel {

    protected $pdo;
    protected $config;
    protected $params = array();
    protected $postData = array();

    public function __construct($config = array(), $init = false) {
        $this->config = $config;
        if($init)
           $this->pdo = $this->dbInit($config);
    }

    protected function dbInit($config = array()) {

        $pdo = null;

        $host   = $config['host'];
        $dbname = $config['dbname'];
        $user   = $config['user'];
        $passwd = $config['passwd'];
        $driver = $config['driver'];
        $port   = $config['port'];

        $dsn = "$driver:host=$host;port=$port;dbname=$dbname;user=$user;password=$passwd";
        $options = array( PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
                         ,PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                         ,PDO::ATTR_EMULATE_PREPARES   => false );

        try {
            $pdo = new PDO($dsn, $user, $passwd, $options);
        } catch (PDOException $err) {
            $err->getMessage();
        }

        return $pdo;
    }


    protected function dbErrorHandler($err, $query, $print = false) {

        // https://postgrespro.ru/docs/postgrespro/9.5/errcodes-appendix   ссылка на ошибки
        $alert = 'Неопределенная ошибка';

        $message = $err->getMessage();
        $code    = $err->getCode();
        $file    = $err->getFile();
        $line    = $err->getLine();

        // $errorInfo = $err->getErrorInfo();
        switch($code) {
            case '42701' : $alert = "Такое поле уже существует (duplicate_column) <br>";break;
            case '42601' : $alert = "Синтаксическая ошибка (syntax_error) <br>"; break;

            default : $alert = " Неопределенная ошибка <br>"; break;
        }

        $alertMessage = $alert . "<br> Sql запрос :: $query <br>
                                  <br> Text :: $message <br>
                                  <br> Code :: $code <br>
                                 ";

        $error = array(
            'alert'   => $alertMessage,
            'message' => $message,
            'code'    => $code,
            'line'    => $line,
            'file'    => $file,
        );

        // print_r($error['alert']);

        if($print)
           print_r($alertMessage);

        return $error;
    }

    protected function exec($query) {
        // print_r($query); die;
        try {
            $result = $this -> pdo -> exec($query);
        } catch (PDOException $err) {
            // $errorMessage = $err->getMessage();
            // $errorCode    = $err->getCode();
            $result['error'] = $this->dbErrorHandler($err, $query);
            // getError($result);
            // print_r($errorMessage);
            // die('--- DB ERROR MESSAGE ---');
            // print_r($result); die;
        }

        return $result;
    }

    protected function assocFormatted($data, $fieldName) {
       $result = array();
       $autoIncIdent = "nextval('users_id_seq'::regclass)";
       foreach($data as $key => $values) {
           if(!empty($values[$fieldName])) {
               $name = $values[$fieldName];
               $result[$name] = $values;
           }
       }
       return $result;
    }

    protected function getTableList($shemeName = '') {
          $shemeName = (!$shemeName) ? 'public' : $shemeName;
          $query = "
            SELECT table_name
                   ,table_catalog
                   ,table_schema
            FROM information_schema.columns
            WHERE table_schema='{$shemeName}'
          ";
          $res = $this->pdo->prepare($query);
          $res->execute();
          return $res->fetchAll();
    }

    protected function getTableListSheme($shemeName = '') {
        $shemeName = (!$shemeName) ? 'public' : $shemeName;
        $query = "
            SELECT *
            FROM information_schema.columns
            WHERE table_schema='{$shemeName}' ";
        $res = $this->pdo->prepare($query);
        $res->execute();
        $list = $res->fetchAll();
        $result = array();

        foreach ($list as $key => $values) {
            $tableName = $values['table_name'];
            $fieldName = $values['column_name'];
            $auto = $values['column_default'];
            $fieldType = $values['data_type'];
            if($auto) $auto = true;
            $field = array(
                'name' => $fieldName,
                'auto' => $auto,
                'type' => $fieldType,
            );
            $result[$tableName]['name'] = $tableName;
            $result[$tableName]['fields'][] = $field;
        }
        return $result;
    }

    protected function getTableFields($tableName) {
          $query = "
                SELECT  column_name
                       ,column_default
                       ,data_type
                FROM information_schema.columns
                WHERE table_name='{$tableName}'";
          $fields = $this->queryPrepareExec($query);
          $result = array();
          $autoIncText = "nextval('{$tableName}_id_seq'::regclass)";
          foreach ($fields as $key => $values) {

              $autoIncState = false;
              if($values['column_default'] == $autoIncText)
                  $autoIncState = true;
              $values['auto_increment'] = $autoIncState;

              $inputType = 'VARCHAR';
              switch($values['data_type']) {
                  case 'character varying' : $inputType = 'VARCHAR'; break;
                  case 'integer'           : $inputType = 'INTEGER'; break;
                  case 'text'              : $inputType = 'TEXT'; break;
              }
              $values['input_type'] = $inputType;

              $result[$values['column_name']] = $values;
          }

          // print_r($result); die;
          return $result;
    }

    // получить имя поля autoincrement
    protected function getTableIdName($fields) {
        $result = array();
        // $fields = $this->getTableFields($tableName);
        $autoIncIdent = "nextval('users_id_seq'::regclass)";
        foreach($fields as $key => $values) {
            if($values['column_default'] == $autoIncIdent) {
                $result = $values;
            }
        }
        return $result;
    }

    // получаем данные из таблицы
    protected function getTableData($tableName){
        $query = "SELECT * FROM {$tableName} ";
        $response = $this->queryPrepareExec($query);
        return $response;
    }

    protected function queryPrepareExec($query, $getType = 'get') {
        $resp = $this->pdo->prepare($query);
        $resp->execute();
        switch ($getType) {
            case 'get' :  $resp = $resp->fetchAll(); break;
        }
        return $resp;
    }

    protected function prepareData($query, $params = array(), $fName = '') {

        $result = array();
        $stmt = $this -> pdo->prepare($query);
        if(!empty($params))
           $stmt->execute($params);
        else
           $stmt->execute();

        if(!$fName) {
            foreach ($stmt as $row)
               $result[] = $row;
            return $result;
        }

        foreach ($stmt as $row) {
            if(!empty($row[$fName])) {
                $name = $row[$fName];
                $result[$name] = $row;
            }
        }

        return $result;
    }

    protected function queryData($query) {
        $result = array();
        $stmt = $this->pdo->query($query);
        while ($row = $stmt->fetch()){
           $result[] =  $row;
        }
        return $result;
    }

    protected function execSqlCommand($command, $commandType = 'query') {
        $result = array();
        switch($commandType) {
            case 'query' :
                $result = $this->queryPrepareExec($command);
                break;

            case 'exec' :

                break;
        }
        return $result;
    }

    //  -------------------------
    //  -------------------------

    protected function createTable($tableName, $idName = 'id', $params = array()) {
         $query = "
            CREATE TABLE {$tableName} (
                {$idName} SERIAL
            );
          ";
         $result = $this -> exec($query);
         // print_r($result); die();
         return $result;
    }

    protected function renameTable($tableName, $newTableName){
        $query = "
            ALTER TABLE {$tableName}
            RENAME TO {$newTableName};
        ";
        $result = $this -> exec($query);
        return $result;
    }

    protected function deleteTable($tableName){
        $query = "
            DROP TABLE {$tableName}
        ";
        $result = $this -> exec($query);
        return $result;
    }

    protected function deleteItemRecord($tableName, $fieldName, $fieldValue) {
        $query = "
            DELETE FROM {$tableName} WHERE {$fieldName} = '{$fieldValue}';
        ";
        $result = $this -> exec($query);
        return $result;
    }

    protected function addField($tableName, $fieldName, $fieldType, $fieldSize = '') {

        $fieldInfo = '';
        if($fieldSize) $fieldSize = '(' .$fieldSize. ')';

        switch ($fieldType) {
            case 'VARCHAR' :
                $fieldInfo = $fieldSize;
                break;
        }

        $query = "
            ALTER TABLE {$tableName}
            ADD COLUMN  {$fieldName} {$fieldType}{$fieldInfo};
        ";

        $result = $this-> exec($query);

        return $result;
    }

    protected function deleteField($tableName, $fieldName) {
        $query = "
            ALTER TABLE {$tableName}
            DROP  COLUMN {$fieldName};
        ";
        $result = $this -> exec($query);
        return $result;
    }

    protected function renameField($tableName, $oldFieldName, $newFieldName) {
        $query = "
            ALTER TABLE {$tableName}
            RENAME COLUMN {$oldFieldName} TO {$newFieldName};
        ";
        $result = $this -> exec($query);
        return $result;
    }

    protected function changeFieldType($tableName = '', $fieldName = '', $newType = '') {
        //print_r($tableName); die;
        if((is_array($tableName))) {
            $tableName = $this->isParam(0);
            $fieldName = $this->isParam(1);
            $newType   = $this->isParam(2);
        }
        $query = "
            ALTER TABLE {$tableName}
            ALTER COLUMN {$fieldName} TYPE {$newType};
        ";
        $result = $this -> exec($query);
        return $result;
    }

    protected function editItem($tableName, $fieldName, $itemId, $newValue) {
        $idName = "id";
        $query  = "
            UPDATE {$tableName} SET {$fieldName} = '{$newValue}' WHERE {$idName} = {$itemId}
        ";
        $result = $this ->exec($query);
        return $result;
    }

    protected function addItem($tableName) {
        $idName = "id";
        $fname = $fvalue = '';
        $fields = $this->getTableFields($tableName);
        foreach ($fields as $key => $value) {
            // print_r($value); die;
            if(!$value['column_default'] && $value['data_type'] == 'character varying') {
                $fname = $value['column_name'];
                break;
            }
        }
        // print_r($fields); die;
        $query  = "
           INSERT INTO {$tableName} ({$fname}) VALUES('test')
        ";
        $result = $this ->exec($query);
        return $result;
    }

    protected function isEmpty($arr, $field, $default = '') {
        $result = $default;
        if(!empty($arr[$field])) {
            $result = $arr[$field];
        }
        return $result;
    }


    // функции по работе с базой и пользователем

    // создаем базу
    protected function createDatabase($dbName, $ownerName){
        $query = "CREATE DATABASE {$dbName} OWNER {$ownerName}";
        $response = $this->exec($query);
        return $response;
    }

    // создаем новую базу
    protected function addNewDb($dbName = ''){
        if((is_array($dbName)) || (empty($dbName))) {
            $dbName = $this->isParam(0);
        }
        $query = "CREATE DATABASE {$dbName}";
        $response = $this->exec($query);
        return $response;
    }

    // удаляем базу
    protected function deleteDb($dbName = ''){
        if((is_array($dbName)) || (empty($dbName))) {
            $dbName = $this->isParam(0);
        }

        switch($dbName){
            case  'maikldb' :
            case  'template0':
            case  'template1':
            case  'postgres' :
                die("Базу {$dbName} нельзя удалить");
                return;
        }

        $query = "DROP DATABASE {$dbName}";
        $response = $this->exec($query);
        return $response;
    }

    // получаем все базы на сервере
    protected function showDatabaseList(){
        $query = "SELECT * FROM pg_database;";
        $response = $this->queryPrepareExec($query);
        return $response;
    }

    // получаем текущею базу
    protected function currentDatabase(){
        $query = "SELECT current_database()";
        $response = $this->queryPrepareExec($query);
        // print_r($response); die;
        return $response;
    }

    // получаем текущего пользователя
    protected function getCurrentDbUser(){
        $query = "SELECT datname,usename,client_addr,client_port FROM pg_stat_activity";
        $response = $this->queryPrepareExec($query);
        return $response;
    }

    // получаем всех пользователей
    protected function getDbUsersList(){
        $query = "SELECT * from pg_user";
        $response = $this->queryPrepareExec($query);
        return $response;
    }

    // Создать нового пользователя
    protected function createUser($params = array()) {
        $r1 = $r2 = $r3 = false;
        // print_r(array($userName, $password, $dataBaseName, $superUser)); die;
        $userName = $this->isParam(0);
        $password = $this->isParam(1);
        $dataBaseName = $this->isParam(2);
        $superUser = $this->isParam(3);

        saveFileUserConfig(array('username' => $userName, 
                                 'passwd'   => $password,
                                 'dbname'   => $dataBaseName,
                                 'super_user' => $superUser,
                                ));

        $query = "CREATE USER {$userName} WITH PASSWORD '{$password}';";
        $r1 = $this -> exec($query);

        if($dataBaseName)
            $r2 = $this->setUserPrivileges($userName, $dataBaseName);

        if($superUser)
            $r3 = $this->setSuperUser($userName);

        return array(
            'addUser'  => $r1,
            'setDbAdmin' => $r2,
            'setSuperUser' => $r3
        );
    }

    // Установить привилегии пользователю
    protected function setUserPrivileges($userName, $dbName = '') {
        if((is_array($userName)) || (empty($userName))) {
            $userName = $this->isParam(0);
            $dbName   = $this->isParam(1);
        }
        $query = "GRANT ALL PRIVILEGES ON DATABASE {$dbName} TO {$userName};";
        $result = $this -> exec($query);
        return $result;
    }

    // Удалить привилегии пользователю к базе данных
    protected function delUserPrivileges($userName, $dbName = '') {
        if((is_array($userName)) || (empty($userName))) {
            $userName = $this->isParam(0);
            $dbName   = $this->isParam(1);
        }
        $query = "REVOKE ALL PRIVILEGES ON DATABASE {$dbName} FROM {$userName};";
        $result = $this -> exec($query);
        return $result;
    }

    // Установить суперправа пользователю
    protected function setSuperUser($userName = '') {
        if((is_array($userName)) || (empty($userName))) {
            $userName = $this->isParam(0);
        }
        $query = "ALTER USER {$userName} WITH SUPERUSER;";
        $result = $this -> exec($query);
        return $result;
    }

    // Удалить суперправа пользователю
    protected function delSuperUser($userName = '') {
        if((is_array($userName)) || (empty($userName))) {
            $userName = $this->isParam(0);
        }

        switch($userName){
            case  'w1user' :
            case  'postgres' :
                die("С пользователя {$userName} нельзя снимать привилегии");
                return;
        }

        $query = "ALTER USER {$userName} WITH NOSUPERUSER;";
        $result = $this -> exec($query);
        return $result;
    }

    // удаляем пользователя
    protected function deleteDbUser($userName = ''){
        if((is_array($userName)) || (empty($userName))) {
            $userName = $this->isParam(0);
        }

        switch($userName){
            case  'w1user' :
            case  'postgres' :
            // case  'reestrsrv' :
               die("Пользователя {$userName} нельзя удалить,это системный пользователь");
               return;
        }

        $query = "DROP USER {$userName}";
        $response = $this->exec($query);
        return $response;
    }



}
