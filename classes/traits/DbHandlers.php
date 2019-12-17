<?php

trait DbHandlers {

    // -- Создаем базу и прикрепляем пользователя
    protected function createDatabase($dbName, $ownerName){
        $query = "CREATE DATABASE {$dbName} OWNER {$ownerName}";
        return $this->exec($query);
    }

    // -- Создаем новую базу
    protected function addNewDb($dbName = ''){
        if((is_array($dbName)) || (empty($dbName))) {
            $dbName = $this->isParam(0);
        }
        $query = "CREATE DATABASE {$dbName}";
        return $this->exec($query);
    }

    // -- Удаляем базу
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

    // -- Получаем все базы на сервере
    protected function showDatabaseList(){
        $query = "SELECT * FROM pg_database;";
        return $this->select($query);
    }

    // -- Получаем текущею базу
    protected function currentDatabase(){
        $query = "SELECT current_database()";
        return $this->select($query);
    }

}