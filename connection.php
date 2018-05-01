<?php
//returns db connection
function get_database(){
    
    
    $config = parse_ini_file('./config/config.ini'); 
    
    $mysqli = new mysqli($config['servername'], $config['username'], $config['password'], $config['dbname']);
    
    // Check connection
    if ($mysqli->connect_error) {
        return ("Connection failed: " . $mysqli->connect_error);
    }else{
        //return "Connected successfully" . "<br>";
        return $mysqli;
    }
}




?>