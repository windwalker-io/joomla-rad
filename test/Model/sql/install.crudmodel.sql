DROP TABLE IF EXISTS `#__test_table`;

CREATE TABLE `#__test_table` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `foo` VARCHAR(32) DEFAULT NULL,
  `type` VARCHAR(16) DEFAULT NULL,
  `params` TEXT DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

INSERT INTO `#__test_table` (`id`, `foo`, `type`, `params`) VALUES
(1, 'bad', 'fruit', '{"name":"apple"}'),
(2, 'bag', 'animal', '{"name":"dog"}'),
(3, 'bah', 'animal', '{"name":"cat"}'),
(4, 'bak', 'fruit', '{"name":"banana"}'),
(5, 'bal', 'fruit', '{"name":"mongo"}'),
(6, 'bam', 'flower', '{"name":"sakura"}'),
(7, 'ban', 'flower', '{"name":"rose"}'),
(8, 'bar', 'animal', '{"name":"pig"}'),
(9, 'bat', 'fruit', '{"name":"strawberry"}'),
(10, 'baw', 'flower', '{"name":"lotus"}'),
(11, 'bax', 'animal', '{"name":"chicken"}'),
(12, 'bay', 'fruit', '{"name":"orange"}'),
(13, 'baz', 'fruit', '{"name":"blueberry"}');
