<?php

trait TableHandlers {

    // -- Получить список таблиц
    protected function getTableList($shemeName = '') {
        $shemeName = (!$shemeName) ? 'public' : $shemeName;
        $query = "SELECT table_name,
                           table_catalog,
                           table_schema
                    FROM information_schema.columns
                    WHERE table_schema='{$shemeName}'";
        return  $this->select($query);
    }

    // -- Создаем новую таблицу
    protected function createTable($tableName, $idName = 'id', $params = array()) {
        $query = "CREATE TABLE {$tableName} ({$idName} SERIAL);";
        return $this -> exec($query);
    }

    // -- Изменяем имя таблицы
    protected function renameTable($tableName, $newTableName){
        $query = "ALTER TABLE {$tableName} RENAME TO {$newTableName};";
        return $this -> exec($query);
    }

    // -- Удаляем таблицу
    protected function deleteTable($tableName){
        $query = "DROP TABLE {$tableName};";
        return $this -> exec($query);
    }


    // -- Добавляем новое поле
    protected function addField($tableName, $fieldName, $fieldType, $fieldSize = '') {

        $fieldInfo = '';
        if($fieldSize) $fieldSize = '(' .$fieldSize. ')';

        switch ($fieldType) {
            case 'VARCHAR' :
                $fieldInfo = $fieldSize;
                break;
        }

        $query = "ALTER TABLE {$tableName} 
                  ADD COLUMN  {$fieldName} {$fieldType}{$fieldInfo};";
        return $this-> exec($query);
    }

    // -- Удаляем поле
    protected function deleteField($tableName, $fieldName) {
        $query = "
            ALTER TABLE {$tableName}
            DROP  COLUMN {$fieldName};
        ";
        $result = $this -> exec($query);
        return $result;
    }

    // -- Изменяем имя поля
    protected function renameField($tableName, $oldFieldName, $newFieldName) {
        $query = "
            ALTER TABLE {$tableName}
            RENAME COLUMN {$oldFieldName} TO {$newFieldName};";
        return $this -> exec($query);
    }


    // -- Изменяем тип поля
    protected function changeFieldType($tableName = '', $fieldName = '', $newType = '') {

        if((is_array($tableName))) {
            $tableName = $this->isParam(0);
            $fieldName = $this->isParam(1);
            $newType   = $this->isParam(2);
        }
        $query = "
            ALTER TABLE {$tableName}
            ALTER COLUMN {$fieldName} TYPE {$newType};";
        return $this -> exec($query);
    }

}