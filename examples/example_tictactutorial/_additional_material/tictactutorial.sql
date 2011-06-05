-- phpMyAdmin SQL Dump
-- version 3.2.4
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Erstellungszeit: 05. Juni 2011 um 22:40
-- Server Version: 5.1.41
-- PHP-Version: 5.3.1

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Datenbank: `tictactutorial`
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Daten für Tabelle `actions`
--

INSERT INTO `actions` (`action_id`, `action_module`, `action_name`, `action_description`, `action_active`) VALUES
(1, 1, 'index', 'The default action for the main module', 1),
(2, 2, 'index', 'The default action for the user module', 1),
(3, 2, 'login', 'Log in an existing user', 1),
(4, 2, 'logout', 'Log out a user that is currently logged in', 1),
(5, 2, 'create', 'Create a new user', 1);

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=17 ;

--
-- Daten für Tabelle `configurationcache`
--

INSERT INTO `configurationcache` (`configurationcache_id`, `configurationcache_name`, `configurationcache_timestamp`, `configurationcache_content`) VALUES
(11, 'configuration/application.ini', '1283031751', 'YToyOntzOjExOiJhcHBsaWNhdGlvbiI7YTozOntzOjg6ImJhc2VwYXRoIjtzOjMyOiJodHRwOi8vMTI3LjAuMC4xL3RpY3RhY3R1dG9yaWFsLyI7czoxMjoidGVtcGxhdGVwYXRoIjtzOjMzOiIuL3RlbXBsYXRlcy9hcHBsaWNhdGlvbl90ZW1wbGF0ZXMiO3M6MTE6InZlcnNpb25pbmZvIjtzOjE5OiJBcHBsaWNhdGlvbiBWZXJzaW9uIjt9czoxNDoicGFyYW1ldGVydHlwZXMiO2E6Mjp7czo4OiJ1c2VybmFtZSI7czoyOToiL15bXHfDvMOcw7bDlsOkw4TDnyBdezQsMTZ9JC8iO3M6MTI6InVzZXJwYXNzd29yZCI7czoxMToiL14uezQsMTZ9JC8iO319'),
(13, './modules/main/main.ini', '1277747985', 'YToxOntzOjU6ImluZGV4IjthOjE6e3M6MjE6Imhhc0V4dGVybmFsUGFyYW1ldGVycyI7czo1OiJmYWxzZSI7fX0='),
(14, './zeitgeist/configuration/zeitgeist.ini', '1286608180', 'YTo5OntzOjc6Im1vZHVsZXMiO2E6Mjp7czoxMToiZm9ybWNyZWF0b3IiO3M6NDoidHJ1ZSI7czo0OiJzaG9wIjtzOjQ6InRydWUiO31zOjY6InRhYmxlcyI7YToxNTp7czoxNToidGFibGVfYWN0aW9ubG9nIjtzOjk6ImFjdGlvbmxvZyI7czoxMzoidGFibGVfYWN0aW9ucyI7czo3OiJhY3Rpb25zIjtzOjE4OiJ0YWJsZV9tZXNzYWdlY2FjaGUiO3M6MTI6Im1lc3NhZ2VjYWNoZSI7czoxMzoidGFibGVfbW9kdWxlcyI7czo3OiJtb2R1bGVzIjtzOjE3OiJ0YWJsZV9zZXNzaW9uZGF0YSI7czoxMToic2Vzc2lvbmRhdGEiO3M6MTk6InRhYmxlX3RlbXBsYXRlY2FjaGUiO3M6MTM6InRlbXBsYXRlY2FjaGUiO3M6MTE6InRhYmxlX3VzZXJzIjtzOjU6InVzZXJzIjtzOjE0OiJ0YWJsZV91c2VyZGF0YSI7czo4OiJ1c2VyZGF0YSI7czoxNjoidGFibGVfdXNlcnJpZ2h0cyI7czoxMDoidXNlcnJpZ2h0cyI7czoxNToidGFibGVfdXNlcnJvbGVzIjtzOjk6InVzZXJyb2xlcyI7czoyMDoidGFibGVfdXNlcmNoYXJhY3RlcnMiO3M6MTQ6InVzZXJjaGFyYWN0ZXJzIjtzOjI0OiJ0YWJsZV91c2Vycm9sZXNfdG9fdXNlcnMiO3M6MTg6InVzZXJyb2xlc190b191c2VycyI7czoyNjoidGFibGVfdXNlcnJvbGVzX3RvX2FjdGlvbnMiO3M6MjA6InVzZXJyb2xlc190b19hY3Rpb25zIjtzOjE4OiJ0YWJsZV91c2Vyc2Vzc2lvbnMiO3M6MTI6InVzZXJzZXNzaW9ucyI7czoyMjoidGFibGVfdXNlcmNvbmZpcm1hdGlvbiI7czoxNjoidXNlcmNvbmZpcm1hdGlvbiI7fXM6Nzoic2Vzc2lvbiI7YTozOntzOjE1OiJzZXNzaW9uX3N0b3JhZ2UiO3M6ODoiZGF0YWJhc2UiO3M6MTI6InNlc3Npb25fbmFtZSI7czoxOToiWkVJVEdFSVNUX1NFU1NJT05JRCI7czoxNjoic2Vzc2lvbl9saWZldGltZSI7czoxOiIwIjt9czo4OiJtZXNzYWdlcyI7YToxOntzOjIzOiJ1c2VfcGVyc2lzdGVudF9tZXNzYWdlcyI7czoxOiIxIjt9czo4OiJ0ZW1wbGF0ZSI7YToxNTp7czoxMjoicmV3cml0ZV91cmxzIjtzOjE6IjAiO3M6MTg6InZhcmlhYmxlU3Vic3RCZWdpbiI7czo1OiI8IS0tQCI7czoxNjoidmFyaWFibGVTdWJzdEVuZCI7czo0OiJALS0+IjtzOjE1OiJibG9ja1N1YnN0QmVnaW4iO3M6NToiPCEtLSMiO3M6MTM6ImJsb2NrU3Vic3RFbmQiO3M6NDoiIy0tPiI7czo5OiJsaW5rQmVnaW4iO3M6NDoiQEB7WyI7czo3OiJsaW5rRW5kIjtzOjQ6Il19QEAiO3M6MTM6InZhcmlhYmxlQmVnaW4iO3M6MzoiQEB7IjtzOjExOiJ2YXJpYWJsZUVuZCI7czozOiJ9QEAiO3M6MTQ6ImJsb2NrT3BlbkJlZ2luIjtzOjMwOiI8IS0tIFRlbXBsYXRlQmVnaW5CbG9jayBuYW1lPSIiO3M6MTI6ImJsb2NrT3BlbkVuZCI7czo1OiIiIC0tPiI7czoxMDoiYmxvY2tDbG9zZSI7czoyNToiPCEtLSBUZW1wbGF0ZUVuZEJsb2NrIC0tPiI7czoxOToiVXNlcm1lc3NhZ2VNZXNzYWdlcyI7czoxMjoidXNlcm1lc3NhZ2VzIjtzOjE5OiJVc2VybWVzc2FnZVdhcm5pbmdzIjtzOjEyOiJ1c2Vyd2FybmluZ3MiO3M6MTc6IlVzZXJtZXNzYWdlRXJyb3JzIjtzOjEwOiJ1c2VyZXJyb3JzIjt9czo5OiJhY3Rpb25sb2ciO2E6MTp7czoxNjoiYWN0aW9ubG9nX2FjdGl2ZSI7czoxOiIwIjt9czoxMjoiZXJyb3JoYW5kbGVyIjthOjE6e3M6MTc6ImVycm9yX3JlcG9ydGxldmVsIjtzOjE6IjIiO31zOjExOiJ1c2VyaGFuZGxlciI7YToxOntzOjE1OiJ1c2VfZG91Ymxlb3B0aW4iO3M6MToiMSI7fXM6MTA6InBhcmFtZXRlcnMiO2E6OTp7czoxNzoiZXNjYXBlX3BhcmFtZXRlcnMiO3M6MToiMSI7czo1OiJlbWFpbCI7czo2NjoiL15bXHdcLVwrXCZcKl0rKD86XC5bXHdcLVxfXCtcJlwqXSspKkAoPzpbXHctXStcLikrW2EtekEtWl17Miw3fSQvIjtzOjM6InVybCI7czo4NToiL14oZnRwfGh0dHB8aHR0cHMpOlwvXC8oXHcrOnswLDF9XHcqQCk/KFxTKykoOlswLTldKyk/KFwvfFwvKFtcdyMhOi4/Kz0mJUAhXC1cL10pKT8kLyI7czozOiJ6aXAiO3M6MTE6Ii9eXGR7Myw1fSQvIjtzOjY6InN0cmluZyI7czo2OToiL15bXHfDvMOcw6TDhMO2w5YgXSsoKFtcLFxAXC5cOlwtXC9cKFwpXCFcPyBdKT9bXHfDvMOcw6TDhMO2w5YgXSopKiQvIjtzOjQ6InRleHQiO3M6Nzk6Ii9eW1x3w7zDnMOkw4TDtsOWIF0rKChbXFxcQFwiXCxcLlw6XC1cL1xyXG5cdFwhXD9cKFwpIF0pP1tcd8O8w5zDpMOEw7bDliBdKikqJC8iO3M6NjoibnVtYmVyIjtzOjI0OiIvXlswLTldKihcLnxcLCk/WzAtOV0rJC8iO3M6NzoiYm9vbGVhbiI7czoxMjoiL15bMC0xXXsxfSQvIjtzOjQ6ImRhdGUiO3M6Mzg6Ii9eWzAtOV17Mn0oXC4pP1swLTldezJ9KFwuKT9bMC05XXs0fSQvIjt9fQ=='),
(15, './configuration/zeitgeist.ini', '1286608192', 'YTozOntzOjc6InNlc3Npb24iO2E6Mzp7czoxNToic2Vzc2lvbl9zdG9yYWdlIjtzOjg6ImRhdGFiYXNlIjtzOjEyOiJzZXNzaW9uX25hbWUiO3M6MTM6IlpHQVBQTElDQVRJT04iO3M6MTY6InNlc3Npb25fbGlmZXRpbWUiO3M6MToiMCI7fXM6MTM6InRyYWZmaWNsb2dnZXIiO2E6MTp7czoyMDoidHJhZmZpY2xvZ2dlcl9hY3RpdmUiO3M6MToiMSI7fXM6ODoibWVzc2FnZXMiO2E6MTp7czoyMzoidXNlX3BlcnNpc3RlbnRfbWVzc2FnZXMiO3M6MToiMSI7fX0='),
(16, './modules/user/user.ini', '1286647160', 'YTozOntzOjU6ImluZGV4IjthOjE6e3M6MjE6Imhhc0V4dGVybmFsUGFyYW1ldGVycyI7czo1OiJmYWxzZSI7fXM6NToibG9naW4iO2E6NDp7czoyMToiaGFzRXh0ZXJuYWxQYXJhbWV0ZXJzIjtzOjQ6InRydWUiO3M6ODoidXNlcm5hbWUiO2E6Mzp7czo5OiJwYXJhbWV0ZXIiO3M6NDoidHJ1ZSI7czo2OiJzb3VyY2UiO3M6NDoiUE9TVCI7czo4OiJleHBlY3RlZCI7czozMToiW1t6ZWl0Z2Vpc3QucGFyYW1ldGVycy5zdHJpbmddXSI7fXM6ODoicGFzc3dvcmQiO2E6Mzp7czo5OiJwYXJhbWV0ZXIiO3M6NDoidHJ1ZSI7czo2OiJzb3VyY2UiO3M6NDoiUE9TVCI7czo4OiJleHBlY3RlZCI7czoxMToiL14uezQsMzJ9JC8iO31zOjU6ImxvZ2luIjthOjQ6e3M6OToicGFyYW1ldGVyIjtzOjQ6InRydWUiO3M6Njoic291cmNlIjtzOjQ6IlBPU1QiO3M6ODoiZXhwZWN0ZWQiO3M6ODoiQ09OU1RBTlQiO3M6NToidmFsdWUiO3M6NToiTG9naW4iO319czo2OiJjcmVhdGUiO2E6NTp7czoyMToiaGFzRXh0ZXJuYWxQYXJhbWV0ZXJzIjtzOjQ6InRydWUiO3M6ODoidXNlcm5hbWUiO2E6Mzp7czo5OiJwYXJhbWV0ZXIiO3M6NDoidHJ1ZSI7czo2OiJzb3VyY2UiO3M6NDoiUE9TVCI7czo4OiJleHBlY3RlZCI7czozMToiW1t6ZWl0Z2Vpc3QucGFyYW1ldGVycy5zdHJpbmddXSI7fXM6ODoicGFzc3dvcmQiO2E6Mzp7czo5OiJwYXJhbWV0ZXIiO3M6NDoidHJ1ZSI7czo2OiJzb3VyY2UiO3M6NDoiUE9TVCI7czo4OiJleHBlY3RlZCI7czoxMToiL14uezQsMzJ9JC8iO31zOjIxOiJwYXNzd29yZF9jb25maXJtYXRpb24iO2E6Mzp7czo5OiJwYXJhbWV0ZXIiO3M6NDoidHJ1ZSI7czo2OiJzb3VyY2UiO3M6NDoiUE9TVCI7czo4OiJleHBlY3RlZCI7czoxMToiL14uezQsMzJ9JC8iO31zOjY6ImNyZWF0ZSI7YTo0OntzOjk6InBhcmFtZXRlciI7czo0OiJ0cnVlIjtzOjY6InNvdXJjZSI7czo0OiJQT1NUIjtzOjg6ImV4cGVjdGVkIjtzOjg6IkNPTlNUQU5UIjtzOjU6InZhbHVlIjtzOjEyOiIiQ3JlYXRlIFVzZXIiO319fQ==');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `game_actions`
--

