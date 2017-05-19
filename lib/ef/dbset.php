<?php
class DbSet{    
    
    public $tableName;
    public $pluralName;

    private $selects;
    private $joins;
    private $conditions;
    private $others;

    private $conn;

    function __construct($conn, $tableName, $pluralName){
        $this->conn = $conn;
         
        $this->tableName = $tableName;
        $this->pluralName = $pluralName;
    }

    function select(...$columns){
        $cols = count($columns);
        for($i = 0; $i < $cols; $i++){
            if($this->selects != "")
                $this->selects .= ", ";
            $this->selects .= $columns[$i];
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

    function insert($columns, $data){
        $query = "INSERT INTO $this->tableName ($columns) ".
        "\nVALUES ($data);";

        $this->execute($query);
        return $this->conn->insert_id;        
    }

    function update($id, $updates){
        $query = "UPDATE $this->tableName ".
        "\nSET $updates".
        "\nWHERE $this->tableName.id = $id;";

        $this->execute($query);
    }

    function delete($id){
        $query = "DELETE FROM $this->tableName ".            
        "\nWHERE $this->tableName.id = $id;";

        $this->execute($query);
    }

    function first($id){
        $result= $this->select()
        ->where("$this->tableName.id = $id")
        ->toList();
        return $result[0];        
    }

    private function createSelectQuery(){
        $this->selects == null ?? "*";
        $query = "SELECT $this->selects FROM $this->pluralName ".
        $this->joins."WHERE $this->conditions".
        $this->others.";";

        $this->printDebug($query);
        $this->selects = "";
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

    private $debug = true;
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