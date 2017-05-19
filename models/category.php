<?php
class Category extends DbSet{
    public $id;
    public $img;

    function __construct($conn, $name, $pluralName){
        parent::__construct($conn, $name, $pluralName);       
        
        $this->id = $pluralName.".id";
        $this->img = $pluralName.".image_path";
    }
}
?>