CREATE TABLE IF NOT EXISTS `game_actions` (
  `action_id` int(11) NOT NULL AUTO_INCREMENT,
  `action_name` varchar(255) NOT NULL,
  `action_class` varchar(32) NOT NULL,
  PRIMARY KEY (`action_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Daten für Tabelle `game_actions`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `game_assemblages`
--

CREATE TABLE IF NOT EXISTS `game_assemblages` (
  `assemblage_id` int(11) NOT NULL AUTO_INCREMENT,
  `assemblage_name` varchar(32) NOT NULL,
  `assemblage_description` text,
  PRIMARY KEY (`assemblage_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Daten für Tabelle `game_assemblage_components`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `game_components`
--

CREATE TABLE IF NOT EXISTS `game_components` (
  `component_id` int(11) NOT NULL AUTO_INCREMENT,
  `component_name` varchar(32) NOT NULL,
  `component_description` text,
  PRIMARY KEY (`component_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Daten für Tabelle `game_components`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `game_entities`
--

CREATE TABLE IF NOT EXISTS `game_entities` (
  `entity_id` int(11) NOT NULL AUTO_INCREMENT,
  `entity_name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`entity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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
  `eventlog_parameter` varchar(32) NOT NULL,
  `eventlog_player` int(11) NOT NULL,
  `eventlog_time` int(11) NOT NULL,
  `eventlog_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`eventlog_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

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
  `event_parameter` varchar(32) NOT NULL,
  `event_player` int(11) NOT NULL,
  `event_time` int(11) NOT NULL,
  `event_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`event_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Daten für Tabelle `modules`
--

INSERT INTO `modules` (`module_id`, `module_name`, `module_description`, `module_active`) VALUES
(1, 'main', 'The default module', 1),
(2, 'user', 'The user module', 1);

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

INSERT INTO `sessiondata` (`sessiondata_id`, `sessiondata_created`, `sessiondata_lastupdate`, `sessiondata_content`, `sessiondata_ip`) VALUES
('l5c3uou798qvk2cvbtotao9lu3', 1286226047, 1286226426, 'messagecache_session|s:3249:"a:17:{i:0;O:9:"zgMessage":4:{s:7:"message";s:62:"Could not establish user session: user id not found in session";s:4:"type";s:7:"warning";s:4:"from";s:21:"userhandler.class.php";s:2:"to";s:0:"";}i:1;O:9:"zgMessage":4:{s:7:"message";s:62:"Could not establish user session: user id not found in session";s:4:"type";s:7:"warning";s:4:"from";s:21:"userhandler.class.php";s:2:"to";s:0:"";}i:2;O:9:"zgMessage":4:{s:7:"message";s:62:"Could not establish user session: user id not found in session";s:4:"type";s:7:"warning";s:4:"from";s:21:"userhandler.class.php";s:2:"to";s:0:"";}i:3;O:9:"zgMessage":4:{s:7:"message";s:62:"Could not establish user session: user id not found in session";s:4:"type";s:7:"warning";s:4:"from";s:21:"userhandler.class.php";s:2:"to";s:0:"";}i:4;O:9:"zgMessage":4:{s:7:"message";s:62:"Could not establish user session: user id not found in session";s:4:"type";s:7:"warning";s:4:"from";s:21:"userhandler.class.php";s:2:"to";s:0:"";}i:5;O:9:"zgMessage":4:{s:7:"message";s:62:"Could not establish user session: user id not found in session";s:4:"type";s:7:"warning";s:4:"from";s:21:"userhandler.class.php";s:2:"to";s:0:"";}i:6;O:9:"zgMessage":4:{s:7:"message";s:62:"Could not establish user session: user id not found in session";s:4:"type";s:7:"warning";s:4:"from";s:21:"userhandler.class.php";s:2:"to";s:0:"";}i:7;O:9:"zgMessage":4:{s:7:"message";s:62:"Could not establish user session: user id not found in session";s:4:"type";s:7:"warning";s:4:"from";s:21:"userhandler.class.php";s:2:"to";s:0:"";}i:8;O:9:"zgMessage":4:{s:7:"message";s:62:"Could not establish user session: user id not found in session";s:4:"type";s:7:"warning";s:4:"from";s:21:"userhandler.class.php";s:2:"to";s:0:"";}i:9;O:9:"zgMessage":4:{s:7:"message";s:62:"Could not establish user session: user id not found in session";s:4:"type";s:7:"warning";s:4:"from";s:21:"userhandler.class.php";s:2:"to";s:0:"";}i:10;O:9:"zgMessage":4:{s:7:"message";s:62:"Could not establish user session: user id not found in session";s:4:"type";s:7:"warning";s:4:"from";s:21:"userhandler.class.php";s:2:"to";s:0:"";}i:11;O:9:"zgMessage":4:{s:7:"message";s:62:"Could not establish user session: user id not found in session";s:4:"type";s:7:"warning";s:4:"from";s:21:"userhandler.class.php";s:2:"to";s:0:"";}i:12;O:9:"zgMessage":4:{s:7:"message";s:49:"No messagedata is stored in session for this user";s:4:"type";s:7:"warning";s:4:"from";s:18:"messages.class.php";s:2:"to";s:0:"";}i:13;O:9:"zgMessage":4:{s:7:"message";s:67:"Problem logging in: user not found/is inactive or password is wrong";s:4:"type";s:7:"warning";s:4:"from";s:23:"userfunctions.class.php";s:2:"to";s:0:"";}i:14;O:9:"zgMessage":4:{s:7:"message";s:74:"Problem validating a user: user not found/is inactive or password is wrong";s:4:"type";s:7:"warning";s:4:"from";s:21:"userhandler.class.php";s:2:"to";s:0:"";}i:15;O:9:"zgMessage":4:{s:7:"message";s:67:"Problem logging in: user not found/is inactive or password is wrong";s:4:"type";s:7:"warning";s:4:"from";s:23:"userfunctions.class.php";s:2:"to";s:0:"";}i:16;O:9:"zgMessage":4:{s:7:"message";s:74:"Problem validating a user: user not found/is inactive or password is wrong";s:4:"type";s:7:"warning";s:4:"from";s:21:"userhandler.class.php";s:2:"to";s:0:"";}}";', 2130706433),
('v2cne2j5lpcgk00lrmjh4admu3', 1307281885, 1307281959, 'messagecache_session|s:3875:"a:20:{i:0;O:9:"zgMessage":4:{s:7:"message";s:62:"Could not establish user session: user id not found in session";s:4:"type";s:7:"warning";s:4:"from";s:21:"userhandler.class.php";s:2:"to";s:0:"";}i:1;O:9:"zgMessage":4:{s:7:"message";s:62:"Could not establish user session: user id not found in session";s:4:"type";s:7:"warning";s:4:"from";s:21:"userhandler.class.php";s:2:"to";s:0:"";}i:2;O:9:"zgMessage":4:{s:7:"message";s:93:"Possible problem getting the roles of a user: there seems to be no roles assigned to the user";s:4:"type";s:7:"warning";s:4:"from";s:19:"userroles.class.php";s:2:"to";s:0:"";}i:3;O:9:"zgMessage":4:{s:7:"message";s:53:"User does not have the requested right for action (1)";s:4:"type";s:7:"message";s:4:"from";s:21:"userhandler.class.php";s:2:"to";s:0:"";}i:4;O:9:"zgMessage":4:{s:7:"message";s:62:"Could not establish user session: user id not found in session";s:4:"type";s:7:"warning";s:4:"from";s:21:"userhandler.class.php";s:2:"to";s:0:"";}i:5;O:9:"zgMessage":4:{s:7:"message";s:62:"Could not establish user session: user id not found in session";s:4:"type";s:7:"warning";s:4:"from";s:21:"userhandler.class.php";s:2:"to";s:0:"";}i:6;O:9:"zgMessage":4:{s:7:"message";s:93:"Possible problem getting the roles of a user: there seems to be no roles assigned to the user";s:4:"type";s:7:"warning";s:4:"from";s:19:"userroles.class.php";s:2:"to";s:0:"";}i:7;O:9:"zgMessage":4:{s:7:"message";s:62:"Could not establish user session: user id not found in session";s:4:"type";s:7:"warning";s:4:"from";s:21:"userhandler.class.php";s:2:"to";s:0:"";}i:8;O:9:"zgMessage":4:{s:7:"message";s:62:"Could not establish user session: user id not found in session";s:4:"type";s:7:"warning";s:4:"from";s:21:"userhandler.class.php";s:2:"to";s:0:"";}i:9;O:9:"zgMessage":4:{s:7:"message";s:93:"Possible problem getting the roles of a user: there seems to be no roles assigned to the user";s:4:"type";s:7:"warning";s:4:"from";s:19:"userroles.class.php";s:2:"to";s:0:"";}i:10;O:9:"zgMessage":4:{s:7:"message";s:53:"User does not have the requested right for action (1)";s:4:"type";s:7:"message";s:4:"from";s:21:"userhandler.class.php";s:2:"to";s:0:"";}i:11;O:9:"zgMessage":4:{s:7:"message";s:62:"Could not establish user session: user id not found in session";s:4:"type";s:7:"warning";s:4:"from";s:21:"userhandler.class.php";s:2:"to";s:0:"";}i:12;O:9:"zgMessage":4:{s:7:"message";s:62:"Could not establish user session: user id not found in session";s:4:"type";s:7:"warning";s:4:"from";s:21:"userhandler.class.php";s:2:"to";s:0:"";}i:13;O:9:"zgMessage":4:{s:7:"message";s:93:"Possible problem getting the roles of a user: there seems to be no roles assigned to the user";s:4:"type";s:7:"warning";s:4:"from";s:19:"userroles.class.php";s:2:"to";s:0:"";}i:14;O:9:"zgMessage":4:{s:7:"message";s:62:"Could not establish user session: user id not found in session";s:4:"type";s:7:"warning";s:4:"from";s:21:"userhandler.class.php";s:2:"to";s:0:"";}i:15;O:9:"zgMessage":4:{s:7:"message";s:62:"Could not establish user session: user id not found in session";s:4:"type";s:7:"warning";s:4:"from";s:21:"userhandler.class.php";s:2:"to";s:0:"";}i:16;O:9:"zgMessage":4:{s:7:"message";s:93:"Possible problem getting the roles of a user: there seems to be no roles assigned to the user";s:4:"type";s:7:"warning";s:4:"from";s:19:"userroles.class.php";s:2:"to";s:0:"";}i:17;O:9:"zgMessage":4:{s:7:"message";s:53:"User does not have the requested right for action (1)";s:4:"type";s:7:"message";s:4:"from";s:21:"userhandler.class.php";s:2:"to";s:0:"";}i:18;O:9:"zgMessage":4:{s:7:"message";s:49:"No messagedata is stored in session for this user";s:4:"type";s:7:"warning";s:4:"from";s:18:"messages.class.php";s:2:"to";s:0:"";}i:19;O:9:"zgMessage":4:{s:7:"message";s:41:"Template data in the database is outdated";s:4:"type";s:7:"message";s:4:"from";s:18:"template.class.php";s:2:"to";s:0:"";}}";', 2130706433);

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

--
-- Daten für Tabelle `templatecache`
--

INSERT INTO `templatecache` (`templatecache_id`, `templatecache_name`, `templatecache_timestamp`, `templatecache_content`) VALUES
(6, './templates/application_templates/main_index.tpl.html', '1286224431', 'YTo0OntzOjQ6ImZpbGUiO3M6NTM6Ii4vdGVtcGxhdGVzL2FwcGxpY2F0aW9uX3RlbXBsYXRlcy9tYWluX2luZGV4LnRwbC5odG1sIjtzOjc6ImNvbnRlbnQiO3M6MjExOiI8aHRtbD4NCjxoZWFkPg0KCTx0aXRsZT5XZWxjb21lIHRvIFRpYyBUYWMgVHV0b3JpYWw8L3RpdGxlPg0KPC9oZWFkPg0KDQo8Ym9keT4NCg0KCTxoMT5XZWxjb21lIHRvIFRpYyBUYWMgVHV0b3JpYWw8L2gxPg0KDQogICAgPHA+PGEgaHJlZj0iaW5kZXgucGhwP21vZHVsZT11c2VyJmFjdGlvbj1sb2dvdXQiPkxvZyBvdXQ8L2E+PC9wPg0KDQo8L2JvZHk+DQo8L2h0bWw+IjtzOjY6ImJsb2NrcyI7YToxOntzOjQ6InJvb3QiO086MTU6InpnVGVtcGxhdGVCbG9jayI6NDp7czoxNDoiY3VycmVudENvbnRlbnQiO3M6MDoiIjtzOjE1OiJvcmlnaW5hbENvbnRlbnQiO3M6MDoiIjtzOjExOiJibG9ja1BhcmVudCI7czowOiIiO3M6MTQ6ImJsb2NrVmFyaWFibGVzIjthOjA6e319fXM6OToidmFyaWFibGVzIjthOjA6e319'),
(8, './templates/application_templates/user_login.tpl.html', '1286565492', 'YTo0OntzOjQ6ImZpbGUiO3M6NTM6Ii4vdGVtcGxhdGVzL2FwcGxpY2F0aW9uX3RlbXBsYXRlcy91c2VyX2xvZ2luLnRwbC5odG1sIjtzOjc6ImNvbnRlbnQiO3M6MTE4NjoiPGh0bWw+DQo8aGVhZD4NCgk8dGl0bGU+V2VsY29tZSB0byBUaWMgVGFjIFR1dG9yaWFsPC90aXRsZT4NCjwvaGVhZD4NCg0KPGJvZHk+DQoNCgk8aDE+V2VsY29tZSB0byBUaWMgVGFjIFR1dG9yaWFsPC9oMT4NCg0KICAgIDwhLS0jbG9naW5FcnJvciMtLT4NCg0KICAgIDxwPlBsZWFzZSBsb2cgaW4gdG8gcGxheSB0aGUgZ2FtZS4gRW50ZXIgeW91ciB1c2VybmFtZSBhbmQgcGFzc3dvcmQgaGVyZTo8L3A+DQoNCiAgICA8Zm9ybSBtZXRob2Q9InBvc3QiIGFjdGlvbj0iaW5kZXgucGhwP21vZHVsZT11c2VyJmFjdGlvbj1sb2dpbiIgbmFtZT0ibG9naW4iPg0KICAgICAgICA8dGFibGUgYm9yZGVyPSIwIj4NCiAgICAgICAgICAgIDx0cj4NCiAgICAgICAgICAgICAgICA8dGQgYWxpZ249InJpZ2h0IiBub3dyYXAgd2lkdGg9IjExMCI+PHA+VXNlcm5hbWU8L3A+PC90ZD4NCiAgICAgICAgICAgICAgICA8dGQgYWxpZ249ImxlZnQiIHZhbGlnbj0ibWlkZGxlIiBub3dyYXA+PGlucHV0IHR5cGU9InRleHQiIHNpemU9IjM1IiBtYXhsZW5ndGg9IjIwIiBuYW1lPSJ1c2VybmFtZSIgc3R5bGU9IndpZHRoOjI1MHB4OyIgLz48L3RkPg0KICAgICAgICAgICAgPC90cj4NCiAgICAgICAgICAgIDx0cj4NCiAgICAgICAgICAgICAgICA8dGQgYWxpZ249InJpZ2h0IiBub3dyYXA+PHA+UGFzc3dvcmQ8L3A+PC90ZD4NCiAgICAgICAgICAgICAgICA8dGQgYWxpZ249ImxlZnQiIHZhbGlnbj0ibWlkZGxlIiBub3dyYXA+PGlucHV0IHR5cGU9InBhc3N3b3JkIiBzaXplPSIzNSIgbWF4bGVuZ3RoPSIzMiIgbmFtZT0icGFzc3dvcmQiIHN0eWxlPSJ3aWR0aDoyNTBweDsiIC8+PC90ZD4NCiAgICAgICAgICAgIDwvdHI+DQogICAgICAgICAgICA8dHI+DQogICAgICAgICAgICAgICAgPHRkIGFsaWduPSJsZWZ0IiBub3dyYXA+Jm5ic3A7PC90ZD4NCiAgICAgICAgICAgICAgICA8dGQgYWxpZ249InJpZ2h0IiB2YWxpZ249Im1pZGRsZSIgbm93cmFwPjxpbnB1dCB0eXBlPSJzdWJtaXQiIG5hbWU9ImxvZ2luIiB2YWx1ZT0iTG9naW4iIC8+PC90ZD4NCiAgICAgICAgICAgIDwvdHI+DQogICAgICAgIDwvdGFibGU+DQogICAgPC9mb3JtPg0KDQoJPHA+PGEgaHJlZj0iaW5kZXgucGhwP21vZHVsZT11c2VyJmFjdGlvbj1jcmVhdGUiPkNyZWF0ZSBhIG5ldyB1c2VyPC9hPjwvcD4NCg0KPC9ib2R5Pg0KPC9odG1sPiI7czo2OiJibG9ja3MiO2E6Mjp7czoxMDoibG9naW5FcnJvciI7TzoxNToiemdUZW1wbGF0ZUJsb2NrIjo0OntzOjE0OiJjdXJyZW50Q29udGVudCI7czoxMDM6Ig0KICAgICAgICAgICAgPHA+PGI+QW4gZXJyb3IgaGFzIG9jY3VyZWQuIFBsZWFzZSBlbnRlciB5b3VyIGxvZ2luIGNyZWRlbnRpYWxzIGNhcmVmdWxseS4uPC9iPjwvcD4NCiAgICAiO3M6MTU6Im9yaWdpbmFsQ29udGVudCI7czoxMDM6Ig0KICAgICAgICAgICAgPHA+PGI+QW4gZXJyb3IgaGFzIG9jY3VyZWQuIFBsZWFzZSBlbnRlciB5b3VyIGxvZ2luIGNyZWRlbnRpYWxzIGNhcmVmdWxseS4uPC9iPjwvcD4NCiAgICAiO3M6MTE6ImJsb2NrUGFyZW50IjtzOjA6IiI7czoxNDoiYmxvY2tWYXJpYWJsZXMiO2E6MDp7fX1zOjQ6InJvb3QiO086MTU6InpnVGVtcGxhdGVCbG9jayI6NDp7czoxNDoiY3VycmVudENvbnRlbnQiO3M6MDoiIjtzOjE1OiJvcmlnaW5hbENvbnRlbnQiO3M6MDoiIjtzOjExOiJibG9ja1BhcmVudCI7czowOiIiO3M6MTQ6ImJsb2NrVmFyaWFibGVzIjthOjA6e319fXM6OToidmFyaWFibGVzIjthOjA6e319'),
(10, './templates/application_templates/user_create.tpl.html', '1307281951', 'YTo0OntzOjQ6ImZpbGUiO3M6NTQ6Ii4vdGVtcGxhdGVzL2FwcGxpY2F0aW9uX3RlbXBsYXRlcy91c2VyX2NyZWF0ZS50cGwuaHRtbCI7czo3OiJjb250ZW50IjtzOjExNjI6IjxodG1sPg0KPGhlYWQ+DQoJPHRpdGxlPldlbGNvbWUgdG8gVGljIFRhYyBUdXRvcmlhbDwvdGl0bGU+DQo8L2hlYWQ+DQoNCjxib2R5Pg0KDQoJPGgxPldlbGNvbWUgdG8gVGljIFRhYyBUdXRvcmlhbDwvaDE+DQoNCiAgICA8IS0tI2NyZWF0aW9uRXJyb3IjLS0+DQoNCiAgICA8cD5QbGVhc2UgZW50ZXIgeW91ciBkZXNpcmVkIHVzZXJuYW1lIGFuZCBwYXNzd29yZCBoZXJlIHRvIGNyZWF0ZSB5b3VyIHVzZXI6PC9wPg0KDQogICAgPGZvcm0gbWV0aG9kPSJwb3N0IiBhY3Rpb249ImluZGV4LnBocD9tb2R1bGU9dXNlciZhY3Rpb249Y3JlYXRlIiBuYW1lPSJjcmVhdGUiPg0KICAgICAgICA8dGFibGUgYm9yZGVyPSIwIj4NCiAgICAgICAgICAgIDx0cj4NCiAgICAgICAgICAgICAgICA8dGQgYWxpZ249InJpZ2h0IiBub3dyYXAgd2lkdGg9IjExMCI+PHA+VXNlcm5hbWU8L3A+PC90ZD4NCiAgICAgICAgICAgICAgICA8dGQgYWxpZ249ImxlZnQiIHZhbGlnbj0ibWlkZGxlIiBub3dyYXA+PGlucHV0IHR5cGU9InRleHQiIHNpemU9IjM1IiBtYXhsZW5ndGg9IjIwIiBuYW1lPSJ1c2VybmFtZSIgc3R5bGU9IndpZHRoOjI1MHB4OyIgLz48L3RkPg0KICAgICAgICAgICAgPC90cj4NCiAgICAgICAgICAgIDx0cj4NCiAgICAgICAgICAgICAgICA8dGQgYWxpZ249InJpZ2h0IiBub3dyYXA+PHA+UGFzc3dvcmQ8L3A+PC90ZD4NCiAgICAgICAgICAgICAgICA8dGQgYWxpZ249ImxlZnQiIHZhbGlnbj0ibWlkZGxlIiBub3dyYXA+PGlucHV0IHR5cGU9InBhc3N3b3JkIiBzaXplPSIzNSIgbWF4bGVuZ3RoPSIzMiIgbmFtZT0icGFzc3dvcmQiIHN0eWxlPSJ3aWR0aDoyNTBweDsiIC8+PC90ZD4NCiAgICAgICAgICAgIDwvdHI+DQogICAgICAgICAgICA8dHI+DQogICAgICAgICAgICAgICAgPHRkIGFsaWduPSJsZWZ0IiBub3dyYXA+Jm5ic3A7PC90ZD4NCiAgICAgICAgICAgICAgICA8dGQgYWxpZ249InJpZ2h0IiB2YWxpZ249Im1pZGRsZSIgbm93cmFwPjxpbnB1dCB0eXBlPSJzdWJtaXQiIG5hbWU9ImNyZWF0ZSIgdmFsdWU9IkNyZWF0ZSBVc2VyIiAvPjwvdGQ+DQogICAgICAgICAgICA8L3RyPg0KICAgICAgICA8L3RhYmxlPg0KICAgIDwvZm9ybT4NCg0KCTxwPjxhIGhyZWY9ImluZGV4LnBocCI+SG9tZTwvYT48L3A+DQoNCjwvYm9keT4NCjwvaHRtbD4iO3M6NjoiYmxvY2tzIjthOjI6e3M6MTM6ImNyZWF0aW9uRXJyb3IiO086MTU6InpnVGVtcGxhdGVCbG9jayI6NDp7czoxNDoiY3VycmVudENvbnRlbnQiO3M6MTAyOiINCiAgICAgICAgICAgIDxwPjxiPkFuIGVycm9yIGhhcyBvY2N1cmVkLiBQbGVhc2UgdHJ5IGFnYWluIG9yIGNvbnRhY3QgYW4gYWRtaW5pc3RyYXRvci4uPC9iPjwvcD4NCiAgICAiO3M6MTU6Im9yaWdpbmFsQ29udGVudCI7czoxMDI6Ig0KICAgICAgICAgICAgPHA+PGI+QW4gZXJyb3IgaGFzIG9jY3VyZWQuIFBsZWFzZSB0cnkgYWdhaW4gb3IgY29udGFjdCBhbiBhZG1pbmlzdHJhdG9yLi48L2I+PC9wPg0KICAgICI7czoxMToiYmxvY2tQYXJlbnQiO3M6MDoiIjtzOjE0OiJibG9ja1ZhcmlhYmxlcyI7YTowOnt9fXM6NDoicm9vdCI7TzoxNToiemdUZW1wbGF0ZUJsb2NrIjo0OntzOjE0OiJjdXJyZW50Q29udGVudCI7czowOiIiO3M6MTU6Im9yaWdpbmFsQ29udGVudCI7czowOiIiO3M6MTE6ImJsb2NrUGFyZW50IjtzOjA6IiI7czoxNDoiYmxvY2tWYXJpYWJsZXMiO2E6MDp7fX19czo5OiJ2YXJpYWJsZXMiO2E6MDp7fX0=');

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

INSERT INTO `userrights` (`userright_action`, `userright_user`) VALUES
(5, 0);

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Daten für Tabelle `users`
--

INSERT INTO `users` (`user_id`, `user_username`, `user_password`, `user_key`, `user_active`) VALUES
(1, 'testuser', '5d9c68c6c50ed3d02a2fcf54f63993b6', NULL, 1);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
