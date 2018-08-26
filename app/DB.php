<?php
namespace App;

class DB
{
    private static $instance; 
    private $pdo;
    

    private function __construct() 
    {
        $db_host = getenv('DB_HOST'); 
        $db_port = getenv('DB_PORT'); 
        $db_database = getenv('DB_DATABASE'); 
        $db_username = getenv('DB_USERNAME'); 
        $db_password = getenv('DB_PASSWORD'); 

        $dsn = "mysql:host=$db_host;dbname=$db_database;charset=utf8mb4";
        $opt = [
            \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            \PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        if(!$this->pdo) { 
	        try {
                $this->pdo = new \PDO($dsn, $db_username, $db_password, $opt);
			} catch (PDOException $e) { 
			   die("PDO CONNECTION ERROR: " . $e->getMessage() . "<br/>");
			}
    	}   	    	
    }

    public static function getInstance(){
        if (self::$instance == null)
        {
            self::$instance = new DB();
        } 
        return self::$instance;
    }

    public function getConnection(){
        return $this->pdo;
    }

    public function query($statment){
        $con = $this->getConnection();
        return $con->query($statment);
    }

    public function execute($statment){
        $con = $this->getConnection();
        return $con->execute($statment);
    }

    public function prepare($statment){
        $con = $this->getConnection();
        return $con->prepare($statment);
    }


    public static function initialize(){
        // set up the database tables for the first time.
    }

    


}