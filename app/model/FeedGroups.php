<?php

namespace Models;

use Nette,
    Nette\Utils\Strings;


/**
 * Správa feed groups.
 */
class FeedGroups extends \Nette\Object
{
    /** @var Nette\Database\Context */
    private $database;


    public function __construct(Nette\Database\Context $database)
    {
        $this->database = $database;
    }

    public function getAll() {
        return $this->database->fetchAll("select * from feed_groups order by name;");
    }

    public function get($id) {
        return $this->database->fetch("select * from feed_groups where id=?;",$id);
    }

    public function getPairs() {
        return $this->database->fetchPairs("SELECT * from feed_groups order by name;");
    }

    public function getByUrl($url) {
      return $this->database->fetch("select * from feed_groups where url=?;",$url);
    }

    public function save($vals) {
        $arr = ["name" => $vals['name'],
                "url" => $vals['url']];
        if (empty($vals['id'])) {
            $this->database->query("INSERT INTO feed_groups", $arr);
            $id = $this->database->getInsertId();
        } else {
            $this->database->query("UPDATE feed_groups SET", $arr, "WHERE id=?",$vals['id']);
            $id = $vals['id'];
        }
        return $this->get($id);
    }
}
