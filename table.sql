CREATE TABLE IF NOT EXISTS `HstsResult` (
  `HstsResultId` int(11) NOT NULL AUTO_INCREMENT,
  `Hostname` varchar(255) NOT NULL,
  `HasHeader` tinyint(1) NOT NULL,
  `MaxAge` bigint(20) DEFAULT NULL,
  `IncludeSubdomains` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`HstsResultId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;