<?php

namespace Models;

use Nette,
    Nette\Utils\Strings;

/**
 * Users management.
 */
class Uzivatele implements \IUzivatele
{
    const
        TABLE_NAME = 'uzivatele',
        TABLE_NAME_ROLES = 'uzivatele_role',
        COLUMN_ID = 'id',
        COLUMN_IDENTITY = 'identita',
        COLUMN_NAME = 'jmeno',
        COLUMN_EMAIL = 'email',
        COLUMN_ROLE = 'role',
        COLUMN_KEYCLOAK_ID = 'keycloak_id';


    /** @var Nette\Database\Context */
    private $database;


    public function __construct(Nette\Database\Context $database)
    {
        $this->database = $database;
    }

    public function addKeycloak($keycloakId, $name, $email) {
        $uzivatel = $this->database->fetch("SELECT * FROM ".self::TABLE_NAME." WHERE ".self::COLUMN_KEYCLOAK_ID."=?;",$keycloakId);

        $arr[self::COLUMN_NAME] = $name;
        $arr[self::COLUMN_EMAIL] = $email;

        if (empty($uzivatel)) {
            $arr[self::COLUMN_KEYCLOAK_ID] = $keycloakId;
            $this->database->query("INSERT INTO ".self::TABLE_NAME, $arr);
        } else {
            $this->database->query("UPDATE ".self::TABLE_NAME." SET ",$arr, " WHERE ".self::COLUMN_KEYCLOAK_ID."=?;",$keycloakId);
        }
        $uzivatel = $this->database->fetch("SELECT * FROM ".self::TABLE_NAME." WHERE ".self::COLUMN_KEYCLOAK_ID."=?;",$keycloakId);
        return $uzivatel;
    }

    public function get($identity) {
        if (\is_numeric($identity)) {
            return $this->database->fetch("SELECT * FROM ".self::TABLE_NAME." WHERE ".self::COLUMN_ID."=?;",$identity);
        } else {
            return $this->database->fetch("SELECT * FROM ".self::TABLE_NAME." WHERE ".self::COLUMN_IDENTITY."=?;",$identity);
        }
    }

    public function getRole($id) {

        return $this->database->fetchPairs("SELECT id,role
                    FROM role r
                    JOIN uzivatele_role ur ON (r.id = ur.role_id)
                     WHERE ur.uzivatel_id=?;",$id);
    }

    public function getRolePairs()
    {
        return $this->database->fetchPairs("SELECT id, nazev FROM role;");
    }

    public function getAll()
    {
        return $this->database->fetchAll("SELECT * FROM uzivatele;");
    }

    public function setRole($id, $roles) {
        $this->database->query("DELETE FROM uzivatele_role WHERE uzivatel_id=?;",$id);
        foreach ($roles as $k => $v) {
            if ($v==1) {
                $this->database->query("INSERT INTO uzivatele_role ",array("role_id" => $k, "uzivatel_id" => $id));
            }
        }
    }
}
