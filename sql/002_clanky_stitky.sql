ALTER TABLE `stitky` RENAME TO `clanky_stitky`;

CREATE TABLE `stitky` (
  `id` int unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `stitek` varchar(255) NOT NULL
) COLLATE 'utf8_czech_ci';

insert into stitky (stitek) select distinct stitek from clanky_stitky;

ALTER TABLE `stitky` ADD UNIQUE `stitek` (`stitek`);
