<?php

trait DataHandlers {

    // -- Удаляем запись из базы
    protected function deleteItemRecord($tableName, $fieldName, $fieldValue) {
        $query = "
            DELETE FROM {$tableName} WHERE {$fieldName} = '{$fieldValue}';
        ";
        $result = $this -> exec($query);
        return $result;
    }

    // -- Редактируем запись в базы
    protected function editItem($tableName, $fieldName, $itemId, $newValue) {
        $idName = "id";
        $query  = "
            UPDATE {$tableName} SET {$fieldName} = '{$newValue}' WHERE {$idName} = {$itemId}
        ";
        $result = $this ->exec($query);
        return $result;
    }

    // -- Добавляем новую запись в базу
    protected function addItem($tableName) {
        $idName = "id";
        $fname = $fvalue = '';
        $fields = $this->getTableFields($tableName);
        foreach ($fields as $key => $value) {
            // print_r($value); die;
            if(!$value['column_default'] && $value['data_type'] == 'character varying') {
                $fname = $value['column_name'];
                break;
            }
        }

        $query  = "
           INSERT INTO {$tableName} ({$fname}) VALUES('test')
        ";
        $result = $this ->exec($query);
        return $result;
    }

    // -- Получаем данные из таблицы
    protected function getTableData($tableName){
        $query = "SELECT * FROM {$tableName} ";
        $response = $this->queryPrepareExec($query);
        return $response;
    }

}