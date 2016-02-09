
CREATE TABLE IF NOT EXISTS `#__{{extension.name.lower}}_{{controller.list.name.lower}}` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary Key',
  `title` varchar(255) NOT NULL COMMENT 'Record title',
  `alias` varchar(255) NOT NULL COMMENT 'URL Alias',
  `introtext` mediumtext NOT NULL COMMENT 'Intro',
  `fulltext` mediumtext NOT NULL COMMENT 'Full content',
  `images` text NOT NULL COMMENT 'Images',
  `created` datetime NOT NULL COMMENT 'Created time',
  `created_by` int(11) NOT NULL COMMENT 'Author',
  `modified` datetime NOT NULL COMMENT 'Modified time',
  `modified_by` int(11) NOT NULL COMMENT 'Modified user',
  `state` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Record state',
  `params` text NOT NULL COMMENT 'Params',
  PRIMARY KEY (`id`),
  KEY `idx_access` (`access`),
  KEY `idx_alias` (`alias`),
  KEY `idx_created_by` (`created_by`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;
