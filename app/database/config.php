<?php

class config {
    private $hostname = "localhost";
    private $username = "root";
    private $password = "";
    private $databasename = "ecommerce";
    private $con;
    public function __construct() {
        $this->con = new mysqli($this->hostname,$this->username,$this->password,$this->databasename);
        // if ($this->con->connect_error) {
        //     die("Connection failed: " . $this->con->connect_error);
        //   }
        //   echo "Connected successfully";
    }
    // insert - update -delete
    public function runDML(string $query) : bool
    {
        $result = $this->con->query($query);
        if($result){
            return true;
        }
        return false;
    }
    // selects
    public function runDQL(string $query) 
    {
        $result = $this->con->query($query);
        if($result->num_rows > 0){
            return $result;
        }
        return [];
    }
}
// $x = new config;