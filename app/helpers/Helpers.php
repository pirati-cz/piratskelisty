<?php

/**
 * Description of Heplers
 *
 * @author stanley
 */
class Helpers {

    private static $czDays = array(0 => "neděle", "pondělí", "úterý", "středa", "čtvrtek", "pátek", "sobota");
    private static $czShortDays = array(0 => "Ne", "Po", "Út", "St", "Čt", "Pá", "So");
    private static $czMonths = array(1 => "leden", "únor", "březen", "duben", "květen", "červen", "červenec", "srpen", "září", "říjen", "listopad", "prosinec");
    private static $czMonths2 = array(1 => "ledna", "února", "března", "dubna", "května", "června", "července", "srpna", "září", "října", "listopadu", "prosince");

    public static function czDay($day) {
        return self::$czDays[$day];
    }

    public static function czShortDay($day) {
        return self::$czShortDays[$day];
    }

    public static function czNumber($value, $dec=0) {
        return number_format($value, $dec, ",", " ");
    }

    public static function czMonth($month) {
        return self::$czMonths[$month];
    }

    public static function czMoney($value, $dec=2) {
        return self::czNumber((float) $value, $dec) . " Kč";
    }

    public static function czItem($value) {
        if (abs($value) == 1)
            return $value . " položka";
        if (abs($value) < 5)
            return $value . " položky";
        return $value . " položek";
    }

    public static function czBoolean($value) {
        if ($value) {
            return "ano";
        } else {
            return "ne";
        }
    }

    public static function nbsp($value) {
        return str_replace(" ", "&nbsp;", $value);
    }
    public static function czDate($date) {
        if (!$date instanceof \DateTime) {
            $date = new \DateTime($date);
        }
        return $date->format("j. n. Y");
    }
    public static function czTextDate($date) {
        if (!$date instanceof \DateTime) {
            $date = new \DateTime($date);
        }

        return $date->format("j. ") . self::$czMonths2[$date->format("n")] . $date->format(" Y");
    }
    public static function czTime($time) {
        if (!$time instanceof \DateTime) {
            $time = new \DateTime($time);
        }
        return $time->format("H.i");
    }
    public static function czDateTime($date) {
        return self::czDate($date)." ".self::czTime($date);
    }
    public static function ean($barcode) {
        $sum = 0;
        for ($i = (strlen($barcode) - 1); $i >= 0; $i--) {
            $sum += ( ($i % 2) * 2 + 1 ) * substr($barcode, $i, 1);
        }
        return $barcode . (10 - ($sum % 10));
    }
    public static function stripTag($str, $tag = "p") {
          return preg_replace("/<\/?" . $tag . "(.|\s)*?>/","",$str);
    }
    public static function stripTagsArray($str, $tags = array("p","a")) {
        foreach ($tags as $tag) {
            $str = preg_replace("/<\/?" . $tag . "(.|\s)*?>/","",$str);
        }
        return $str;
    }
    public static function komentare($i) {
        if ($i==1) return "1&nbsp;komentář";
        if ($i<5) return $i."&nbsp;komentáře";
        return $i."&nbsp;komentářů";
    }
}
