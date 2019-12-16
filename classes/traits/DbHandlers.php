<?php

trait DbHandlers {

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

}