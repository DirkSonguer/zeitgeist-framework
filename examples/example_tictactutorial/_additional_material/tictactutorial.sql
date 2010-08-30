-- phpMyAdmin SQL Dump
-- version 3.2.4
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Erstellungszeit: 30. August 2010 um 06:39
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Daten für Tabelle `actions`
--

INSERT INTO `actions` (`action_id`, `action_module`, `action_name`, `action_description`, `action_active`) VALUES
(1, 1, 'index', 'The default action for the main module', 1);

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

--
-- Daten für Tabelle `configurationcache`
--

INSERT INTO `configurationcache` (`configurationcache_id`, `configurationcache_name`, `configurationcache_timestamp`, `configurationcache_content`) VALUES
(2, './configuration/zeitgeist.ini', '1277747985', 'YTozOntzOjc6InNlc3Npb24iO2E6Mzp7czoxNToic2Vzc2lvbl9zdG9yYWdlIjtzOjg6ImRhdGFiYXNlIjtzOjEyOiJzZXNzaW9uX25hbWUiO3M6MTM6IlpHQVBQTElDQVRJT04iO3M6MTY6InNlc3Npb25fbGlmZXRpbWUiO3M6MToiMCI7fXM6MTM6InRyYWZmaWNsb2dnZXIiO2E6MTp7czoyMDoidHJhZmZpY2xvZ2dlcl9hY3RpdmUiO3M6MToiMSI7fXM6ODoibWVzc2FnZXMiO2E6MTp7czoyMzoidXNlX3BlcnNpc3RlbnRfbWVzc2FnZXMiO3M6MToiMSI7fX0='),
(4, './zeitgeist/configuration/zgfacebook.ini', '1265534293', 'YToyOntzOjM6ImFwaSI7YToyOntzOjc6ImFwaV9rZXkiO3M6NzoiQVBJX0tFWSI7czoxMDoic2VjcmV0X2tleSI7czoxMDoiU0VDUkVUX0tFWSI7fXM6NjoidGFibGVzIjthOjE6e3M6MTk6InRhYmxlX2ZhY2Vib29rdXNlcnMiO3M6MTQ6InVzZXJzX2ZhY2Vib29rIjt9fQ=='),
(5, './modules/main/main.ini', '1277747985', 'YToxOntzOjU6ImluZGV4IjthOjE6e3M6MjE6Imhhc0V4dGVybmFsUGFyYW1ldGVycyI7czo1OiJmYWxzZSI7fX0='),
(6, './zeitgeist/configuration/zeitgeist.ini', '1282678190', 'YTo5OntzOjc6Im1vZHVsZXMiO2E6Mjp7czoxMToiZm9ybWNyZWF0b3IiO3M6NDoidHJ1ZSI7czo0OiJzaG9wIjtzOjQ6InRydWUiO31zOjY6InRhYmxlcyI7YToxNTp7czoxNToidGFibGVfYWN0aW9ubG9nIjtzOjk6ImFjdGlvbmxvZyI7czoxMzoidGFibGVfYWN0aW9ucyI7czo3OiJhY3Rpb25zIjtzOjE4OiJ0YWJsZV9tZXNzYWdlY2FjaGUiO3M6MTI6Im1lc3NhZ2VjYWNoZSI7czoxMzoidGFibGVfbW9kdWxlcyI7czo3OiJtb2R1bGVzIjtzOjE3OiJ0YWJsZV9zZXNzaW9uZGF0YSI7czoxMToic2Vzc2lvbmRhdGEiO3M6MTk6InRhYmxlX3RlbXBsYXRlY2FjaGUiO3M6MTM6InRlbXBsYXRlY2FjaGUiO3M6MTE6InRhYmxlX3VzZXJzIjtzOjU6InVzZXJzIjtzOjE0OiJ0YWJsZV91c2VyZGF0YSI7czo4OiJ1c2VyZGF0YSI7czoxNjoidGFibGVfdXNlcnJpZ2h0cyI7czoxMDoidXNlcnJpZ2h0cyI7czoxNToidGFibGVfdXNlcnJvbGVzIjtzOjk6InVzZXJyb2xlcyI7czoyMDoidGFibGVfdXNlcmNoYXJhY3RlcnMiO3M6MTQ6InVzZXJjaGFyYWN0ZXJzIjtzOjI0OiJ0YWJsZV91c2Vycm9sZXNfdG9fdXNlcnMiO3M6MTg6InVzZXJyb2xlc190b191c2VycyI7czoyNjoidGFibGVfdXNlcnJvbGVzX3RvX2FjdGlvbnMiO3M6MjA6InVzZXJyb2xlc190b19hY3Rpb25zIjtzOjE4OiJ0YWJsZV91c2Vyc2Vzc2lvbnMiO3M6MTI6InVzZXJzZXNzaW9ucyI7czoyMjoidGFibGVfdXNlcmNvbmZpcm1hdGlvbiI7czoxNjoidXNlcmNvbmZpcm1hdGlvbiI7fXM6Nzoic2Vzc2lvbiI7YTozOntzOjE1OiJzZXNzaW9uX3N0b3JhZ2UiO3M6ODoiZGF0YWJhc2UiO3M6MTI6InNlc3Npb25fbmFtZSI7czoxOToiWkVJVEdFSVNUX1NFU1NJT05JRCI7czoxNjoic2Vzc2lvbl9saWZldGltZSI7czoxOiIwIjt9czo4OiJtZXNzYWdlcyI7YToxOntzOjIzOiJ1c2VfcGVyc2lzdGVudF9tZXNzYWdlcyI7czoxOiIxIjt9czo4OiJ0ZW1wbGF0ZSI7YToxNTp7czoxMjoicmV3cml0ZV91cmxzIjtzOjE6IjAiO3M6MTg6InZhcmlhYmxlU3Vic3RCZWdpbiI7czo1OiI8IS0tQCI7czoxNjoidmFyaWFibGVTdWJzdEVuZCI7czo0OiJALS0+IjtzOjE1OiJibG9ja1N1YnN0QmVnaW4iO3M6NToiPCEtLSMiO3M6MTM6ImJsb2NrU3Vic3RFbmQiO3M6NDoiIy0tPiI7czo5OiJsaW5rQmVnaW4iO3M6NDoiQEB7WyI7czo3OiJsaW5rRW5kIjtzOjQ6Il19QEAiO3M6MTM6InZhcmlhYmxlQmVnaW4iO3M6MzoiQEB7IjtzOjExOiJ2YXJpYWJsZUVuZCI7czozOiJ9QEAiO3M6MTQ6ImJsb2NrT3BlbkJlZ2luIjtzOjMwOiI8IS0tIFRlbXBsYXRlQmVnaW5CbG9jayBuYW1lPSIiO3M6MTI6ImJsb2NrT3BlbkVuZCI7czo1OiIiIC0tPiI7czoxMDoiYmxvY2tDbG9zZSI7czoyNToiPCEtLSBUZW1wbGF0ZUVuZEJsb2NrIC0tPiI7czoxOToiVXNlcm1lc3NhZ2VNZXNzYWdlcyI7czoxMjoidXNlcm1lc3NhZ2VzIjtzOjE5OiJVc2VybWVzc2FnZVdhcm5pbmdzIjtzOjEyOiJ1c2Vyd2FybmluZ3MiO3M6MTc6IlVzZXJtZXNzYWdlRXJyb3JzIjtzOjEwOiJ1c2VyZXJyb3JzIjt9czo5OiJhY3Rpb25sb2ciO2E6MTp7czoxNjoiYWN0aW9ubG9nX2FjdGl2ZSI7czoxOiIwIjt9czoxMjoiZXJyb3JoYW5kbGVyIjthOjE6e3M6MTc6ImVycm9yX3JlcG9ydGxldmVsIjtzOjE6IjIiO31zOjExOiJ1c2VyaGFuZGxlciI7YToxOntzOjE1OiJ1c2VfZG91Ymxlb3B0aW4iO3M6MToiMSI7fXM6MTA6InBhcmFtZXRlcnMiO2E6OTp7czoxNzoiZXNjYXBlX3BhcmFtZXRlcnMiO3M6MToiMSI7czo1OiJlbWFpbCI7czo2NjoiL15bXHdcLVwrXCZcKl0rKD86XC5bXHdcLVxfXCtcJlwqXSspKkAoPzpbXHctXStcLikrW2EtekEtWl17Miw3fSQvIjtzOjM6InVybCI7czo4NToiL14oZnRwfGh0dHB8aHR0cHMpOlwvXC8oXHcrOnswLDF9XHcqQCk/KFxTKykoOlswLTldKyk/KFwvfFwvKFtcdyMhOi4/Kz0mJUAhXC1cL10pKT8kLyI7czozOiJ6aXAiO3M6MTE6Ii9eXGR7Myw1fSQvIjtzOjY6InN0cmluZyI7czo2OToiL15bXHfDvMOcw6TDhMO2w5YgXSsoKFtcLFxAXC5cOlwtXC9cKFwpXCFcPyBdKT9bXHfDvMOcw6TDhMO2w5YgXSopKiQvIjtzOjQ6InRleHQiO3M6Nzk6Ii9eW1x3w7zDnMOkw4TDtsOWIF0rKChbXFxcQFwiXCxcLlw6XC1cL1xyXG5cdFwhXD9cKFwpIF0pP1tcd8O8w5zDpMOEw7bDliBdKikqJC8iO3M6NjoibnVtYmVyIjtzOjI0OiIvXlswLTldKihcLnxcLCk/WzAtOV0rJC8iO3M6NzoiYm9vbGVhbiI7czoxMjoiL15bMC0xXXsxfSQvIjtzOjQ6ImRhdGUiO3M6Mzg6Ii9eWzAtOV17Mn0oXC4pP1swLTldezJ9KFwuKT9bMC05XXs0fSQvIjt9fQ=='),
(8, 'configuration/application.ini', '1283031751', 'YToyOntzOjExOiJhcHBsaWNhdGlvbiI7YTozOntzOjg6ImJhc2VwYXRoIjtzOjMyOiJodHRwOi8vMTI3LjAuMC4xL3RpY3RhY3R1dG9yaWFsLyI7czoxMjoidGVtcGxhdGVwYXRoIjtzOjMzOiIuL3RlbXBsYXRlcy9hcHBsaWNhdGlvbl90ZW1wbGF0ZXMiO3M6MTE6InZlcnNpb25pbmZvIjtzOjE5OiJBcHBsaWNhdGlvbiBWZXJzaW9uIjt9czoxNDoicGFyYW1ldGVydHlwZXMiO2E6Mjp7czo4OiJ1c2VybmFtZSI7czoyOToiL15bXHfDvMOcw7bDlsOkw4TDnyBdezQsMTZ9JC8iO3M6MTI6InVzZXJwYXNzd29yZCI7czoxMToiL14uezQsMTZ9JC8iO319');

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Daten für Tabelle `modules`
--

