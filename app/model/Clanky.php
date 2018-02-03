<?php

namespace Models;

use Nette,
    Nette\Utils\Strings;


/**
 * Správa článků.
 */
class Clanky extends \Nette\Object
{
    /** @var Nette\Database\Context */
    private $database;


    public function __construct(Nette\Database\Context $database)
    {
        $this->database = $database;
    }

    public function getAll() {
        return $this->database->fetchAll("SELECT *,IF(datum_vydani IS NULL, 1, 0) AS vydano FROM clanky WHERE smazano=0 ORDER BY vydano DESC,datum_vydani DESC;");
    }
    public function getPairs($id) {
	return $this->database->fetchPairs("SELECT id,titulek,IF(datum_vydani IS NULL, 1, 0) AS vydano from clanky
	where id!=?
	and id not in (select souvisejici_id from souvisejici_clanky where clanek_id=?)
	order by vydano DESC,datum_vydani desc;",$id,$id);
    }
    public function getSouvisejici($id,$all=false) {
        if ($all) {
            return $this->database->fetchAll("SELECT * FROM clanky
	    where id in (SELECT souvisejici_id FROM souvisejici_clanky where clanek_id=?);
	    order by datum_vydani desc",$id);
	} else {
            return $this->database->fetchAll("SELECT * FROM clanky
	    where datum_vydani is not null
	    and id in (SELECT souvisejici_id FROM souvisejici_clanky where clanek_id=?);
	    order by datum_vydani desc",$id);
	}
    }
    public function getClanky($limit=10,$offset=0,$url=null, $stitek=null,$aktuality = false,$search = null,$skupina = null) {
        $query1 = "SELECT c.*,cr.perex,cr.text,cr.datum as aktualizovano, k.nazev as kategorie,cr.id as crid,
                                                   ko.komentaru, u.alt, u.title
                                                   FROM clanky c
                                                   JOIN (select * from clanky_revize where id in (SELECT max(id) from clanky_revize group by clanek_id )) cr ON (c.id=cr.clanek_id)
                                                   JOIN kategorie k ON (c.kategorie_id = k.id)
                                                   LEFT JOIN upload u ON (c.obrazek_id = u.id)
                                                   LEFT JOIN (select clanek_id,count(*) as komentaru from komentare group by clanek_id) ko ON (ko.clanek_id=c.id)
                                                   WHERE datum_vydani IS NOT NULL AND datum_vydani<=STR_TO_DATE(?, '%Y-%m-%d %H:%i:%s') AND c.smazano=0
                                                    ";
        $query2 = " GROUP BY c.id
                                                   ORDER BY datum_vydani DESC
                                                   LIMIT ? OFFSET ?;";
        if ($aktuality) {
            return $this->database->fetchAll($query1."AND k.url='aktuality' AND k.url=?".$query2
                ,new \DateTime(),$url,$limit,$offset);
        } elseif (!empty($search)) {
            return $this->database->fetchAll($query1."AND MATCH(c.titulek,cr.perex,cr.text) AGAINST (? IN BOOLEAN MODE)".$query2
                   ,new \DateTime(),$search,$limit,$offset);
        } elseif (!empty($url)) {
            return $this->database->fetchAll($query1."AND k.url!='aktuality' AND k.url=?".$query2
                ,new \DateTime(),$url,$limit,$offset);
        } elseif (!empty($stitek)) {
            return $this->database->fetchAll($query1."AND c.id IN (SELECT clanek_id FROM clanky_stitky WHERE stitek=?)".$query2
                ,new \DateTime(),$stitek,$limit,$offset);
        } elseif (!empty($skupina)) {
            return $this->database->fetchAll($query1."AND c.skupina=?".$query2
                ,new \DateTime(),$skupina,$limit,$offset);
        } else {
            return $this->database->fetchAll($query1."AND k.url!='aktuality'".$query2
                                                   ,new \DateTime(),$limit,$offset);
        }

    }
    public function getByStareId($stare_id) {
        $id = $this->database->fetchField("SELECT id FROM clanky WHERE stare_id=?;",$stare_id);
      return $this->get($id);
    }

    public function get($id) {
        $clanek = $this->database->fetch("SELECT c.*, cr.perex, cr.text,cr.datum as aktualizovano, k.nazev as kategorie,cr.id as crid,
                                                      ko.komentaru, u.alt, u.title
                                        FROM clanky c
                                        JOIN (select * from clanky_revize where id in (SELECT max(id) from clanky_revize group by clanek_id )) cr ON (c.id=cr.clanek_id)
                                        JOIN kategorie k ON (c.kategorie_id = k.id)
                                        LEFT JOIN (select clanek_id,count(*) as komentaru from komentare group by clanek_id) ko ON (ko.clanek_id=c.id)
                                        LEFT JOIN upload u ON (c.obrazek_id = u.id)
                                        WHERE c.id=? and c.smazano=0 ORDER BY cr.id DESC LIMIT 1;",$id);

        $stitky = $this->database->fetchPairs("SELECT stitek FROM clanky_stitky WHERE clanek_id=?;",$id);
        if (!empty($clanek))
		$clanek->stitky = array_unique($stitky);

        return $clanek;

    }

    public function add($vals) {
        $arr = array("titulek" => $vals['titulek'],
                     "autor" => $vals["autor"],
                     "kategorie_id" => $vals['kategorie_id'],
                     "obrazek_id" => $vals['obrazek_id']
                     );
        if (!empty($vals['skupina'])) {
            $arr['skupina'] = $vals['skupina'];
        }
        if (empty($vals['id'])) {
            $this->database->query("INSERT INTO clanky ", $arr);
            $id = $this->database->getInsertId();
        } else {
            $this->database->query("UPDATE clanky SET ", $arr, " WHERE id=?",$vals['id']);
            $id = $vals['id'];
        }

        $arr = array("clanek_id" => $id, "perex" => $vals['perex'], "text" => $vals['text']);
        $this->database->query("INSERT INTO clanky_revize", $arr);

        $this->database->query("DELETE FROM clanky_stitky WHERE clanek_id=?;",$id);
        $stitky = explode("\n", $vals['stitky_text']);
        foreach ($stitky as $key => $stitek) {
            if (empty($stitek)) continue;
            $stitky[$key] = trim($stitek);
        }
        $stitky = array_unique($stitky);
        foreach ($stitky as $stitek) {
            $this->database->query("INSERT INTO clanky_stitky ", array("clanek_id" => $id, "stitek" => trim($stitek)));
        }
        return $id;
    }

    public function addHodnoceni($id,$hodnoceni,$ip) {
        if (abs($hodnoceni)!=1) return;
		$this->database->beginTransaction();
        $h = $this->getHodnoceniByIp($id, $ip);
        if (empty($h)) {
			$arr = array("clanek_id" => $id, "hodnoceni" => $hodnoceni, "ip" => $ip);
			$this->database->query("INSERT INTO hodnoceni ",$arr);
			$this->database->commit();
		} else {
			$this->database->rollBack();
		}
    }
    public function getHodnoceni($id) {
        return $this->database->fetch("SELECT sum(case when hodnoceni>0 then 1 else 0 end) as plus,
                                              sum(case when hodnoceni<0 then 1 else 0 end) as minus
                                        FROM hodnoceni
                                        WHERE clanek_id=?;",$id);
    }
    public function getHodnoceniByIp($id,$ip) {
        return $this->database->fetch("SELECT * FROM hodnoceni WHERE clanek_id=? and ip=?;",$id,$ip);
    }
    public function delete($id) {
        $this->database->query("UPDATE clanky SET smazano=1 WHERE id=?;",$id);
    }
    public function publish($vals) {
        $datum_vydani = \DateTime::createFromFormat("Y-m-d H:i", $vals['datum_vydani']." ".$vals['cas_vydani']);
        $this->database->query("UPDATE clanky SET datum_vydani=? WHERE id=?;",$datum_vydani,$vals['id']);
    }
    public function precteno($id) {
        $this->database->query("UPDATE clanky SET precteno=precteno+1 WHERE id=?;",$id);
    }
    public function getStitky() {
        return $this->database->query("select count(stitek) as cnt,stitek from clanky_stitky group by stitek order by stitek");
    }
    public function getSkupiny() {
        return $this->database->query("select count(skupina) as cnt,skupina from clanky group by skupina order by skupina");
    }
    public function addSouvisejici($vals) {
        $this->database->query("INSERT INTO souvisejici_clanky ",array("clanek_id" => $vals['clanek_id'], "souvisejici_id" => $vals['souvisejici_id']));
    }
    public function removeSouvisejici($clanek_id,$souvisejici_id) {
        $this->database->query("DELETE FROM souvisejici_clanky WHERE clanek_id=? and souvisejici_id=?;",$clanek_id,$souvisejici_id);
    }
}
