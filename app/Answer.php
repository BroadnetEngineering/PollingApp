<?php
namespace App;
use imonroe\ana\Ana;

class Answer
{
    public $id;
    public $poll_id;
    public $option_id;
    public $user_ip;
    public $user_agent_string;
    public $created;
    public $updated;
    private $db;
    private $cookie_name;

    public function __construct($answer_id = false){
        $this->db = DB::getInstance();
        if ($answer_id){
            $this->load($answer_id);
        }
    }

    public function load($answer_id){
        $query = $this->db->prepare("SELECT * FROM answers where id=:id");
        $query->execute(['id' => (int)$answer_id]);
        $answer = $query->fetch();
        $this->id = $answer['id'];
        $this->poll_id = $answer['poll_id'];
        $this->option_id = $answer['option_id'];
        $this->user_ip = $answer['user_ip'];
        $this->user_agent_string = $answer['user_agent_string'];
        $this->created = $answer['created'];
        $this->updated = $answer['updated'];
        $this->cookie_name = "Ian_Broadnet_Poll_" . $answer['poll_id'];
    }

    public function save(){
        $user_ip = Ana::get_ip();
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        $query = $this->db->prepare('INSERT INTO answers (poll_id, option_id, user_ip, user_agent_string) VALUES (:poll_id, :option_id, :user_ip, :user_agent_string)');
        $result = $query->execute([':poll_id' => $this->poll_id, ':option_id' => $this->option_id,':user_ip' => $user_ip, ':user_agent_string' => $user_agent]);

        // update this object with it's new ID and so forth.
        $new_id = $this->db->lastInsertId();
        $this->load($new_id);
        
        if ($result){
            $this->set_cookie();
            return true;
        } else {
            return false;
        }
    }

    public function delete(){
        $query = $this->db->prepare('DELETE FROM answers WHERE id=:id');
        return $query->execute([':id' => $this->id]);
    }

    private function set_cookie(){
        setcookie($this->cookie_name, (string)$this->id, time()+86400 );
    }

}