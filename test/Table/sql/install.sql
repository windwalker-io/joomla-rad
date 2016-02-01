DROP TABLE IF EXISTS `#__test_table`, `#__test_table2`;

CREATE TABLE `#__test_table` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `foo` VARCHAR(32) DEFAULT NULL,
  `params` TEXT DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

CREATE TABLE `#__test_table2` (
  `pk` INT(11) NOT NULL AUTO_INCREMENT,
  `bar` VARCHAR(32) DEFAULT NULL,
  `params` TEXT DEFAULT NULL,
  PRIMARY KEY (`pk`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

INSERT INTO `#__test_table` (`id`, `foo`, `params`) VALUES
(1, 'bar', '{"foo":"bar"}'),
(2, 'baz', '{"foz":"bra"}');

INSERT INTO `#__test_table2` (`pk`, `bar`, `params`) VALUES
(1, 'foo', '{"bar":"foo"}'),
(2, 'foz', '{"bra":"foz"}'),
(3, 'sms', '{"sms":"taiwan"}');
