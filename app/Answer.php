<?php
namespace App;

use imonroe\Ana;

class Answer
{
    public $id;
    public $poll_id;
    public $option_id;
    public $user_ip;
    public $user_agent_string;
    public $created;
    public $update;
    private $db;
    private $cookie_name;

    public function __construct(){
        $this->db = DB::getInstance();
    }

    public function save(){
        if ($this->validate_vote()){
            $user_ip = Ana::get_ip();
            $user_agent = $_SERVER['HTTP_USER_AGENT'];
            $query = $this->db->prepare('INSERT INTO answers (poll_id, option_id, user_ip, user_agent_string) VALUES (:poll_id, :option_id, :user_ip, :user_agent_string)');
            $result = $query->execute([':poll_id' => $this->poll_id, ':option_id' => $this->option_id,':user_ip' => $user_ip, ':user_agent_string' => $user_agent]);
            if ($result){
                $this->set_cookie();
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function delete(){
        $query = $this->db->prepare('DELETE FROM answers WHERE id=:id');
        return $query->execute([':id' => $this->id]);
    }

    private function set_cookie(){
        $cookie_name = "Ian_Broadnet_Poll_" . $this->poll_id;
        setcookie($cookie_name, "true", time()+(60*60*24));
    }

    public function validate_vote(){
        $cookie_name = "Ian_Broadnet_Poll_" . $this->poll_id;
        if (isset($_COOKIE[$cookie_name])) {
            // Cookie exists, already voted.
            return false;
        } else {
            // No cookie, allow vote.
            return true;
        }
    }

}