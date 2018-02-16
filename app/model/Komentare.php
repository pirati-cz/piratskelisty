<?php

namespace Models;

use Nette,
    Nette\Utils\Strings;


/**
 * Category management.
 */
class Komentare extends \Nette\Object
{
    /** @var Nette\Database\Context */
    private $database;

    public function __construct(Nette\Database\Context $database)
    {
        $this->database = $database;
    }

    public function getForClanek($id) {
        return $this->database->fetchAll("SELECT * FROM komentare WHERE clanek_id=? ORDER BY id desc ;",$id);
    }

    public function add($vals) {
        $arr = array("jmeno" => $vals['jmeno'], "text" => $vals['text'], "clanek_id" => $vals['clanek_id'], "datum" => new \Nette\DateTime());
        $this->database->query("INSERT INTO komentare ",$arr);
    }
}
