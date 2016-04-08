
SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
--  Table structure for `ipgeo`
-- ----------------------------
DROP TABLE IF EXISTS `ipgeo`;
CREATE TABLE `ipgeo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip1` varchar(15) DEFAULT NULL,
  `ip2` varchar(15) DEFAULT NULL,
  `a` bigint(10) DEFAULT NULL,
  `b` bigint(10) DEFAULT NULL,
  `countryCode` varchar(4) DEFAULT NULL,
  `country` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `a` (`a`) USING BTREE,
  KEY `b` (`b`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

SET FOREIGN_KEY_CHECKS = 1;
