DROP TABLE IF EXISTS `bots`;
CREATE TABLE IF NOT EXISTS `bots` (
  `bot_id` int(11) NOT NULL AUTO_INCREMENT,
  `bot_name` varchar(255) NOT NULL,
  `bot_desc` varchar(255) NOT NULL,
  `bot_active` int(11) NOT NULL DEFAULT '1',
  `bot_parent_id` int(11) NOT NULL DEFAULT '0',
  `format` varchar(10) NOT NULL DEFAULT 'html',
  `use_aiml_code` int(11) DEFAULT '1',
  `update_aiml_code` int(11) NOT NULL DEFAULT '1',
  `save_state` enum('session','database') NOT NULL DEFAULT 'session',
  `conversation_lines` int(11) NOT NULL DEFAULT '7',
  `remember_up_to` int(11) NOT NULL DEFAULT '10',
  `debugemail` TEXT NOT NULL,
  `debugshow` int(11) NOT NULL DEFAULT '1',
  `debugmode` int(11) NOT NULL DEFAULT '1',
  `default_aiml_pattern` varchar(255) NOT NULL DEFAULT 'RANDOM PICKUP LINE',
  `error_response` text not null,
  PRIMARY KEY (`bot_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;
CREATE TABLE IF NOT EXISTS `wordcensor` (
  `censor_id` int(11) NOT NULL AUTO_INCREMENT,
  `word_to_censor` varchar(50) NOT NULL,
  `replace_with` varchar(50) NOT NULL DEFAULT '****',
  `bot_exclude` varchar(255) NOT NULL,
  PRIMARY KEY (`censor_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;
ALTER TABLE `users` ADD `name` text NOT NULL AFTER `id`;
ALTER TABLE `users` ADD `bot_id` int(11) NOT NULL AFTER `session_id`;
ALTER TABLE `users` ADD `state` text NOT NULL AFTER `last_update`;
ALTER TABLE `aiml` ADD `bot_id` INT( 11 ) NOT NULL DEFAULT '1' AFTER `id`;
ALTER TABLE `aiml` ADD `php_code` TEXT NULL AFTER `filename`;