INSERT INTO `modules` (`module_id`, `module_name`, `module_description`, `module_active`) VALUES
(1, 'main', 'The default module', 1);

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
('cje99jeig281peg9bddma3gf97', 1277757781, 1277759057, 'messagecache_session|s:537:"a:3:{i:0;O:9:"zgMessage":4:{s:7:"message";s:62:"Could not establish user session: user id not found in session";s:4:"type";s:7:"warning";s:4:"from";s:21:"userhandler.class.php";s:2:"to";N;}i:1;O:9:"zgMessage":4:{s:7:"message";s:50:"No messagedata is stored in database for this user";s:4:"type";s:7:"warning";s:4:"from";s:18:"messages.class.php";s:2:"to";N;}i:2;O:9:"zgMessage":4:{s:7:"message";s:54:"No configuration is stored in database for this module";s:4:"type";s:7:"warning";s:4:"from";s:23:"configuration.class.php";s:2:"to";N;}}";', 2130706433),
('fjet29ubo83hr6mrs1j09cobf4', 1283025438, 1283142505, 'messagecache_session|s:3787:"a:19:{i:0;O:9:"zgMessage":4:{s:7:"message";s:62:"Could not establish user session: user id not found in session";s:4:"type";s:7:"warning";s:4:"from";s:21:"userhandler.class.php";s:2:"to";s:0:"";}i:1;O:9:"zgMessage":4:{s:7:"message";s:62:"Could not establish user session: user id not found in session";s:4:"type";s:7:"warning";s:4:"from";s:21:"userhandler.class.php";s:2:"to";s:0:"";}i:2;O:9:"zgMessage":4:{s:7:"message";s:62:"Could not establish user session: user id not found in session";s:4:"type";s:7:"warning";s:4:"from";s:21:"userhandler.class.php";s:2:"to";s:0:"";}i:3;O:9:"zgMessage":4:{s:7:"message";s:62:"Could not establish user session: user id not found in session";s:4:"type";s:7:"warning";s:4:"from";s:21:"userhandler.class.php";s:2:"to";s:0:"";}i:4;O:9:"zgMessage":4:{s:7:"message";s:98:"Info while loading the configuration from database: configuration data in the database is outdated";s:4:"type";s:7:"warning";s:4:"from";s:23:"configuration.class.php";s:2:"to";s:0:"";}i:5;O:9:"zgMessage":4:{s:7:"message";s:62:"Could not establish user session: user id not found in session";s:4:"type";s:7:"warning";s:4:"from";s:21:"userhandler.class.php";s:2:"to";s:0:"";}i:6;O:9:"zgMessage":4:{s:7:"message";s:98:"Info while loading the configuration from database: configuration data in the database is outdated";s:4:"type";s:7:"warning";s:4:"from";s:23:"configuration.class.php";s:2:"to";s:0:"";}i:7;O:9:"zgMessage":4:{s:7:"message";s:62:"Could not establish user session: user id not found in session";s:4:"type";s:7:"warning";s:4:"from";s:21:"userhandler.class.php";s:2:"to";s:0:"";}i:8;O:9:"zgMessage":4:{s:7:"message";s:62:"Could not establish user session: user id not found in session";s:4:"type";s:7:"warning";s:4:"from";s:21:"userhandler.class.php";s:2:"to";s:0:"";}i:9;O:9:"zgMessage":4:{s:7:"message";s:62:"Could not establish user session: user id not found in session";s:4:"type";s:7:"warning";s:4:"from";s:21:"userhandler.class.php";s:2:"to";s:0:"";}i:10;O:9:"zgMessage":4:{s:7:"message";s:62:"Could not establish user session: user id not found in session";s:4:"type";s:7:"warning";s:4:"from";s:21:"userhandler.class.php";s:2:"to";s:0:"";}i:11;O:9:"zgMessage":4:{s:7:"message";s:49:"No messagedata is stored in session for this user";s:4:"type";s:7:"warning";s:4:"from";s:18:"messages.class.php";s:2:"to";s:0:"";}i:12;O:9:"zgMessage":4:{s:7:"message";s:104:"Problem reading the configuration: configuration not found (tictactutorial - application - templatepath)";s:4:"type";s:7:"warning";s:4:"from";s:23:"configuration.class.php";s:2:"to";s:0:"";}i:13;O:9:"zgMessage":4:{s:7:"message";s:94:"Problem loading the template: could not find the template file: /templates/main_index.tpl.html";s:4:"type";s:7:"warning";s:4:"from";s:18:"template.class.php";s:2:"to";s:0:"";}i:14;O:9:"zgMessage":4:{s:7:"message";s:115:"Problem loading the template: could not find the template file: application_templates/templates/main_index.tpl.html";s:4:"type";s:7:"warning";s:4:"from";s:18:"template.class.php";s:2:"to";s:0:"";}i:15;O:9:"zgMessage":4:{s:7:"message";s:117:"Problem loading the template: could not find the template file: ./application_templates/templates/main_index.tpl.html";s:4:"type";s:7:"warning";s:4:"from";s:18:"template.class.php";s:2:"to";s:0:"";}i:16;O:9:"zgMessage":4:{s:7:"message";s:55:"No templatedata is stored in database for this template";s:4:"type";s:7:"warning";s:4:"from";s:18:"template.class.php";s:2:"to";s:0:"";}i:17;O:9:"zgMessage":4:{s:7:"message";s:41:"Template data in the database is outdated";s:4:"type";s:7:"message";s:4:"from";s:18:"template.class.php";s:2:"to";s:0:"";}i:18;O:9:"zgMessage":4:{s:7:"message";s:41:"Template data in the database is outdated";s:4:"type";s:7:"message";s:4:"from";s:18:"template.class.php";s:2:"to";s:0:"";}}";', 2130706433);

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Daten für Tabelle `templatecache`
--

