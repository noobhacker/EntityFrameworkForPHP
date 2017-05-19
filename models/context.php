<?php

include_once $_SERVER["DOCUMENT_ROOT"].'/lib/ef/dbset.php';
include_once $_SERVER["DOCUMENT_ROOT"].'/lib/ef/base_context.php';

spl_autoload_register(function ($class_name) {
    include $class_name . '.php';
});

class DbContext extends BaseContext{
    public $cats;

    function __construct(){
        parent::__construct();        
        $this->cats = new Category($this->conn, "category", "categories");
    }    

}

?>