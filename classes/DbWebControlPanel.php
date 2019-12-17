<?php


class DbWebControlPanel {

    protected $pdo;
    protected $config;
    protected $params = array();
    protected $postData = array();

//    protected $fieldTypes = array(
//
//          array('date_type'   => 'character varying',
//                'form_type'   => 'text',
//                'input_type'  => 'varchar'),
//
//          array('date_type'   => 'integer',
//                'form_type'   => 'num',
//                'input_type'  => 'integer'),
//
//          array('date_type'   => 'text',
//                'form_type'   => 'textarea',
//                'input_type'  => 'text'),
//    );

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

        $fname  = __FUNCTION__;
        try {
            $pdo = new PDO($dsn, $user, $passwd, $options);
        } catch (PDOException $err) {
            // $err->getMessage();
            $this->error($err, '_CONSTRUCT RUN ERROR', $fname, true);
        }

        return $pdo;
    }

    // -- Выполнение запроса в базе
    protected function exec($query) {
        $fname  = __FUNCTION__;
        try {
            $result = $this -> pdo -> exec($query);
        } catch (PDOException $err) {
            $result['error'] = $this->error($err, $query, $fname,true);
        }
        return $result;
    }

    // -- Выборка из базы
    protected function select($query) {
        $result = array();
        $fname  = __FUNCTION__;
        try {
            $resp = $this->pdo->prepare($query);
            $resp->execute();
            $result = $resp->fetchAll();
        } catch (PDOException $err) {
            $result['error'] = $this->error($err, $query, $fname, true);
        }
        return $result;
    }


    protected function error($err, $query, $fname, $print = false) {

        // https://postgrespro.ru/docs/postgrespro/9.5/errcodes-appendix   ссылка на ошибки
        $alert = 'Неопределенная ошибка';

        $message = $err->getMessage();
        $code    = $err->getCode();
        $file    = $err->getFile();
        $line    = $err->getLine();

        $debugTrace = debug_backtrace();

        // $errorInfo = $err->getErrorInfo();
        switch($code) {
            case '42701' : $alert = "Такое поле уже существует (duplicate_column) <br>";break;
            case '42601' : $alert = "Синтаксическая ошибка (syntax_error) <br>"; break;

            default : $alert = " Неопределенная ошибка <br>"; break;
        }

        $alertMessage = $alert . "<br> Sql запрос :: <h3 style='font-weight: bold;color:red'>$query</h3> <br>
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

        if($print) {
            // print_r($alertMessage);
            echo "\n\n ======== FuncName ====== \n\n";
            echo $fname;
            echo "\n\n ======== Error ========= \n\n";
            print_r($error);
            echo "\n\n ======== Debug trace ==== \n\n";
            print_r($debugTrace);
            die();
        }

        return $error;
    }

    protected function isEmpty($arr, $field, $default = '') {
        $result = $default;
        if(!empty($arr[$field])) {
            $result = $arr[$field];
        }
        return $result;
    }

    // -- Получить все таблицы и схему
    protected function getTableListSheme($shemeName = '') {
        $shemeName = (!$shemeName) ? 'public' : $shemeName;
        $query = "SELECT * FROM information_schema.columns
                  WHERE table_schema='{$shemeName}'";
        $list = $this->select($query);
        $result = array();

        foreach ($list as $key => $values) {
            $tableName = $values['table_name'];
            $fieldName = $values['column_name'];
            $auto      = $values['column_default'];
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

    // -- Получить список полей
    protected function getTableFields($tableName) {
          $query = "
                SELECT  column_name
                       ,column_default
                       ,data_type
                FROM  information_schema.columns
                WHERE table_name='{$tableName}'";
          $fields = $this->select($query);
          // -- обрабатываем поля
          return $this->fieldsFormat($fields, $tableName);
    }

    protected function fieldsFormat($fields, $tableName) {
        $result = array();
        $autoIncString = "nextval('{$tableName}_id_seq'::regclass)";
        foreach ($fields as $key => $item) {
            $fieldName = $item['column_name'];
            $item = $this->fieldSetType($item, $autoIncString);
            $result[$fieldName] = $item;
        }
        return $result;
    }

    // --- Устанавливаем тип и auto_increment поля
    protected function fieldSetType($item, $autoIncrement) {
          $formtype = 'text';
          $type     = 'varchar';

          $incrementState = false;
          if($item['column_default'] == $autoIncrement)
              $incrementState = true;

          switch($item['data_type']) {
            case 'character varying' :
                 $type = 'varchar';
                 $formtype = 'text';
                 break;

            case 'integer' :
                 $type = 'integer';
                 $formtype = 'num';
                 break;

            case 'text' :
                $type = 'text';
                $formtype = 'textarea';
                 break;
          }

          $item['auto_increment'] = $incrementState;
          $item['input_type']     = $type;
          $item['form_type']      = $formtype;

          return $item;
    }

    protected function execSqlCommand($command, $commandType = 'query', $tableName = '') {
        $result = array();
        // print_r($commandType); die;
        // $command = textareaHandler($command);
        switch($commandType) {
            case 'query' :
                $result = $this->select($command);
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

    // -- Получить имя поля Autoincrement
    protected function getAutoIncrementName($fields) {
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

    protected function assocFormatted($data, $fieldName) {
        $result = array();
        // $autoIncIdent = "nextval('users_id_seq'::regclass)";
        foreach($data as $key => $values) {
            if(!empty($values[$fieldName])) {
                $name = $values[$fieldName];
                $result[$name] = $values;
            }
        }
        return $result;
    }

//    protected function getTableIdName($fields) {
//        $result = array();
//        // $fields = $this->getTableFields($tableName);
//        $autoIncIdent = "nextval('users_id_seq'::regclass)";
//        foreach($fields as $key => $values) {
//            if($values['column_default'] == $autoIncIdent) {
//                $result = $values;
//            }
//        }
//        return $result;
//    }


    //###########################################
    // -- Устаревшие функции (требуется удаление)

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

}
