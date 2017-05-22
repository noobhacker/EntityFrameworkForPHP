<?php
include 'models/context.php';
$db = new DbContext();

// these are for demonstration purpose
$cats = $db->cats
    ->select()
    ->select()
    ->select()
    ->select()
    ->select()
    ->select()
    ->select()
    ->select()
    ->select();
       

$db->close_conn();

