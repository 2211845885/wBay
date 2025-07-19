<?php
//this file makes it easier and safer to connect to the db to anyone that wants to write backend code
$config = include_once("config.php");
class dbhandle{

    //attributes to deal with connection, statements and results;
    private mysqli $connect;
    private mysqli_stmt $dbstmt;
    private mysqli_result $queryresult;

    //constructer to create connection for $connect var
    public function __construct(){
        global $config;
        $this->connect = new mysqli($config["dbserver"], $config["dbuser"], $config["dbpass"], $config["dbname"]);

        if($this->connect->connect_error){
            die("connection error check creds");
        }
    }

    //simple function for a normal query with no inputs only takes an sql statement
    public function query($sql){
        $this->queryresult = $this->connect->query($sql);
        return $this->queryresult->fetch_all(MYSQLI_ASSOC);
    }

    //function that takes an sql statment undefiend amount of inputs and their data types and returns 0 or more rows
    //example prepquery($sql, "sis", $string, $integer, $string)
    // the line above would send an sql statement with 3 variables of 2 different type sis meaning string integer string
    //the data types has to be in order of the inputs
    public function prepQuery($sql, $datatype, ...$values){
        $this->dbstmt = $this->connect->prepare($sql);
        $this->dbstmt->bind_param($datatype, ...$values);
        $this->dbstmt->execute();
        $this->queryresult = $this->dbstmt->get_result();
        return $this->queryresult->fetch_all(MYSQLI_ASSOC);
    }

    //same as the function above but doesn't return anything
    public function prepUpdate($sql, $datatype, ...$values){
        $this->dbstmt = $this->connect->prepare($sql);
        $this->dbstmt->bind_param($datatype, ...$values);
        $this->dbstmt->execute();
    }

    //closes the connection
    public function close(){
        $this->connect->close();
    }
}
?>