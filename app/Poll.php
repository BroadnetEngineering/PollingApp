<?php
namespace App;

class Poll
{
    public $id;
    public $poll_question;
    public $poll_options;
    public $created;
    public $updated;
    private $db;
    private $allow_multiple_votes;
    public $cookie_name;
    public $user_has_voted;

    public function __construct($poll_id = false)
    {
        $this->allow_multiple_votes = true;
        $this->db = DB::getInstance();
        $this->user_has_voted = false;
        if ($poll_id) {
            $this->load($poll_id);
        }
    }

    public function load($poll_id)
    {
        $query = $this->db->prepare("SELECT * FROM polls where id=:id");
        $query->execute(['id' => (int)$poll_id]);
        $poll = $query->fetch();
        $this->id = $poll['id'];
        $this->poll_question = $poll['poll_question'];
        $this->created = $poll['created'];
        $this->updated = $poll['updated'];
        $this->cookie_name = "Ian_Broadnet_Poll_" . $poll['id'];
        $this->load_options();
        $this->has_user_voted();
    }

    private function load_options()
    {
        $query = $this->db->prepare('SELECT * from poll_options WHERE poll_id=:id');
        $query->execute([':id' => $this->id]);
        $options = $query->fetchAll();
        if (!empty($options)) {
            $this->poll_options = [];
            foreach ($options as $option) {
                $this->poll_options[] = new PollOption($option['id']);
            }
        }
    }

    public function has_user_voted()
    {
        if (isset($_COOKIE[$this->cookie_name])) {
            $this->user_has_voted = true;
        } else {
            $this->user_has_voted = false;
        }
    }

    public function all()
    {
        $output = array();
        $query = $this->db->query('SELECT id from polls');
        foreach ($query as $row) {
            $output[] = new Poll($row['id']);
        }
        return $output;
    }

    public function save()
    {
        $query = $this->db->prepare('INSERT INTO polls (poll_question) VALUES (:poll_question)');
        return $query->execute([':poll_question' => $this->poll_question]);
    }

    public function update()
    {
        $query = $this->db->prepare('UPDATE polls SET `poll_question`=:poll_question WHERE id=:id');
        return $query->execute([':poll_question' => $this->poll_question, ':id' => $this->id]);
    }

    public function delete()
    {
        $query = $this->db->prepare('DELETE FROM polls WHERE id=:id');
        return $query->execute([':id' => $this->id]);
    }

    public function chart_results()
    {
        // Google Chart library wants some weird JSON-esque arrays
        $output = "[";
        foreach ($this->poll_options as $option) {
            $output .= '["' . $option->option . '", ' . $option->votes . '],';
        }
        // trim the trailing comma
        $output = rtrim($output, ",");
        $output .= "]";
        return $output;
    }
}
