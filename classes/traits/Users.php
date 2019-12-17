<?php

trait Users {

    // получаем текущего пользователя
    protected function getCurrentDbUser(){
        $query = "SELECT datname,usename,client_addr,client_port FROM pg_stat_activity ";
        // lg($query);
        return $this->select($query);
    }

    // получаем всех пользователей
    protected function getDbUsersList(){
        $query = "SELECT * from pg_user";
        return $this->select($query);
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
                die("Пользователя {$userName} нельзя удалить , это системный пользователь");
                return;
        }

        $query = "DROP USER {$userName}";
        $response = $this->exec($query);
        return $response;
    }

}