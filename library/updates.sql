CREATE TABLE IF NOT EXISTS `bots` (
  `bot_id` int(11) NOT NULL AUTO_INCREMENT,
  `bot_name` varchar(255) NOT NULL,
  `bot_desc` varchar(255) NOT NULL,
  `bot_active` int(11) NOT NULL DEFAULT '1',
  `bot_parent_id` int(11) NOT NULL DEFAULT '0',
  `format` varchar(10) NOT NULL DEFAULT 'html',
  `use_aiml_code` int(11) NOT NULL DEFAULT '1',
  `update_aiml_code` int(11) NOT NULL DEFAULT '1',
  `save_state` enum('session','database') NOT NULL DEFAULT 'session',
  `conversation_lines` int(11) NOT NULL DEFAULT '7',
  `remember_up_to` int(11) NOT NULL DEFAULT '10',
  `debugemail` int(11) NOT NULL,
  `debugshow` int(11) NOT NULL DEFAULT '1',
  `debugmode` int(11) NOT NULL DEFAULT '1',
  `default_aiml_pattern` varchar(255) NOT NULL DEFAULT 'RANDOM PICKUP LINE',
  PRIMARY KEY (`bot_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `bots`
--

INSERT INTO `bots` (`bot_id`, `bot_name`, `bot_desc`, `bot_active`, `bot_parent_id`, `format`, `use_aiml_code`, `update_aiml_code`, `save_state`, `conversation_lines`, `remember_up_to`, `debugemail`, `debugshow`, `debugmode`, `default_aiml_pattern`) VALUES
(1, 'Program O', 'The default Program O chatbot...', 1, 0, 'html', 1, 1, 'session', 7, 10, 0, 3, 1, 'RANDOM PICKUP LINE'),


ALTER TABLE  `aiml` ADD  `bot_id` INT NOT NULL DEFAULT  '1' AFTER  `id` ;
ALTER TABLE  `users` ADD  `bot_id` INT NOT NULL AFTER  `session_id` ;
ALTER TABLE  `users` ADD  `state` TEXT NOT NULL ;
ALTER TABLE `aiml` ADD `php_code` TEXT NOT NULL ;
