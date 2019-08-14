-- Adminer 4.2.5 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

DROP TABLE IF EXISTS `geo_regions`;
CREATE TABLE `geo_regions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `region_code` varchar(3) NOT NULL DEFAULT '',
  `area` varchar(255) NOT NULL,
  `parent_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `geo_regions_region_code_unique` (`region_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

INSERT INTO `geo_regions` (`id`, `region_code`, `area`, `parent_id`) VALUES
(1,	'001',	'Global',	NULL),
(2,	'002',	'Africa',	NULL),
(3,	'003',	'North America (includes 021, 029, 013)[5]',	19),
(5,	'005',	'South America',	19),
(9,	'009',	'Oceania',	NULL),
(11,	'011',	'Western Africa',	2),
(13,	'013',	'Central America',	19),
(14,	'014',	'Eastern Africa',	2),
(15,	'015',	'Northern Africa',	2),
(17,	'017',	'Middle Africa',	2),
(18,	'018',	'Southern Africa',	2),
(19,	'019',	'Americas',	NULL),
(21,	'021',	'Northern America',	19),
(29,	'029',	'Caribbean',	19),
(30,	'030',	'Eastern Asia',	142),
(34,	'034',	'Southern Asia',	142),
(35,	'035',	'South-Eastern Asia',	142),
(39,	'039',	'Southern Europe',	150),
(53,	'053',	'Australia and New Zealand',	9),
(54,	'054',	'Melanesia',	9),
(57,	'057',	'Micronesia',	9),
(61,	'061',	'Polynesia',	9),
(142,	'142',	'Asia',	NULL),
(143,	'143',	'Central Asia',	142),
(145,	'145',	'Western Asia',	142),
(150,	'150',	'Europe',	NULL),
(151,	'151',	'Eastern Europe',	150),
(154,	'154',	'Northern Europe',	150),
(155,	'155',	'Western Europe',	150),
(419,	'419',	'Latin America and the Caribbean',	19);

-- 2018-05-14 22:40:47
