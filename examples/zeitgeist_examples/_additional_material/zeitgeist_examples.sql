-- phpMyAdmin SQL Dump
-- version 3.2.4
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Erstellungszeit: 11. Juli 2010 um 00:22
-- Server Version: 5.1.41
-- PHP-Version: 5.3.1

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Datenbank: `zeitgeist_examples`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `actionlog`
--

CREATE TABLE IF NOT EXISTS `actionlog` (
  `actionlog_id` int(12) NOT NULL AUTO_INCREMENT,
  `actionlog_module` int(12) NOT NULL,
  `actionlog_action` int(12) NOT NULL,
  `actionlog_ip` int(10) unsigned DEFAULT NULL,
  `actionlog_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`actionlog_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Daten für Tabelle `actionlog`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `actionlog_parameters`
--

CREATE TABLE IF NOT EXISTS `actionlog_parameters` (
  `actionparameter_id` int(12) NOT NULL AUTO_INCREMENT,
  `actionparameter_trafficid` int(12) NOT NULL,
  `actionparameter_key` varchar(64) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `actionparameter_value` text CHARACTER SET latin1 COLLATE latin1_general_ci,
  PRIMARY KEY (`actionparameter_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Daten für Tabelle `actionlog_parameters`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `actions`
--

CREATE TABLE IF NOT EXISTS `actions` (
  `action_id` int(12) NOT NULL AUTO_INCREMENT,
  `action_module` int(12) NOT NULL,
  `action_name` varchar(30) NOT NULL DEFAULT '',
  `action_description` text NOT NULL,
  `action_active` tinyint(1) NOT NULL default '0',
  PRIMARY KEY (`action_id`),
  KEY `action_module` (`action_module`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

--
-- Daten für Tabelle `actions`
--

INSERT INTO `actions` (`action_id`, `action_module`, `action_name`, `action_description`, `action_active`) VALUES
(1, 1, 'index', 'Main index action', 1),
(2, 5, 'index', 'Index for the configuration exampels', 1),
(4, 3, 'index', 'Overview of message examples', 1),
(3, 2, 'index', 'Examples for the debug class', 1),
(5, 4, 'index', 'Template Examples', 1),
(6, 6, 'index', 'Index for dataserver examples', 1),
(7, 7, 'index', 'Index for the parameterhandler examples', 1),
(8, 8, 'index', 'Examples for the userhandler', 1),
(9, 9, 'index', 'Acrion for the object handler examples', 1),
(10, 10, 'index', 'Index for the controller examples', 1);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `configurationcache`
--

CREATE TABLE IF NOT EXISTS `configurationcache` (
  `configurationcache_id` int(12) NOT NULL AUTO_INCREMENT,
  `configurationcache_name` varchar(128) NOT NULL,
  `configurationcache_timestamp` varchar(32) NOT NULL,
  `configurationcache_content` text NOT NULL,
  PRIMARY KEY (`configurationcache_id`),
  UNIQUE KEY `configuration_cache_modulename` (`configurationcache_name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=12 ;

--
-- Daten für Tabelle `configurationcache`
--

INSERT INTO `configurationcache` (`configurationcache_id`, `configurationcache_name`, `configurationcache_timestamp`, `configurationcache_content`) VALUES
(1, './zeitgeist/configuration/zeitgeist.ini', '1278537440', 'YTo5OntzOjc6Im1vZHVsZXMiO2E6Mjp7czoxMToiZm9ybWNyZWF0b3IiO3M6NDoidHJ1ZSI7czo0OiJzaG9wIjtzOjQ6InRydWUiO31zOjY6InRhYmxlcyI7YToxNDp7czoxMzoidGFibGVfYWN0aW9ucyI7czo3OiJhY3Rpb25zIjtzOjE4OiJ0YWJsZV9tZXNzYWdlY2FjaGUiO3M6MTI6Im1lc3NhZ2VjYWNoZSI7czoxMzoidGFibGVfbW9kdWxlcyI7czo3OiJtb2R1bGVzIjtzOjE3OiJ0YWJsZV9zZXNzaW9uZGF0YSI7czoxMToic2Vzc2lvbmRhdGEiO3M6MTk6InRhYmxlX3RlbXBsYXRlY2FjaGUiO3M6MTM6InRlbXBsYXRlY2FjaGUiO3M6MTE6InRhYmxlX3VzZXJzIjtzOjU6InVzZXJzIjtzOjE0OiJ0YWJsZV91c2VyZGF0YSI7czo4OiJ1c2VyZGF0YSI7czoxNjoidGFibGVfdXNlcnJpZ2h0cyI7czoxMDoidXNlcnJpZ2h0cyI7czoxNToidGFibGVfdXNlcnJvbGVzIjtzOjk6InVzZXJyb2xlcyI7czoyMDoidGFibGVfdXNlcmNoYXJhY3RlcnMiO3M6MTQ6InVzZXJjaGFyYWN0ZXJzIjtzOjI0OiJ0YWJsZV91c2Vycm9sZXNfdG9fdXNlcnMiO3M6MTg6InVzZXJyb2xlc190b191c2VycyI7czoyNjoidGFibGVfdXNlcnJvbGVzX3RvX2FjdGlvbnMiO3M6MjA6InVzZXJyb2xlc190b19hY3Rpb25zIjtzOjE4OiJ0YWJsZV91c2Vyc2Vzc2lvbnMiO3M6MTI6InVzZXJzZXNzaW9ucyI7czoyMjoidGFibGVfdXNlcmNvbmZpcm1hdGlvbiI7czoxNjoidXNlcmNvbmZpcm1hdGlvbiI7fXM6Nzoic2Vzc2lvbiI7YTozOntzOjE1OiJzZXNzaW9uX3N0b3JhZ2UiO3M6ODoiZGF0YWJhc2UiO3M6MTI6InNlc3Npb25fbmFtZSI7czoxOToiWkVJVEdFSVNUX1NFU1NJT05JRCI7czoxNjoic2Vzc2lvbl9saWZldGltZSI7czoxOiIwIjt9czo4OiJtZXNzYWdlcyI7YToxOntzOjIzOiJ1c2VfcGVyc2lzdGVudF9tZXNzYWdlcyI7czoxOiIxIjt9czo4OiJ0ZW1wbGF0ZSI7YToxNTp7czoxMjoicmV3cml0ZV91cmxzIjtzOjE6IjAiO3M6MTg6InZhcmlhYmxlU3Vic3RCZWdpbiI7czo1OiI8IS0tQCI7czoxNjoidmFyaWFibGVTdWJzdEVuZCI7czo0OiJALS0+IjtzOjE1OiJibG9ja1N1YnN0QmVnaW4iO3M6NToiPCEtLSMiO3M6MTM6ImJsb2NrU3Vic3RFbmQiO3M6NDoiIy0tPiI7czo5OiJsaW5rQmVnaW4iO3M6NDoiQEB7WyI7czo3OiJsaW5rRW5kIjtzOjQ6Il19QEAiO3M6MTM6InZhcmlhYmxlQmVnaW4iO3M6MzoiQEB7IjtzOjExOiJ2YXJpYWJsZUVuZCI7czozOiJ9QEAiO3M6MTQ6ImJsb2NrT3BlbkJlZ2luIjtzOjMwOiI8IS0tIFRlbXBsYXRlQmVnaW5CbG9jayBuYW1lPSIiO3M6MTI6ImJsb2NrT3BlbkVuZCI7czo1OiIiIC0tPiI7czoxMDoiYmxvY2tDbG9zZSI7czoyNToiPCEtLSBUZW1wbGF0ZUVuZEJsb2NrIC0tPiI7czoxOToiVXNlcm1lc3NhZ2VNZXNzYWdlcyI7czoxMjoidXNlcm1lc3NhZ2VzIjtzOjE5OiJVc2VybWVzc2FnZVdhcm5pbmdzIjtzOjEyOiJ1c2Vyd2FybmluZ3MiO3M6MTc6IlVzZXJtZXNzYWdlRXJyb3JzIjtzOjEwOiJ1c2VyZXJyb3JzIjt9czo5OiJhY3Rpb25sb2ciO2E6MTp7czoxNjoiYWN0aW9ubG9nX2FjdGl2ZSI7czoxOiIwIjt9czoxMjoiZXJyb3JoYW5kbGVyIjthOjE6e3M6MTc6ImVycm9yX3JlcG9ydGxldmVsIjtzOjE6IjIiO31zOjExOiJ1c2VyaGFuZGxlciI7YToxOntzOjE1OiJ1c2VfZG91Ymxlb3B0aW4iO3M6MToiMSI7fXM6MTA6InBhcmFtZXRlcnMiO2E6OTp7czoxNzoiZXNjYXBlX3BhcmFtZXRlcnMiO3M6MToiMSI7czo1OiJlbWFpbCI7czo2NjoiL15bXHdcLVwrXCZcKl0rKD86XC5bXHdcLVxfXCtcJlwqXSspKkAoPzpbXHctXStcLikrW2EtekEtWl17Miw3fSQvIjtzOjM6InVybCI7czo4NToiL14oZnRwfGh0dHB8aHR0cHMpOlwvXC8oXHcrOnswLDF9XHcqQCk/KFxTKykoOlswLTldKyk/KFwvfFwvKFtcdyMhOi4/Kz0mJUAhXC1cL10pKT8kLyI7czozOiJ6aXAiO3M6MTE6Ii9eXGR7Myw1fSQvIjtzOjY6InN0cmluZyI7czo2OToiL15bXHfDvMOcw6TDhMO2w5YgXSsoKFtcLFxAXC5cOlwtXC9cKFwpXCFcPyBdKT9bXHfDvMOcw6TDhMO2w5YgXSopKiQvIjtzOjQ6InRleHQiO3M6Nzk6Ii9eW1x3w7zDnMOkw4TDtsOWIF0rKChbXFxcQFwiXCxcLlw6XC1cL1xyXG5cdFwhXD9cKFwpIF0pP1tcd8O8w5zDpMOEw7bDliBdKikqJC8iO3M6NjoibnVtYmVyIjtzOjI0OiIvXlswLTldKihcLnxcLCk/WzAtOV0rJC8iO3M6NzoiYm9vbGVhbiI7czoxMjoiL15bMC0xXXsxfSQvIjtzOjQ6ImRhdGUiO3M6Mzg6Ii9eWzAtOV17Mn0oXC4pP1swLTldezJ9KFwuKT9bMC05XXs0fSQvIjt9fQ=='),
(2, './configuration/zeitgeist.ini', '1277754238', 'YToxOntzOjEzOiJvdmVyd3JpdGV0ZXN0IjthOjE6e3M6NDoidGVzdCI7czo0OiJ0cnVlIjt9fQ=='),
(3, './modules/templates/templates.ini', '1277754239', 'YToyOntzOjU6ImluZGV4IjthOjE6e3M6MjE6Imhhc0V4dGVybmFsUGFyYW1ldGVycyI7czo1OiJmYWxzZSI7fXM6OToidGVtcGxhdGVzIjthOjE6e3M6MTY6ImV4YW1wbGVfdGVtcGxhdGUiO3M6NDM6Il9hZGRpdGlvbmFsX2ZpbGVzL2V4YW1wbGVfdGVtcGxhdGUudHBsLmh0bWwiO319'),
(4, './modules/main/main.ini', '1277754238', 'YToxOntzOjU6ImluZGV4IjthOjE6e3M6MjE6Imhhc0V4dGVybmFsUGFyYW1ldGVycyI7czo1OiJmYWxzZSI7fX0='),
(5, './modules/debug/debug.ini', '1277754239', 'YToxOntzOjU6ImluZGV4IjthOjE6e3M6MjE6Imhhc0V4dGVybmFsUGFyYW1ldGVycyI7czo1OiJmYWxzZSI7fX0='),
(6, './modules/messages/messages.ini', '1277754239', 'YToxOntzOjU6ImluZGV4IjthOjE6e3M6MjE6Imhhc0V4dGVybmFsUGFyYW1ldGVycyI7czo1OiJmYWxzZSI7fX0='),
(7, './modules/configuration/configuration.ini', '1277754239', 'YToyOntzOjU6ImluZGV4IjthOjE6e3M6MjE6Imhhc0V4dGVybmFsUGFyYW1ldGVycyI7czo1OiJmYWxzZSI7fXM6MTk6ImV4YW1wbGVibG9ja19tb2R1bGUiO2E6MTp7czoxMDoiZXhhbXBsZWtleSI7czoxMjoiZXhhbXBsZXZhbHVlIjt9fQ=='),
(8, './modules/dataserver/dataserver.ini', '1277754238', 'YToxOntzOjU6ImluZGV4IjthOjE6e3M6MjE6Imhhc0V4dGVybmFsUGFyYW1ldGVycyI7czo1OiJmYWxzZSI7fX0='),
(9, './modules/parameters/parameters.ini', '1277754238', 'YToxOntzOjU6ImluZGV4IjthOjU6e3M6MjE6Imhhc0V4dGVybmFsUGFyYW1ldGVycyI7czo0OiJ0cnVlIjtzOjEwOiJ0ZXN0c3RyaW5nIjthOjU6e3M6OToicGFyYW1ldGVyIjtzOjQ6InRydWUiO3M6Njoic291cmNlIjtzOjM6IkdFVCI7czo4OiJleHBlY3RlZCI7czoxMDoiL14uezQsNX0kLyI7czo2OiJlc2NhcGUiO3M6NToiZmFsc2UiO3M6MTI6InN0cmlwc2xhc2hlcyI7czo1OiJmYWxzZSI7fXM6MTA6InRlc3RudW1iZXIiO2E6NTp7czo5OiJwYXJhbWV0ZXIiO3M6NDoidHJ1ZSI7czo2OiJzb3VyY2UiO3M6MzoiR0VUIjtzOjg6ImV4cGVjdGVkIjtzOjMxOiJbW3plaXRnZWlzdC5wYXJhbWV0ZXJzLm51bWJlcl1dIjtzOjY6ImVzY2FwZSI7czo1OiJmYWxzZSI7czoxMjoic3RyaXBzbGFzaGVzIjtzOjU6ImZhbHNlIjt9czoxMDoidGVzdGVzY2FwZSI7YTo0OntzOjk6InBhcmFtZXRlciI7czo0OiJ0cnVlIjtzOjY6InNvdXJjZSI7czozOiJHRVQiO3M6ODoiZXhwZWN0ZWQiO3M6MTE6Ii9eLnsxLDUwfSQvIjtzOjY6ImVzY2FwZSI7czo0OiJ0cnVlIjt9czoxNjoidGVzdHN0cmlwc2xhc2hlcyI7YTo1OntzOjk6InBhcmFtZXRlciI7czo0OiJ0cnVlIjtzOjY6InNvdXJjZSI7czozOiJHRVQiO3M6ODoiZXhwZWN0ZWQiO3M6MTE6Ii9eLnsxLDUwfSQvIjtzOjY6ImVzY2FwZSI7czo1OiJmYWxzZSI7czoxMjoic3RyaXBzbGFzaGVzIjtzOjQ6InRydWUiO319fQ=='),
(10, './modules/forms/forms.ini', '1277754239', 'YToxOntzOjU6ImluZGV4IjthOjM6e3M6MjE6Imhhc0V4dGVybmFsUGFyYW1ldGVycyI7czo0OiJ0cnVlIjtzOjExOiJleGFtcGxlZm9ybSI7YTozOntzOjk6InBhcmFtZXRlciI7czo0OiJ0cnVlIjtzOjY6InNvdXJjZSI7czo0OiJQT1NUIjtzOjg6ImV4cGVjdGVkIjtzOjU6IkFSUkFZIjt9czo2OiJzdWJtaXQiO2E6NDp7czo5OiJwYXJhbWV0ZXIiO3M6NDoidHJ1ZSI7czo2OiJzb3VyY2UiO3M6NDoiUE9TVCI7czo4OiJleHBlY3RlZCI7czo4OiJDT05TVEFOVCI7czo1OiJ2YWx1ZSI7czoxODoiU3VibWl0IEV4YW1wbGVmb3JtIjt9fX0='),
(11, '_additional_material/example.form.ini', '1277754235', 'YToyOntzOjQ6ImZvcm0iO2E6Mjp7czo0OiJuYW1lIjtzOjExOiJleGFtcGxlZm9ybSI7czo2OiJtZXRob2QiO3M6NDoiUE9TVCI7fXM6ODoiZWxlbWVudHMiO2E6Njp7czoxMzoic3RyaW5nZXhhbXBsZSI7YTo1OntzOjU6InZhbHVlIjtzOjA6IiI7czo4OiJyZXF1aXJlZCI7czoxOiIxIjtzOjg6ImV4cGVjdGVkIjtzOjMxOiJbW3plaXRnZWlzdC5wYXJhbWV0ZXJzLnN0cmluZ11dIjtzOjg6ImVycm9ybXNnIjtzOjI3OiJUaGlzIHdhcyBub3QgYSB2YWxpZCBzdHJpbmciO3M6NjoiZXNjYXBlIjtzOjU6ImZhbHNlIjt9czoxOToic3RyaXBzbGFzaGVzZXhhbXBsZSI7YTo1OntzOjU6InZhbHVlIjtzOjA6IiI7czo4OiJyZXF1aXJlZCI7czoxOiIxIjtzOjg6ImV4cGVjdGVkIjtzOjExOiIvXi57MSwzMn0kLyI7czo4OiJlcnJvcm1zZyI7czoyNzoiVGhpcyB3YXMgbm90IGEgdmFsaWQgc3RyaW5nIjtzOjEyOiJzdHJpcHNsYXNoZXMiO3M6NDoidHJ1ZSI7fXM6MTM6ImVzY2FwZWV4YW1wbGUiO2E6NTp7czo1OiJ2YWx1ZSI7czowOiIiO3M6ODoicmVxdWlyZWQiO3M6MToiMSI7czo4OiJleHBlY3RlZCI7czoxMToiL14uezEsMzJ9JC8iO3M6ODoiZXJyb3Jtc2ciO3M6Mjc6IlRoaXMgd2FzIG5vdCBhIHZhbGlkIHN0cmluZyI7czo2OiJlc2NhcGUiO3M6NDoidHJ1ZSI7fXM6MTM6Im51bWJlcmV4YW1wbGUiO2E6NDp7czo1OiJ2YWx1ZSI7czowOiIiO3M6ODoicmVxdWlyZWQiO3M6MToiMSI7czo4OiJleHBlY3RlZCI7czozMToiW1t6ZWl0Z2Vpc3QucGFyYW1ldGVycy5udW1iZXJdXSI7czo4OiJlcnJvcm1zZyI7czoyNzoiVGhpcyB3YXMgbm90IGEgdmFsaWQgbnVtYmVyIjt9czoxMjoiZW1haWxleGFtcGxlIjthOjQ6e3M6NToidmFsdWUiO3M6MDoiIjtzOjg6InJlcXVpcmVkIjtzOjE6IjEiO3M6ODoiZXhwZWN0ZWQiO3M6MzA6IltbemVpdGdlaXN0LnBhcmFtZXRlcnMuZW1haWxdXSI7czo4OiJlcnJvcm1zZyI7czoyNjoiVGhpcyB3YXMgbm90IGEgdmFsaWQgZW1haWwiO31zOjE1OiJ0ZXh0YXJlYWV4YW1wbGUiO2E6NDp7czo1OiJ2YWx1ZSI7czowOiIiO3M6ODoicmVxdWlyZWQiO3M6MToiMSI7czo4OiJleHBlY3RlZCI7czoyOToiW1t6ZWl0Z2Vpc3QucGFyYW1ldGVycy50ZXh0XV0iO3M6ODoiZXJyb3Jtc2ciO3M6MjU6IlRoaXMgd2FzIG5vdCBhIHZhbGlkIFRleHQiO319fQ==');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `modules`
--

CREATE TABLE IF NOT EXISTS `modules` (
  `module_id` int(12) NOT NULL AUTO_INCREMENT,
  `module_name` varchar(30) NOT NULL DEFAULT '',
  `module_description` text NOT NULL,
  `module_active` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`module_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

--
-- Daten für Tabelle `modules`
--

INSERT INTO `modules` (`module_id`, `module_name`, `module_description`, `module_active`) VALUES
(1, 'main', 'Main module', 1),
(2, 'debug', 'Examples for the debug class', 1),
(3, 'messages', 'Examples for the message class', 1),
(4, 'templates', 'Template Examples', 1),
(5, 'configuration', 'Examples for configuration', 1),
(6, 'dataserver', 'Dataserver examples', 1),
(7, 'parameters', 'Examples for the parameterhandler', 1),
(8, 'userhandler', 'Examples for the user handler', 1),
(9, 'objects', 'Examples for the object handler', 1),
(10, 'forms', 'Examples for the forms', 1);

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `sessiondata`
--

INSERT INTO `sessiondata` (`sessiondata_id`, `sessiondata_created`, `sessiondata_lastupdate`, `sessiondata_content`, `sessiondata_ip`) VALUES
('6dhf84mga4322o30fronhfvuj3', 1278799702, 1278800537, 'messagecache_session|s:2939:"a:14:{i:0;O:9:"zgMessage":4:{s:7:"message";s:106:"Info while loading the configuration from database: no configuration is stored in database for this module";s:4:"type";s:7:"warning";s:4:"from";s:23:"configuration.class.php";s:2:"to";N;}i:1;O:9:"zgMessage":4:{s:7:"message";s:106:"Info while loading the configuration from database: no configuration is stored in database for this module";s:4:"type";s:7:"warning";s:4:"from";s:23:"configuration.class.php";s:2:"to";N;}i:2;O:9:"zgMessage":4:{s:7:"message";s:98:"Error loading the configuration file ./_additional_files/example_configuration.ini: file not found";s:4:"type";s:5:"error";s:4:"from";s:23:"configuration.class.php";s:2:"to";N;}i:3;O:9:"zgMessage":4:{s:7:"message";s:65:"Problem loading the configuration: no contents could be extracted";s:4:"type";s:7:"warning";s:4:"from";s:23:"configuration.class.php";s:2:"to";N;}i:4;O:9:"zgMessage":4:{s:7:"message";s:90:"Problem reading the configuration: configuration not found (config_example - block - var1)";s:4:"type";s:7:"warning";s:4:"from";s:23:"configuration.class.php";s:2:"to";N;}i:5;O:9:"zgMessage":4:{s:7:"message";s:90:"Problem reading the configuration: configuration not found (config_example - block - var2)";s:4:"type";s:7:"warning";s:4:"from";s:23:"configuration.class.php";s:2:"to";N;}i:6;O:9:"zgMessage":4:{s:7:"message";s:52:"Problem reading the configuration: section not found";s:4:"type";s:7:"warning";s:4:"from";s:23:"configuration.class.php";s:2:"to";N;}i:7;O:9:"zgMessage":4:{s:7:"message";s:51:"Problem reading the configuration: module not found";s:4:"type";s:7:"warning";s:4:"from";s:23:"configuration.class.php";s:2:"to";N;}i:8;O:9:"zgMessage":4:{s:7:"message";s:106:"Info while loading the configuration from database: no configuration is stored in database for this module";s:4:"type";s:7:"warning";s:4:"from";s:23:"configuration.class.php";s:2:"to";N;}i:9;O:9:"zgMessage":4:{s:7:"message";s:106:"Info while loading the configuration from database: no configuration is stored in database for this module";s:4:"type";s:7:"warning";s:4:"from";s:23:"configuration.class.php";s:2:"to";N;}i:10;O:9:"zgMessage":4:{s:7:"message";s:106:"Info while loading the configuration from database: no configuration is stored in database for this module";s:4:"type";s:7:"warning";s:4:"from";s:23:"configuration.class.php";s:2:"to";N;}i:11;O:9:"zgMessage":4:{s:7:"message";s:55:"No templatedata is stored in database for this template";s:4:"type";s:7:"warning";s:4:"from";s:18:"template.class.php";s:2:"to";N;}i:12;O:9:"zgMessage":4:{s:7:"message";s:106:"Info while loading the configuration from database: no configuration is stored in database for this module";s:4:"type";s:7:"warning";s:4:"from";s:23:"configuration.class.php";s:2:"to";N;}i:13;O:9:"zgMessage":4:{s:7:"message";s:65:"Problem processing the form data: no formdata found in parameters";s:4:"type";s:7:"warning";s:4:"from";s:14:"form.class.php";s:2:"to";N;}}";', 2130706433);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `templatecache`
--

CREATE TABLE IF NOT EXISTS `templatecache` (
  `templatecache_id` int(12) NOT NULL AUTO_INCREMENT,
  `templatecache_name` varchar(128) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `templatecache_timestamp` varchar(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `templatecache_content` text CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  PRIMARY KEY (`templatecache_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Daten für Tabelle `templatecache`
--

INSERT INTO `templatecache` (`templatecache_id`, `templatecache_name`, `templatecache_timestamp`, `templatecache_content`) VALUES
(1, '_additional_material/example_template.tpl.html', '1277754235', 'YTo0OntzOjQ6ImZpbGUiO3M6NDY6Il9hZGRpdGlvbmFsX21hdGVyaWFsL2V4YW1wbGVfdGVtcGxhdGUudHBsLmh0bWwiO3M6NzoiY29udGVudCI7czoxMjQ0OiI8IURPQ1RZUEUgaHRtbCBQVUJMSUMgIi0vL1czQy8vRFREIFhIVE1MIDEuMCBUcmFuc2l0aW9uYWwvL0VOIiAiaHR0cDovL3d3dy53My5vcmcvVFIveGh0bWwxL0RURC94aHRtbDEtdHJhbnNpdGlvbmFsLmR0ZCI+DQo8aHRtbCB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94aHRtbCI+DQoJPGhlYWQ+DQoJCTx0aXRsZT4gVGVtcGxhdGUgZXhhbXBsZSA8L3RpdGxlPg0KCTwvaGVhZD4NCg0KCTxib2R5Pg0KDQoJCTwhLS0gdmFyaWFibGVzIGluIGEgdGVtcGxhdGUgYXJlIGRlZmluZWQgRHJlYW13ZWF2ZXItc3R5bGU6IDwhLS1AVkFSTkFNRUAtLT4gLS0+DQoJCTwhLS0gVXNlIHRoZSBuYW1lIHRvIGFkZHJlc3MgaXQgZnJvbSB0aGUgYXBwbGljYXRpb24gLS0+DQoJCTxwPlRoaXMgY29udGVudCBpcyBhc3NpZ25lZCBieSB0aGUgYXBwbGljYXRpb246IDxiPjwhLS1AZXhhbXBsZWNvbnRlbnRALS0+PC9iPjwvcD4NCg0KCQk8IS0tIGxpbmtzIGluc2lkZSB0aGUgYXBwbGljYXRpb24gY2FuIGJlIGRlZmluZWQgbGlrZSB0aGlzOiBpbmRleC5waHA/bW9kdWxlPU1PRFVMRSZhY3Rpb249QUNUSU9OIC0tPg0KCQk8IS0tIHRoZXkgd2lsbCBiZSBhdXRvbWF0aWNhbGx5IGdlbmVyYXRlZCB3aXRoIHRoZSBjb3JyZWN0IHBhdGhzIC0tPg0KCQk8cD5UaGlzIGlzIGEgPGI+PGEgaHJlZj0iaW5kZXgucGhwIj5saW5rIHRvIHRoZSBob21lIHBhZ2U8L2E+PC9iPi4gSXQncyBjcmVhdGVkIGF1dG9tYXRpY2FsbHkuPC9wPg0KDQoJCTwhLS0gbGlua3MgY2FuIGFsc28gYmUgY3JlYXRlZCBieSB0aGUgYXBwbGljYXRpb24uIHVzZSBhIHZhcmlhYmxlIHRvIGFzc2lnbiBpdCBpbnRvIHRoZSB0ZW1wbGF0ZSAtLT4NCgkJPHA+VGhpcyBpcyBhIDxiPjxhIGhyZWY9IjwhLS1AbWFudWFsbGlua0AtLT4iPmxpbmsgdG8gdGhlIGhvbWUgcGFnZTwvYT48L2I+LiBJdCdzIGNyZWF0ZWQgbWFudWFsbHkuPC9wPg0KICANCgkJPCEtLSBibG9ja3MgaW4gYSB0ZW1wbGF0ZSBhcmUgYWxzbyBkZWZpbmVkIERyZWFtd2VhdmVyLXN0eWxlIC0tPg0KCQk8IS0tIFVzZSB0aGUgbmFtZSB0byBhZGRyZXNzIGl0IGZyb20gdGhlIGFwcGxpY2F0aW9uIC0tPg0KCQk8IS0tI2V4YW1wbGVibG9jayMtLT4NCg0KCQk8IS0tIFRoZXNlIGFyZSB1c2VkIHRvIHNob3cgZGF0YXNldCBoYW5kbGluZyAtLT4NCgkJPHA+PGI+PCEtLUBoZWxsb0AtLT4sIDwhLS1AdGVtcGxhdGVALS0+PC9iPjwvcD4NCg0KCTwvYm9keT4NCjwvaHRtbD4NCiI7czo2OiJibG9ja3MiO2E6Mjp7czoxMjoiZXhhbXBsZWJsb2NrIjtPOjE1OiJ6Z1RlbXBsYXRlQmxvY2siOjQ6e3M6MTQ6ImN1cnJlbnRDb250ZW50IjtzOjc2OiINCgkJCTxwPlRoaXMgaXMgY29udGVudCBpbnNpZGUgYSBibG9jazogPGI+PCEtLUBibG9ja2NvbnRlbnRALS0+PC9iPjwvcD4NCgkJIjtzOjE1OiJvcmlnaW5hbENvbnRlbnQiO3M6NzY6Ig0KCQkJPHA+VGhpcyBpcyBjb250ZW50IGluc2lkZSBhIGJsb2NrOiA8Yj48IS0tQGJsb2NrY29udGVudEAtLT48L2I+PC9wPg0KCQkiO3M6MTE6ImJsb2NrUGFyZW50IjtOO3M6MTQ6ImJsb2NrVmFyaWFibGVzIjthOjE6e3M6MTI6ImJsb2NrY29udGVudCI7czoyMToiPCEtLUBibG9ja2NvbnRlbnRALS0+Ijt9fXM6NDoicm9vdCI7TzoxNToiemdUZW1wbGF0ZUJsb2NrIjo0OntzOjE0OiJjdXJyZW50Q29udGVudCI7TjtzOjE1OiJvcmlnaW5hbENvbnRlbnQiO047czoxMToiYmxvY2tQYXJlbnQiO047czoxNDoiYmxvY2tWYXJpYWJsZXMiO2E6NTp7czo3OiJWQVJOQU1FIjtzOjE2OiI8IS0tQFZBUk5BTUVALS0+IjtzOjE0OiJleGFtcGxlY29udGVudCI7czoyMzoiPCEtLUBleGFtcGxlY29udGVudEAtLT4iO3M6MTA6Im1hbnVhbGxpbmsiO3M6MTk6IjwhLS1AbWFudWFsbGlua0AtLT4iO3M6NToiaGVsbG8iO3M6MTQ6IjwhLS1AaGVsbG9ALS0+IjtzOjg6InRlbXBsYXRlIjtzOjE3OiI8IS0tQHRlbXBsYXRlQC0tPiI7fX19czo5OiJ2YXJpYWJsZXMiO2E6Njp7czoxMjoiYmxvY2tjb250ZW50IjtPOjE4OiJ6Z1RlbXBsYXRlVmFyaWFibGUiOjI6e3M6MTQ6ImN1cnJlbnRDb250ZW50IjtOO3M6MTQ6ImRlZmF1bHRDb250ZW50IjtOO31zOjc6IlZBUk5BTUUiO086MTg6InpnVGVtcGxhdGVWYXJpYWJsZSI6Mjp7czoxNDoiY3VycmVudENvbnRlbnQiO047czoxNDoiZGVmYXVsdENvbnRlbnQiO047fXM6MTQ6ImV4YW1wbGVjb250ZW50IjtPOjE4OiJ6Z1RlbXBsYXRlVmFyaWFibGUiOjI6e3M6MTQ6ImN1cnJlbnRDb250ZW50IjtOO3M6MTQ6ImRlZmF1bHRDb250ZW50IjtOO31zOjEwOiJtYW51YWxsaW5rIjtPOjE4OiJ6Z1RlbXBsYXRlVmFyaWFibGUiOjI6e3M6MTQ6ImN1cnJlbnRDb250ZW50IjtOO3M6MTQ6ImRlZmF1bHRDb250ZW50IjtOO31zOjU6ImhlbGxvIjtPOjE4OiJ6Z1RlbXBsYXRlVmFyaWFibGUiOjI6e3M6MTQ6ImN1cnJlbnRDb250ZW50IjtOO3M6MTQ6ImRlZmF1bHRDb250ZW50IjtOO31zOjg6InRlbXBsYXRlIjtPOjE4OiJ6Z1RlbXBsYXRlVmFyaWFibGUiOjI6e3M6MTQ6ImN1cnJlbnRDb250ZW50IjtOO3M6MTQ6ImRlZmF1bHRDb250ZW50IjtOO319fQ=='),
(2, '_additional_material/example_form.tpl.html', '1277754235', 'YTo0OntzOjQ6ImZpbGUiO3M6NDI6Il9hZGRpdGlvbmFsX21hdGVyaWFsL2V4YW1wbGVfZm9ybS50cGwuaHRtbCI7czo3OiJjb250ZW50IjtzOjE4Mzc6IjwhRE9DVFlQRSBodG1sIFBVQkxJQyAiLS8vVzNDLy9EVEQgWEhUTUwgMS4wIFRyYW5zaXRpb25hbC8vRU4iICJodHRwOi8vd3d3LnczLm9yZy9UUi94aHRtbDEvRFREL3hodG1sMS10cmFuc2l0aW9uYWwuZHRkIj4NCjxodG1sIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5L3hodG1sIj4NCgk8aGVhZD4NCgkJPHRpdGxlPiBGb3JtIGV4YW1wbGUgPC90aXRsZT4NCgk8L2hlYWQ+DQoNCgk8Ym9keT4NCg0KICAgICAgICA8Zm9ybSBtZXRob2Q9InBvc3QiIGFjdGlvbj0iaW5kZXgucGhwP21vZHVsZT1mb3JtcyIgbmFtZT0iZXhhbXBsZWZvcm0iIGVuY3R5cGU9Im11bHRpcGFydC9mb3JtLWRhdGE7IGNoYXJzZXQ9dXRmLTgiPg0KICAgICAgICANCiAgICAgICAgICAgIDxsYWJlbD5TdHJpbmcgRXhhbXBsZTwvbGFiZWw+DQoJCQk8aW5wdXQgdHlwZT0idGV4dCIgbmFtZT0iZXhhbXBsZWZvcm1bc3RyaW5nZXhhbXBsZV0iIHZhbHVlPSI8IS0tQHN0cmluZ2V4YW1wbGU6dmFsdWVALS0+IiAvPg0KICAgICAgICAgICAgPCEtLSNzdHJpbmdleGFtcGxlOmVycm9yYmxvY2sjLS0+DQoJCQk8YnIgLz48YnIgLz4NCiAgICAgICAgDQogICAgICAgICAgICA8bGFiZWw+U3RyaW5nIHdpdGggU3RyaXBzbGFzaGVzIEV4YW1wbGU8L2xhYmVsPg0KCQkJPGlucHV0IHR5cGU9InRleHQiIG5hbWU9ImV4YW1wbGVmb3JtW3N0cmlwc2xhc2hlc2V4YW1wbGVdIiB2YWx1ZT0iPCEtLUBzdHJpcHNsYXNoZXNleGFtcGxlOnZhbHVlQC0tPiIgLz4NCiAgICAgICAgICAgIDwhLS0jc3RyaXBzbGFzaGVzZXhhbXBsZTplcnJvcmJsb2NrIy0tPg0KCQkJPGJyIC8+PGJyIC8+DQogICAgICAgIA0KICAgICAgICAgICAgPGxhYmVsPkVzY2FwZWQgU3RyaW5nIEV4YW1wbGU8L2xhYmVsPg0KCQkJPGlucHV0IHR5cGU9InRleHQiIG5hbWU9ImV4YW1wbGVmb3JtW2VzY2FwZWV4YW1wbGVdIiB2YWx1ZT0iPCEtLUBlc2NhcGVleGFtcGxlOnZhbHVlQC0tPiIgLz4NCiAgICAgICAgICAgIDwhLS0jZXNjYXBlZXhhbXBsZTplcnJvcmJsb2NrIy0tPg0KCQkJPGJyIC8+PGJyIC8+DQoNCiAgICAgICAgICAgIDxsYWJlbD5OdW1iZXIgRXhhbXBsZTwvbGFiZWw+DQoJCQk8aW5wdXQgdHlwZT0idGV4dCIgbmFtZT0iZXhhbXBsZWZvcm1bbnVtYmVyZXhhbXBsZV0iIHZhbHVlPSI8IS0tQG51bWJlcmV4YW1wbGU6dmFsdWVALS0+IiAvPg0KICAgICAgICAgICAgPCEtLSNudW1iZXJleGFtcGxlOmVycm9yYmxvY2sjLS0+DQoJCQk8YnIgLz48YnIgLz4NCiAgICAgICAgICAgIA0KICAgICAgICAgICAgPGxhYmVsPkUtTWFpbCBFeGFtcGxlPC9sYWJlbD4NCgkJCTxpbnB1dCB0eXBlPSJ0ZXh0IiBuYW1lPSJleGFtcGxlZm9ybVtlbWFpbGV4YW1wbGVdIiB2YWx1ZT0iPCEtLUBlbWFpbGV4YW1wbGU6dmFsdWVALS0+IiAvPg0KICAgICAgICAgICAgPCEtLSNlbWFpbGV4YW1wbGU6ZXJyb3JibG9jayMtLT4gICAgICAgICAgICAgIA0KCQkJPGJyIC8+PGJyIC8+DQoNCiAgICAgICAgICAgIDxsYWJlbD5UZXh0YXJlYSBFeGFtcGxlPC9sYWJlbD4NCgkJCTx0ZXh0YXJlYSBuYW1lPSJleGFtcGxlZm9ybVt0ZXh0YXJlYWV4YW1wbGVdIiBjb2xzPSIxNSIgcm93cz0iMTAiPjwhLS1AdGV4dGFyZWFleGFtcGxlOnZhbHVlQC0tPjwvdGV4dGFyZWE+DQogICAgICAgICAgICA8IS0tI3RleHRhcmVhZXhhbXBsZTplcnJvcmJsb2NrIy0tPiAgICAgICAgICAgICAgDQoJCQk8YnIgLz48YnIgLz4NCg0KICAgICAgICAgICAgPGlucHV0IHR5cGU9InN1Ym1pdCIgbmFtZT0ic3VibWl0IiB2YWx1ZT0iU3VibWl0IEV4YW1wbGVmb3JtIiAvPg0KDQogICAgICA8L2Zvcm0+DQoNCgk8L2JvZHk+DQo8L2h0bWw+DQoiO3M6NjoiYmxvY2tzIjthOjc6e3M6MjQ6InN0cmluZ2V4YW1wbGU6ZXJyb3JibG9jayI7TzoxNToiemdUZW1wbGF0ZUJsb2NrIjo0OntzOjE0OiJjdXJyZW50Q29udGVudCI7czoxMDc6Ig0KICAgICAgICAgICAgICAgIDxiciAvPjxzcGFuIGNsYXNzPSJmb3JtZXJyb3Jtc2ciPjwhLS1Ac3RyaW5nZXhhbXBsZTplcnJvcm1lc3NhZ2VALS0+PC9zcGFuPg0KICAgICAgICAgICAgIjtzOjE1OiJvcmlnaW5hbENvbnRlbnQiO3M6MTA3OiINCiAgICAgICAgICAgICAgICA8YnIgLz48c3BhbiBjbGFzcz0iZm9ybWVycm9ybXNnIj48IS0tQHN0cmluZ2V4YW1wbGU6ZXJyb3JtZXNzYWdlQC0tPjwvc3Bhbj4NCiAgICAgICAgICAgICI7czoxMToiYmxvY2tQYXJlbnQiO047czoxNDoiYmxvY2tWYXJpYWJsZXMiO2E6MTp7czoyNjoic3RyaW5nZXhhbXBsZTplcnJvcm1lc3NhZ2UiO3M6MzU6IjwhLS1Ac3RyaW5nZXhhbXBsZTplcnJvcm1lc3NhZ2VALS0+Ijt9fXM6MzA6InN0cmlwc2xhc2hlc2V4YW1wbGU6ZXJyb3JibG9jayI7TzoxNToiemdUZW1wbGF0ZUJsb2NrIjo0OntzOjE0OiJjdXJyZW50Q29udGVudCI7czoxMTM6Ig0KICAgICAgICAgICAgICAgIDxiciAvPjxzcGFuIGNsYXNzPSJmb3JtZXJyb3Jtc2ciPjwhLS1Ac3RyaXBzbGFzaGVzZXhhbXBsZTplcnJvcm1lc3NhZ2VALS0+PC9zcGFuPg0KICAgICAgICAgICAgIjtzOjE1OiJvcmlnaW5hbENvbnRlbnQiO3M6MTEzOiINCiAgICAgICAgICAgICAgICA8YnIgLz48c3BhbiBjbGFzcz0iZm9ybWVycm9ybXNnIj48IS0tQHN0cmlwc2xhc2hlc2V4YW1wbGU6ZXJyb3JtZXNzYWdlQC0tPjwvc3Bhbj4NCiAgICAgICAgICAgICI7czoxMToiYmxvY2tQYXJlbnQiO047czoxNDoiYmxvY2tWYXJpYWJsZXMiO2E6MTp7czozMjoic3RyaXBzbGFzaGVzZXhhbXBsZTplcnJvcm1lc3NhZ2UiO3M6NDE6IjwhLS1Ac3RyaXBzbGFzaGVzZXhhbXBsZTplcnJvcm1lc3NhZ2VALS0+Ijt9fXM6MjQ6ImVzY2FwZWV4YW1wbGU6ZXJyb3JibG9jayI7TzoxNToiemdUZW1wbGF0ZUJsb2NrIjo0OntzOjE0OiJjdXJyZW50Q29udGVudCI7czoxMDc6Ig0KICAgICAgICAgICAgICAgIDxiciAvPjxzcGFuIGNsYXNzPSJmb3JtZXJyb3Jtc2ciPjwhLS1AZXNjYXBlZXhhbXBsZTplcnJvcm1lc3NhZ2VALS0+PC9zcGFuPg0KICAgICAgICAgICAgIjtzOjE1OiJvcmlnaW5hbENvbnRlbnQiO3M6MTA3OiINCiAgICAgICAgICAgICAgICA8YnIgLz48c3BhbiBjbGFzcz0iZm9ybWVycm9ybXNnIj48IS0tQGVzY2FwZWV4YW1wbGU6ZXJyb3JtZXNzYWdlQC0tPjwvc3Bhbj4NCiAgICAgICAgICAgICI7czoxMToiYmxvY2tQYXJlbnQiO047czoxNDoiYmxvY2tWYXJpYWJsZXMiO2E6MTp7czoyNjoiZXNjYXBlZXhhbXBsZTplcnJvcm1lc3NhZ2UiO3M6MzU6IjwhLS1AZXNjYXBlZXhhbXBsZTplcnJvcm1lc3NhZ2VALS0+Ijt9fXM6MjQ6Im51bWJlcmV4YW1wbGU6ZXJyb3JibG9jayI7TzoxNToiemdUZW1wbGF0ZUJsb2NrIjo0OntzOjE0OiJjdXJyZW50Q29udGVudCI7czoxMDc6Ig0KICAgICAgICAgICAgICAgIDxiciAvPjxzcGFuIGNsYXNzPSJmb3JtZXJyb3Jtc2ciPjwhLS1AbnVtYmVyZXhhbXBsZTplcnJvcm1lc3NhZ2VALS0+PC9zcGFuPg0KICAgICAgICAgICAgIjtzOjE1OiJvcmlnaW5hbENvbnRlbnQiO3M6MTA3OiINCiAgICAgICAgICAgICAgICA8YnIgLz48c3BhbiBjbGFzcz0iZm9ybWVycm9ybXNnIj48IS0tQG51bWJlcmV4YW1wbGU6ZXJyb3JtZXNzYWdlQC0tPjwvc3Bhbj4NCiAgICAgICAgICAgICI7czoxMToiYmxvY2tQYXJlbnQiO047czoxNDoiYmxvY2tWYXJpYWJsZXMiO2E6MTp7czoyNjoibnVtYmVyZXhhbXBsZTplcnJvcm1lc3NhZ2UiO3M6MzU6IjwhLS1AbnVtYmVyZXhhbXBsZTplcnJvcm1lc3NhZ2VALS0+Ijt9fXM6MjM6ImVtYWlsZXhhbXBsZTplcnJvcmJsb2NrIjtPOjE1OiJ6Z1RlbXBsYXRlQmxvY2siOjQ6e3M6MTQ6ImN1cnJlbnRDb250ZW50IjtzOjEwNjoiDQogICAgICAgICAgICAgICAgPGJyIC8+PHNwYW4gY2xhc3M9ImZvcm1lcnJvcm1zZyI+PCEtLUBlbWFpbGV4YW1wbGU6ZXJyb3JtZXNzYWdlQC0tPjwvc3Bhbj4NCiAgICAgICAgICAgICI7czoxNToib3JpZ2luYWxDb250ZW50IjtzOjEwNjoiDQogICAgICAgICAgICAgICAgPGJyIC8+PHNwYW4gY2xhc3M9ImZvcm1lcnJvcm1zZyI+PCEtLUBlbWFpbGV4YW1wbGU6ZXJyb3JtZXNzYWdlQC0tPjwvc3Bhbj4NCiAgICAgICAgICAgICI7czoxMToiYmxvY2tQYXJlbnQiO047czoxNDoiYmxvY2tWYXJpYWJsZXMiO2E6MTp7czoyNToiZW1haWxleGFtcGxlOmVycm9ybWVzc2FnZSI7czozNDoiPCEtLUBlbWFpbGV4YW1wbGU6ZXJyb3JtZXNzYWdlQC0tPiI7fX1zOjI2OiJ0ZXh0YXJlYWV4YW1wbGU6ZXJyb3JibG9jayI7TzoxNToiemdUZW1wbGF0ZUJsb2NrIjo0OntzOjE0OiJjdXJyZW50Q29udGVudCI7czoxMDk6Ig0KICAgICAgICAgICAgICAgIDxiciAvPjxzcGFuIGNsYXNzPSJmb3JtZXJyb3Jtc2ciPjwhLS1AdGV4dGFyZWFleGFtcGxlOmVycm9ybWVzc2FnZUAtLT48L3NwYW4+DQogICAgICAgICAgICAiO3M6MTU6Im9yaWdpbmFsQ29udGVudCI7czoxMDk6Ig0KICAgICAgICAgICAgICAgIDxiciAvPjxzcGFuIGNsYXNzPSJmb3JtZXJyb3Jtc2ciPjwhLS1AdGV4dGFyZWFleGFtcGxlOmVycm9ybWVzc2FnZUAtLT48L3NwYW4+DQogICAgICAgICAgICAiO3M6MTE6ImJsb2NrUGFyZW50IjtOO3M6MTQ6ImJsb2NrVmFyaWFibGVzIjthOjE6e3M6Mjg6InRleHRhcmVhZXhhbXBsZTplcnJvcm1lc3NhZ2UiO3M6Mzc6IjwhLS1AdGV4dGFyZWFleGFtcGxlOmVycm9ybWVzc2FnZUAtLT4iO319czo0OiJyb290IjtPOjE1OiJ6Z1RlbXBsYXRlQmxvY2siOjQ6e3M6MTQ6ImN1cnJlbnRDb250ZW50IjtOO3M6MTU6Im9yaWdpbmFsQ29udGVudCI7TjtzOjExOiJibG9ja1BhcmVudCI7TjtzOjE0OiJibG9ja1ZhcmlhYmxlcyI7YTo2OntzOjE5OiJzdHJpbmdleGFtcGxlOnZhbHVlIjtzOjI4OiI8IS0tQHN0cmluZ2V4YW1wbGU6dmFsdWVALS0+IjtzOjI1OiJzdHJpcHNsYXNoZXNleGFtcGxlOnZhbHVlIjtzOjM0OiI8IS0tQHN0cmlwc2xhc2hlc2V4YW1wbGU6dmFsdWVALS0+IjtzOjE5OiJlc2NhcGVleGFtcGxlOnZhbHVlIjtzOjI4OiI8IS0tQGVzY2FwZWV4YW1wbGU6dmFsdWVALS0+IjtzOjE5OiJudW1iZXJleGFtcGxlOnZhbHVlIjtzOjI4OiI8IS0tQG51bWJlcmV4YW1wbGU6dmFsdWVALS0+IjtzOjE4OiJlbWFpbGV4YW1wbGU6dmFsdWUiO3M6Mjc6IjwhLS1AZW1haWxleGFtcGxlOnZhbHVlQC0tPiI7czoyMToidGV4dGFyZWFleGFtcGxlOnZhbHVlIjtzOjMwOiI8IS0tQHRleHRhcmVhZXhhbXBsZTp2YWx1ZUAtLT4iO319fXM6OToidmFyaWFibGVzIjthOjEyOntzOjI2OiJzdHJpbmdleGFtcGxlOmVycm9ybWVzc2FnZSI7TzoxODoiemdUZW1wbGF0ZVZhcmlhYmxlIjoyOntzOjE0OiJjdXJyZW50Q29udGVudCI7TjtzOjE0OiJkZWZhdWx0Q29udGVudCI7Tjt9czozMjoic3RyaXBzbGFzaGVzZXhhbXBsZTplcnJvcm1lc3NhZ2UiO086MTg6InpnVGVtcGxhdGVWYXJpYWJsZSI6Mjp7czoxNDoiY3VycmVudENvbnRlbnQiO047czoxNDoiZGVmYXVsdENvbnRlbnQiO047fXM6MjY6ImVzY2FwZWV4YW1wbGU6ZXJyb3JtZXNzYWdlIjtPOjE4OiJ6Z1RlbXBsYXRlVmFyaWFibGUiOjI6e3M6MTQ6ImN1cnJlbnRDb250ZW50IjtOO3M6MTQ6ImRlZmF1bHRDb250ZW50IjtOO31zOjI2OiJudW1iZXJleGFtcGxlOmVycm9ybWVzc2FnZSI7TzoxODoiemdUZW1wbGF0ZVZhcmlhYmxlIjoyOntzOjE0OiJjdXJyZW50Q29udGVudCI7TjtzOjE0OiJkZWZhdWx0Q29udGVudCI7Tjt9czoyNToiZW1haWxleGFtcGxlOmVycm9ybWVzc2FnZSI7TzoxODoiemdUZW1wbGF0ZVZhcmlhYmxlIjoyOntzOjE0OiJjdXJyZW50Q29udGVudCI7TjtzOjE0OiJkZWZhdWx0Q29udGVudCI7Tjt9czoyODoidGV4dGFyZWFleGFtcGxlOmVycm9ybWVzc2FnZSI7TzoxODoiemdUZW1wbGF0ZVZhcmlhYmxlIjoyOntzOjE0OiJjdXJyZW50Q29udGVudCI7TjtzOjE0OiJkZWZhdWx0Q29udGVudCI7Tjt9czoxOToic3RyaW5nZXhhbXBsZTp2YWx1ZSI7TzoxODoiemdUZW1wbGF0ZVZhcmlhYmxlIjoyOntzOjE0OiJjdXJyZW50Q29udGVudCI7TjtzOjE0OiJkZWZhdWx0Q29udGVudCI7Tjt9czoyNToic3RyaXBzbGFzaGVzZXhhbXBsZTp2YWx1ZSI7TzoxODoiemdUZW1wbGF0ZVZhcmlhYmxlIjoyOntzOjE0OiJjdXJyZW50Q29udGVudCI7TjtzOjE0OiJkZWZhdWx0Q29udGVudCI7Tjt9czoxOToiZXNjYXBlZXhhbXBsZTp2YWx1ZSI7TzoxODoiemdUZW1wbGF0ZVZhcmlhYmxlIjoyOntzOjE0OiJjdXJyZW50Q29udGVudCI7TjtzOjE0OiJkZWZhdWx0Q29udGVudCI7Tjt9czoxOToibnVtYmVyZXhhbXBsZTp2YWx1ZSI7TzoxODoiemdUZW1wbGF0ZVZhcmlhYmxlIjoyOntzOjE0OiJjdXJyZW50Q29udGVudCI7TjtzOjE0OiJkZWZhdWx0Q29udGVudCI7Tjt9czoxODoiZW1haWxleGFtcGxlOnZhbHVlIjtPOjE4OiJ6Z1RlbXBsYXRlVmFyaWFibGUiOjI6e3M6MTQ6ImN1cnJlbnRDb250ZW50IjtOO3M6MTQ6ImRlZmF1bHRDb250ZW50IjtOO31zOjIxOiJ0ZXh0YXJlYWV4YW1wbGU6dmFsdWUiO086MTg6InpnVGVtcGxhdGVWYXJpYWJsZSI6Mjp7czoxNDoiY3VycmVudENvbnRlbnQiO047czoxNDoiZGVmYXVsdENvbnRlbnQiO047fX19');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `userconfirmation`
--

CREATE TABLE IF NOT EXISTS `userconfirmation` (
  `userconfirmation_user` int(12) NOT NULL,
  `userconfirmation_key` varchar(100) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  PRIMARY KEY (`userconfirmation_user`,`userconfirmation_key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `userconfirmation`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `userdata`
--

CREATE TABLE IF NOT EXISTS `userdata` (
  `userdata_id` int(12) NOT NULL AUTO_INCREMENT,
  `userdata_user` int(12) NOT NULL,
  `userdata_username` varchar(100) DEFAULT NULL,
  `userdata_url` varchar(255) DEFAULT NULL,
  `userdata_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`userdata_id`),
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
  `userright_action` int(12) NOT NULL DEFAULT '0',
  `userright_user` int(12) NOT NULL DEFAULT '0',
  PRIMARY KEY (`userright_action`,`userright_user`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `userrights`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `userroles`
--

CREATE TABLE IF NOT EXISTS `userroles` (
  `userrole_id` int(12) NOT NULL AUTO_INCREMENT,
  `userrole_name` varchar(30) NOT NULL DEFAULT '',
  `userrole_description` text,
  PRIMARY KEY (`userrole_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Daten für Tabelle `userroles`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `userroles_to_actions`
--

CREATE TABLE IF NOT EXISTS `userroles_to_actions` (
  `userroleaction_userrole` int(12) NOT NULL DEFAULT '0',
  `userroleaction_action` int(12) NOT NULL DEFAULT '0',
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
  `userroleuser_userrole` int(12) NOT NULL DEFAULT '0',
  `userroleuser_user` int(12) NOT NULL DEFAULT '0',
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
  `user_id` int(12) NOT NULL AUTO_INCREMENT,
  `user_username` varchar(255) NOT NULL,
  `user_password` varchar(255) NOT NULL DEFAULT '',
  `user_key` varchar(64) DEFAULT NULL,
  `user_active` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Daten für Tabelle `users`
--


/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
