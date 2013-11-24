CREATE TABLE IF NOT EXISTS `HstsResult` (
  `HstsResultId` int(11) NOT NULL AUTO_INCREMENT,
  `AlexaRank` int(11) NOT NULL,
  `Hostname` varchar(255) NOT NULL,
  `Https` tinyint(1) NOT NULL,
  `HasHeader` tinyint(1) DEFAULT NULL,
  `MaxAge` bigint(20) DEFAULT NULL,
  `IncludeSubdomains` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`HstsResultId`),
  UNIQUE KEY `Hostname` (`Hostname`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;