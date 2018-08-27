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

    public function lastInsertId(){
        $con = $this->getConnection();
        return $con->lastInsertId();
    }

    public function initialize(){
        // set up the database tables for the first time.
        $create_answers = "CREATE TABLE IF NOT EXISTS `answers` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `poll_id` int(11) NOT NULL DEFAULT '0',
            `option_id` int(11) NOT NULL DEFAULT '0',
            `user_ip` varchar(50) NOT NULL DEFAULT '0',
            `user_agent_string` varchar(255) NOT NULL DEFAULT '0',
            `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `updated` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`)
          ) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;";

        $answers_data = "INSERT INTO `answers` (`id`, `poll_id`, `option_id`, `user_ip`, `user_agent_string`, `created`, `updated`) VALUES
        (3, 1, 3, '192.168.10.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/68.0.3440.106 Safari/537.36', '2018-08-26 23:41:48', '2018-08-26 23:41:48'),
        (6, 2, 4, '192.168.10.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/68.0.3440.106 Safari/537.36', '2018-08-26 23:42:50', '2018-08-26 23:42:50');";

        $create_polls = "CREATE TABLE IF NOT EXISTS `polls` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `poll_question` text,
            `created` datetime DEFAULT CURRENT_TIMESTAMP,
            `updated` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`)
          ) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;";
        
        $polls_data = "INSERT INTO `polls` (`id`, `poll_question`, `created`, `updated`) VALUES
        (1, 'Does this demo seem to work?', '2018-08-26 23:39:01', '2018-08-26 23:39:01'),
        (2, 'Would you answer my second poll?', '2018-08-26 23:42:19', '2018-08-26 23:42:19');";

        $create_poll_options = "CREATE TABLE IF NOT EXISTS `poll_options` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `poll_id` int(11) NOT NULL DEFAULT '0',
            `option` text NOT NULL,
            `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `updated` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`)
          ) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;";

        $poll_options_data = "INSERT INTO `poll_options` (`id`, `poll_id`, `option`, `created`, `updated`) VALUES
        (1, 1, 'It sure does!', '2018-08-26 23:39:11', '2018-08-26 23:39:11'),
        (2, 1, 'No, something is wrong.', '2018-08-26 23:39:22', '2018-08-26 23:39:22'),
        (3, 1, 'So far, so good.', '2018-08-26 23:39:30', '2018-08-26 23:39:30'),
        (4, 2, 'Yes.', '2018-08-26 23:42:26', '2018-08-26 23:42:26'),
        (5, 2, 'No.', '2018-08-26 23:42:30', '2018-08-26 23:42:30');";

        // Run the queries.
        $con = $this->getConnection();
        $result = $con->query($create_answers);
        $result = $con->query($answers_data);
        $result = $con->query($create_polls);
        $result = $con->query($polls_data);
        $result = $con->query($create_poll_options);
        $result = $con->query($poll_options_data);

    }

    public function db_exists() {
        $con = $this->getConnection();
        try {
            $result = $con->query("SELECT 1 FROM polls LIMIT 1");
        } catch (\Exception $e) {
            // We got an exception == table not found
            return FALSE;
        }
        return true;
    }
}