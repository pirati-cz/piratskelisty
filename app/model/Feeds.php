<?php

namespace Models;

use Nette,
    Nette\Utils\Strings;


/**
 * Správa feeds.
 */
class Feeds
{
    /** @var Nette\Database\Context */
    private $database;


    public function __construct(Nette\Database\Context $database)
    {
        $this->database = $database;
    }

    public function getAll() {
        return $this->database->fetchAll("select * from feeds order by name;");
    }

    public function get($id) {
        return $this->database->fetch("select * from feeds where id=?;",$id);
    }

    public function getAllByFeedGroupId($id) {
        return $this->database->fetchAll("select * from feeds where feed_group_id=?;",$id);
    }

    public function save($vals) {
        $arr = ["name" => $vals['name'],
                "url" => $vals['url'],
                "feed_group_id" => $vals['feed_group_id']];
        if (empty($vals['id'])) {
            $this->database->query("INSERT INTO feeds", $arr);
            $id = $this->database->getInsertId();
        } else {
            $this->database->query("UPDATE feeds SET", $arr, "WHERE id=?",$vals['id']);
            $id = $vals['id'];
        }
        return $this->get($id);
    }

    public function remove($id) {
        $this->database->query("DELETE FROM feeds WHERE id=?;",$id);
    }
}
