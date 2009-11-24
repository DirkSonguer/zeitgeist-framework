
--
-- Tabellenstruktur für Tabelle `actionlog`
--

CREATE TABLE IF NOT EXISTS `actionlog` (
  `actionlog_id` int(12) NOT NULL auto_increment,
  `actionlog_module` int(12) NOT NULL,
  `actionlog_action` int(12) NOT NULL,
  `actionlog_ip` int(10) unsigned default NULL,
  `actionlog_timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`actionlog_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Daten für Tabelle `actionlog`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `actionlog_parameters`
--

CREATE TABLE IF NOT EXISTS `actionlog_parameters` (
  `actionparameter_id` int(12) NOT NULL auto_increment,
  `actionparameter_trafficid` int(12) NOT NULL,
  `actionparameter_key` varchar(64) character set latin1 collate latin1_general_ci NOT NULL,
  `actionparameter_value` text character set latin1 collate latin1_general_ci,
  PRIMARY KEY  (`actionparameter_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Daten für Tabelle `actionlog_parameters`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `actions`
--

CREATE TABLE IF NOT EXISTS `actions` (
  `action_id` int(12) NOT NULL auto_increment,
  `action_module` int(12) NOT NULL,
  `action_name` varchar(30) NOT NULL default '',
  `action_description` text NOT NULL,
  `action_requiresuserright` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`action_id`),
  KEY `action_module` (`action_module`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Daten für Tabelle `actions`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `configurationcache`
--

CREATE TABLE IF NOT EXISTS `configurationcache` (
  `configurationcache_id` int(12) NOT NULL auto_increment,
  `configurationcache_name` varchar(128) NOT NULL,
  `configurationcache_timestamp` varchar(32) NOT NULL,
  `configurationcache_content` text NOT NULL,
  PRIMARY KEY  (`configurationcache_id`),
  UNIQUE KEY `configuration_cache_modulename` (`configurationcache_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Daten für Tabelle `configurationcache`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `modules`
--

CREATE TABLE IF NOT EXISTS `modules` (
  `module_id` int(12) NOT NULL auto_increment,
  `module_name` varchar(30) NOT NULL default '',
  `module_description` text NOT NULL,
  `module_active` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`module_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Daten für Tabelle `modules`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `sessiondata`
--

CREATE TABLE IF NOT EXISTS `sessiondata` (
  `sessiondata_id` varchar(32) character set latin1 NOT NULL,
  `sessiondata_created` int(11) NOT NULL default '0',
  `sessiondata_lastupdate` int(11) NOT NULL default '0',
  `sessiondata_content` text NOT NULL,
  `sessiondata_ip` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`sessiondata_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `sessiondata`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `templatecache`
--

CREATE TABLE IF NOT EXISTS `templatecache` (
  `templatecache_id` int(12) NOT NULL auto_increment,
  `templatecache_name` varchar(128) character set latin1 collate latin1_general_ci NOT NULL,
  `templatecache_timestamp` varchar(32) character set latin1 collate latin1_general_ci NOT NULL,
  `templatecache_content` text character set latin1 collate latin1_general_ci NOT NULL,
  PRIMARY KEY  (`templatecache_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Daten für Tabelle `templatecache`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `userconfirmation`
--

CREATE TABLE IF NOT EXISTS `userconfirmation` (
  `userconfirmation_user` int(12) NOT NULL,
  `userconfirmation_key` varchar(100) character set latin1 collate latin1_general_ci NOT NULL,
  PRIMARY KEY  (`userconfirmation_user`,`userconfirmation_key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `userconfirmation`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `userdata`
--

CREATE TABLE IF NOT EXISTS `userdata` (
  `userdata_id` int(12) NOT NULL auto_increment,
  `userdata_user` int(12) NOT NULL,
  `userdata_username` varchar(100) default NULL,
  `userdata_url` varchar(255) default NULL,
  `userdata_timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`userdata_id`),
  KEY `userdata_user` (`userdata_user`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Daten für Tabelle `userdata`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `userrights`
--

CREATE TABLE IF NOT EXISTS `userrights` (
  `userright_action` int(12) NOT NULL default '0',
  `userright_user` int(12) NOT NULL default '0',
  PRIMARY KEY  (`userright_action`,`userright_user`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `userrights`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `userroles`
--

CREATE TABLE IF NOT EXISTS `userroles` (
  `userrole_id` int(12) NOT NULL auto_increment,
  `userrole_name` varchar(30) NOT NULL default '',
  `userrole_description` text,
  PRIMARY KEY  (`userrole_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Daten für Tabelle `userroles`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `userroles_to_actions`
--

CREATE TABLE IF NOT EXISTS `userroles_to_actions` (
  `userroleaction_userrole` int(12) NOT NULL default '0',
  `userroleaction_action` int(12) NOT NULL default '0',
  KEY `userroleright_userrole` (`userroleaction_userrole`),
  KEY `userroleright_userright` (`userroleaction_action`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `userroles_to_actions`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `userroles_to_users`
--

CREATE TABLE IF NOT EXISTS `userroles_to_users` (
  `userroleuser_userrole` int(12) NOT NULL default '0',
  `userroleuser_user` int(12) NOT NULL default '0',
  KEY `userroleuser_userrole` (`userroleuser_userrole`),
  KEY `userroleuser_user` (`userroleuser_user`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `userroles_to_users`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int(12) NOT NULL auto_increment,
  `user_username` varchar(255) NOT NULL,
  `user_password` varchar(255) NOT NULL default '',
  `user_key` varchar(64) default NULL,
  `user_active` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Daten für Tabelle `users`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `game_entities`
--

CREATE TABLE IF NOT EXISTS `game_entities` (
  `entity_id` int(12) NOT NULL auto_increment,
  `entity_name` varchar(255),
  PRIMARY KEY  (`entity_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Daten für Tabelle `game_entities`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `game_components`
--

CREATE TABLE IF NOT EXISTS `game_components` (
  `component_id` int(12) NOT NULL auto_increment,
  `component_name` varchar(32) NOT NULL,
  `component_description` text,
  PRIMARY KEY  (`component_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ;

--
-- Daten für Tabelle `game_components`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `game_entity_components`
--

CREATE TABLE IF NOT EXISTS `game_entity_components` (
  `entitycomponent_entity` int(11) NOT NULL,
  `entitycomponent_component` int(11) NOT NULL,
  `entitycomponent_componentdata` int(11) NOT NULL,
  PRIMARY KEY  (`entitycomponent_entity` , `entitycomponent_component`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ;

--
-- Daten für Tabelle `game_entity_components`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `game_actionlog`
--

CREATE TABLE IF NOT EXISTS `game_actionlog` (
  `actionlog_id` int(11) NOT NULL,
  `actionlog_action` int(11) NOT NULL,
  `actionlog_parameter` varchar(32) NOT NULL,
  `actionlog_player` int(11) NOT NULL,
  `actionlog_timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`actionlog_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ;

--
-- Daten für Tabelle `game_actionlog`
--



-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `game_actions`
--

CREATE TABLE IF NOT EXISTS `game_actions` (
  `action_id` int(11) NOT NULL,
  `action_name` varchar(255) NOT NULL,
  `action_class` varchar(32) NOT NULL,
  PRIMARY KEY  (`action_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ;

--
-- Daten für Tabelle `game_actions`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `game_events`
--

CREATE TABLE IF NOT EXISTS `game_events` (
  `event_id` int(11) NOT NULL,
  `event_action` int(11) NOT NULL,
  `event_parameter` varchar(32) NOT NULL,
  `event_player` int(11) NOT NULL,
  `event_time` int(11) NOT NULL,
  `event_timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`event_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ;

--
-- Daten für Tabelle `game_events`
--