INSERT INTO `templatecache` (`templatecache_id`, `templatecache_name`, `templatecache_timestamp`, `templatecache_content`) VALUES
(3, './templates/application_templates/main_index.tpl.html', '1283142470', 'YTo0OntzOjQ6ImZpbGUiO3M6NTM6Ii4vdGVtcGxhdGVzL2FwcGxpY2F0aW9uX3RlbXBsYXRlcy9tYWluX2luZGV4LnRwbC5odG1sIjtzOjc6ImNvbnRlbnQiO3M6MTU3OiI8aHRtbD4NCjxoZWFkPg0KCTx0aXRsZT5XZWxjb21lIHRvIFRpYyBUYWMgVHV0b3JpYWw8L3RpdGxlPg0KPC9oZWFkPg0KDQo8Ym9keT4NCg0KCTxoMT48IS0tQGhlYWRsaW5lQC0tPjwvaDE+DQoNCgk8cD48IS0tQGNvbnRlbnRALS0+PC9wPg0KDQo8L2JvZHk+DQo8L2h0bWw+IjtzOjY6ImJsb2NrcyI7YToxOntzOjQ6InJvb3QiO086MTU6InpnVGVtcGxhdGVCbG9jayI6NDp7czoxNDoiY3VycmVudENvbnRlbnQiO3M6MDoiIjtzOjE1OiJvcmlnaW5hbENvbnRlbnQiO3M6MDoiIjtzOjExOiJibG9ja1BhcmVudCI7czowOiIiO3M6MTQ6ImJsb2NrVmFyaWFibGVzIjthOjI6e3M6ODoiaGVhZGxpbmUiO3M6MTc6IjwhLS1AaGVhZGxpbmVALS0+IjtzOjc6ImNvbnRlbnQiO3M6MTY6IjwhLS1AY29udGVudEAtLT4iO319fXM6OToidmFyaWFibGVzIjthOjI6e3M6ODoiaGVhZGxpbmUiO086MTg6InpnVGVtcGxhdGVWYXJpYWJsZSI6Mjp7czoxNDoiY3VycmVudENvbnRlbnQiO3M6MDoiIjtzOjE0OiJkZWZhdWx0Q29udGVudCI7czowOiIiO31zOjc6ImNvbnRlbnQiO086MTg6InpnVGVtcGxhdGVWYXJpYWJsZSI6Mjp7czoxNDoiY3VycmVudENvbnRlbnQiO3M6MDoiIjtzOjE0OiJkZWZhdWx0Q29udGVudCI7czowOiIiO319fQ==');

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


/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
