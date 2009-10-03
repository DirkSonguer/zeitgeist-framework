-- phpMyAdmin SQL Dump
-- version 3.1.3.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Erstellungszeit: 03. Oktober 2009 um 08:13
-- Server Version: 5.1.33
-- PHP-Version: 5.2.9

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Daten für Tabelle `actionlog`
--

INSERT INTO `actionlog` (`actionlog_id`, `actionlog_module`, `actionlog_action`, `actionlog_ip`, `actionlog_timestamp`) VALUES
(1, 1360, 405, 2130706433, '2009-09-14 14:38:03');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `actionlog_parameters`
--

CREATE TABLE IF NOT EXISTS `actionlog_parameters` (
  `actionparameters_id` int(12) NOT NULL AUTO_INCREMENT,
  `actionparameters_trafficid` int(12) NOT NULL,
  `actionparameters_key` varchar(64) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `actionparameters_value` text CHARACTER SET latin1 COLLATE latin1_general_ci,
  PRIMARY KEY (`actionparameters_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Daten für Tabelle `actionlog_parameters`
--

INSERT INTO `actionlog_parameters` (`actionparameters_id`, `actionparameters_trafficid`, `actionparameters_key`, `actionparameters_value`) VALUES
(1, 1, 'test1', 'test1360'),
(2, 1, 'test2', 'test405');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `actions`
--

CREATE TABLE IF NOT EXISTS `actions` (
  `action_id` int(12) NOT NULL AUTO_INCREMENT,
  `action_module` int(12) NOT NULL DEFAULT '0',
  `action_name` varchar(30) NOT NULL DEFAULT '',
  `action_description` text NOT NULL,
  `action_requiresuserright` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`action_id`),
  KEY `action_module` (`action_module`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=26 ;

--
-- Daten für Tabelle `actions`
--

INSERT INTO `actions` (`action_id`, `action_module`, `action_name`, `action_description`, `action_requiresuserright`) VALUES
(1, 1, 'index', 'Main index action', 0),
(2, 5, 'index', 'Index for the configuration exampels', 0),
(4, 3, 'index', 'Overview of message examples', 0),
(3, 2, 'index', 'Examples for the debug class', 0),
(5, 4, 'index', 'Template Examples', 0),
(6, 6, 'index', 'Index for dataserver examples', 0),
(7, 7, 'index', 'Index for the parameterhandler examples', 0),
(8, 8, 'index', 'Examples for the userhandler', 0),
(9, 9, 'index', 'Acrion for the object handler examples', 0);

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=18 ;

--
-- Daten für Tabelle `configurationcache`
--

INSERT INTO `configurationcache` (`configurationcache_id`, `configurationcache_name`, `configurationcache_timestamp`, `configurationcache_content`) VALUES
(15, './zeitgeist/configuration/zeitgeist.ini', '1253771904', 'YToxMDp7czo3OiJtb2R1bGVzIjthOjI6e3M6MTE6ImZvcm1jcmVhdG9yIjtzOjQ6InRydWUiO3M6NDoic2hvcCI7czo0OiJ0cnVlIjt9czo2OiJ0YWJsZXMiO2E6MTQ6e3M6MTM6InRhYmxlX2FjdGlvbnMiO3M6NzoiYWN0aW9ucyI7czoxODoidGFibGVfbWVzc2FnZWNhY2hlIjtzOjEyOiJtZXNzYWdlY2FjaGUiO3M6MTM6InRhYmxlX21vZHVsZXMiO3M6NzoibW9kdWxlcyI7czoxNzoidGFibGVfc2Vzc2lvbmRhdGEiO3M6MTE6InNlc3Npb25kYXRhIjtzOjE5OiJ0YWJsZV90ZW1wbGF0ZWNhY2hlIjtzOjEzOiJ0ZW1wbGF0ZWNhY2hlIjtzOjExOiJ0YWJsZV91c2VycyI7czo1OiJ1c2VycyI7czoxNDoidGFibGVfdXNlcmRhdGEiO3M6ODoidXNlcmRhdGEiO3M6MTY6InRhYmxlX3VzZXJyaWdodHMiO3M6MTA6InVzZXJyaWdodHMiO3M6MTU6InRhYmxlX3VzZXJyb2xlcyI7czo5OiJ1c2Vycm9sZXMiO3M6MjA6InRhYmxlX3VzZXJjaGFyYWN0ZXJzIjtzOjE0OiJ1c2VyY2hhcmFjdGVycyI7czoyNDoidGFibGVfdXNlcnJvbGVzX3RvX3VzZXJzIjtzOjE4OiJ1c2Vycm9sZXNfdG9fdXNlcnMiO3M6MjY6InRhYmxlX3VzZXJyb2xlc190b19hY3Rpb25zIjtzOjIwOiJ1c2Vycm9sZXNfdG9fYWN0aW9ucyI7czoxODoidGFibGVfdXNlcnNlc3Npb25zIjtzOjEyOiJ1c2Vyc2Vzc2lvbnMiO3M6MjI6InRhYmxlX3VzZXJjb25maXJtYXRpb24iO3M6MTY6InVzZXJjb25maXJtYXRpb24iO31zOjc6InNlc3Npb24iO2E6Mzp7czoxNToic2Vzc2lvbl9zdG9yYWdlIjtzOjg6ImRhdGFiYXNlIjtzOjEyOiJzZXNzaW9uX25hbWUiO3M6MTk6IlpFSVRHRUlTVF9TRVNTSU9OSUQiO3M6MTY6InNlc3Npb25fbGlmZXRpbWUiO3M6MToiMCI7fXM6ODoibWVzc2FnZXMiO2E6MTp7czoyMzoidXNlX3BlcnNpc3RlbnRfbWVzc2FnZXMiO3M6MToiMSI7fXM6ODoidGVtcGxhdGUiO2E6MTU6e3M6MTI6InJld3JpdGVfdXJscyI7czoxOiIwIjtzOjE4OiJ2YXJpYWJsZVN1YnN0QmVnaW4iO3M6NToiPCEtLUAiO3M6MTY6InZhcmlhYmxlU3Vic3RFbmQiO3M6NDoiQC0tPiI7czoxNToiYmxvY2tTdWJzdEJlZ2luIjtzOjU6IjwhLS0jIjtzOjEzOiJibG9ja1N1YnN0RW5kIjtzOjQ6IiMtLT4iO3M6OToibGlua0JlZ2luIjtzOjQ6IkBAe1siO3M6NzoibGlua0VuZCI7czo0OiJdfUBAIjtzOjEzOiJ2YXJpYWJsZUJlZ2luIjtzOjM6IkBAeyI7czoxMToidmFyaWFibGVFbmQiO3M6MzoifUBAIjtzOjE0OiJibG9ja09wZW5CZWdpbiI7czozMDoiPCEtLSBUZW1wbGF0ZUJlZ2luQmxvY2sgbmFtZT0iIjtzOjEyOiJibG9ja09wZW5FbmQiO3M6NToiIiAtLT4iO3M6MTA6ImJsb2NrQ2xvc2UiO3M6MjU6IjwhLS0gVGVtcGxhdGVFbmRCbG9jayAtLT4iO3M6MTk6IlVzZXJtZXNzYWdlTWVzc2FnZXMiO3M6MTI6InVzZXJtZXNzYWdlcyI7czoxOToiVXNlcm1lc3NhZ2VXYXJuaW5ncyI7czoxMjoidXNlcndhcm5pbmdzIjtzOjE3OiJVc2VybWVzc2FnZUVycm9ycyI7czoxMDoidXNlcmVycm9ycyI7fXM6MTA6ImNvbnRyb2xsZXIiO2E6Mzp7czoyNDoibm9fdXNlcnJpZ2h0c19mb3JfYWN0aW9uIjtzOjI6Ii0xIjtzOjI4OiJyZXF1aXJlZF9wYXJhbWV0ZXJfbm90X2ZvdW5kIjtzOjI6Ii0yIjtzOjk6Im1ldGhvZF9vayI7czoxOiIxIjt9czo5OiJhY3Rpb25sb2ciO2E6MTp7czoxNjoiYWN0aW9ubG9nX2FjdGl2ZSI7czoxOiIwIjt9czoxMjoiZXJyb3JoYW5kbGVyIjthOjE6e3M6MTc6ImVycm9yX3JlcG9ydGxldmVsIjtzOjE6IjIiO31zOjExOiJ1c2VyaGFuZGxlciI7YToxOntzOjE1OiJ1c2VfZG91Ymxlb3B0aW4iO3M6MToiMSI7fXM6MTA6InBhcmFtZXRlcnMiO2E6OTp7czoxNzoiZXNjYXBlX3BhcmFtZXRlcnMiO3M6MToiMSI7czo1OiJlbWFpbCI7czo2NjoiL15bXHdcLVwrXCZcKl0rKD86XC5bXHdcLVxfXCtcJlwqXSspKkAoPzpbXHctXStcLikrW2EtekEtWl17Miw3fSQvIjtzOjM6InVybCI7czo4NToiL14oZnRwfGh0dHB8aHR0cHMpOlwvXC8oXHcrOnswLDF9XHcqQCk/KFxTKykoOlswLTldKyk/KFwvfFwvKFtcdyMhOi4/Kz0mJUAhXC1cL10pKT8kLyI7czozOiJ6aXAiO3M6MTE6Ii9eXGR7Myw1fSQvIjtzOjY6InN0cmluZyI7czo2NzoiL15bXHfDvMOcw6TDhMO2w5YgXSsoKFtcLFwuXDpcLVwvXChcKVwhXD8gXSk/W1x3w7zDnMOkw4TDtsOWIF0qKSokLyI7czo0OiJ0ZXh0IjtzOjc3OiIvXltcd8O8w5zDpMOEw7bDliBdKygoW1xcXCJcLFwuXDpcLVwvXHJcblx0XCFcP1woXCkgXSk/W1x3w7zDnMOkw4TDtsOWIF0qKSokLyI7czo2OiJudW1iZXIiO3M6MjQ6Ii9eWzAtOV0qKFwufFwsKT9bMC05XSskLyI7czo3OiJib29sZWFuIjtzOjEyOiIvXlswLTFdezF9JC8iO3M6NDoiZGF0ZSI7czozODoiL15bMC05XXsyfShcLik/WzAtOV17Mn0oXC4pP1swLTldezR9JC8iO319'),
(2, './configuration/zeitgeist.ini', '1250535645', 'YToxOntzOjEzOiJvdmVyd3JpdGV0ZXN0IjthOjE6e3M6NDoidGVzdCI7czoxOiIxIjt9fQ=='),
(3, './modules/configuration/configuration.ini', '1251096050', 'YTozOntzOjEzOiJjb25maWd1cmF0aW9uIjthOjA6e31zOjU6ImluZGV4IjthOjE6e3M6MjE6Imhhc0V4dGVybmFsUGFyYW1ldGVycyI7czowOiIiO31zOjE5OiJleGFtcGxlYmxvY2tfbW9kdWxlIjthOjE6e3M6MTA6ImV4YW1wbGVrZXkiO3M6MTI6ImV4YW1wbGV2YWx1ZSI7fX0='),
(4, './_additional_files/example_configuration.ini', '1251132934', 'YTozOntzOjE0OiJjb25maWdfZXhhbXBsZSI7YToxOntzOjQ6InRlc3QiO3M6MToiMSI7fXM6NToiYmxvY2siO2E6Mzp7czo0OiJ2YXIxIjtzOjU6ImhlbGxvIjtzOjQ6InZhcjIiO3M6MTM6ImNvbmZpZ3VyYXRpb24iO3M6NDoidmFyMyI7czowOiIiO31zOjQ1OiJbY29uZmlndXJhdGlvbi5leGFtcGxlYmxvY2tfbW9kdWxlLmV4YW1wbGVrZXkiO2E6MDp7fX0='),
(6, './modules/main/main.ini', '1252929851', 'YToyOntzOjU6ImluZGV4IjthOjM6e3M6MjE6Imhhc0V4dGVybmFsUGFyYW1ldGVycyI7czo0OiJ0cnVlIjtzOjU6InRlc3QxIjthOjM6e3M6OToicGFyYW1ldGVyIjtzOjQ6InRydWUiO3M6Njoic291cmNlIjtzOjM6IkdFVCI7czo0OiJ0eXBlIjtzOjExOiIvXi57NCwzMn0kLyI7fXM6NToidGVzdDIiO2E6Mzp7czo5OiJwYXJhbWV0ZXIiO3M6NDoidHJ1ZSI7czo2OiJzb3VyY2UiO3M6MzoiR0VUIjtzOjQ6InR5cGUiO3M6NToiL14uJC8iO319czo0OiJzaG93IjthOjM6e3M6OToiUHJlU25hcEluIjthOjI6e2k6MDtzOjEwOiJ0ZXN0LmZ1bmMxIjtpOjE7czoxMDoidGVzdC5mdW5jMiI7fXM6MjE6Imhhc0V4dGVybmFsUGFyYW1ldGVycyI7czo0OiJ0cnVlIjtzOjk6ImlkX3NvdXJjZSI7czo0OiJfR0VUIjt9fQ=='),
(7, '../configuration/zeitgeist.ini', '1252930317', 'YToxMDp7czo3OiJtb2R1bGVzIjthOjI6e3M6MTE6ImZvcm1jcmVhdG9yIjtzOjQ6InRydWUiO3M6NDoic2hvcCI7czo0OiJ0cnVlIjt9czo2OiJ0YWJsZXMiO2E6MTQ6e3M6MTM6InRhYmxlX2FjdGlvbnMiO3M6NzoiYWN0aW9ucyI7czoxODoidGFibGVfbWVzc2FnZWNhY2hlIjtzOjEyOiJtZXNzYWdlY2FjaGUiO3M6MTM6InRhYmxlX21vZHVsZXMiO3M6NzoibW9kdWxlcyI7czoxNzoidGFibGVfc2Vzc2lvbmRhdGEiO3M6MTE6InNlc3Npb25kYXRhIjtzOjE5OiJ0YWJsZV90ZW1wbGF0ZWNhY2hlIjtzOjEzOiJ0ZW1wbGF0ZWNhY2hlIjtzOjExOiJ0YWJsZV91c2VycyI7czo1OiJ1c2VycyI7czoxNDoidGFibGVfdXNlcmRhdGEiO3M6ODoidXNlcmRhdGEiO3M6MTY6InRhYmxlX3VzZXJyaWdodHMiO3M6MTA6InVzZXJyaWdodHMiO3M6MTU6InRhYmxlX3VzZXJyb2xlcyI7czo5OiJ1c2Vycm9sZXMiO3M6MjA6InRhYmxlX3VzZXJjaGFyYWN0ZXJzIjtzOjE0OiJ1c2VyY2hhcmFjdGVycyI7czoyNDoidGFibGVfdXNlcnJvbGVzX3RvX3VzZXJzIjtzOjE4OiJ1c2Vycm9sZXNfdG9fdXNlcnMiO3M6MjY6InRhYmxlX3VzZXJyb2xlc190b19hY3Rpb25zIjtzOjIwOiJ1c2Vycm9sZXNfdG9fYWN0aW9ucyI7czoxODoidGFibGVfdXNlcnNlc3Npb25zIjtzOjEyOiJ1c2Vyc2Vzc2lvbnMiO3M6MjI6InRhYmxlX3VzZXJjb25maXJtYXRpb24iO3M6MTY6InVzZXJjb25maXJtYXRpb24iO31zOjc6InNlc3Npb24iO2E6Mzp7czoxNToic2Vzc2lvbl9zdG9yYWdlIjtzOjg6ImRhdGFiYXNlIjtzOjEyOiJzZXNzaW9uX25hbWUiO3M6MTk6IlpFSVRHRUlTVF9TRVNTSU9OSUQiO3M6MTY6InNlc3Npb25fbGlmZXRpbWUiO3M6MToiMCI7fXM6ODoibWVzc2FnZXMiO2E6MTp7czoyMzoidXNlX3BlcnNpc3RlbnRfbWVzc2FnZXMiO3M6MToiMSI7fXM6ODoidGVtcGxhdGUiO2E6MTU6e3M6MTI6InJld3JpdGVfdXJscyI7czoxOiIwIjtzOjE4OiJ2YXJpYWJsZVN1YnN0QmVnaW4iO3M6NToiPCEtLUAiO3M6MTY6InZhcmlhYmxlU3Vic3RFbmQiO3M6NDoiQC0tPiI7czoxNToiYmxvY2tTdWJzdEJlZ2luIjtzOjU6IjwhLS0jIjtzOjEzOiJibG9ja1N1YnN0RW5kIjtzOjQ6IiMtLT4iO3M6OToibGlua0JlZ2luIjtzOjQ6IkBAe1siO3M6NzoibGlua0VuZCI7czo0OiJdfUBAIjtzOjEzOiJ2YXJpYWJsZUJlZ2luIjtzOjM6IkBAeyI7czoxMToidmFyaWFibGVFbmQiO3M6MzoifUBAIjtzOjE0OiJibG9ja09wZW5CZWdpbiI7czozMDoiPCEtLSBUZW1wbGF0ZUJlZ2luQmxvY2sgbmFtZT0iIjtzOjEyOiJibG9ja09wZW5FbmQiO3M6NToiIiAtLT4iO3M6MTA6ImJsb2NrQ2xvc2UiO3M6MjU6IjwhLS0gVGVtcGxhdGVFbmRCbG9jayAtLT4iO3M6MTk6IlVzZXJtZXNzYWdlTWVzc2FnZXMiO3M6MTI6InVzZXJtZXNzYWdlcyI7czoxOToiVXNlcm1lc3NhZ2VXYXJuaW5ncyI7czoxMjoidXNlcndhcm5pbmdzIjtzOjE3OiJVc2VybWVzc2FnZUVycm9ycyI7czoxMDoidXNlcmVycm9ycyI7fXM6MTA6ImNvbnRyb2xsZXIiO2E6Mzp7czoyNDoibm9fdXNlcnJpZ2h0c19mb3JfYWN0aW9uIjtzOjI6Ii0xIjtzOjI4OiJyZXF1aXJlZF9wYXJhbWV0ZXJfbm90X2ZvdW5kIjtzOjI6Ii0yIjtzOjk6Im1ldGhvZF9vayI7czoxOiIxIjt9czo5OiJhY3Rpb25sb2ciO2E6MTp7czoxNjoiYWN0aW9ubG9nX2FjdGl2ZSI7czoxOiIwIjt9czoxMjoiZXJyb3JoYW5kbGVyIjthOjE6e3M6MTc6ImVycm9yX3JlcG9ydGxldmVsIjtzOjE6IjIiO31zOjExOiJ1c2VyaGFuZGxlciI7YToxOntzOjE1OiJ1c2VfZG91Ymxlb3B0aW4iO3M6MToiMSI7fXM6MTA6InBhcmFtZXRlcnMiO2E6ODp7czoxNzoiZXNjYXBlX3BhcmFtZXRlcnMiO3M6MToiMSI7czo1OiJlbWFpbCI7czo2NjoiL15bXHdcLVwrXCZcKl0rKD86XC5bXHdcLVxfXCtcJlwqXSspKkAoPzpbXHctXStcLikrW2EtekEtWl17Miw3fSQvIjtzOjM6InVybCI7czo4NToiL14oZnRwfGh0dHB8aHR0cHMpOlwvXC8oXHcrOnswLDF9XHcqQCk/KFxTKykoOlswLTldKyk/KFwvfFwvKFtcdyMhOi4/Kz0mJUAhXC1cL10pKT8kLyI7czozOiJ6aXAiO3M6MTE6Ii9eXGR7Myw1fSQvIjtzOjY6InN0cmluZyI7czo2NzoiL15bXHfDvMOcw6TDhMO2w5YgXSsoKFtcLFwuXDpcLVwvXChcKVwhXD8gXSk/W1x3w7zDnMOkw4TDtsOWIF0qKSokLyI7czo0OiJ0ZXh0IjtzOjc3OiIvXltcd8O8w5zDpMOEw7bDliBdKygoW1xcXCJcLFwuXDpcLVwvXHJcblx0XCFcP1woXCkgXSk/W1x3w7zDnMOkw4TDtsOWIF0qKSokLyI7czo2OiJudW1iZXIiO3M6MjQ6Ii9eWzAtOV0qKFwufFwsKT9bMC05XSskLyI7czo0OiJkYXRlIjtzOjM4OiIvXlswLTldezJ9KFwuKT9bMC05XXsyfShcLik/WzAtOV17NH0kLyI7fX0='),
(8, '../tests/testdata/testconfiguration.ini', '1252389919', 'YToyOntzOjEwOiJ0ZXN0YmxvY2sxIjthOjM6e3M6ODoidGVzdHZhcjEiO3M6NDoidHJ1ZSI7czo4OiJ0ZXN0dmFyMiI7czoxOiIxIjtzOjg6InRlc3R2YXIzIjtzOjU6InRlc3QzIjt9czoxMDoidGVzdGJsb2NrMiI7YTozOntzOjg6InRlc3R2YXI0IjtzOjU6ImZhbHNlIjtzOjg6InRlc3R2YXI1IjtzOjE6IjIiO3M6ODoidGVzdHZhcjYiO3M6NToidGVzdDQiO319'),
(9, '../tests/testdata/testparameters.ini', '1252910250', 'YToxOntzOjE1OiJ0ZXN0X3BhcmFtZXRlcnMiO2E6Njp7czoyMToiaGFzRXh0ZXJuYWxQYXJhbWV0ZXJzIjtzOjQ6InRydWUiO3M6MTE6InRlc3RfcmVnZXhwIjthOjM6e3M6OToicGFyYW1ldGVyIjtzOjQ6InRydWUiO3M6Njoic291cmNlIjtzOjM6IkdFVCI7czo0OiJ0eXBlIjtzOjEwOiIvXi57NCw1fSQvIjt9czoxOToidGVzdF9yZWdleHBfZXNjYXBlZCI7YTo0OntzOjk6InBhcmFtZXRlciI7czo0OiJ0cnVlIjtzOjY6InNvdXJjZSI7czozOiJHRVQiO3M6NDoidHlwZSI7czoxMDoiL14uezQsNX0kLyI7czo2OiJlc2NhcGUiO3M6NDoidHJ1ZSI7fXM6OToidGVzdF90ZXh0IjthOjM6e3M6OToicGFyYW1ldGVyIjtzOjQ6InRydWUiO3M6Njoic291cmNlIjtzOjM6IkdFVCI7czo0OiJ0eXBlIjtzOjI5OiJbW3plaXRnZWlzdC5wYXJhbWV0ZXJzLnRleHRdXSI7fXM6MTE6InRlc3Rfc3RyaW5nIjthOjM6e3M6OToicGFyYW1ldGVyIjtzOjQ6InRydWUiO3M6Njoic291cmNlIjtzOjM6IkdFVCI7czo0OiJ0eXBlIjtzOjMxOiJbW3plaXRnZWlzdC5wYXJhbWV0ZXJzLnN0cmluZ11dIjt9czo5OiJ0ZXN0X2RhdGUiO2E6Mzp7czo5OiJwYXJhbWV0ZXIiO3M6NDoidHJ1ZSI7czo2OiJzb3VyY2UiO3M6MzoiR0VUIjtzOjQ6InR5cGUiO3M6Mjk6IltbemVpdGdlaXN0LnBhcmFtZXRlcnMuZGF0ZV1dIjt9fX0='),
(11, './modules/messages/messages.ini', '1250535646', 'YToxOntzOjU6ImluZGV4IjthOjE6e3M6MjE6Imhhc0V4dGVybmFsUGFyYW1ldGVycyI7czo1OiJmYWxzZSI7fX0='),
(12, './modules/dataserver/dataserver.ini', '1251133395', 'YToxOntzOjU6ImluZGV4IjthOjE6e3M6MjE6Imhhc0V4dGVybmFsUGFyYW1ldGVycyI7czo1OiJmYWxzZSI7fX0='),
(13, './modules/parameters/parameters.ini', '1252910254', 'YToxOntzOjU6ImluZGV4IjthOjQ6e3M6MjE6Imhhc0V4dGVybmFsUGFyYW1ldGVycyI7czo0OiJ0cnVlIjtzOjEwOiJ0ZXN0c3RyaW5nIjthOjQ6e3M6OToicGFyYW1ldGVyIjtzOjQ6InRydWUiO3M6Njoic291cmNlIjtzOjM6IkdFVCI7czo0OiJ0eXBlIjtzOjEwOiIvXi57NCw1fSQvIjtzOjY6ImVzY2FwZSI7czo1OiJmYWxzZSI7fXM6MTA6InRlc3RudW1iZXIiO2E6NDp7czo5OiJwYXJhbWV0ZXIiO3M6NDoidHJ1ZSI7czo2OiJzb3VyY2UiO3M6MzoiR0VUIjtzOjQ6InR5cGUiO3M6MzE6IltbemVpdGdlaXN0LnBhcmFtZXRlcnMubnVtYmVyXV0iO3M6NjoiZXNjYXBlIjtzOjU6ImZhbHNlIjt9czoxMDoidGVzdGVzY2FwZSI7YTo0OntzOjk6InBhcmFtZXRlciI7czo0OiJ0cnVlIjtzOjY6InNvdXJjZSI7czozOiJHRVQiO3M6NDoidHlwZSI7czoxMToiL14uezEsNTB9JC8iO3M6NjoiZXNjYXBlIjtzOjQ6InRydWUiO319fQ=='),
(14, './modules/userhandler/userhandler.ini', '1251738703', 'YToxOntzOjU6ImluZGV4IjthOjE6e3M6MjE6Imhhc0V4dGVybmFsUGFyYW1ldGVycyI7czo1OiJmYWxzZSI7fX0='),
(16, './modules/objects/objects.ini', '1251787619', 'YToxOntzOjU6ImluZGV4IjthOjE6e3M6MjE6Imhhc0V4dGVybmFsUGFyYW1ldGVycyI7czo1OiJmYWxzZSI7fX0='),
(17, './modules/debug/debug.ini', '1250577873', 'YToxOntzOjU6ImluZGV4IjthOjE6e3M6MjE6Imhhc0V4dGVybmFsUGFyYW1ldGVycyI7czo1OiJmYWxzZSI7fX0=');

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=23 ;

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
(9, 'objects', 'Examples for the object handler', 1);

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
('edca1daff26f156e1d328a02086c0beb', 1253166614, 1253166614, 'messagecache_session|s:175:"a:1:{i:0;O:9:"zgMessage":4:{s:7:"message";s:50:"No messagedata is stored in database for this user";s:4:"type";s:7:"warning";s:4:"from";s:18:"messages.class.php";s:2:"to";N;}}";', 2130706433),
('1777baed15e586a26a7ad8d7416ebf33', 1254252881, 1254253278, 'messagecache_session|s:1050:"a:5:{i:0;O:9:"zgMessage":4:{s:7:"message";s:46:"Configuration data in the database is outdated";s:4:"type";s:7:"warning";s:4:"from";s:23:"configuration.class.php";s:2:"to";N;}i:1;O:9:"zgMessage":4:{s:7:"message";s:50:"No messagedata is stored in database for this user";s:4:"type";s:7:"warning";s:4:"from";s:18:"messages.class.php";s:2:"to";N;}i:2;O:9:"zgMessage":4:{s:7:"message";s:54:"No configuration is stored in database for this module";s:4:"type";s:7:"warning";s:4:"from";s:23:"configuration.class.php";s:2:"to";N;}i:3;O:9:"zgMessage":4:{s:7:"message";s:54:"No configuration is stored in database for this module";s:4:"type";s:7:"warning";s:4:"from";s:23:"configuration.class.php";s:2:"to";N;}i:4;O:9:"zgMessage":4:{s:7:"message";s:229:"Problem executing query: You have an error in your SQL syntax; check the manual that corresponds to your MySQL server version for the right syntax to use near ''''Hello Database Error'''' at line 1 Query was: "''Hello Database Error''"";s:4:"type";s:7:"warning";s:4:"from";s:18:"database.class.php";s:2:"to";N;}}";', 2130706433);

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=16 ;

--
-- Daten für Tabelle `templatecache`
--

INSERT INTO `templatecache` (`templatecache_id`, `templatecache_name`, `templatecache_timestamp`, `templatecache_content`) VALUES
(15, 'templates/zgexamples/main_basics.tpl.html', '1250535647', 'YTo0OntzOjQ6ImZpbGUiO3M6NDE6InRlbXBsYXRlcy96Z2V4YW1wbGVzL21haW5fYmFzaWNzLnRwbC5odG1sIjtzOjc6ImNvbnRlbnQiO3M6MjE3MjoiPCFET0NUWVBFIGh0bWwgUFVCTElDICItLy9XM0MvL0RURCBYSFRNTCAxLjAgVHJhbnNpdGlvbmFsLy9FTiIgImh0dHA6Ly93d3cudzMub3JnL1RSL3hodG1sMS9EVEQveGh0bWwxLXRyYW5zaXRpb25hbC5kdGQiPg0KPGh0bWwgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGh0bWwiPjwhLS0gSW5zdGFuY2VCZWdpbiB0ZW1wbGF0ZT0iL1RlbXBsYXRlcy96Z2V4YW1wbGVzLmR3dCIgY29kZU91dHNpZGVIVE1MSXNMb2NrZWQ9ImZhbHNlIiAtLT4NCjxoZWFkPg0KPG1ldGEgaHR0cC1lcXVpdj0iQ29udGVudC1UeXBlIiBjb250ZW50PSJ0ZXh0L2h0bWw7IGNoYXJzZXQ9dXRmLTgiIC8+DQo8IS0tIEluc3RhbmNlQmVnaW5FZGl0YWJsZSBuYW1lPSJkb2N0aXRsZSIgLS0+DQo8dGl0bGU+WmVpdGdlaXN0IEV4YW1wbGVzIC0gSG9tZTwvdGl0bGU+DQo8IS0tIEluc3RhbmNlRW5kRWRpdGFibGUgLS0+DQoNCjxsaW5rIHJlbD0ic3R5bGVzaGVldCIgdHlwZT0idGV4dC9jc3MiIGhyZWY9InRlbXBsYXRlcy96Z2V4YW1wbGVzL2Nzcy96Z2V4YW1wbGVzLmNzcyIgLz4NCg0KPHNjcmlwdCBsYW5ndWFnZT0iamF2YXNjcmlwdCIgdHlwZT0idGV4dC9qYXZhc2NyaXB0Ij4NCg0KPC9zY3JpcHQ+DQoJDQo8IS0tIEluc3RhbmNlQmVnaW5FZGl0YWJsZSBuYW1lPSJoZWFkIiAtLT4NCjwhLS0gSW5zdGFuY2VFbmRFZGl0YWJsZSAtLT4NCjwvaGVhZD4NCg0KPGJvZHk+DQoNCgk8ZGl2IGlkPSJoZWFkZXIiPg0KCQk8ZGl2IGlkPSJtZW51Ij4NCgkNCgkJCTxkaXYgaWQ9IndlbGNvbWVtZXNzYWdlIj5aZWl0Z2Vpc3QgRXhhbXBsZXM8L2Rpdj4NCgkJCTxkaXYgaWQ9Im1haW5tZW51Ij4NCgkJCTxhIGhyZWY9ImluZGV4LnBocCI+SG9tZTwvYT4NCgkJCTxhIGhyZWY9ImluZGV4LnBocD9hY3Rpb249YmFzaWNzIj5CYXNpY3M8L2E+DQoJCQk8YSBocmVmPSJpbmRleC5waHA/bW9kdWxlPWV2ZW50aGFuZGxlciI+RXZlbnQgSGFuZGxlcjwvYT4NCgkJCTxhIGhyZWY9ImluZGV4LnBocD9tb2R1bGU9dGVtcGxhdGVzIj5UZW1wbGF0ZSBFbmdpbmU8L2E+DQoJCQk8L2Rpdj4NCgkJPC9kaXY+DQoJPC9kaXY+DQoNCgk8IS0tI3VzZXJtZXNzYWdlIy0tPg0KDQoJPCEtLSN1c2Vyd2FybmluZ3MjLS0+DQoNCgk8IS0tI3VzZXJlcnJvcnMjLS0+DQoNCgk8ZGl2IGlkPSJjb250ZW50Ij4NCgk8IS0tIEluc3RhbmNlQmVnaW5FZGl0YWJsZSBuYW1lPSJib2R5IiAtLT4NCiAgICANCiAgICA8aDEgc3R5bGU9Im1hcmdpbi10b3A6MHB4OyI+WmVpdGdlaXN0IEV4YW1wbGVzIC0gT3ZlcnZpZXc8L2gxPg0KCQkNCgk8cD5XZWxjb21lIHRvIFplaXRnZWlzdCwgYSBQSFAgYmFzZWQgbXVsdGkgcHVycG9zZSBmcmFtZXdvcmsgZm9yIHdlYiBhcHBsaWNhdGlvbnMuPC9wPg0KDQoJPHA+Rm9sbG93aW5nIGFyZSBleGFtcGxlcyB0byBnZXQgeW91IHN0YXJ0ZWQgdW5kZXJzdGFuZGluZyBhbmQgdXNpbmcgWmVpdGdlaXN0LiBUaGlzIGNvbGxlY3Rpb24gc2VydmVzIGFzIHR1dG9yaWFsIGFzIHdlbGwgYXMgcmVmZXJlbmNlIG1hbnVhbCBhbmQgc3R5bGUgZ3VpZGUuIDwvcD4NCgkNCgk8cD5NYXliZSB5b3UgYWxyZWFkeSBsb29rZWQgYXQgdGhlIGNvZGUgeW91IGp1c3QgaW5zdGFsbGVkIC0gdGhhdCdzIG9rLiBXaGVuIGxvb2tpbmcgYXQgdGhlIHNvdXJjZXMgb2YgdGhlIGV4YW1wbGVzLCBwbGVhc2Uga2VlcCB0aGUgZm9sbG93aW5nIHRoaW5ncyBpbiBtaW5kOjwvcD4NCgkNCgk8b2w+DQoJCTxsaT5UaGlzIGlzIG5vdCBhIHJlYWwgYXBwbGljYXRpb24gYnV0IG1lcmVseSBhIGNvbGxlY3Rpb24gb2YgZXhhbXBsZXMgYW5kIHR1dG9yaWFscy48L2xpPg0KCQk8bGk+TWFueSBmdW5jdGlvbmFsaXRpZXMgYXJlIHVzZWQgaW4gYSBkdW1iZWQgZG93biB3YXkuIFRoZXNlIGFyZSBleGFtcGxlcyBhZnRlciBhbGwuIEhvd2V2ZXIgd2UgdHJ5IHRvIHByZXNlbnQgYmVzdCBwcmFjdGlzZXMgZm9yIGVhY2ggZnJhbWV3b3JrIG1vZHVsZS48L2xpPg0KCTwvb2w+DQoJDQoJPHVsPg0KCQk8bGk+QmFzaWNzPC9saT4NCgkJPGxpPkV2ZW50IEhhbmRsZXI8L2xpPg0KCQk8bGk+VGVtcGxhdGUgRW5naW5lPC9saT4NCgk8L3VsPg0KCQkNCg0KPCEtLSBJbnN0YW5jZUVuZEVkaXRhYmxlIC0tPg0KDQoJPC9kaXY+DQoNCjwvYm9keT4NCjwhLS0gSW5zdGFuY2VFbmQgLS0+PC9odG1sPg0KIjtzOjY6ImJsb2NrcyI7YTo0OntzOjExOiJ1c2VybWVzc2FnZSI7TzoxNToiemdUZW1wbGF0ZUJsb2NrIjo0OntzOjE0OiJjdXJyZW50Q29udGVudCI7czo1NjoiDQoJCTxoMyBjbGFzcz0idXNlcm1lc3NhZ2UiPjwhLS1AdXNlcm1lc3NhZ2VALS0+PC9oMz4NCgkiO3M6MTU6Im9yaWdpbmFsQ29udGVudCI7czo1NjoiDQoJCTxoMyBjbGFzcz0idXNlcm1lc3NhZ2UiPjwhLS1AdXNlcm1lc3NhZ2VALS0+PC9oMz4NCgkiO3M6MTE6ImJsb2NrUGFyZW50IjtOO3M6MTQ6ImJsb2NrVmFyaWFibGVzIjthOjE6e3M6MTE6InVzZXJtZXNzYWdlIjtzOjIwOiI8IS0tQHVzZXJtZXNzYWdlQC0tPiI7fX1zOjEyOiJ1c2Vyd2FybmluZ3MiO086MTU6InpnVGVtcGxhdGVCbG9jayI6NDp7czoxNDoiY3VycmVudENvbnRlbnQiO3M6NTY6Ig0KCQk8aDMgY2xhc3M9InVzZXJ3YXJuaW5nIj48IS0tQHVzZXJ3YXJuaW5nQC0tPjwvaDM+DQoJIjtzOjE1OiJvcmlnaW5hbENvbnRlbnQiO3M6NTY6Ig0KCQk8aDMgY2xhc3M9InVzZXJ3YXJuaW5nIj48IS0tQHVzZXJ3YXJuaW5nQC0tPjwvaDM+DQoJIjtzOjExOiJibG9ja1BhcmVudCI7TjtzOjE0OiJibG9ja1ZhcmlhYmxlcyI7YToxOntzOjExOiJ1c2Vyd2FybmluZyI7czoyMDoiPCEtLUB1c2Vyd2FybmluZ0AtLT4iO319czoxMDoidXNlcmVycm9ycyI7TzoxNToiemdUZW1wbGF0ZUJsb2NrIjo0OntzOjE0OiJjdXJyZW50Q29udGVudCI7czo1NDoiDQoJCTxoMyBjbGFzcz0idXNlcndhcm5pbmciPjwhLS1AdXNlcmVycm9yQC0tPjwvaDM+DQoJIjtzOjE1OiJvcmlnaW5hbENvbnRlbnQiO3M6NTQ6Ig0KCQk8aDMgY2xhc3M9InVzZXJ3YXJuaW5nIj48IS0tQHVzZXJlcnJvckAtLT48L2gzPg0KCSI7czoxMToiYmxvY2tQYXJlbnQiO047czoxNDoiYmxvY2tWYXJpYWJsZXMiO2E6MTp7czo5OiJ1c2VyZXJyb3IiO3M6MTg6IjwhLS1AdXNlcmVycm9yQC0tPiI7fX1zOjQ6InJvb3QiO086MTU6InpnVGVtcGxhdGVCbG9jayI6NDp7czoxNDoiY3VycmVudENvbnRlbnQiO047czoxNToib3JpZ2luYWxDb250ZW50IjtOO3M6MTE6ImJsb2NrUGFyZW50IjtOO3M6MTQ6ImJsb2NrVmFyaWFibGVzIjtOO319czo5OiJ2YXJpYWJsZXMiO2E6Mzp7czoxMToidXNlcm1lc3NhZ2UiO086MTg6InpnVGVtcGxhdGVWYXJpYWJsZSI6Mjp7czoxNDoiY3VycmVudENvbnRlbnQiO047czoxNDoiZGVmYXVsdENvbnRlbnQiO047fXM6MTE6InVzZXJ3YXJuaW5nIjtPOjE4OiJ6Z1RlbXBsYXRlVmFyaWFibGUiOjI6e3M6MTQ6ImN1cnJlbnRDb250ZW50IjtOO3M6MTQ6ImRlZmF1bHRDb250ZW50IjtOO31zOjk6InVzZXJlcnJvciI7TzoxODoiemdUZW1wbGF0ZVZhcmlhYmxlIjoyOntzOjE0OiJjdXJyZW50Q29udGVudCI7TjtzOjE0OiJkZWZhdWx0Q29udGVudCI7Tjt9fX0='),
(10, 'templates/zgexamples/main_index.tpl.html', '1250535647', 'YTo0OntzOjQ6ImZpbGUiO3M6NDA6InRlbXBsYXRlcy96Z2V4YW1wbGVzL21haW5faW5kZXgudHBsLmh0bWwiO3M6NzoiY29udGVudCI7czoyMjkwOiI8IURPQ1RZUEUgaHRtbCBQVUJMSUMgIi0vL1czQy8vRFREIFhIVE1MIDEuMCBUcmFuc2l0aW9uYWwvL0VOIiAiaHR0cDovL3d3dy53My5vcmcvVFIveGh0bWwxL0RURC94aHRtbDEtdHJhbnNpdGlvbmFsLmR0ZCI+DQo8aHRtbCB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94aHRtbCI+PCEtLSBJbnN0YW5jZUJlZ2luIHRlbXBsYXRlPSIvVGVtcGxhdGVzL3pnZXhhbXBsZXMuZHd0IiBjb2RlT3V0c2lkZUhUTUxJc0xvY2tlZD0iZmFsc2UiIC0tPg0KPGhlYWQ+DQo8bWV0YSBodHRwLWVxdWl2PSJDb250ZW50LVR5cGUiIGNvbnRlbnQ9InRleHQvaHRtbDsgY2hhcnNldD11dGYtOCIgLz4NCjwhLS0gSW5zdGFuY2VCZWdpbkVkaXRhYmxlIG5hbWU9ImRvY3RpdGxlIiAtLT4NCjx0aXRsZT5aZWl0Z2Vpc3QgRXhhbXBsZXMgLSBIb21lPC90aXRsZT4NCjwhLS0gSW5zdGFuY2VFbmRFZGl0YWJsZSAtLT4NCg0KPGxpbmsgcmVsPSJzdHlsZXNoZWV0IiB0eXBlPSJ0ZXh0L2NzcyIgaHJlZj0idGVtcGxhdGVzL3pnZXhhbXBsZXMvY3NzL3pnZXhhbXBsZXMuY3NzIiAvPg0KDQo8c2NyaXB0IGxhbmd1YWdlPSJqYXZhc2NyaXB0IiB0eXBlPSJ0ZXh0L2phdmFzY3JpcHQiPg0KDQo8L3NjcmlwdD4NCgkNCjwhLS0gSW5zdGFuY2VCZWdpbkVkaXRhYmxlIG5hbWU9ImhlYWQiIC0tPg0KPCEtLSBJbnN0YW5jZUVuZEVkaXRhYmxlIC0tPg0KPC9oZWFkPg0KDQo8Ym9keT4NCg0KCTxkaXYgaWQ9ImhlYWRlciI+DQoJCTxkaXYgaWQ9Im1lbnUiPg0KCQ0KCQkJPGRpdiBpZD0id2VsY29tZW1lc3NhZ2UiPlplaXRnZWlzdCBFeGFtcGxlczwvZGl2Pg0KCQkJPGRpdiBpZD0ibWFpbm1lbnUiPg0KCQkJPGEgaHJlZj0iaW5kZXgucGhwIj5Ib21lPC9hPg0KCQkJPGEgaHJlZj0iaW5kZXgucGhwP2FjdGlvbj1iYXNpY3MiPkJhc2ljczwvYT4NCgkJCTxhIGhyZWY9ImluZGV4LnBocD9tb2R1bGU9ZXZlbnRoYW5kbGVyIj5FdmVudCBIYW5kbGVyPC9hPg0KCQkJPGEgaHJlZj0iaW5kZXgucGhwP21vZHVsZT1kZWJ1ZyI+RGVidWdnaW5nPC9hPg0KCQkJPGEgaHJlZj0iaW5kZXgucGhwP21vZHVsZT1tZXNzYWdlcyI+TWVzc2FnZXM8L2E+DQoJCQk8YSBocmVmPSJpbmRleC5waHA/bW9kdWxlPXRlbXBsYXRlcyI+VGVtcGxhdGUgRW5naW5lPC9hPg0KCQkJPC9kaXY+DQoJCTwvZGl2Pg0KCTwvZGl2Pg0KDQoJPCEtLSN1c2VybWVzc2FnZSMtLT4NCg0KCTwhLS0jdXNlcndhcm5pbmdzIy0tPg0KDQoJPCEtLSN1c2VyZXJyb3JzIy0tPg0KDQoJPGRpdiBpZD0iY29udGVudCI+DQoJPCEtLSBJbnN0YW5jZUJlZ2luRWRpdGFibGUgbmFtZT0iYm9keSIgLS0+DQogICAgDQogICAgPGgxIHN0eWxlPSJtYXJnaW4tdG9wOjBweDsiPlplaXRnZWlzdCBFeGFtcGxlcyAtIE92ZXJ2aWV3PC9oMT4NCgkJDQoJPHA+V2VsY29tZSB0byBaZWl0Z2Vpc3QsIGEgUEhQIGJhc2VkIG11bHRpIHB1cnBvc2UgZnJhbWV3b3JrIGZvciB3ZWIgYXBwbGljYXRpb25zLjwvcD4NCg0KCTxwPkZvbGxvd2luZyBhcmUgZXhhbXBsZXMgdG8gc2hvdyB5b3UgaG93IHRvIHVzZSBaZWl0Z2Vpc3QuIFRoaXMgY29sbGVjdGlvbiBzZXJ2ZXMgYXMgdHV0b3JpYWwgYXMgd2VsbCBhcyByZWZlcmVuY2UgbWFudWFsIGFuZCBzdHlsZSBndWlkZS4gPC9wPg0KCQ0KCTxwPk1heWJlIHlvdSBhbHJlYWR5IGxvb2tlZCBhdCB0aGUgY29kZSB5b3UganVzdCBwdXQgb24gdGhlIHNlcnZlciAtIHRoYXQncyBvay4gV2hlbiBsb29raW5nIGF0IHRoZSBzb3VyY2VzIG9mIHRoZSBleGFtcGxlcywgcGxlYXNlIGtlZXAgdGhlIGZvbGxvd2luZyB0aGluZ3MgaW4gbWluZDo8L3A+DQoJDQoJPG9sPg0KCQk8bGk+VGhpcyBpcyBub3QgYSByZWFsIGFwcGxpY2F0aW9uIGJ1dCBtZXJlbHkgYSBjb2xsZWN0aW9uIG9mIGV4YW1wbGVzIGFuZCB0dXRvcmlhbHMuPC9saT4NCgkJPGxpPk1hbnkgZnVuY3Rpb25hbGl0aWVzIGFyZSB1c2VkIGluIGEgZHVtYmVkIGRvd24gd2F5LiBUaGVzZSBhcmUgZXhhbXBsZXMgYWZ0ZXIgYWxsLiBIb3dldmVyIHdlIHRyeSB0byBwcmVzZW50IGJlc3QgcHJhY3Rpc2VzIGZvciBlYWNoIGZyYW1ld29yayBtb2R1bGUuPC9saT4NCgkJPGxpPklmIHlvdSB3YW50IHRvIHNlZSBhIG1vcmUgInJlYWwgd29ybGQiIGFwcGxpY2F0aW9uLCB0YWtlIGEgbG9vayBhdCB0aGUgWmVpdGdlaXN0IEFkbWluaXN0cmF0b3IgYXBwbGljYXRpb24uPC9saT4NCgk8L29sPg0KCQ0KPCEtLSBJbnN0YW5jZUVuZEVkaXRhYmxlIC0tPg0KDQoJPC9kaXY+DQoNCjwvYm9keT4NCjwhLS0gSW5zdGFuY2VFbmQgLS0+PC9odG1sPg0KIjtzOjY6ImJsb2NrcyI7YTo0OntzOjExOiJ1c2VybWVzc2FnZSI7TzoxNToiemdUZW1wbGF0ZUJsb2NrIjo0OntzOjE0OiJjdXJyZW50Q29udGVudCI7czo1NjoiDQoJCTxoMyBjbGFzcz0idXNlcm1lc3NhZ2UiPjwhLS1AdXNlcm1lc3NhZ2VALS0+PC9oMz4NCgkiO3M6MTU6Im9yaWdpbmFsQ29udGVudCI7czo1NjoiDQoJCTxoMyBjbGFzcz0idXNlcm1lc3NhZ2UiPjwhLS1AdXNlcm1lc3NhZ2VALS0+PC9oMz4NCgkiO3M6MTE6ImJsb2NrUGFyZW50IjtOO3M6MTQ6ImJsb2NrVmFyaWFibGVzIjthOjE6e3M6MTE6InVzZXJtZXNzYWdlIjtzOjIwOiI8IS0tQHVzZXJtZXNzYWdlQC0tPiI7fX1zOjEyOiJ1c2Vyd2FybmluZ3MiO086MTU6InpnVGVtcGxhdGVCbG9jayI6NDp7czoxNDoiY3VycmVudENvbnRlbnQiO3M6NTY6Ig0KCQk8aDMgY2xhc3M9InVzZXJ3YXJuaW5nIj48IS0tQHVzZXJ3YXJuaW5nQC0tPjwvaDM+DQoJIjtzOjE1OiJvcmlnaW5hbENvbnRlbnQiO3M6NTY6Ig0KCQk8aDMgY2xhc3M9InVzZXJ3YXJuaW5nIj48IS0tQHVzZXJ3YXJuaW5nQC0tPjwvaDM+DQoJIjtzOjExOiJibG9ja1BhcmVudCI7TjtzOjE0OiJibG9ja1ZhcmlhYmxlcyI7YToxOntzOjExOiJ1c2Vyd2FybmluZyI7czoyMDoiPCEtLUB1c2Vyd2FybmluZ0AtLT4iO319czoxMDoidXNlcmVycm9ycyI7TzoxNToiemdUZW1wbGF0ZUJsb2NrIjo0OntzOjE0OiJjdXJyZW50Q29udGVudCI7czo1NDoiDQoJCTxoMyBjbGFzcz0idXNlcndhcm5pbmciPjwhLS1AdXNlcmVycm9yQC0tPjwvaDM+DQoJIjtzOjE1OiJvcmlnaW5hbENvbnRlbnQiO3M6NTQ6Ig0KCQk8aDMgY2xhc3M9InVzZXJ3YXJuaW5nIj48IS0tQHVzZXJlcnJvckAtLT48L2gzPg0KCSI7czoxMToiYmxvY2tQYXJlbnQiO047czoxNDoiYmxvY2tWYXJpYWJsZXMiO2E6MTp7czo5OiJ1c2VyZXJyb3IiO3M6MTg6IjwhLS1AdXNlcmVycm9yQC0tPiI7fX1zOjQ6InJvb3QiO086MTU6InpnVGVtcGxhdGVCbG9jayI6NDp7czoxNDoiY3VycmVudENvbnRlbnQiO047czoxNToib3JpZ2luYWxDb250ZW50IjtOO3M6MTE6ImJsb2NrUGFyZW50IjtOO3M6MTQ6ImJsb2NrVmFyaWFibGVzIjtOO319czo5OiJ2YXJpYWJsZXMiO2E6Mzp7czoxMToidXNlcm1lc3NhZ2UiO086MTg6InpnVGVtcGxhdGVWYXJpYWJsZSI6Mjp7czoxNDoiY3VycmVudENvbnRlbnQiO047czoxNDoiZGVmYXVsdENvbnRlbnQiO047fXM6MTE6InVzZXJ3YXJuaW5nIjtPOjE4OiJ6Z1RlbXBsYXRlVmFyaWFibGUiOjI6e3M6MTQ6ImN1cnJlbnRDb250ZW50IjtOO3M6MTQ6ImRlZmF1bHRDb250ZW50IjtOO31zOjk6InVzZXJlcnJvciI7TzoxODoiemdUZW1wbGF0ZVZhcmlhYmxlIjoyOntzOjE0OiJjdXJyZW50Q29udGVudCI7TjtzOjE0OiJkZWZhdWx0Q29udGVudCI7Tjt9fX0='),
(14, '_additional_files/example_template.tpl.html', '1250662829', 'YTo0OntzOjQ6ImZpbGUiO3M6NDM6Il9hZGRpdGlvbmFsX2ZpbGVzL2V4YW1wbGVfdGVtcGxhdGUudHBsLmh0bWwiO3M6NzoiY29udGVudCI7czoxMjQ0OiI8IURPQ1RZUEUgaHRtbCBQVUJMSUMgIi0vL1czQy8vRFREIFhIVE1MIDEuMCBUcmFuc2l0aW9uYWwvL0VOIiAiaHR0cDovL3d3dy53My5vcmcvVFIveGh0bWwxL0RURC94aHRtbDEtdHJhbnNpdGlvbmFsLmR0ZCI+DQo8aHRtbCB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94aHRtbCI+DQoJPGhlYWQ+DQoJCTx0aXRsZT4gVGVtcGxhdGUgZXhhbXBsZSA8L3RpdGxlPg0KCTwvaGVhZD4NCg0KCTxib2R5Pg0KDQoJCTwhLS0gdmFyaWFibGVzIGluIGEgdGVtcGxhdGUgYXJlIGRlZmluZWQgRHJlYW13ZWF2ZXItc3R5bGU6IDwhLS1AVkFSTkFNRUAtLT4gLS0+DQoJCTwhLS0gVXNlIHRoZSBuYW1lIHRvIGFkZHJlc3MgaXQgZnJvbSB0aGUgYXBwbGljYXRpb24gLS0+DQoJCTxwPlRoaXMgY29udGVudCBpcyBhc3NpZ25lZCBieSB0aGUgYXBwbGljYXRpb246IDxiPjwhLS1AZXhhbXBsZWNvbnRlbnRALS0+PC9iPjwvcD4NCg0KCQk8IS0tIGxpbmtzIGluc2lkZSB0aGUgYXBwbGljYXRpb24gY2FuIGJlIGRlZmluZWQgbGlrZSB0aGlzOiBpbmRleC5waHA/bW9kdWxlPU1PRFVMRSZhY3Rpb249QUNUSU9OIC0tPg0KCQk8IS0tIHRoZXkgd2lsbCBiZSBhdXRvbWF0aWNhbGx5IGdlbmVyYXRlZCB3aXRoIHRoZSBjb3JyZWN0IHBhdGhzIC0tPg0KCQk8cD5UaGlzIGlzIGEgPGI+PGEgaHJlZj0iaW5kZXgucGhwIj5saW5rIHRvIHRoZSBob21lIHBhZ2U8L2E+PC9iPi4gSXQncyBjcmVhdGVkIGF1dG9tYXRpY2FsbHkuPC9wPg0KDQoJCTwhLS0gbGlua3MgY2FuIGFsc28gYmUgY3JlYXRlZCBieSB0aGUgYXBwbGljYXRpb24uIHVzZSBhIHZhcmlhYmxlIHRvIGFzc2lnbiBpdCBpbnRvIHRoZSB0ZW1wbGF0ZSAtLT4NCgkJPHA+VGhpcyBpcyBhIDxiPjxhIGhyZWY9IjwhLS1AbWFudWFsbGlua0AtLT4iPmxpbmsgdG8gdGhlIGhvbWUgcGFnZTwvYT48L2I+LiBJdCdzIGNyZWF0ZWQgbWFudWFsbHkuPC9wPg0KICANCgkJPCEtLSBibG9ja3MgaW4gYSB0ZW1wbGF0ZSBhcmUgYWxzbyBkZWZpbmVkIERyZWFtd2VhdmVyLXN0eWxlIC0tPg0KCQk8IS0tIFVzZSB0aGUgbmFtZSB0byBhZGRyZXNzIGl0IGZyb20gdGhlIGFwcGxpY2F0aW9uIC0tPg0KCQk8IS0tI2V4YW1wbGVibG9jayMtLT4NCg0KCQk8IS0tIFRoZXNlIGFyZSB1c2VkIHRvIHNob3cgZGF0YXNldCBoYW5kbGluZyAtLT4NCgkJPHA+PGI+PCEtLUBoZWxsb0AtLT4sIDwhLS1AdGVtcGxhdGVALS0+PC9iPjwvcD4NCg0KCTwvYm9keT4NCjwvaHRtbD4NCiI7czo2OiJibG9ja3MiO2E6Mjp7czoxMjoiZXhhbXBsZWJsb2NrIjtPOjE1OiJ6Z1RlbXBsYXRlQmxvY2siOjQ6e3M6MTQ6ImN1cnJlbnRDb250ZW50IjtzOjc2OiINCgkJCTxwPlRoaXMgaXMgY29udGVudCBpbnNpZGUgYSBibG9jazogPGI+PCEtLUBibG9ja2NvbnRlbnRALS0+PC9iPjwvcD4NCgkJIjtzOjE1OiJvcmlnaW5hbENvbnRlbnQiO3M6NzY6Ig0KCQkJPHA+VGhpcyBpcyBjb250ZW50IGluc2lkZSBhIGJsb2NrOiA8Yj48IS0tQGJsb2NrY29udGVudEAtLT48L2I+PC9wPg0KCQkiO3M6MTE6ImJsb2NrUGFyZW50IjtOO3M6MTQ6ImJsb2NrVmFyaWFibGVzIjthOjE6e3M6MTI6ImJsb2NrY29udGVudCI7czoyMToiPCEtLUBibG9ja2NvbnRlbnRALS0+Ijt9fXM6NDoicm9vdCI7TzoxNToiemdUZW1wbGF0ZUJsb2NrIjo0OntzOjE0OiJjdXJyZW50Q29udGVudCI7TjtzOjE1OiJvcmlnaW5hbENvbnRlbnQiO047czoxMToiYmxvY2tQYXJlbnQiO047czoxNDoiYmxvY2tWYXJpYWJsZXMiO2E6NTp7czo3OiJWQVJOQU1FIjtzOjE2OiI8IS0tQFZBUk5BTUVALS0+IjtzOjE0OiJleGFtcGxlY29udGVudCI7czoyMzoiPCEtLUBleGFtcGxlY29udGVudEAtLT4iO3M6MTA6Im1hbnVhbGxpbmsiO3M6MTk6IjwhLS1AbWFudWFsbGlua0AtLT4iO3M6NToiaGVsbG8iO3M6MTQ6IjwhLS1AaGVsbG9ALS0+IjtzOjg6InRlbXBsYXRlIjtzOjE3OiI8IS0tQHRlbXBsYXRlQC0tPiI7fX19czo5OiJ2YXJpYWJsZXMiO2E6Njp7czoxMjoiYmxvY2tjb250ZW50IjtPOjE4OiJ6Z1RlbXBsYXRlVmFyaWFibGUiOjI6e3M6MTQ6ImN1cnJlbnRDb250ZW50IjtOO3M6MTQ6ImRlZmF1bHRDb250ZW50IjtOO31zOjc6IlZBUk5BTUUiO086MTg6InpnVGVtcGxhdGVWYXJpYWJsZSI6Mjp7czoxNDoiY3VycmVudENvbnRlbnQiO047czoxNDoiZGVmYXVsdENvbnRlbnQiO047fXM6MTQ6ImV4YW1wbGVjb250ZW50IjtPOjE4OiJ6Z1RlbXBsYXRlVmFyaWFibGUiOjI6e3M6MTQ6ImN1cnJlbnRDb250ZW50IjtOO3M6MTQ6ImRlZmF1bHRDb250ZW50IjtOO31zOjEwOiJtYW51YWxsaW5rIjtPOjE4OiJ6Z1RlbXBsYXRlVmFyaWFibGUiOjI6e3M6MTQ6ImN1cnJlbnRDb250ZW50IjtOO3M6MTQ6ImRlZmF1bHRDb250ZW50IjtOO31zOjU6ImhlbGxvIjtPOjE4OiJ6Z1RlbXBsYXRlVmFyaWFibGUiOjI6e3M6MTQ6ImN1cnJlbnRDb250ZW50IjtOO3M6MTQ6ImRlZmF1bHRDb250ZW50IjtOO31zOjg6InRlbXBsYXRlIjtPOjE4OiJ6Z1RlbXBsYXRlVmFyaWFibGUiOjI6e3M6MTQ6ImN1cnJlbnRDb250ZW50IjtOO3M6MTQ6ImRlZmF1bHRDb250ZW50IjtOO319fQ==');

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
  `userdata_user` int(12) NOT NULL DEFAULT '0',
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
  `userright_id` int(12) NOT NULL AUTO_INCREMENT,
  `userright_action` int(12) NOT NULL DEFAULT '0',
  `userright_user` int(12) NOT NULL DEFAULT '0',
  PRIMARY KEY (`userright_id`),
  KEY `userright_action` (`userright_action`),
  KEY `userright_user` (`userright_user`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

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
  `user_active` tinyint(1) NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Daten für Tabelle `users`
--

