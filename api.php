<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: *');
header('Content-Type: text/html; charset=utf-8');
header('Content-Type: text/html; charset=utf-8');

ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

//getFileUsersConfig();

define('ROOT_DIR', __DIR__);
define('CONF_DIR', ROOT_DIR . '/config');
define('CONF_FILE_NAME', CONF_DIR . '/conf.php');

require_once ROOT_DIR . '/classes/DataBaseControlPanel.php';
require_once ROOT_DIR . '/classes/ControlDispatch.php';

$routes = routerInit();

//$conf = array(
//     'host'   => '185.63.191.96'
//    ,'dbname' => 'maikldb'
//    ,'user'   => 'w1user'
//    ,'passwd' => 'w1password'
//    ,'driver' => 'pgsql'
//    ,'port'   => 5432
//);
// pre($dbConf);
// saveConfig($conf, CONF_FILE_NAME);


// http://shop1.bolderp5.bget.ru/API_DB_CONTROL_PANEL/api.php/CREATE_DATABASE/dbvlad/w1user
// http://shop1.bolderp5.bget.ru/API_DB_CONTROL_PANEL/api.php/setSuperUser/w1user

// Пример запроса от клиента : Добавление нового поля в таблицу
// var url = 'Ключ(действие) / имя таблицы / имя поля / тип поля;
// var url = 'ADD_FIELD/users/email/VARCHAR;
// var url = 'ADD_FIELD/' + this.tableName + '/' + name + '/' + type;

$dbConf    = require CONF_FILE_NAME;       // --- получаем конфиги
// print_r($_SERVER); die;
$dbcontrol = new ControlDispatch($dbConf); // --- создаем объект
$result    = $dbcontrol->route($routes);   // --- выполняем
getResponse($result);                      // --- возвращаем результат


///////////////////////////////////
//////////////////////////////////
///
///
///
///

//$tableName = 'test_table';
//$tableName = 'test_table22';
//
//$tableList  = $dbcontrol->dispatch('GET_TABLE_LIST', array());
//$fieldsList = $dbcontrol->dispatch('GET_TABLE_FIELDS', array('products'));
//
//pre(array($fieldsList, $tableList));

//// $dbcontrol->dispatch('CREATE_TABLE', array($tableName));
//// $dbcontrol->dispatch('DELETE_TABLE', array('t_new_tab'));
//
//$renameTable = array($tableName, 't_new_tab');
//// $dbcontrol->dispatch('RENAME_TABLE', $renameTable);
//
//$newField = array($tableName,'newf1234', 'VARCHAR', 250);
//// $dbcontrol->dispatch('ADD_FIELD', $newField);
//
//$deleteField = array($tableName, 'u1');
//// $dbcontrol->dispatch('DELETE_FIELD', $deleteField);
//
//$renameField = array($tableName, 'u1maikl', 'retserg');
//// $dbcontrol->dispatch('RENAME_FIELD', $renameField);
//// print_r($dbcontrol->funcRun("public.getUsers"));


function pre($data) {
    $r = print_r($data, true);
    echo '<pre>';
    echo print($r);
    echo '</pre>';
    die();
}

function getError($data) {
    getResponse($data, 'error');
}

function getDebug($data) {
    getResponse($data, 'debug');
}

function getResponse($data, $name = 'data') {
    die(json_encode(array($name => $data)));
}

function routerInit($fName = 'PATH_INFO') {
    $server = $_SERVER;
    $routes = array();
    if(!empty($server[$fName])) {
        $pathInfo = $server[$fName];
        $pathInfo = trim($pathInfo, '/');
        $routes   = explode('/', $pathInfo);
    }

    return $routes;
}

function saveConfig($newData, $oldData, $fileName = 'config/conf.php', $confDir = 'config') {

    $newStringData = renderArrayToString($newData);
    $oldStringData = renderArrayToString($oldData);

    // сохраняем старый конфиг
    $oldFileName = $confDir . '/old_config/conf_' . date("Y_m_d___H_i_s") . '.txt';

    if(empty($oldData))  return false;

    file_put_contents($oldFileName, $oldStringData);

    if(empty($newData))  return false;

    // перезаписываем новый конфиг
    file_put_contents($fileName, $newStringData);

    return true;
}

// --- записывает данные пользователя в файл
function saveFileUserConfig($newData, $fileName = 'config/dbUsers/') {
    $userName = $newData['username'];
    $newData['create_date'] = date("Y_m_d___H_i_s");
    $fileName = $fileName . $userName. '.php';
    $newStringData = renderArrayToString($newData);
    file_put_contents($fileName, $newStringData);
    return true;
}

// --- получаем  данные пользователей из файлов
function getFileUsersConfig($dirName = 'config/dbUsers/') {

    $result = array();
    $files = scandir($dirName); 
    
    foreach($files as $key => $name) {
        if($name == '.' || $name == '..') continue;
        $item = include $dirName . $name;
        $arrName = explode('.', $name);
        $userName = $arrName[0];
        $result[$userName] = $item;
    }

    // print_r($result); die;
    return $result;
}

// --- получаем дефолтные (базовые) настройки
function getDefaultConfig() {
    $config = include CONF_DIR .'/config_default.php';
    return $config;
}


function renderArrayToString($arrData) {
    $ch = 0;
    $stringData = "";
    foreach ($arrData as $key => $value) {
        ($ch) ? $sep = ',' :  $sep = '';
        $stringData .= "{$sep}'{$key}' => '{$value}' \n";
        $ch++;
    }
    $stringData = "<?php \n return array(\n {$stringData}); \n";
    return $stringData;
}


function s2($data) {

    $fileName = "my_file.php";
    $stringData = serialize($data);
    file_put_contents($fileName, $stringData);
    $stringData = file_get_contents($fileName);
    $configData = unserialize($stringData);
    return $configData;
}

?>