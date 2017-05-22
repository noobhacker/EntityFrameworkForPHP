<?php

include_once $_SERVER["DOCUMENT_ROOT"].'/lib/ef/dbset.php';
include_once $_SERVER["DOCUMENT_ROOT"].'/lib/ef/basecontext.php';

spl_autoload_register(function ($class_name) {
    include $class_name . '.php';
});

class DbContext extends BaseContext{
    // initialize with name you like
    public $cats;

    function __construct(){
        parent::__construct();  
        //initialize the object and provide names in db     
        $this->cats = new Category($this->conn, "category", "categories");
    }    

}

?>