CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `loginName` varchar(45) COLLATE utf8_bin DEFAULT NULL,
  `userName` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `firstName` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `lastName` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `password` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `active` int(1) DEFAULT '1',
  `type` varchar(16) COLLATE utf8_bin DEFAULT NULL,
  `email` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `createdOn` datetime DEFAULT NULL,
  `createdBy` varchar(45) COLLATE utf8_bin DEFAULT NULL,
  `changedOn` datetime DEFAULT NULL,
  `changedBy` varchar(45) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `loginName_UNIQUE` (`loginName`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
INSERT INTO `user` VALUES (1,'admin','Admin','Admin','user','$2y$10$gZ7Uh847Wg.RpUtHcNcl5Oqc8tG63.aGMFgUyI1koa6.Vy2qnSi.S',1,'local','chenwen9@163.com',NULL,NULL,NULL,NULL);