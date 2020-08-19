<?php

namespace Models;

use Nette,
    Nette\Utils\Strings;


/**
 * Category management.
 */
class Kategorie
{
    /** @var Nette\Database\Context */
    private $database;


    public function __construct(Nette\Database\Context $database)
    {
        $this->database = $database;
    }

    public function getMenu() {
        return $this->database->fetchAll("SELECT * FROM kategorie WHERE menu=1;");
    }
    public function getPairs() {
        return $this->database->fetchPairs("SELECT id, nazev FROM kategorie;");
    }
    public function getByUrl($url) {
        return $this->database->fetch("SELECT * FROM kategorie WHERE url=?;",$url);
    }
}
