<?php

class Database {
    public $name; 
    public $connexion;

    function __construct($name) {
        $this->name = $name;
    }

    function server_connecte(){
        $this->connexion = new PDO("mysql:host=localhost","root",""); 
        if(!$this->connexion){
            echo "error to connect to server ! <br>"; 
        } else {
            echo " server bien connecté <br>  "; 
        }
    }

    function create_database(){
        $request = "CREATE DATABASE IF NOT EXISTS `" . $this->name . "` "; 
        $x = $this->connexion->prepare($request); 
        $e = $x->execute(); 
        if(!$e){
            echo "database not created error !!!! <br>"; 
        } else {
            echo "database bien cree !!! <br>"; 
        }
    }

    function select_database(){
        $this->connexion = new PDO("mysql:host=localhost;dbname=" . $this->name, "root", "");
        if(!$this->connexion){
            echo "database not selected ! <br>"; 
        } else {
            echo "database selected good !! <br> "; 
        }
    }
}

$db = new Database("TP4");
$db->server_connecte(); 
$db->create_database(); 
$db->select_database();
?>