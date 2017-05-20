<?php
class DbSet{    
    // print sql statements    
    private $debug = true;

    public $tableName;
    public $pluralName;

    private $selects;
    private $joins;
    private $conditions;
    private $others;    

    private $insertColumns;
    private $insertDatas;

    private $updateId;
    private $updateColumns;
    private $updateDatas;

    private $conn;

    function __construct($conn, $tableName, $pluralName){
        $this->conn = $conn;
         
        $this->tableName = $tableName;
        $this->pluralName = $pluralName;
    }

    function select(...$columns){
        if($columns != null){
            foreach($columns as $column) {
                if($column == null)
                    return $this;

                if($this->selects != "")
                    $this->selects .= ", ";
                $this->selects .= $column;
            }  
        }
        return $this;
    }

    function selectAs($column, $aliasName){
        if($this->selects != "")
                $this->selects .= ", ";
            $this->selects .= "$column". " AS ". $aliasName;
    }

    function join(...$targets){
        foreach($targets as $target){
            $foreignKey = $this->tableName."_id";
            $this->joins .= "\nINNER JOIN $target->pluralName ";            
            $this->joins .= "ON $target->pluralName.$foreignKey = $this->pluralName.id ";    
        }

        return $this;
    }

    function where($conditions, $equals){
        if($this->conditions != "")
            $this->conditions .= " && \n";
        else 
            $this->conditions .= "\n";

        $this->conditions .= "$conditions = $equals";
        return $this;
    }

    function limit($number){
        $this->others .= "\nLIMIT $number ";

        return $this;
    }

    function orderBy($column){
        $this->others .= "\nORDER BY $column ";

        return $this;
    }

    function orderByDesc($column){
        $this->others .= "\nORDER BY $column DESC ";

        return $this;
    }

    private function getColumnName($column){
            $lastIndex = strripos($column, ".");
            return substr($column, $lastIndex + 1); // +1 for the dot
    }

    function insertColumns(...$columns){  
        foreach($columns as $column) {
            if($this->insertColumns != "")
                $this->insertColumns .= ", ";

            $column = $this->getColumnName($column);
            $this->insertColumns .= $column;          
        }

        return $this;
    }

    function insertDatas(...$datas){
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

    function insertSingle($column, $data){
        $column = $this->getColumnName($column);
        $query = "INSERT INTO $this->pluralName ($column) ".
            "VALUES ($data)";
        $this->execute($query);
        return $this->conn->insert_id;
    }

    function update($id){
        $this->updateId = $id;
    }

    function updateColumns(...$columns){        
        foreach($columns as $column) {
            if($this->updateColumns != "")
                $this->updateColumns .= ", ";

            $column = $this->getColumnName($column);
            $this->updateColumns .= $column;
        }
        return $this;
    }

    function updateDatas(...$datas){
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

    function updateSingle($id, $column, $data){
        $column = getColumnName($column);
        $query = "UPDATE $this->tableName ".
            "\nSET $column = $data ".
            "\nWHERE $this->tableName.id = $id";

        $this->query->execute($query);
    }

    function first($id){
        $result= $this->select()
        ->where($this->id, $id)
        ->toList();
        return $result[0];        
    }

    private function createSelectQuery(){
        $this->selects ?? $this->selects = "*";
        $query = "SELECT $this->selects FROM $this->pluralName ".
        $this->joins;

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

    function toList(){
        $query = $this->createSelectQuery();
        $rows = [];

        if($results = $this->conn->query($query)){
            while($row = mysqli_fetch_object($results)){
                $rows[] = $row;
            }

            $results->close();
        }  
       
        return $rows;
    }

    private function execute($query){
        $this->conn->query($query);
        $this->printDebug($query);
    }

    private function printDebug($query){
        if($this->debug){
            $input = $query;
            $input = str_replace("\n", "",$input);
            echo '<script>console.log("SQL: '.$input.'");</script>
            ';
        }
    }

}

?>