-- Adminer 3.7.1 MySQL dump

SET NAMES utf8;
SET foreign_key_checks = 0;
SET time_zone = '+01:00';
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `anketa`;
CREATE TABLE `anketa` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `question` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


DROP TABLE IF EXISTS `anketa_moznosti`;
CREATE TABLE `anketa_moznosti` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `survey_id` int(11) NOT NULL,
  `answer` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `value` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


DROP TABLE IF EXISTS `anketa_odpovedi`;
CREATE TABLE `anketa_odpovedi` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `option_id` int(11) NOT NULL,
  `ip` varchar(15) CHARACTER SET utf8 COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_danish_ci;


DROP TABLE IF EXISTS `clanky`;
CREATE TABLE `clanky` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `stare_id` int(11) DEFAULT NULL,
  `autor` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `redaktor_id` int(11) NOT NULL,
  `autor_id` int(11) NOT NULL,
  `titulek` text COLLATE utf8_czech_ci NOT NULL,
  `kategorie_id` int(11) NOT NULL,
  `datum_vydani` datetime DEFAULT NULL,
  `obrazek_id` int(11) DEFAULT NULL,
  `smazano` int(1) NOT NULL,
  `precteno` int(11) NOT NULL,
  `skupina` varchar(20) COLLATE utf8_czech_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `stare_id` (`stare_id`),
  FULLTEXT KEY `titulek` (`titulek`,`autor`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


DROP TABLE IF EXISTS `clanky_revize`;
CREATE TABLE `clanky_revize` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `clanek_id` int(10) unsigned DEFAULT NULL,
  `autor_id` int(10) unsigned NOT NULL,
  `perex` text COLLATE utf8_czech_ci NOT NULL,
  `text` text COLLATE utf8_czech_ci NOT NULL,
  `datum` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `clanek_id` (`clanek_id`),
  FULLTEXT KEY `perex` (`perex`,`text`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


DROP TABLE IF EXISTS `hodnoceni`;
CREATE TABLE `hodnoceni` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `clanek_id` int(10) unsigned NOT NULL,
  `hodnoceni` int(11) NOT NULL,
  `ip` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `clanek_id_ip` (`clanek_id`,`ip`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


DROP TABLE IF EXISTS `kategorie`;
CREATE TABLE `kategorie` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nazev` varchar(255) CHARACTER SET utf8 COLLATE utf8_czech_ci NOT NULL,
  `url` varchar(255) CHARACTER SET utf8 COLLATE utf8_czech_ci NOT NULL,
  `barva` int(11) NOT NULL,
  `menu` int(11) NOT NULL,
  `datum` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `pridal_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

INSERT INTO `kategorie` (`id`, `nazev`, `url`, `barva`, `menu`, `datum`, `pridal_id`) VALUES
(1,	'Internet',	'internet',	1,	1,	'2014-04-12 11:12:48',	0),
(2,	'Kauzy',	'kauzy',	2,	1,	'2014-04-12 11:12:48',	0),
(3,	'Kopírování',	'kopirovani',	3,	1,	'2014-04-12 11:12:56',	0),
(4,	'Strana',	'strana',	4,	1,	'2014-04-12 11:12:48',	0),
(5,	'Zahraničí',	'zahranici',	5,	1,	'2014-04-12 11:12:48',	0),
(6,	'Názory',	'nazory',	6,	1,	'2014-04-12 11:12:48',	0),
(7,	'Archiv',	'archiv',	7,	1,	'2014-04-12 11:12:48',	0),
(8,	'Aktuality',	'aktuality',	0,	0,	'2014-04-12 13:22:37',	0);

DROP TABLE IF EXISTS `komentare`;
CREATE TABLE `komentare` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `clanek_id` int(10) unsigned NOT NULL,
  `titulek` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `text` text COLLATE utf8_czech_ci NOT NULL,
  `datum` datetime NOT NULL,
  `jmeno` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `nadrazeny_id` int(11) NOT NULL,
  `smazano` int(11) NOT NULL,
  `hodnoceni` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `clanek_id` (`clanek_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


DROP TABLE IF EXISTS `role`;
CREATE TABLE `role` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `role` enum('spravce','redaktor','autor') COLLATE utf8_czech_ci NOT NULL,
  `nazev` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

INSERT INTO `role` (`id`, `role`, `nazev`) VALUES
(1,	'spravce',	'Správce'),
(2,	'redaktor',	'Redaktor'),
(3,	'autor',	'Autor');

DROP TABLE IF EXISTS `souvisejici_clanky`;
CREATE TABLE `souvisejici_clanky` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `clanek_id` int(10) unsigned NOT NULL,
  `souvisejici_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `stitky`;
CREATE TABLE `stitky` (
  `stitek` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `clanek_id` int(10) unsigned NOT NULL,
  UNIQUE KEY `clanek_id_stitek` (`clanek_id`,`stitek`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


DROP TABLE IF EXISTS `upload`;
CREATE TABLE `upload` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `alt` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `title` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `extension` varchar(10) COLLATE utf8_czech_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


DROP TABLE IF EXISTS `uzivatele`;
CREATE TABLE `uzivatele` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `identita` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `jmeno` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


DROP TABLE IF EXISTS `uzivatele_role`;
CREATE TABLE `uzivatele_role` (
  `uzivatel_id` int(10) unsigned NOT NULL,
  `role_id` int(10) unsigned NOT NULL,
  UNIQUE KEY `uzivatel_id_role` (`uzivatel_id`,`role_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


-- 2016-02-09 08:15:45
