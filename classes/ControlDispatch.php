<?php

// Пример запроса от клиента : Добавление нового поля в таблицу
// var url = 'Ключ(действие) / имя таблицы / имя поля / тип поля;
// var url = 'ADD_FIELD/users/email/VARCHAR;
// var url = 'ADD_FIELD/' + this.tableName + '/' + name + '/' + type;

// http://shop1.bolderp5.bget.ru/API_DB_CONTROL_PANEL/api.php/CREATE_DATABASE/dbvlad/w1user

class ControlDispatch extends DataBaseControlPanel {

    protected function getPostData() {
        $methods = $_SERVER['REQUEST_METHOD'];
        $data = (array)json_decode(file_get_contents('php://input'));
        if(!empty($data)) {
            $this->postData = $data;
        }
        return $data;
    }


    public function isParam($index = 0, $secondIndex = false) {
        $result = '';
        // print_r($this->params); die($index);
        if(!empty($this->params[$index])) {
            $result = $this->params[$index];
        }
        return $result;
    }

    protected function argsProcess($args = array()) {
        $result = '';
        // если аргументов нет
        if(empty($args)) // то берем первый елемент из this->params
            return $this->isParam(0);

        // если аргументы есть
        if (is_array($args))   // и это массив
            if (!empty($args[0]))  // берем первый элемент из массива
                $result = $args[0];
            else
                $result = $args;

        return $result;
    }

    public function route($routes) {
        $result = array();
        $actionName = $routes[0];
        $params     = $this->renderParams($routes);

        $this->prepPocessing($actionName, $params);

        if(!$this->pdo) {
            $this->pdo = $this->dbInit($this->config);
        }

        if(method_exists($this, $actionName)) {
            $result = $this->$actionName($params);
        } else {
            $result = $this->dispatch($actionName, $params);
        }
        return $result;
    }

    protected function renderParams($routes) {
        $params = array();
        foreach ($routes as $key => $value) {
            if ($key)
                $params[] = $value;
        }

        $this->params = $params;
        return $params;
    }

    // Предварительная обработка,до подключения базы
    protected function prepPocessing($actionName, $params = array()) {
        // $post = $this->getPostData();
        switch ($actionName) {
            case 'SET_DEFAULT_CONFIG' :
                  $newConfig = getDefaultConfig();
                  // print_r($newConfig); die;
                  $res = saveConfig($newConfig, $this->config, CONF_FILE_NAME, CONF_DIR);
                  // getResponse($res);
                  break;
        }
    }

    public function dispatch($action, $params = array()) {

        $response = array('error' => '', 'result' => '');

        $errorMessage = $newFieldName = $newTableName = '';
        $fieldSize = '250';
        $fieldType = 'VARCHAR';

        $tableName = $this->isEmpty($params, 0);
        $fieldName = $this->isEmpty($params, 1);

        /**
        if(!$tableName) {
        $errorMessage = 'Нет имени таблицы';
        return $response['error'] = $errorMessage;
        }
         **/

        $res = array();

        switch ($action) {

            case 'SAVE_CONFIG' :
                $newConfig = $this->getPostData();
                // getResponse($confData, 'error');
                $res = saveConfig($newConfig, $this->config, CONF_FILE_NAME, CONF_DIR);
                break;

            case 'ADD_FIELD_LIST' :

                $fields = getFieldList();
                $tableName = 'lk_evgroup';
                foreach ($fields as $key => $item) {
                    $fieldName = $item[0];
                    $fieldType = $item[1];
                    $desc      = $item[2];
                    $res[] = $this->addField($tableName, $fieldName, $fieldType);
                }

                print_r($res);
                die('stop - 133');
                break;

            case 'GET_CUR_CONFIG' :
                $res = $this->config;
                break;

            case 'GET_FILE_USERS_CONFIG' :
                $res = getFileUsersConfig('config/dbUsers/');
                break;    

            case 'CURRENT_DATABASE' :
                $res = $this->currentDatabase();
                break;

            case 'EXEC_SQL_COMMAND' :
                $command = $this->isEmpty($params, 0);
                $commandType = $this->isEmpty($params, 1);
                $res = $this->execSqlCommand($command, $commandType);
                break;    

            case 'CURRENT_DB_USER' :
                $res = $this->getCurrentDbUser();
                break;

            case 'CREATE_DATABASE' :
                $newDbName = $this->isEmpty($params, 0);
                $ownerName = $this->isEmpty($params, 1);
                $res = $this->createDatabase($newDbName, $ownerName);
                break;

            case 'SHOW_DATABASE_LIST' :
                $res = $this->showDatabaseList();
                break;

            case 'DELETE_ITEM' :
                $fieldValue = $this->isEmpty($params, 2);
                $res = $this->deleteItemRecord($tableName, $fieldName, $fieldValue);
                break;

            case 'GET_TABLE_LIST' :
                $shemeName = $this->isEmpty($params, 0);
                $tableList    = $this->getTableList($shemeName);
                $res = $this->assocFormatted($tableList, 'table_name');
                break;

            case 'GET_TABLE_FIELDS' :
                $fieldsList = $this->getTableFields($tableName);
                // $res = $this->assocFormatted($fieldsList, 'column_name');
                $res = $fieldsList;
                // print_r($fieldsList); die;
                break;

            case 'GET_TABLE_LIST_SHEME' :
                $shemeName = $this->isEmpty($params, 0);
                $res = $this->getTableListSheme($shemeName);
                break;

            case 'GET_TABLE_ID_NAME' :
                $fieldsList = $this->getTableFields($tableName);
                $res = $this->getTableIdName($fieldsList);
                break;

            case 'GET_TABLE_DATA' :
                $res = $this->getTableData($tableName);
                break;

            case 'CREATE_TABLE' :
                $res = $this->createTable($tableName);
                break;

            case 'DELETE_TABLE' :
                $res = $this->deleteTable($tableName);
                break;

            case 'RENAME_TABLE' :
                $newTableName = $this->isEmpty($params, 1);
                $res = $this->renameTable($tableName, $newTableName);
                break;

            case 'ADD_FIELD' :
                $fieldType = $this->isEmpty($params, 2, $fieldType);
                $res = $this->addField($tableName, $fieldName , $fieldType, $fieldSize);
                break;

            case 'DELETE_FIELD' :
                $res = $this->deleteField($tableName, $fieldName);
                break;

            case 'RENAME_FIELD' :
                $newFieldName = $this->isEmpty($params, 2);
                $res = $this->renameField($tableName, $fieldName, $newFieldName);
                break;

            case 'EDIT_ITEM' :
                $itemId = $this->isEmpty($params, 2);
                $newValue = $this->isEmpty($params, 3);
                $res = $this->editItem($tableName, $fieldName, $itemId, $newValue);
                break;

            case 'ADD_ITEM' :
                $res = $this->addItem($tableName);
                break;

            // Функции для работы с пользователем
            case 'ADD_DB_USER' :
                $userName  = $this->isEmpty($params, 0);
                $password  = $this->isEmpty($params, 1);
                $dbName    = $this->isEmpty($params, 2);
                $userState = $this->isEmpty($params, 3);
                $res = $this->createUser($userName, $password, $dbName, $userState);
                break;

            case 'SET_USER_PRIVILEGES' :
                $userName  = $this->isEmpty($params, 0);
                $dbName    = $this->isEmpty($params, 1);
                $res = $this->setUserPrivileges($userName, $dbName);
                break;

            case 'SET_SUPER_USER' :
                $userName  = $this->isEmpty($params, 0);
                $res = $this->setSuperUser($userName);
                break;

            case 'DELETE_DB_USER' :
                $userName  = $this->isEmpty($params, 0);
                // $res = $this->setSuperUser($userName);
                break;

        }

        // if(empty($res)) { print_r($action); die();}

        return $response['result'] = $res;
    }

}


