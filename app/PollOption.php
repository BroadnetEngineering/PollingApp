<?php
namespace App;

class PollOption
{
 
    public $id;
    public $poll_id;
    public $option;
    public $created;
    public $updated;
    public $db;
    public $votes;
    
    public function __construct($poll_option_id = false)
    {
        $this->db = DB::getInstance();
        if ($poll_option_id) {
            $this->load($poll_option_id);
        }
    }
    
    public function load($poll_option_id)
    {
        $query = $this->db->prepare("SELECT * FROM poll_options where id=:id");
        $query->execute(['id' => (int)$poll_option_id]);
        $poll_option = $query->fetch();
        $this->id = $poll_option['id'];
        $this->poll_id = $poll_option['poll_id'];
        $this->option = $poll_option['option'];
        $this->created = $poll_option['created'];
        $this->updated = $poll_option['updated'];
        $this->load_votes();
    }

    private function load_votes()
    {
        $query = $this->db->prepare('SELECT count(*) from answers where option_id=:id');
        $query->execute([':id' => $this->id]);
        $this->votes = $query->fetchColumn();
    }

    public function all()
    {
        $output = array();
        $query = $this->db->query('SELECT id from poll_options');
        foreach ($query as $row) {
            $output[] = new PollOption($row['id']);
        }
        return $output;
    }

    public function save()
    {
        $query = $this->db->prepare('INSERT INTO poll_options (`poll_id`, `option`) VALUES (:poll_id, :option)');
        $query->execute([':poll_id' => $this->poll_id, ':option' => $this->option ]);
        $new_id = $this->db->lastInsertId();
        $this->load($new_id);
        return $this;
    }

    public function update()
    {
        $query = $this->db->prepare('UPDATE poll_options (`poll_id`, `option`) VALUES (:poll_id, :option) WHERE id=:id');
        return $query->execute([':poll_id' => $this->poll_id, ':option' => $this->option ]);
    }

    public function delete()
    {
        $query = $this->db->prepare('DELETE FROM poll_options WHERE id=:id');
        return $query->execute([':id' => $this->id]);
    }
}
