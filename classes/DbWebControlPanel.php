<?php


class DbWebControlPanel {

    protected $pdo;
    protected $config;
    protected $params = array();
    protected $postData = array();

    use Users;
    use DbHandlers;
    use TableHandlers;
    use DataHandlers;

    public function __construct($config = array(), $connect = false) {
        $this->config = $config;
        if($connect)
           $this->pdo = $this->connect($config);
    }

    protected function connect($config = array()) {
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
            // $err->getMessage();
            $this->dbErrorHandler($err, '__construct init error', true);
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

    protected function isEmpty($arr, $field, $default = '') {
        $result = $default;
        if(!empty($arr[$field])) {
            $result = $arr[$field];
        }
        return $result;
    }

    protected function exec($query) {
        try {
            $result = $this -> pdo -> exec($query);
        } catch (PDOException $err) {
            $result['error'] = $this->dbErrorHandler($err, $query);

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

    protected function execSqlCommand($command, $commandType = 'query', $tableName = '') {
        $result = array();
        // print_r($commandType); die;
        // $command = textareaHandler($command);
        switch($commandType) {
            case 'query' :
                $result = $this->queryPrepareExec($command);
                break;
            case 'exec' :
                // $result = $this->exec($command);
                break;
            case 'add_fields' :
            case 'create_fields' :
                $items = textareaHandler($command);
                // print_r($items); die;
                if((!empty($items)) && $tableName) {
                    foreach($items as $key => $value) {
                        $name = $size = '';
                        $type = 'varchar';
                        $item = array();
                        $fieldItem = explode(" ", $value);

                        foreach($fieldItem as $k => $v) {
                           if($v) $item[] = $v;
                        }

                        if(!isset($item[0]))
                            return array();

                        $name = $item[0];
                        if(isset($item[1])) $type = $item[1];
                        if(isset($item[2])) $size = $item[2];

                        // prinr_r($item); die;

                        $result[] = $this->addField($tableName, $name, $type, $size);
                    }
                }

                break;

            case 'add_table' :
            case 'create_table' :
                $items = textareaHandler($command);
                // print_r($items); die;
                if((empty($items))) return array();

                $r = array();
                foreach($items as $key => $value) {
                    $item = array();
                    $fieldItem = explode(" ", $value);
                    foreach($fieldItem as $k => $v) {
                        if($v)
                            $item[] = $v;
                    }
                    if(!isset($item[0]))
                        return array();
                    $r[] = $item;
                }

                $ch = 0;
                $tableName = '';

                foreach($r as $key => $item) {

                   if($ch == 0) {
                       if(!isset($item[0]))
                           return array();

                       $tableName = $item[0];
                       $autoIncName = 'id';

                       if(isset($item[1]))
                           $autoIncName = $item[1];
                       $this->createTable($tableName, $autoIncName);

                   } elseif($tableName) {
                       if(!isset($item[0])) continue;

                       $size = '';
                       $fieldName = $item[0];
                       $type = 'varchar';
                       if(isset($item[1])) $type = $item[1];
                       if(isset($item[2])) $size = $item[2];
                       $result[] = $this->addField($tableName, $fieldName, $type, $size);
                   }

                   $ch++;
                }

                // print_r($r); die;

                break;

        }
        return $result;
    }


}
