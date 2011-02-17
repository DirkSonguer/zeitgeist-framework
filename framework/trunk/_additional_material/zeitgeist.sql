-- phpMyAdmin SQL Dump
-- version 3.2.4
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Erstellungszeit: 17. Februar 2011 um 07:27
-- Server Version: 5.1.41
-- PHP-Version: 5.3.1

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Datenbank: `zeitgeist`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `actionlog`
--

CREATE TABLE IF NOT EXISTS `actionlog` (
  `actionlog_id` int(11) NOT NULL AUTO_INCREMENT,
  `actionlog_module` int(11) NOT NULL,
  `actionlog_action` int(11) NOT NULL,
  `actionlog_ip` int(10) unsigned DEFAULT NULL,
  `actionlog_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`actionlog_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Daten für Tabelle `actionlog`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `actionlog_parameters`
--

CREATE TABLE IF NOT EXISTS `actionlog_parameters` (
  `actionparameter_id` int(11) NOT NULL AUTO_INCREMENT,
  `actionparameter_trafficid` int(11) NOT NULL,
  `actionparameter_key` varchar(64) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `actionparameter_value` text CHARACTER SET latin1 COLLATE latin1_general_ci,
  PRIMARY KEY (`actionparameter_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Daten für Tabelle `actionlog_parameters`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `actions`
--

CREATE TABLE IF NOT EXISTS `actions` (
  `action_id` int(11) NOT NULL AUTO_INCREMENT,
  `action_module` int(11) NOT NULL,
  `action_name` varchar(30) NOT NULL DEFAULT '',
  `action_description` text NOT NULL,
  `action_active` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`action_id`),
  KEY `action_module` (`action_module`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Daten für Tabelle `actions`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `configurationcache`
--

CREATE TABLE IF NOT EXISTS `configurationcache` (
  `configurationcache_id` int(11) NOT NULL AUTO_INCREMENT,
  `configurationcache_name` varchar(128) NOT NULL,
  `configurationcache_timestamp` varchar(32) NOT NULL,
  `configurationcache_content` text NOT NULL,
  PRIMARY KEY (`configurationcache_id`),
  UNIQUE KEY `configuration_cache_modulename` (`configurationcache_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Daten für Tabelle `configurationcache`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `game_actions`
--

CREATE TABLE IF NOT EXISTS `game_actions` (
  `action_id` int(11) NOT NULL AUTO_INCREMENT,
  `action_name` varchar(255) CHARACTER SET latin1 NOT NULL,
  `action_class` varchar(32) CHARACTER SET latin1 NOT NULL,
  PRIMARY KEY (`action_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Daten für Tabelle `game_actions`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `game_assemblages`
--

CREATE TABLE IF NOT EXISTS `game_assemblages` (
  `assemblage_id` int(11) NOT NULL AUTO_INCREMENT,
  `assemblage_name` varchar(32) CHARACTER SET latin1 NOT NULL,
  `assemblage_description` text CHARACTER SET latin1,
  PRIMARY KEY (`assemblage_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Daten für Tabelle `game_assemblages`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `game_assemblage_components`
--

CREATE TABLE IF NOT EXISTS `game_assemblage_components` (
  `assemblagecomponent_assemblage` int(11) NOT NULL,
  `assemblagecomponent_component` int(11) NOT NULL,
  KEY `assemblagecomponent_assemblage` (`assemblagecomponent_assemblage`),
  KEY `assemblagecomponent_component` (`assemblagecomponent_component`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `game_assemblage_components`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `game_components`
--

CREATE TABLE IF NOT EXISTS `game_components` (
  `component_id` int(11) NOT NULL AUTO_INCREMENT,
  `component_name` varchar(32) CHARACTER SET latin1 NOT NULL,
  `component_description` text CHARACTER SET latin1,
  PRIMARY KEY (`component_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Daten für Tabelle `game_components`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `game_entities`
--

CREATE TABLE IF NOT EXISTS `game_entities` (
  `entity_id` int(11) NOT NULL AUTO_INCREMENT,
  `entity_name` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  PRIMARY KEY (`entity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Daten für Tabelle `game_entities`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `game_entity_components`
--

CREATE TABLE IF NOT EXISTS `game_entity_components` (
  `entitycomponent_entity` int(11) NOT NULL,
  `entitycomponent_component` int(11) NOT NULL,
  `entitycomponent_componentdata` int(11) NOT NULL,
  PRIMARY KEY (`entitycomponent_entity`,`entitycomponent_component`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `game_entity_components`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `game_eventlog`
--

CREATE TABLE IF NOT EXISTS `game_eventlog` (
  `eventlog_id` int(11) NOT NULL AUTO_INCREMENT,
  `eventlog_game` int(11) NOT NULL DEFAULT '0',
  `eventlog_action` int(11) NOT NULL,
  `eventlog_parameter` varchar(32) CHARACTER SET latin1 NOT NULL,
  `eventlog_player` int(11) NOT NULL,
  `eventlog_time` int(11) NOT NULL,
  `eventlog_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`eventlog_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Daten für Tabelle `game_eventlog`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `game_events`
--

CREATE TABLE IF NOT EXISTS `game_events` (
  `event_id` int(11) NOT NULL AUTO_INCREMENT,
  `event_game` int(11) NOT NULL DEFAULT '0',
  `event_action` int(11) NOT NULL,
  `event_parameter` varchar(32) CHARACTER SET latin1 NOT NULL,
  `event_player` int(11) NOT NULL,
  `event_time` int(11) NOT NULL,
  `event_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`event_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Daten für Tabelle `game_events`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `modules`
--

CREATE TABLE IF NOT EXISTS `modules` (
  `module_id` int(11) NOT NULL AUTO_INCREMENT,
  `module_name` varchar(30) NOT NULL DEFAULT '',
  `module_description` text NOT NULL,
  `module_active` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`module_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Daten für Tabelle `modules`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `sessiondata`
--

CREATE TABLE IF NOT EXISTS `sessiondata` (
  `sessiondata_id` varchar(32) CHARACTER SET latin1 NOT NULL,
  `sessiondata_created` int(11) NOT NULL DEFAULT '0',
  `sessiondata_lastupdate` int(11) NOT NULL DEFAULT '0',
  `sessiondata_content` text NOT NULL,
  `sessiondata_ip` int(10) unsigned NOT NULL,
  PRIMARY KEY (`sessiondata_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `sessiondata`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `templatecache`
--

CREATE TABLE IF NOT EXISTS `templatecache` (
  `templatecache_id` int(11) NOT NULL AUTO_INCREMENT,
  `templatecache_name` varchar(128) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `templatecache_timestamp` varchar(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `templatecache_content` text CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  PRIMARY KEY (`templatecache_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Daten für Tabelle `templatecache`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `userconfirmation`
--

CREATE TABLE IF NOT EXISTS `userconfirmation` (
  `userconfirmation_user` int(11) NOT NULL,
  `userconfirmation_key` varchar(100) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  PRIMARY KEY (`userconfirmation_user`,`userconfirmation_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `userconfirmation`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `userdata`
--

CREATE TABLE IF NOT EXISTS `userdata` (
  `userdata_id` int(11) NOT NULL AUTO_INCREMENT,
  `userdata_user` int(11) NOT NULL,
  `userdata_username` varchar(100) DEFAULT NULL,
  `userdata_url` varchar(255) DEFAULT NULL,
  `userdata_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`userdata_id`),
  KEY `userdata_user` (`userdata_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Daten für Tabelle `userdata`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `userrights`
--

CREATE TABLE IF NOT EXISTS `userrights` (
  `userright_action` int(11) NOT NULL DEFAULT '0',
  `userright_user` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`userright_action`,`userright_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `userrights`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `userroles`
--

CREATE TABLE IF NOT EXISTS `userroles` (
  `userrole_id` int(11) NOT NULL AUTO_INCREMENT,
  `userrole_name` varchar(30) NOT NULL DEFAULT '',
  `userrole_description` text,
  PRIMARY KEY (`userrole_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Daten für Tabelle `userroles`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `userroles_to_actions`
--

CREATE TABLE IF NOT EXISTS `userroles_to_actions` (
  `userroleaction_userrole` int(11) NOT NULL DEFAULT '0',
  `userroleaction_action` int(11) NOT NULL DEFAULT '0',
  KEY `userroleright_userrole` (`userroleaction_userrole`),
  KEY `userroleright_userright` (`userroleaction_action`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `userroles_to_actions`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `userroles_to_users`
--

CREATE TABLE IF NOT EXISTS `userroles_to_users` (
  `userroleuser_userrole` int(11) NOT NULL DEFAULT '0',
  `userroleuser_user` int(11) NOT NULL DEFAULT '0',
  KEY `userroleuser_userrole` (`userroleuser_userrole`),
  KEY `userroleuser_user` (`userroleuser_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `userroles_to_users`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_username` varchar(255) NOT NULL,
  `user_password` varchar(255) NOT NULL DEFAULT '',
  `user_key` varchar(64) DEFAULT NULL,
  `user_active` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Daten für Tabelle `users`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `users_facebook`
--

CREATE TABLE IF NOT EXISTS `users_facebook` (
  `facebookuser_fbid` int(12) NOT NULL,
  `facebookuser_user` int(12) NOT NULL,
  PRIMARY KEY (`facebookuser_fbid`,`facebookuser_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `users_facebook`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `users_twitter`
--

CREATE TABLE IF NOT EXISTS `users_twitter` (
  `twitteruser_twitterid` int(11) NOT NULL,
  `twitteruser_user` int(11) NOT NULL,
  PRIMARY KEY (`twitteruser_twitterid`,`twitteruser_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `users_twitter`
--


/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
