CREATE TABLE IF NOT EXISTS `perm_group` (
  `ID` int(11) NOT NULL auto_increment,
  `Name` varchar(255) NOT NULL,
  `Title` varchar(1024) NOT NULL,
  `Type` varchar(255) NOT NULL,
  PRIMARY KEY  (`ID`),
  UNIQUE KEY `Name_UNIQUE` (`Name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `perm_usergroup` (
  `GroupID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  PRIMARY KEY  (`GroupID`,`UserID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

INSERT INTO `perm_group` (`ID`, `Name`, `Title`, `Type`)
VALUES (1, 'admin', 'admin group', 'local');

INSERT INTO `perm_usergroup` (`GroupID`, `UserID`)
VALUES (1, 1);
