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

    protected function joinBase($one, $many) {
        $foreignKey = $one->tableName."_id";
        $this->joins .= "\nINNER JOIN $many->pluralName ";
        $this->joins .= "ON $many->pluralName.$foreignKey = $one->pluralName.id ";
    }

    protected function getColumnName($column) {
        $lastIndex = strripos($column, ".");
        return substr($column, $lastIndex + 1); // +1 for the dot
    }

    protected function createQuery($isDelete, $deleteTable=null) {
        $query = "";
        if($isDelete){
            $query .= "DELETE FROM $deleteTable ";
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
        $this->conn->query($query);
        $this->printDebug($query);
    }

    protected function printDebug($query) {
        if($this->debug){
            $input = $query;
            $input = str_replace("\n", "",$input);
            echo '<script>console.log("SQL: '.$input.'");</script>
            ';
        }
    }

}