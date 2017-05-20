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
    }

    function join(...$targets) {
        foreach($targets as $target){
            $this->joinBase($this, $target);
        }

        return $this;
    }

    function customJoin($one, $many) {
        $this->joinBase($one, $many);

        return $this;
    }

    function where($conditions, $equals) {
        if($this->conditions != "")
            $this->conditions .= " AND \n";
        else
            $this->conditions .= "\n";

        $this->conditions .= "$conditions = $equals";
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
            $insertDatas .= $data;
        }

        $query = "INSERT INTO $this->pluralName ($this->insertColumns) ".
            "VALUES ($insertDatas);";

        $this->execute($query);
        $this->insertColumns = "";
        return $this->conn->insert_id;
    }

    function insertSingle($column, $data) {
        $column = $this->getColumnName($column);
        $query = "INSERT INTO $this->pluralName ($column) ".
            "VALUES ($data)";
        $this->execute($query);
        return $this->conn->insert_id;
    }

    function update($id){
        $this->updateId = $id;
    }

    function updateColumns(...$columns) {
        foreach($columns as $column) {
            if($this->updateColumns != "")
                $this->updateColumns .= ", ";

            $column = $this->getColumnName($column);
            $this->updateColumns .= $column;
        }
        return $this;
    }

    function updateDatas(...$datas) {
        $updateDatas = "";
        foreach($datas as $data) {
            if($updateDatas != "")
                $updateDatas .= ", ";
            $updateDatas .= $data;
        }

        $updates = "";
        for($i = 0; $i < count($data); $i++) {
            if($updates != "")
                $updates .= ", \n";
            $updates .= $this->updateColumns. " = ". $updateDatas;
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

    function deleteMany($table){
        $query = $this->createQuery(true, $table);
        $this->execute($query);
    }

    function toList() {
        $query = $this->createQuery(false);
        $rows = [];

        if($results = $this->conn->query($query)){
            while($row = mysqli_fetch_object($results)){
                $rows[] = $row;
            }

            $results->close();
        }

        return $rows;
    }

}