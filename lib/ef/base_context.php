<?php

class BaseContext{
    
    protected $conn;

    function __construct(){            
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "test";
        
        $this->conn = new mysqli($servername, $username, $password, $dbname);
        $this->conn->set_charset("utf8");
    }
    
    public function close_conn(){
        $this->conn->close();
    }

}
?>