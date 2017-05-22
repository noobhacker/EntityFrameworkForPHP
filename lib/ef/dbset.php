<?php
include 'DbCore.php';
class DbSet extends DbCore{

    function __construct($conn, $tableName, $pluralName) {
        $this->conn = $conn;
        $this->tableName = $tableName;
        $this->pluralName = $pluralName;
    }

    function take($number) {
        $this->others .= "\nLIMIT $number ";
        return $this;
    }

    function orderBy($column) {
        $this->others .= "\nORDER BY $column ";
        return $this;
    }

    function orderByDesc($column) {
        $this->others .= "\nORDER BY $column DESC ";
        return $this;
    }

    function first($id) {
        $result= $this->select()
            ->where($this->id, $id)
            ->toList();
        return $result[0];        
    }

    function forEach($func){
        $results = $this->toList();
        if($results != null) {
            foreach($results as $result) {
                $func($result);
            }
        }
    }

}
