<?php

    ini_set('error_reporting', E_ALL);
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);

    header('Content-Type: text/html; charset=utf-8');

    define('ROOT_DIR'   , __DIR__);
    define('PAGE_DIR'   , ROOT_DIR . '/pages');
    define('EXT'  , 'php');
    define('HEADER_PAGE', 'inc/header.php');
    define('FOOTER_PAGE', 'inc/footer.php');
    define('HEAD'       , 'inc/head.php');

    $topMenu = array(
        'common'   => array('title' => 'Главная'),
        'data'     => array('title' => 'Данные'),
        'database' => array('title' => 'Базы'),
        'users'    => array('title' => 'Пользователи'),
        'scheme'   => array('title' => 'Схема'),
        'tables'   => array('title' => 'Таблицы'),
        'query_builder' => array('title' => 'Запросы'),
    );

    //print_r($_SERVER); die;
    //print_r(explode('/', $_SERVER['REQUEST_URI'])); die;
    $siteUrl = '/';
    $uriArr = explode('/', $_SERVER['PHP_SELF']);
    // $uriArr = explode('/', $_SERVER['REQUEST_URI']);
    foreach ($uriArr as $key => $uri) {
        if(!$uri) continue;
        $pos = strpos($uri, '.php');
        if ($pos === false) $siteUrl .= $uri . '/';
    }

    // print_r($uriArr); die($siteUrl);

    $pageName = 'common';
    if(!empty($_GET['page'])) {
        $pageName =  $_GET['page'];
    }

    define('SITE_URL'  , $siteUrl);
    define('PAGE_NAME' , $pageName);

    $fileName = PAGE_DIR . '/' . PAGE_NAME . '.' . EXT;

    if(!file_exists($fileName)) {
        die('Файл ' . $fileName . ' не найден');
    }

    define('FILE_NAME' , $fileName);

    // die(FILE_NAME);

?>