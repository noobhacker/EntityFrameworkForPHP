<?php
include 'models/context.php';
$db = new DbContext();

// these are for demonstration purpose
$results = $db->cats
    ->select($db->cats->id)
    ->join($db->cat_names)
    ->where($db->cats->id, 1)
    ->toList();

$results = $db->conts
    ->select($db->cont_names->name)
    ->join($db->cont_names, $db->parags)
    ->where($db->conts->id, 1)
    ->where($db->parags->lang_id, $db->cont_names->lang_id)
    ->toList();
    
print_r($results);

$db->close_conn();
?>
