CREATE TABLE `actionlog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `route` varchar(255) DEFAULT NULL,
  `operator` varchar(255) DEFAULT NULL,
  `actiontime` bigint(20) DEFAULT NULL,
  `desc` text,
  `params` text,
  `uri` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;