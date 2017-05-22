<?php
include 'DbBase.php';
/**
 * Created by PhpStorm.
 * User: Song
 * Date: 5/21/2017
 * Time: 1:50 AM
 */
class DbCore extends DbBase
{
    function select(...$columns) {
        return $this->selectBase(false, $columns);
    }

    function selectAsId(...$tables) {
        return $this->selectBase(true, $tables);
    }

    function selectAs($column, $aliasName) {
        if($this->selects != "")
            $this->selects .= ", ";
        $this->selects .= "$column". " AS ". $aliasName;

        return $this;
    }

    function join(...$targets) {
        foreach($targets as $target){
            $foreignKey = "$target->tableName"."Id";

            // if holding target foreign key, self is many target is one
            // if not holding, target holding, self is one target is many
            if(property_exists($this, $foreignKey))
                $this->joinOneBase($this, $target);
            else
                $this->joinManyBase($this, $target);
        }

        return $this;
    }

    function customJoin($from, $to) {
        $foreignKey = "$to->tableName"."Id";
        if(property_exists($from, $foreignKey))
            $from->joinOneBase($from, $to);
        else
            $from->joinManyBase($from, $to);

        return $this;
    }

    function where($conditions, $equals) {
        $this->whereBase($conditions, "=", $equals);
        return $this;
    }

    function whereNot($conditions, $notEquals) {
        $this->whereBase($conditions, "!=", $notEquals);
        return $this;
    }

    function customWhere($left, $middle, $right) {
        $this->whereBase($left, $middle, $right);
        return $this;
    }

    function insertColumns(...$columns) {
        foreach($columns as $column) {
            if($this->insertColumns != "")
                $this->insertColumns .= ", ";

            $column = $this->getColumnName($column);
            $this->insertColumns .= $column;
        }

        return $this;
    }

    function insertDatas(...$datas) {
        $insertDatas = "";
        foreach($datas as $data) {
            if($insertDatas != "")
                $insertDatas .= ", ";
            $insertDatas .= $this->checkData($data);
        }

        $query = "INSERT INTO $this->pluralName ($this->insertColumns) ".
            "VALUES ($insertDatas);";

        $this->execute($query);
        $this->insertColumns = "";
        return $this->conn->insert_id;
    }

    function insertSingle($column, $data) {
        $column = $this->getColumnName($column);
        $data = $this->checkData($data);
        $query = "INSERT INTO $this->pluralName ($column) ".
            "VALUES ($data)";
        $this->execute($query);
        return $this->conn->insert_id;
    }

    function update($id){
        $this->updateId = $id;
    }

    function updateColumns(...$columns) {
        $this->updateColumns = [];
        foreach($columns as $column) {
            $column = $this->getColumnName($column);
            $this->updateColumns = $column;
        }
        return $this;
    }

    function updateDatas(...$datas) {
        $updateDatas = [];
        foreach($datas as $data) {
            $updateDatas = $this->checkData($data);
        }

        $updates = "";
        for($i = 0; $i < count($datas); $i++) {
            if(count($updateDatas) != 0)
                $updates .= ", \n";
            $updates .= $this->updateColumns[$i]. " = ". $updateDatas[$i];
        }

        $query = "UPDATE $this->pluralName ".
            "\nSET ". $updates.
            "\n WHERE $this->pluralName.id = $this->updateId;";

        $this->execute($query);
        $this->insertColumns = "";
        $this->updateId = 0;
    }

    function updateSingle($id, $column, $data) {
        $column = $this->getColumnName($column);
        $data = $this->checkData($data);
        $query = "UPDATE $this->pluralName ".
            "\nSET $column = $data ".
            "\nWHERE $this->pluralName.id = $id";

        $this->execute($query);
    }

    function delete($id) {
        $query = "DELETE FROM $this->pluralName ".
            "\nWHERE $this->pluralName.id = $id;";

        $this->execute($query);
    }

    function deleteMany($deleteTable = null){
        $query = $this->createQuery(true, $deleteTable);
        $this->execute($query);
    }


}