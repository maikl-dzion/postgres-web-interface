<?php

trait TableHandlers {

    // -- Создаем новую таблицу
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

    // -- Изменяем имя таблицы
    protected function renameTable($tableName, $newTableName){
        $query = "
            ALTER TABLE {$tableName}
            RENAME TO {$newTableName};
        ";
        $result = $this -> exec($query);
        return $result;
    }

    // -- Удаляем таблицу
    protected function deleteTable($tableName){
        $query = "
            DROP TABLE {$tableName}
        ";
        $result = $this -> exec($query);
        return $result;
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

        $query = "
            ALTER TABLE {$tableName}
            ADD COLUMN  {$fieldName} {$fieldType}{$fieldInfo};
        ";

        $result = $this-> exec($query);

        return $result;
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
            RENAME COLUMN {$oldFieldName} TO {$newFieldName};
        ";
        $result = $this -> exec($query);
        return $result;
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
            ALTER COLUMN {$fieldName} TYPE {$newType};
        ";
        $result = $this -> exec($query);
        return $result;
    }

}