function getFieldList($fieldList = array()) {

    $result = array (

//        0 =>
//            array (
//                0 => 'id',
//                1 => 'int',
//                2 => 'Первичный ключ',
//            ),

        1 =>
            array (
                0 => 'global_id',
                1 => 'int',
                2 => 'Идентификатор ',
            ),
        2 =>
            array (
                0 => 'global_name',
                1 => 'varchar',
                2 => 'Наименование ',
            ),
        3 =>
            array (
                0 => 'has_automapdrow',
                1 => 'boolean',
                2 => 'Признак автоматического нанесения на карту',
            ),
        4 =>
            array (
                0 => 'has_default',
                1 => 'boolean',
                2 => 'Признак «Выбор по умолчанию»',
            ),
        5 =>
            array (
                0 => 'has_extrime',
                1 => 'boolean',
                2 => 'Признак ЧС',
            ),
        6 =>
            array (
                0 => 'has_forsendcds',
                1 => 'boolean',
                2 => 'Признак «Для отправки в ДС АГ»',
            ),
        7 =>
            array (
                0 => 'hours_cont_calc',
                1 => 'int',
                2 => 'Кол-во часов на устранение по умолчанию',
            ),
        8 =>
            array (
                0 => 'icon_id',
                1 => 'int',
                2 => 'Идентификатор значка на карте',
            ),
        9 =>
            array (
                0 => 'id_type_adress',
                1 => 'int',
                2 => 'Код типа происшествия',
            ),
        10 =>
            array (
                0 => 'id_type_place_event',
                1 => 'int',
                2 => 'Код места происшествия',
            ),
        11 =>
            array (
                0 => 'isshow',
                1 => 'boolean',
                2 => 'Признак «видимость»',
            ),
        12 =>
            array (
                0 => 'l_stat',
                1 => 'boolean',
                2 => 'Признак «не используется в программе»',
            ),
        13 =>
            array (
                0 => 'l_tsz',
                1 => 'boolean',
                2 => 'Признак использования в отчете по ЖЭС',
            ),
        14 =>
            array (
                0 => 'mode_id',
                1 => 'int',
                2 => 'Код режима типизированного значка на карте',
            ),
        15 =>
            array (
                0 => 'name',
                1 => 'varchar',
                2 => 'Категория обращения',
            ),
        16 =>
            array (
                0 => 'path',
                1 => 'varchar',
                2 => 'Поле связи в дереве',
            ),
        17 =>
            array (
                0 => 'prev',
                1 => 'int',
                2 => 'Ссылка на вышестоящую категорию',
            ),
        18 =>
            array (
                0 => 'type_id',
                1 => 'int',
                2 => 'Код типа типизированного значка на карте',
            ),
    );

    return $result;
}



