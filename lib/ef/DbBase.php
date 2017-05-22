<?php

/**
 * Created by PhpStorm.
 * User: Song
 * Date: 5/21/2017
 * Time: 1:31 AM
 */
class DbBase
{
    private $debug = true;

    public $tableName;
    public $pluralName;

    protected $selects;
    protected $joins;
    protected $conditions;
    protected $others;

    protected $insertColumns;
    protected $updateId;
    protected $updateColumns;
    protected $conn;

    protected function selectBase($isAsId, $Ts) {
        if($Ts != null){
            foreach($Ts as $T) {
                if($T == null)
                    return $this;

                if($this->selects != "")
                    $this->selects .= ", ";

                if($isAsId)
                    $this->selects .= $T->id. " AS ". $T->tableName. "_id";
                else
                    $this->selects .= $T;
            }
        }
        return $this;
    }

    private function joinBase($one, $many, $target) {
        $foreignKey = $one->tableName."_id";
        $this->joins .= "\nINNER JOIN $target->pluralName ";

        $this->joins .= "ON $many->pluralName.$foreignKey = $one->pluralName.id ";
    }

    protected function joinManyBase($one, $many) {
        $this->joinBase($one, $many, $many);
    }

    protected function joinOneBase($many, $one) {
        $this->joinBase($one, $many, $one);
    }

    protected function whereBase($left, $middle, $right) {
        if($this->conditions != "")
            $this->conditions .= " AND \n";
        else
            $this->conditions .= "\n";

        $this->conditions .= "$left $middle $right";
    }

    protected function getColumnName($column) {
        $lastIndex = strripos($column, ".");
        return substr($column, $lastIndex + 1); // +1 for the dot
    }


    protected function checkData($data){
        return is_string($data) ? "'$data'" : $data;
    }

    protected function createQuery($isDelete, $deleteTable = null) {
        $query = "";
        if($isDelete){
            if($deleteTable == null)
                $deleteTable = $this;
            $query .= "DELETE $deleteTable->pluralName FROM $this->pluralName ";
        } else {
            $this->selects ?? $this->selects = "*";
            $query .= "SELECT $this->selects FROM $this->pluralName ";
        }

        $query .= $this->joins;

        if($this->conditions != null){
            $query .= "WHERE $this->conditions";
        }

        $query .= $this->others.";";

        $this->printDebug($query);
        $this->selects = null;
        $this->joins = "";
        $this->conditions = "";
        $this->others = "";

        return $query;
    }

    protected function execute($query) {
        $this->printDebug($query);
        $this->conn->query($query);
}

    protected function printDebug($query) {
        if($this->debug){
            $input = $query;
            $input = str_replace("\n", "",$input);
            echo '<script>console.log("SQL: '.$input.'");</script>
            ';
        }
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