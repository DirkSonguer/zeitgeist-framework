-- phpMyAdmin SQL Dump
-- version 3.1.3.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Erstellungszeit: 17. August 2009 um 20:55
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=25 ;

--
-- Daten für Tabelle `actions`
--

INSERT INTO `actions` (`action_id`, `action_module`, `action_name`, `action_description`, `action_requiresuserright`) VALUES
(1, 1, 'index', 'Main index action', 0),
(2, 1, 'basics', 'Explains the framework basics', 0),
(22, 3, 'index', 'Overview of message examples', 0),
(23, 3, 'example1', 'Example 1', 0),
(24, 3, 'example2', 'Example 2', 0);

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Daten für Tabelle `configurationcache`
--

INSERT INTO `configurationcache` (`configurationcache_id`, `configurationcache_name`, `configurationcache_timestamp`, `configurationcache_content`) VALUES
(5, './zeitgeist/configuration/zeitgeist.ini', '1250340228', 'YToxMDp7czo3OiJtb2R1bGVzIjthOjI6e3M6MTE6ImZvcm1jcmVhdG9yIjtzOjQ6InRydWUiO3M6NDoic2hvcCI7czo0OiJ0cnVlIjt9czo2OiJ0YWJsZXMiO2E6MTQ6e3M6MTM6InRhYmxlX2FjdGlvbnMiO3M6NzoiYWN0aW9ucyI7czoxODoidGFibGVfbWVzc2FnZWNhY2hlIjtzOjEyOiJtZXNzYWdlY2FjaGUiO3M6MTM6InRhYmxlX21vZHVsZXMiO3M6NzoibW9kdWxlcyI7czoxNzoidGFibGVfc2Vzc2lvbmRhdGEiO3M6MTE6InNlc3Npb25kYXRhIjtzOjE5OiJ0YWJsZV90ZW1wbGF0ZWNhY2hlIjtzOjEzOiJ0ZW1wbGF0ZWNhY2hlIjtzOjExOiJ0YWJsZV91c2VycyI7czo1OiJ1c2VycyI7czoxNDoidGFibGVfdXNlcmRhdGEiO3M6ODoidXNlcmRhdGEiO3M6MTY6InRhYmxlX3VzZXJyaWdodHMiO3M6MTA6InVzZXJyaWdodHMiO3M6MTU6InRhYmxlX3VzZXJyb2xlcyI7czo5OiJ1c2Vycm9sZXMiO3M6MjA6InRhYmxlX3VzZXJjaGFyYWN0ZXJzIjtzOjE0OiJ1c2VyY2hhcmFjdGVycyI7czoyNDoidGFibGVfdXNlcnJvbGVzX3RvX3VzZXJzIjtzOjE4OiJ1c2Vycm9sZXNfdG9fdXNlcnMiO3M6MjY6InRhYmxlX3VzZXJyb2xlc190b19hY3Rpb25zIjtzOjIwOiJ1c2Vycm9sZXNfdG9fYWN0aW9ucyI7czoxODoidGFibGVfdXNlcnNlc3Npb25zIjtzOjEyOiJ1c2Vyc2Vzc2lvbnMiO3M6MjI6InRhYmxlX3VzZXJjb25maXJtYXRpb24iO3M6MTY6InVzZXJjb25maXJtYXRpb24iO31zOjc6InNlc3Npb24iO2E6Mzp7czoxNToic2Vzc2lvbl9zdG9yYWdlIjtzOjg6ImRhdGFiYXNlIjtzOjEyOiJzZXNzaW9uX25hbWUiO3M6MTk6IlpFSVRHRUlTVF9TRVNTSU9OSUQiO3M6MTY6InNlc3Npb25fbGlmZXRpbWUiO3M6MToiMCI7fXM6ODoibWVzc2FnZXMiO2E6MTp7czoyMzoidXNlX3BlcnNpc3RlbnRfbWVzc2FnZXMiO3M6MToiMSI7fXM6ODoidGVtcGxhdGUiO2E6MTU6e3M6MTI6InJld3JpdGVfdXJscyI7czoxOiIwIjtzOjE4OiJ2YXJpYWJsZVN1YnN0QmVnaW4iO3M6NToiPCEtLUAiO3M6MTY6InZhcmlhYmxlU3Vic3RFbmQiO3M6NDoiQC0tPiI7czoxNToiYmxvY2tTdWJzdEJlZ2luIjtzOjU6IjwhLS0jIjtzOjEzOiJibG9ja1N1YnN0RW5kIjtzOjQ6IiMtLT4iO3M6OToibGlua0JlZ2luIjtzOjQ6IkBAe1siO3M6NzoibGlua0VuZCI7czo0OiJdfUBAIjtzOjEzOiJ2YXJpYWJsZUJlZ2luIjtzOjM6IkBAeyI7czoxMToidmFyaWFibGVFbmQiO3M6MzoifUBAIjtzOjE0OiJibG9ja09wZW5CZWdpbiI7czozMDoiPCEtLSBUZW1wbGF0ZUJlZ2luQmxvY2sgbmFtZT0iIjtzOjEyOiJibG9ja09wZW5FbmQiO3M6NToiIiAtLT4iO3M6MTA6ImJsb2NrQ2xvc2UiO3M6MjU6IjwhLS0gVGVtcGxhdGVFbmRCbG9jayAtLT4iO3M6MTk6IlVzZXJtZXNzYWdlV2FybmluZ3MiO3M6MTI6InVzZXJ3YXJuaW5ncyI7czoxNzoiVXNlcm1lc3NhZ2VFcnJvcnMiO3M6MTA6InVzZXJlcnJvcnMiO3M6MTk6IlVzZXJtZXNzYWdlTWVzc2FnZXMiO3M6MTI6InVzZXJtZXNzYWdlcyI7fXM6MTI6ImV2ZW50aGFuZGxlciI7YTozOntzOjI0OiJub191c2VycmlnaHRzX2Zvcl9hY3Rpb24iO3M6MjoiLTEiO3M6Mjg6InJlcXVpcmVkX3BhcmFtZXRlcl9ub3RfZm91bmQiO3M6MjoiLTIiO3M6OToibWV0aG9kX29rIjtzOjE6IjEiO31zOjEzOiJ0cmFmZmljbG9nZ2VyIjthOjE6e3M6MjA6InRyYWZmaWNsb2dnZXJfYWN0aXZlIjtzOjE6IjAiO31zOjEyOiJlcnJvcmhhbmRsZXIiO2E6MTp7czoxNzoiZXJyb3JfcmVwb3J0bGV2ZWwiO3M6MToiMiI7fXM6MTE6InVzZXJoYW5kbGVyIjthOjE6e3M6MTU6InVzZV9kb3VibGVvcHRpbiI7czoxOiIxIjt9czoxNjoicGFyYW1ldGVyaGFuZGxlciI7YTo4OntzOjE3OiJlc2NhcGVfcGFyYW1ldGVycyI7czoxOiIxIjtzOjU6ImVtYWlsIjtzOjY2OiIvXltcd1wtXCtcJlwqXSsoPzpcLltcd1wtXF9cK1wmXCpdKykqQCg/Oltcdy1dK1wuKStbYS16QS1aXXsyLDd9JC8iO3M6MzoidXJsIjtzOjg1OiIvXihmdHB8aHR0cHxodHRwcyk6XC9cLyhcdys6ezAsMX1cdypAKT8oXFMrKSg6WzAtOV0rKT8oXC98XC8oW1x3IyE6Lj8rPSYlQCFcLVwvXSkpPyQvIjtzOjM6InppcCI7czoxMToiL15cZHszLDV9JC8iO3M6Njoic3RyaW5nIjtzOjY3OiIvXltcd8O8w5zDpMOEw7bDliBdKygoW1wsXC5cOlwtXC9cKFwpXCFcPyBdKT9bXHfDvMOcw6TDhMO2w5YgXSopKiQvIjtzOjQ6InRleHQiO3M6Nzc6Ii9eW1x3w7zDnMOkw4TDtsOWIF0rKChbXFxcIlwsXC5cOlwtXC9cclxuXHRcIVw/XChcKSBdKT9bXHfDvMOcw6TDhMO2w5YgXSopKiQvIjtzOjY6Im51bWJlciI7czoyNDoiL15bMC05XSooXC58XCwpP1swLTldKyQvIjtzOjQ6ImRhdGUiO3M6Mzg6Ii9eWzAtOV17Mn0oXC4pP1swLTldezJ9KFwuKT9bMC05XXs0fSQvIjt9fQ=='),
(2, './configuration/zeitgeist.ini', '1196407276', 'YToxOntzOjEzOiJvdmVyd3JpdGV0ZXN0IjthOjE6e3M6NDoidGVzdCI7czo0OiJ0cnVlIjt9fQ=='),
(3, './modules/main/main.ini', '1195978414', 'YToyOntzOjU6ImluZGV4IjthOjM6e3M6MjE6Imhhc0V4dGVybmFsUGFyYW1ldGVycyI7czo0OiJ0cnVlIjtzOjU6InRlc3QxIjthOjM6e3M6OToicGFyYW1ldGVyIjtzOjQ6InRydWUiO3M6Njoic291cmNlIjtzOjM6IkdFVCI7czo0OiJ0eXBlIjtzOjExOiIvXi57NCwzMn0kLyI7fXM6NToidGVzdDIiO2E6Mzp7czo5OiJwYXJhbWV0ZXIiO3M6NDoidHJ1ZSI7czo2OiJzb3VyY2UiO3M6MzoiR0VUIjtzOjQ6InR5cGUiO3M6NToiL14uJC8iO319czo0OiJzaG93IjthOjM6e3M6OToiUHJlU25hcEluIjthOjI6e2k6MDtzOjEwOiJ0ZXN0LmZ1bmMxIjtpOjE7czoxMDoidGVzdC5mdW5jMiI7fXM6MjE6Imhhc0V4dGVybmFsUGFyYW1ldGVycyI7czo0OiJ0cnVlIjtzOjk6ImlkX3NvdXJjZSI7czo0OiJfR0VUIjt9fQ=='),
(4, './modules/messages/messages.ini', '1250527069', 'YToxOntzOjU6ImluZGV4IjthOjE6e3M6MjE6Imhhc0V4dGVybmFsUGFyYW1ldGVycyI7czo1OiJmYWxzZSI7fX0=');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `messagecache`
--

CREATE TABLE IF NOT EXISTS `messagecache` (
  `messagecache_session` varchar(36) NOT NULL,
  `messagecache_content` text CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  PRIMARY KEY (`messagecache_session`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `messagecache`
--

INSERT INTO `messagecache` (`messagecache_session`, `messagecache_content`) VALUES
('0', 'YTo0MTp7aTowO086OToiemdNZXNzYWdlIjo0OntzOjc6Im1lc3NhZ2UiO3M6NjI6IkNvdWxkIG5vdCBlc3RhYmxpc2ggdXNlciBzZXNzaW9uOiB1c2VyIGlkIG5vdCBmb3VuZCBpbiBzZXNzaW9uIjtzOjQ6InR5cGUiO3M6Nzoid2FybmluZyI7czo0OiJmcm9tIjtzOjIxOiJ1c2VyaGFuZGxlci5jbGFzcy5waHAiO3M6MjoidG8iO047fWk6MTtPOjk6InpnTWVzc2FnZSI6NDp7czo3OiJtZXNzYWdlIjtzOjYyOiJDb3VsZCBub3QgZXN0YWJsaXNoIHVzZXIgc2Vzc2lvbjogdXNlciBpZCBub3QgZm91bmQgaW4gc2Vzc2lvbiI7czo0OiJ0eXBlIjtzOjc6Indhcm5pbmciO3M6NDoiZnJvbSI7czoyMToidXNlcmhhbmRsZXIuY2xhc3MucGhwIjtzOjI6InRvIjtOO31pOjI7Tzo5OiJ6Z01lc3NhZ2UiOjQ6e3M6NzoibWVzc2FnZSI7czo2MjoiQ291bGQgbm90IGVzdGFibGlzaCB1c2VyIHNlc3Npb246IHVzZXIgaWQgbm90IGZvdW5kIGluIHNlc3Npb24iO3M6NDoidHlwZSI7czo3OiJ3YXJuaW5nIjtzOjQ6ImZyb20iO3M6MjE6InVzZXJoYW5kbGVyLmNsYXNzLnBocCI7czoyOiJ0byI7Tjt9aTozO086OToiemdNZXNzYWdlIjo0OntzOjc6Im1lc3NhZ2UiO3M6NjI6IkNvdWxkIG5vdCBlc3RhYmxpc2ggdXNlciBzZXNzaW9uOiB1c2VyIGlkIG5vdCBmb3VuZCBpbiBzZXNzaW9uIjtzOjQ6InR5cGUiO3M6Nzoid2FybmluZyI7czo0OiJmcm9tIjtzOjIxOiJ1c2VyaGFuZGxlci5jbGFzcy5waHAiO3M6MjoidG8iO047fWk6NDtPOjk6InpnTWVzc2FnZSI6NDp7czo3OiJtZXNzYWdlIjtzOjYyOiJDb3VsZCBub3QgZXN0YWJsaXNoIHVzZXIgc2Vzc2lvbjogdXNlciBpZCBub3QgZm91bmQgaW4gc2Vzc2lvbiI7czo0OiJ0eXBlIjtzOjc6Indhcm5pbmciO3M6NDoiZnJvbSI7czoyMToidXNlcmhhbmRsZXIuY2xhc3MucGhwIjtzOjI6InRvIjtOO31pOjU7Tzo5OiJ6Z01lc3NhZ2UiOjQ6e3M6NzoibWVzc2FnZSI7czo2MjoiQ291bGQgbm90IGVzdGFibGlzaCB1c2VyIHNlc3Npb246IHVzZXIgaWQgbm90IGZvdW5kIGluIHNlc3Npb24iO3M6NDoidHlwZSI7czo3OiJ3YXJuaW5nIjtzOjQ6ImZyb20iO3M6MjE6InVzZXJoYW5kbGVyLmNsYXNzLnBocCI7czoyOiJ0byI7Tjt9aTo2O086OToiemdNZXNzYWdlIjo0OntzOjc6Im1lc3NhZ2UiO3M6NjI6IkNvdWxkIG5vdCBlc3RhYmxpc2ggdXNlciBzZXNzaW9uOiB1c2VyIGlkIG5vdCBmb3VuZCBpbiBzZXNzaW9uIjtzOjQ6InR5cGUiO3M6Nzoid2FybmluZyI7czo0OiJmcm9tIjtzOjIxOiJ1c2VyaGFuZGxlci5jbGFzcy5waHAiO3M6MjoidG8iO047fWk6NztPOjk6InpnTWVzc2FnZSI6NDp7czo3OiJtZXNzYWdlIjtzOjYyOiJDb3VsZCBub3QgZXN0YWJsaXNoIHVzZXIgc2Vzc2lvbjogdXNlciBpZCBub3QgZm91bmQgaW4gc2Vzc2lvbiI7czo0OiJ0eXBlIjtzOjc6Indhcm5pbmciO3M6NDoiZnJvbSI7czoyMToidXNlcmhhbmRsZXIuY2xhc3MucGhwIjtzOjI6InRvIjtOO31pOjg7Tzo5OiJ6Z01lc3NhZ2UiOjQ6e3M6NzoibWVzc2FnZSI7czo2MjoiQ291bGQgbm90IGVzdGFibGlzaCB1c2VyIHNlc3Npb246IHVzZXIgaWQgbm90IGZvdW5kIGluIHNlc3Npb24iO3M6NDoidHlwZSI7czo3OiJ3YXJuaW5nIjtzOjQ6ImZyb20iO3M6MjE6InVzZXJoYW5kbGVyLmNsYXNzLnBocCI7czoyOiJ0byI7Tjt9aTo5O086OToiemdNZXNzYWdlIjo0OntzOjc6Im1lc3NhZ2UiO3M6NjI6IkNvdWxkIG5vdCBlc3RhYmxpc2ggdXNlciBzZXNzaW9uOiB1c2VyIGlkIG5vdCBmb3VuZCBpbiBzZXNzaW9uIjtzOjQ6InR5cGUiO3M6Nzoid2FybmluZyI7czo0OiJmcm9tIjtzOjIxOiJ1c2VyaGFuZGxlci5jbGFzcy5waHAiO3M6MjoidG8iO047fWk6MTA7Tzo5OiJ6Z01lc3NhZ2UiOjQ6e3M6NzoibWVzc2FnZSI7czo2MjoiQ291bGQgbm90IGVzdGFibGlzaCB1c2VyIHNlc3Npb246IHVzZXIgaWQgbm90IGZvdW5kIGluIHNlc3Npb24iO3M6NDoidHlwZSI7czo3OiJ3YXJuaW5nIjtzOjQ6ImZyb20iO3M6MjE6InVzZXJoYW5kbGVyLmNsYXNzLnBocCI7czoyOiJ0byI7Tjt9aToxMTtPOjk6InpnTWVzc2FnZSI6NDp7czo3OiJtZXNzYWdlIjtzOjYyOiJDb3VsZCBub3QgZXN0YWJsaXNoIHVzZXIgc2Vzc2lvbjogdXNlciBpZCBub3QgZm91bmQgaW4gc2Vzc2lvbiI7czo0OiJ0eXBlIjtzOjc6Indhcm5pbmciO3M6NDoiZnJvbSI7czoyMToidXNlcmhhbmRsZXIuY2xhc3MucGhwIjtzOjI6InRvIjtOO31pOjEyO086OToiemdNZXNzYWdlIjo0OntzOjc6Im1lc3NhZ2UiO3M6NjI6IkNvdWxkIG5vdCBlc3RhYmxpc2ggdXNlciBzZXNzaW9uOiB1c2VyIGlkIG5vdCBmb3VuZCBpbiBzZXNzaW9uIjtzOjQ6InR5cGUiO3M6Nzoid2FybmluZyI7czo0OiJmcm9tIjtzOjIxOiJ1c2VyaGFuZGxlci5jbGFzcy5waHAiO3M6MjoidG8iO047fWk6MTM7Tzo5OiJ6Z01lc3NhZ2UiOjQ6e3M6NzoibWVzc2FnZSI7czo2MjoiQ291bGQgbm90IGVzdGFibGlzaCB1c2VyIHNlc3Npb246IHVzZXIgaWQgbm90IGZvdW5kIGluIHNlc3Npb24iO3M6NDoidHlwZSI7czo3OiJ3YXJuaW5nIjtzOjQ6ImZyb20iO3M6MjE6InVzZXJoYW5kbGVyLmNsYXNzLnBocCI7czoyOiJ0byI7Tjt9aToxNDtPOjk6InpnTWVzc2FnZSI6NDp7czo3OiJtZXNzYWdlIjtzOjYyOiJDb3VsZCBub3QgZXN0YWJsaXNoIHVzZXIgc2Vzc2lvbjogdXNlciBpZCBub3QgZm91bmQgaW4gc2Vzc2lvbiI7czo0OiJ0eXBlIjtzOjc6Indhcm5pbmciO3M6NDoiZnJvbSI7czoyMToidXNlcmhhbmRsZXIuY2xhc3MucGhwIjtzOjI6InRvIjtOO31pOjE1O086OToiemdNZXNzYWdlIjo0OntzOjc6Im1lc3NhZ2UiO3M6NjI6IkNvdWxkIG5vdCBlc3RhYmxpc2ggdXNlciBzZXNzaW9uOiB1c2VyIGlkIG5vdCBmb3VuZCBpbiBzZXNzaW9uIjtzOjQ6InR5cGUiO3M6Nzoid2FybmluZyI7czo0OiJmcm9tIjtzOjIxOiJ1c2VyaGFuZGxlci5jbGFzcy5waHAiO3M6MjoidG8iO047fWk6MTY7Tzo5OiJ6Z01lc3NhZ2UiOjQ6e3M6NzoibWVzc2FnZSI7czo2MjoiQ291bGQgbm90IGVzdGFibGlzaCB1c2VyIHNlc3Npb246IHVzZXIgaWQgbm90IGZvdW5kIGluIHNlc3Npb24iO3M6NDoidHlwZSI7czo3OiJ3YXJuaW5nIjtzOjQ6ImZyb20iO3M6MjE6InVzZXJoYW5kbGVyLmNsYXNzLnBocCI7czoyOiJ0byI7Tjt9aToxNztPOjk6InpnTWVzc2FnZSI6NDp7czo3OiJtZXNzYWdlIjtzOjYyOiJDb3VsZCBub3QgZXN0YWJsaXNoIHVzZXIgc2Vzc2lvbjogdXNlciBpZCBub3QgZm91bmQgaW4gc2Vzc2lvbiI7czo0OiJ0eXBlIjtzOjc6Indhcm5pbmciO3M6NDoiZnJvbSI7czoyMToidXNlcmhhbmRsZXIuY2xhc3MucGhwIjtzOjI6InRvIjtOO31pOjE4O086OToiemdNZXNzYWdlIjo0OntzOjc6Im1lc3NhZ2UiO3M6NjI6IkNvdWxkIG5vdCBlc3RhYmxpc2ggdXNlciBzZXNzaW9uOiB1c2VyIGlkIG5vdCBmb3VuZCBpbiBzZXNzaW9uIjtzOjQ6InR5cGUiO3M6Nzoid2FybmluZyI7czo0OiJmcm9tIjtzOjIxOiJ1c2VyaGFuZGxlci5jbGFzcy5waHAiO3M6MjoidG8iO047fWk6MTk7Tzo5OiJ6Z01lc3NhZ2UiOjQ6e3M6NzoibWVzc2FnZSI7czo2MjoiQ291bGQgbm90IGVzdGFibGlzaCB1c2VyIHNlc3Npb246IHVzZXIgaWQgbm90IGZvdW5kIGluIHNlc3Npb24iO3M6NDoidHlwZSI7czo3OiJ3YXJuaW5nIjtzOjQ6ImZyb20iO3M6MjE6InVzZXJoYW5kbGVyLmNsYXNzLnBocCI7czoyOiJ0byI7Tjt9aToyMDtPOjk6InpnTWVzc2FnZSI6NDp7czo3OiJtZXNzYWdlIjtzOjU0OiJObyBjb25maWd1cmF0aW9uIGlzIHN0b3JlZCBpbiBkYXRhYmFzZSBmb3IgdGhpcyBtb2R1bGUiO3M6NDoidHlwZSI7czo3OiJ3YXJuaW5nIjtzOjQ6ImZyb20iO3M6MjM6ImNvbmZpZ3VyYXRpb24uY2xhc3MucGhwIjtzOjI6InRvIjtOO31pOjIxO086OToiemdNZXNzYWdlIjo0OntzOjc6Im1lc3NhZ2UiO3M6NTQ6Ik5vIGNvbmZpZ3VyYXRpb24gaXMgc3RvcmVkIGluIGRhdGFiYXNlIGZvciB0aGlzIG1vZHVsZSI7czo0OiJ0eXBlIjtzOjc6Indhcm5pbmciO3M6NDoiZnJvbSI7czoyMzoiY29uZmlndXJhdGlvbi5jbGFzcy5waHAiO3M6MjoidG8iO047fWk6MjI7Tzo5OiJ6Z01lc3NhZ2UiOjQ6e3M6NzoibWVzc2FnZSI7czo2MjoiQ291bGQgbm90IGVzdGFibGlzaCB1c2VyIHNlc3Npb246IHVzZXIgaWQgbm90IGZvdW5kIGluIHNlc3Npb24iO3M6NDoidHlwZSI7czo3OiJ3YXJuaW5nIjtzOjQ6ImZyb20iO3M6MjE6InVzZXJoYW5kbGVyLmNsYXNzLnBocCI7czoyOiJ0byI7Tjt9aToyMztPOjk6InpnTWVzc2FnZSI6NDp7czo3OiJtZXNzYWdlIjtzOjUwOiJObyBtZXNzYWdlZGF0YSBpcyBzdG9yZWQgaW4gZGF0YWJhc2UgZm9yIHRoaXMgdXNlciI7czo0OiJ0eXBlIjtzOjc6Indhcm5pbmciO3M6NDoiZnJvbSI7czoyMjoibWVzc2FnZWNhY2hlLmNsYXNzLnBocCI7czoyOiJ0byI7Tjt9aToyNDtPOjk6InpnTWVzc2FnZSI6NDp7czo3OiJtZXNzYWdlIjtzOjU0OiJObyBjb25maWd1cmF0aW9uIGlzIHN0b3JlZCBpbiBkYXRhYmFzZSBmb3IgdGhpcyBtb2R1bGUiO3M6NDoidHlwZSI7czo3OiJ3YXJuaW5nIjtzOjQ6ImZyb20iO3M6MjM6ImNvbmZpZ3VyYXRpb24uY2xhc3MucGhwIjtzOjI6InRvIjtOO31pOjI1O086OToiemdNZXNzYWdlIjo0OntzOjc6Im1lc3NhZ2UiO3M6NzA6IkNvdWxkIG5vdCBmaW5kIHRoZSB0ZW1wbGF0ZSBmaWxlOiB0ZW1wbGF0ZXMvemdleGFtcGxlcy9tYWluX2luZGV4Lmh0bWwiO3M6NDoidHlwZSI7czo1OiJlcnJvciI7czo0OiJmcm9tIjtzOjE4OiJ0ZW1wbGF0ZS5jbGFzcy5waHAiO3M6MjoidG8iO047fWk6MjY7Tzo5OiJ6Z01lc3NhZ2UiOjQ6e3M6NzoibWVzc2FnZSI7czo3MDoiQ291bGQgbm90IGZpbmQgdGhlIHRlbXBsYXRlIGZpbGU6IHRlbXBsYXRlcy96Z2V4YW1wbGVzL21haW5faW5kZXguaHRtbCI7czo0OiJ0eXBlIjtzOjU6ImVycm9yIjtzOjQ6ImZyb20iO3M6MTg6InRlbXBsYXRlLmNsYXNzLnBocCI7czoyOiJ0byI7Tjt9aToyNztPOjk6InpnTWVzc2FnZSI6NDp7czo3OiJtZXNzYWdlIjtzOjcwOiJDb3VsZCBub3QgZmluZCB0aGUgdGVtcGxhdGUgZmlsZTogdGVtcGxhdGVzL3pnZXhhbXBsZXMvbWFpbl9pbmRleC5odG1sIjtzOjQ6InR5cGUiO3M6NToiZXJyb3IiO3M6NDoiZnJvbSI7czoxODoidGVtcGxhdGUuY2xhc3MucGhwIjtzOjI6InRvIjtOO31pOjI4O086OToiemdNZXNzYWdlIjo0OntzOjc6Im1lc3NhZ2UiO3M6NzA6IkNvdWxkIG5vdCBmaW5kIHRoZSB0ZW1wbGF0ZSBmaWxlOiB0ZW1wbGF0ZXMvemdleGFtcGxlcy9tYWluX2luZGV4Lmh0bWwiO3M6NDoidHlwZSI7czo1OiJlcnJvciI7czo0OiJmcm9tIjtzOjE4OiJ0ZW1wbGF0ZS5jbGFzcy5waHAiO3M6MjoidG8iO047fWk6Mjk7Tzo5OiJ6Z01lc3NhZ2UiOjQ6e3M6NzoibWVzc2FnZSI7czo3MDoiQ291bGQgbm90IGZpbmQgdGhlIHRlbXBsYXRlIGZpbGU6IHRlbXBsYXRlcy96Z2V4YW1wbGVzL21haW5faW5kZXguaHRtbCI7czo0OiJ0eXBlIjtzOjU6ImVycm9yIjtzOjQ6ImZyb20iO3M6MTg6InRlbXBsYXRlLmNsYXNzLnBocCI7czoyOiJ0byI7Tjt9aTozMDtPOjk6InpnTWVzc2FnZSI6NDp7czo3OiJtZXNzYWdlIjtzOjcwOiJDb3VsZCBub3QgZmluZCB0aGUgdGVtcGxhdGUgZmlsZTogdGVtcGxhdGVzL3pnZXhhbXBsZXMvbWFpbl9pbmRleC5odG1sIjtzOjQ6InR5cGUiO3M6NToiZXJyb3IiO3M6NDoiZnJvbSI7czoxODoidGVtcGxhdGUuY2xhc3MucGhwIjtzOjI6InRvIjtOO31pOjMxO086OToiemdNZXNzYWdlIjo0OntzOjc6Im1lc3NhZ2UiO3M6NzA6IkNvdWxkIG5vdCBmaW5kIHRoZSB0ZW1wbGF0ZSBmaWxlOiB0ZW1wbGF0ZXMvemdleGFtcGxlcy9tYWluX2luZGV4Lmh0bWwiO3M6NDoidHlwZSI7czo1OiJlcnJvciI7czo0OiJmcm9tIjtzOjE4OiJ0ZW1wbGF0ZS5jbGFzcy5waHAiO3M6MjoidG8iO047fWk6MzI7Tzo5OiJ6Z01lc3NhZ2UiOjQ6e3M6NzoibWVzc2FnZSI7czo3MDoiQ291bGQgbm90IGZpbmQgdGhlIHRlbXBsYXRlIGZpbGU6IHRlbXBsYXRlcy96Z2V4YW1wbGVzL21haW5faW5kZXguaHRtbCI7czo0OiJ0eXBlIjtzOjU6ImVycm9yIjtzOjQ6ImZyb20iO3M6MTg6InRlbXBsYXRlLmNsYXNzLnBocCI7czoyOiJ0byI7Tjt9aTozMztPOjk6InpnTWVzc2FnZSI6NDp7czo3OiJtZXNzYWdlIjtzOjcwOiJDb3VsZCBub3QgZmluZCB0aGUgdGVtcGxhdGUgZmlsZTogdGVtcGxhdGVzL3pnZXhhbXBsZXMvbWFpbl9pbmRleC5odG1sIjtzOjQ6InR5cGUiO3M6NToiZXJyb3IiO3M6NDoiZnJvbSI7czoxODoidGVtcGxhdGUuY2xhc3MucGhwIjtzOjI6InRvIjtOO31pOjM0O086OToiemdNZXNzYWdlIjo0OntzOjc6Im1lc3NhZ2UiO3M6NzA6IkNvdWxkIG5vdCBmaW5kIHRoZSB0ZW1wbGF0ZSBmaWxlOiB0ZW1wbGF0ZXMvemdleGFtcGxlcy9tYWluX2luZGV4Lmh0bWwiO3M6NDoidHlwZSI7czo1OiJlcnJvciI7czo0OiJmcm9tIjtzOjE4OiJ0ZW1wbGF0ZS5jbGFzcy5waHAiO3M6MjoidG8iO047fWk6MzU7Tzo5OiJ6Z01lc3NhZ2UiOjQ6e3M6NzoibWVzc2FnZSI7czo3MDoiQ291bGQgbm90IGZpbmQgdGhlIHRlbXBsYXRlIGZpbGU6IHRlbXBsYXRlcy96Z2V4YW1wbGVzL21haW5faW5kZXguaHRtbCI7czo0OiJ0eXBlIjtzOjU6ImVycm9yIjtzOjQ6ImZyb20iO3M6MTg6InRlbXBsYXRlLmNsYXNzLnBocCI7czoyOiJ0byI7Tjt9aTozNjtPOjk6InpnTWVzc2FnZSI6NDp7czo3OiJtZXNzYWdlIjtzOjcwOiJDb3VsZCBub3QgZmluZCB0aGUgdGVtcGxhdGUgZmlsZTogdGVtcGxhdGVzL3pnZXhhbXBsZXMvbWFpbl9pbmRleC5odG1sIjtzOjQ6InR5cGUiO3M6NToiZXJyb3IiO3M6NDoiZnJvbSI7czoxODoidGVtcGxhdGUuY2xhc3MucGhwIjtzOjI6InRvIjtOO31pOjM3O086OToiemdNZXNzYWdlIjo0OntzOjc6Im1lc3NhZ2UiO3M6NTU6Ik5vIHRlbXBsYXRlZGF0YSBpcyBzdG9yZWQgaW4gZGF0YWJhc2UgZm9yIHRoaXMgdGVtcGxhdGUiO3M6NDoidHlwZSI7czo3OiJ3YXJuaW5nIjtzOjQ6ImZyb20iO3M6MTg6InRlbXBsYXRlLmNsYXNzLnBocCI7czoyOiJ0byI7Tjt9aTozODtPOjk6InpnTWVzc2FnZSI6NDp7czo3OiJtZXNzYWdlIjtzOjQxOiJUZW1wbGF0ZSBkYXRhIGluIHRoZSBkYXRhYmFzZSBpcyBvdXRkYXRlZCI7czo0OiJ0eXBlIjtzOjc6Indhcm5pbmciO3M6NDoiZnJvbSI7czoxODoidGVtcGxhdGUuY2xhc3MucGhwIjtzOjI6InRvIjtOO31pOjM5O086OToiemdNZXNzYWdlIjo0OntzOjc6Im1lc3NhZ2UiO3M6NDE6IlRlbXBsYXRlIGRhdGEgaW4gdGhlIGRhdGFiYXNlIGlzIG91dGRhdGVkIjtzOjQ6InR5cGUiO3M6Nzoid2FybmluZyI7czo0OiJmcm9tIjtzOjE4OiJ0ZW1wbGF0ZS5jbGFzcy5waHAiO3M6MjoidG8iO047fWk6NDA7Tzo5OiJ6Z01lc3NhZ2UiOjQ6e3M6NzoibWVzc2FnZSI7czo0MToiVGVtcGxhdGUgZGF0YSBpbiB0aGUgZGF0YWJhc2UgaXMgb3V0ZGF0ZWQiO3M6NDoidHlwZSI7czo3OiJ3YXJuaW5nIjtzOjQ6ImZyb20iO3M6MTg6InRlbXBsYXRlLmNsYXNzLnBocCI7czoyOiJ0byI7Tjt9fQ=='),
('a6c73210bab2bbe4c16cd72c72d640c4', 'YTo5OntpOjA7Tzo5OiJ6Z01lc3NhZ2UiOjQ6e3M6NzoibWVzc2FnZSI7czo2MjoiQ291bGQgbm90IGVzdGFibGlzaCB1c2VyIHNlc3Npb246IHVzZXIgaWQgbm90IGZvdW5kIGluIHNlc3Npb24iO3M6NDoidHlwZSI7czo3OiJ3YXJuaW5nIjtzOjQ6ImZyb20iO3M6MjE6InVzZXJoYW5kbGVyLmNsYXNzLnBocCI7czoyOiJ0byI7Tjt9aToxO086OToiemdNZXNzYWdlIjo0OntzOjc6Im1lc3NhZ2UiO3M6NjI6IkNvdWxkIG5vdCBlc3RhYmxpc2ggdXNlciBzZXNzaW9uOiB1c2VyIGlkIG5vdCBmb3VuZCBpbiBzZXNzaW9uIjtzOjQ6InR5cGUiO3M6Nzoid2FybmluZyI7czo0OiJmcm9tIjtzOjIxOiJ1c2VyaGFuZGxlci5jbGFzcy5waHAiO3M6MjoidG8iO047fWk6MjtPOjk6InpnTWVzc2FnZSI6NDp7czo3OiJtZXNzYWdlIjtzOjYyOiJDb3VsZCBub3QgZXN0YWJsaXNoIHVzZXIgc2Vzc2lvbjogdXNlciBpZCBub3QgZm91bmQgaW4gc2Vzc2lvbiI7czo0OiJ0eXBlIjtzOjc6Indhcm5pbmciO3M6NDoiZnJvbSI7czoyMToidXNlcmhhbmRsZXIuY2xhc3MucGhwIjtzOjI6InRvIjtOO31pOjM7Tzo5OiJ6Z01lc3NhZ2UiOjQ6e3M6NzoibWVzc2FnZSI7czo0NjoiQ29uZmlndXJhdGlvbiBkYXRhIGluIHRoZSBkYXRhYmFzZSBpcyBvdXRkYXRlZCI7czo0OiJ0eXBlIjtzOjc6Indhcm5pbmciO3M6NDoiZnJvbSI7czoyMzoiY29uZmlndXJhdGlvbi5jbGFzcy5waHAiO3M6MjoidG8iO047fWk6NDtPOjk6InpnTWVzc2FnZSI6NDp7czo3OiJtZXNzYWdlIjtzOjYyOiJDb3VsZCBub3QgZXN0YWJsaXNoIHVzZXIgc2Vzc2lvbjogdXNlciBpZCBub3QgZm91bmQgaW4gc2Vzc2lvbiI7czo0OiJ0eXBlIjtzOjc6Indhcm5pbmciO3M6NDoiZnJvbSI7czoyMToidXNlcmhhbmRsZXIuY2xhc3MucGhwIjtzOjI6InRvIjtOO31pOjU7Tzo5OiJ6Z01lc3NhZ2UiOjQ6e3M6NzoibWVzc2FnZSI7czo1MDoiTm8gbWVzc2FnZWRhdGEgaXMgc3RvcmVkIGluIGRhdGFiYXNlIGZvciB0aGlzIHVzZXIiO3M6NDoidHlwZSI7czo3OiJ3YXJuaW5nIjtzOjQ6ImZyb20iO3M6MjI6Im1lc3NhZ2VjYWNoZS5jbGFzcy5waHAiO3M6MjoidG8iO047fWk6NjtPOjk6InpnTWVzc2FnZSI6NDp7czo3OiJtZXNzYWdlIjtzOjE0OiJIZWxsbyBNZXNzYWdlcyI7czo0OiJ0eXBlIjtzOjc6Im15X3R5cGUiO3M6NDoiZnJvbSI7czoxOToibWVzc2FnZXMubW9kdWxlLnBocCI7czoyOiJ0byI7Tjt9aTo3O086OToiemdNZXNzYWdlIjo0OntzOjc6Im1lc3NhZ2UiO3M6MTQ6IkhlbGxvIE1lc3NhZ2VzIjtzOjQ6InR5cGUiO3M6NzoibXlfdHlwZSI7czo0OiJmcm9tIjtzOjE5OiJtZXNzYWdlcy5tb2R1bGUucGhwIjtzOjI6InRvIjtOO31pOjg7Tzo5OiJ6Z01lc3NhZ2UiOjQ6e3M6NzoibWVzc2FnZSI7czoxNDoiSGVsbG8gTWVzc2FnZXMiO3M6NDoidHlwZSI7czo3OiJteV90eXBlIjtzOjQ6ImZyb20iO3M6MTk6Im1lc3NhZ2VzLm1vZHVsZS5waHAiO3M6MjoidG8iO047fX0=');

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=22 ;

--
-- Daten für Tabelle `modules`
--

INSERT INTO `modules` (`module_id`, `module_name`, `module_description`, `module_active`) VALUES
(1, 'main', 'Main module', 1),
(2, 'debug', 'Examples for the debug class', 1),
(3, 'messages', 'Examples for the message class', 1);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `preferences`
--

CREATE TABLE IF NOT EXISTS `preferences` (
  `preference_id` int(12) NOT NULL AUTO_INCREMENT,
  `preference_key` varchar(30) NOT NULL DEFAULT '',
  `preference_value` varchar(30) NOT NULL DEFAULT '',
  `preference_description` text,
  `preference_order` int(5) NOT NULL DEFAULT '0',
  PRIMARY KEY (`preference_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Daten für Tabelle `preferences`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `preferences_to_users`
--

CREATE TABLE IF NOT EXISTS `preferences_to_users` (
  `preferencesusers_id` int(12) NOT NULL AUTO_INCREMENT,
  `preferencesusers_user` int(12) NOT NULL DEFAULT '0',
  `preferencesusers_preference` int(12) NOT NULL DEFAULT '0',
  PRIMARY KEY (`preferencesusers_id`),
  KEY `preferencesusers_user` (`preferencesusers_user`),
  KEY `preferencesusers_preference` (`preferencesusers_preference`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Daten für Tabelle `preferences_to_users`
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `sessiondata`
--

INSERT INTO `sessiondata` (`sessiondata_id`, `sessiondata_created`, `sessiondata_lastupdate`, `sessiondata_content`, `sessiondata_ip`) VALUES
('4a9a71f9eb963f90385ad3b88d96df92', 1247505586, 1247763799, '', 2130706433),
('a6c73210bab2bbe4c16cd72c72d640c4', 1250490678, 1250527984, '', 2130706433);

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;

--
-- Daten für Tabelle `templatecache`
--

INSERT INTO `templatecache` (`templatecache_id`, `templatecache_name`, `templatecache_timestamp`, `templatecache_content`) VALUES
(9, 'templates/zgexamples/main_index.tpl.html', '1250491677', 'YTo0OntzOjQ6ImZpbGUiO3M6NDA6InRlbXBsYXRlcy96Z2V4YW1wbGVzL21haW5faW5kZXgudHBsLmh0bWwiO3M6NzoiY29udGVudCI7czoyMjkwOiI8IURPQ1RZUEUgaHRtbCBQVUJMSUMgIi0vL1czQy8vRFREIFhIVE1MIDEuMCBUcmFuc2l0aW9uYWwvL0VOIiAiaHR0cDovL3d3dy53My5vcmcvVFIveGh0bWwxL0RURC94aHRtbDEtdHJhbnNpdGlvbmFsLmR0ZCI+DQo8aHRtbCB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94aHRtbCI+PCEtLSBJbnN0YW5jZUJlZ2luIHRlbXBsYXRlPSIvVGVtcGxhdGVzL3pnZXhhbXBsZXMuZHd0IiBjb2RlT3V0c2lkZUhUTUxJc0xvY2tlZD0iZmFsc2UiIC0tPg0KPGhlYWQ+DQo8bWV0YSBodHRwLWVxdWl2PSJDb250ZW50LVR5cGUiIGNvbnRlbnQ9InRleHQvaHRtbDsgY2hhcnNldD11dGYtOCIgLz4NCjwhLS0gSW5zdGFuY2VCZWdpbkVkaXRhYmxlIG5hbWU9ImRvY3RpdGxlIiAtLT4NCjx0aXRsZT5aZWl0Z2Vpc3QgRXhhbXBsZXMgLSBIb21lPC90aXRsZT4NCjwhLS0gSW5zdGFuY2VFbmRFZGl0YWJsZSAtLT4NCg0KPGxpbmsgcmVsPSJzdHlsZXNoZWV0IiB0eXBlPSJ0ZXh0L2NzcyIgaHJlZj0idGVtcGxhdGVzL3pnZXhhbXBsZXMvY3NzL3pnZXhhbXBsZXMuY3NzIiAvPg0KDQo8c2NyaXB0IGxhbmd1YWdlPSJqYXZhc2NyaXB0IiB0eXBlPSJ0ZXh0L2phdmFzY3JpcHQiPg0KDQo8L3NjcmlwdD4NCgkNCjwhLS0gSW5zdGFuY2VCZWdpbkVkaXRhYmxlIG5hbWU9ImhlYWQiIC0tPg0KPCEtLSBJbnN0YW5jZUVuZEVkaXRhYmxlIC0tPg0KPC9oZWFkPg0KDQo8Ym9keT4NCg0KCTxkaXYgaWQ9ImhlYWRlciI+DQoJCTxkaXYgaWQ9Im1lbnUiPg0KCQ0KCQkJPGRpdiBpZD0id2VsY29tZW1lc3NhZ2UiPlplaXRnZWlzdCBFeGFtcGxlczwvZGl2Pg0KCQkJPGRpdiBpZD0ibWFpbm1lbnUiPg0KCQkJPGEgaHJlZj0iaW5kZXgucGhwIj5Ib21lPC9hPg0KCQkJPGEgaHJlZj0iaW5kZXgucGhwP2FjdGlvbj1iYXNpY3MiPkJhc2ljczwvYT4NCgkJCTxhIGhyZWY9ImluZGV4LnBocD9tb2R1bGU9ZXZlbnRoYW5kbGVyIj5FdmVudCBIYW5kbGVyPC9hPg0KCQkJPGEgaHJlZj0iaW5kZXgucGhwP21vZHVsZT1kZWJ1ZyI+RGVidWdnaW5nPC9hPg0KCQkJPGEgaHJlZj0iaW5kZXgucGhwP21vZHVsZT1tZXNzYWdlcyI+TWVzc2FnZXM8L2E+DQoJCQk8YSBocmVmPSJpbmRleC5waHA/bW9kdWxlPXRlbXBsYXRlcyI+VGVtcGxhdGUgRW5naW5lPC9hPg0KCQkJPC9kaXY+DQoJCTwvZGl2Pg0KCTwvZGl2Pg0KDQoJPCEtLSN1c2VybWVzc2FnZSMtLT4NCg0KCTwhLS0jdXNlcndhcm5pbmdzIy0tPg0KDQoJPCEtLSN1c2VyZXJyb3JzIy0tPg0KDQoJPGRpdiBpZD0iY29udGVudCI+DQoJPCEtLSBJbnN0YW5jZUJlZ2luRWRpdGFibGUgbmFtZT0iYm9keSIgLS0+DQogICAgDQogICAgPGgxIHN0eWxlPSJtYXJnaW4tdG9wOjBweDsiPlplaXRnZWlzdCBFeGFtcGxlcyAtIE92ZXJ2aWV3PC9oMT4NCgkJDQoJPHA+V2VsY29tZSB0byBaZWl0Z2Vpc3QsIGEgUEhQIGJhc2VkIG11bHRpIHB1cnBvc2UgZnJhbWV3b3JrIGZvciB3ZWIgYXBwbGljYXRpb25zLjwvcD4NCg0KCTxwPkZvbGxvd2luZyBhcmUgZXhhbXBsZXMgdG8gc2hvdyB5b3UgaG93IHRvIHVzZSBaZWl0Z2Vpc3QuIFRoaXMgY29sbGVjdGlvbiBzZXJ2ZXMgYXMgdHV0b3JpYWwgYXMgd2VsbCBhcyByZWZlcmVuY2UgbWFudWFsIGFuZCBzdHlsZSBndWlkZS4gPC9wPg0KCQ0KCTxwPk1heWJlIHlvdSBhbHJlYWR5IGxvb2tlZCBhdCB0aGUgY29kZSB5b3UganVzdCBwdXQgb24gdGhlIHNlcnZlciAtIHRoYXQncyBvay4gV2hlbiBsb29raW5nIGF0IHRoZSBzb3VyY2VzIG9mIHRoZSBleGFtcGxlcywgcGxlYXNlIGtlZXAgdGhlIGZvbGxvd2luZyB0aGluZ3MgaW4gbWluZDo8L3A+DQoJDQoJPG9sPg0KCQk8bGk+VGhpcyBpcyBub3QgYSByZWFsIGFwcGxpY2F0aW9uIGJ1dCBtZXJlbHkgYSBjb2xsZWN0aW9uIG9mIGV4YW1wbGVzIGFuZCB0dXRvcmlhbHMuPC9saT4NCgkJPGxpPk1hbnkgZnVuY3Rpb25hbGl0aWVzIGFyZSB1c2VkIGluIGEgZHVtYmVkIGRvd24gd2F5LiBUaGVzZSBhcmUgZXhhbXBsZXMgYWZ0ZXIgYWxsLiBIb3dldmVyIHdlIHRyeSB0byBwcmVzZW50IGJlc3QgcHJhY3Rpc2VzIGZvciBlYWNoIGZyYW1ld29yayBtb2R1bGUuPC9saT4NCgkJPGxpPklmIHlvdSB3YW50IHRvIHNlZSBhIG1vcmUgInJlYWwgd29ybGQiIGFwcGxpY2F0aW9uLCB0YWtlIGEgbG9vayBhdCB0aGUgWmVpdGdlaXN0IEFkbWluaXN0cmF0b3IgYXBwbGljYXRpb24uPC9saT4NCgk8L29sPg0KCQ0KPCEtLSBJbnN0YW5jZUVuZEVkaXRhYmxlIC0tPg0KDQoJPC9kaXY+DQoNCjwvYm9keT4NCjwhLS0gSW5zdGFuY2VFbmQgLS0+PC9odG1sPg0KIjtzOjY6ImJsb2NrcyI7YTo0OntzOjExOiJ1c2VybWVzc2FnZSI7TzoxNToiemdUZW1wbGF0ZUJsb2NrIjo0OntzOjE0OiJjdXJyZW50Q29udGVudCI7czo1NjoiDQoJCTxoMyBjbGFzcz0idXNlcm1lc3NhZ2UiPjwhLS1AdXNlcm1lc3NhZ2VALS0+PC9oMz4NCgkiO3M6MTU6Im9yaWdpbmFsQ29udGVudCI7czo1NjoiDQoJCTxoMyBjbGFzcz0idXNlcm1lc3NhZ2UiPjwhLS1AdXNlcm1lc3NhZ2VALS0+PC9oMz4NCgkiO3M6MTE6ImJsb2NrUGFyZW50IjtOO3M6MTQ6ImJsb2NrVmFyaWFibGVzIjthOjE6e3M6MTE6InVzZXJtZXNzYWdlIjtzOjIwOiI8IS0tQHVzZXJtZXNzYWdlQC0tPiI7fX1zOjEyOiJ1c2Vyd2FybmluZ3MiO086MTU6InpnVGVtcGxhdGVCbG9jayI6NDp7czoxNDoiY3VycmVudENvbnRlbnQiO3M6NTY6Ig0KCQk8aDMgY2xhc3M9InVzZXJ3YXJuaW5nIj48IS0tQHVzZXJ3YXJuaW5nQC0tPjwvaDM+DQoJIjtzOjE1OiJvcmlnaW5hbENvbnRlbnQiO3M6NTY6Ig0KCQk8aDMgY2xhc3M9InVzZXJ3YXJuaW5nIj48IS0tQHVzZXJ3YXJuaW5nQC0tPjwvaDM+DQoJIjtzOjExOiJibG9ja1BhcmVudCI7TjtzOjE0OiJibG9ja1ZhcmlhYmxlcyI7YToxOntzOjExOiJ1c2Vyd2FybmluZyI7czoyMDoiPCEtLUB1c2Vyd2FybmluZ0AtLT4iO319czoxMDoidXNlcmVycm9ycyI7TzoxNToiemdUZW1wbGF0ZUJsb2NrIjo0OntzOjE0OiJjdXJyZW50Q29udGVudCI7czo1NDoiDQoJCTxoMyBjbGFzcz0idXNlcndhcm5pbmciPjwhLS1AdXNlcmVycm9yQC0tPjwvaDM+DQoJIjtzOjE1OiJvcmlnaW5hbENvbnRlbnQiO3M6NTQ6Ig0KCQk8aDMgY2xhc3M9InVzZXJ3YXJuaW5nIj48IS0tQHVzZXJlcnJvckAtLT48L2gzPg0KCSI7czoxMToiYmxvY2tQYXJlbnQiO047czoxNDoiYmxvY2tWYXJpYWJsZXMiO2E6MTp7czo5OiJ1c2VyZXJyb3IiO3M6MTg6IjwhLS1AdXNlcmVycm9yQC0tPiI7fX1zOjQ6InJvb3QiO086MTU6InpnVGVtcGxhdGVCbG9jayI6NDp7czoxNDoiY3VycmVudENvbnRlbnQiO047czoxNToib3JpZ2luYWxDb250ZW50IjtOO3M6MTE6ImJsb2NrUGFyZW50IjtOO3M6MTQ6ImJsb2NrVmFyaWFibGVzIjtOO319czo5OiJ2YXJpYWJsZXMiO2E6Mzp7czoxMToidXNlcm1lc3NhZ2UiO086MTg6InpnVGVtcGxhdGVWYXJpYWJsZSI6Mjp7czoxNDoiY3VycmVudENvbnRlbnQiO047czoxNDoiZGVmYXVsdENvbnRlbnQiO047fXM6MTE6InVzZXJ3YXJuaW5nIjtPOjE4OiJ6Z1RlbXBsYXRlVmFyaWFibGUiOjI6e3M6MTQ6ImN1cnJlbnRDb250ZW50IjtOO3M6MTQ6ImRlZmF1bHRDb250ZW50IjtOO31zOjk6InVzZXJlcnJvciI7TzoxODoiemdUZW1wbGF0ZVZhcmlhYmxlIjoyOntzOjE0OiJjdXJyZW50Q29udGVudCI7TjtzOjE0OiJkZWZhdWx0Q29udGVudCI7Tjt9fX0='),
(5, 'templates/zgexamples/main_basics.tpl.html', '1247639883', 'YTo0OntzOjQ6ImZpbGUiO3M6NDE6InRlbXBsYXRlcy96Z2V4YW1wbGVzL21haW5fYmFzaWNzLnRwbC5odG1sIjtzOjc6ImNvbnRlbnQiO3M6MjE3MjoiPCFET0NUWVBFIGh0bWwgUFVCTElDICItLy9XM0MvL0RURCBYSFRNTCAxLjAgVHJhbnNpdGlvbmFsLy9FTiIgImh0dHA6Ly93d3cudzMub3JnL1RSL3hodG1sMS9EVEQveGh0bWwxLXRyYW5zaXRpb25hbC5kdGQiPg0KPGh0bWwgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGh0bWwiPjwhLS0gSW5zdGFuY2VCZWdpbiB0ZW1wbGF0ZT0iL1RlbXBsYXRlcy96Z2V4YW1wbGVzLmR3dCIgY29kZU91dHNpZGVIVE1MSXNMb2NrZWQ9ImZhbHNlIiAtLT4NCjxoZWFkPg0KPG1ldGEgaHR0cC1lcXVpdj0iQ29udGVudC1UeXBlIiBjb250ZW50PSJ0ZXh0L2h0bWw7IGNoYXJzZXQ9dXRmLTgiIC8+DQo8IS0tIEluc3RhbmNlQmVnaW5FZGl0YWJsZSBuYW1lPSJkb2N0aXRsZSIgLS0+DQo8dGl0bGU+WmVpdGdlaXN0IEV4YW1wbGVzIC0gSG9tZTwvdGl0bGU+DQo8IS0tIEluc3RhbmNlRW5kRWRpdGFibGUgLS0+DQoNCjxsaW5rIHJlbD0ic3R5bGVzaGVldCIgdHlwZT0idGV4dC9jc3MiIGhyZWY9InRlbXBsYXRlcy96Z2V4YW1wbGVzL2Nzcy96Z2V4YW1wbGVzLmNzcyIgLz4NCg0KPHNjcmlwdCBsYW5ndWFnZT0iamF2YXNjcmlwdCIgdHlwZT0idGV4dC9qYXZhc2NyaXB0Ij4NCg0KPC9zY3JpcHQ+DQoJDQo8IS0tIEluc3RhbmNlQmVnaW5FZGl0YWJsZSBuYW1lPSJoZWFkIiAtLT4NCjwhLS0gSW5zdGFuY2VFbmRFZGl0YWJsZSAtLT4NCjwvaGVhZD4NCg0KPGJvZHk+DQoNCgk8ZGl2IGlkPSJoZWFkZXIiPg0KCQk8ZGl2IGlkPSJtZW51Ij4NCgkNCgkJCTxkaXYgaWQ9IndlbGNvbWVtZXNzYWdlIj5aZWl0Z2Vpc3QgRXhhbXBsZXM8L2Rpdj4NCgkJCTxkaXYgaWQ9Im1haW5tZW51Ij4NCgkJCTxhIGhyZWY9ImluZGV4LnBocCI+SG9tZTwvYT4NCgkJCTxhIGhyZWY9ImluZGV4LnBocD9hY3Rpb249YmFzaWNzIj5CYXNpY3M8L2E+DQoJCQk8YSBocmVmPSJpbmRleC5waHA/bW9kdWxlPWV2ZW50aGFuZGxlciI+RXZlbnQgSGFuZGxlcjwvYT4NCgkJCTxhIGhyZWY9ImluZGV4LnBocD9tb2R1bGU9dGVtcGxhdGVzIj5UZW1wbGF0ZSBFbmdpbmU8L2E+DQoJCQk8L2Rpdj4NCgkJPC9kaXY+DQoJPC9kaXY+DQoNCgk8IS0tI3VzZXJtZXNzYWdlIy0tPg0KDQoJPCEtLSN1c2Vyd2FybmluZ3MjLS0+DQoNCgk8IS0tI3VzZXJlcnJvcnMjLS0+DQoNCgk8ZGl2IGlkPSJjb250ZW50Ij4NCgk8IS0tIEluc3RhbmNlQmVnaW5FZGl0YWJsZSBuYW1lPSJib2R5IiAtLT4NCiAgICANCiAgICA8aDEgc3R5bGU9Im1hcmdpbi10b3A6MHB4OyI+WmVpdGdlaXN0IEV4YW1wbGVzIC0gT3ZlcnZpZXc8L2gxPg0KCQkNCgk8cD5XZWxjb21lIHRvIFplaXRnZWlzdCwgYSBQSFAgYmFzZWQgbXVsdGkgcHVycG9zZSBmcmFtZXdvcmsgZm9yIHdlYiBhcHBsaWNhdGlvbnMuPC9wPg0KDQoJPHA+Rm9sbG93aW5nIGFyZSBleGFtcGxlcyB0byBnZXQgeW91IHN0YXJ0ZWQgdW5kZXJzdGFuZGluZyBhbmQgdXNpbmcgWmVpdGdlaXN0LiBUaGlzIGNvbGxlY3Rpb24gc2VydmVzIGFzIHR1dG9yaWFsIGFzIHdlbGwgYXMgcmVmZXJlbmNlIG1hbnVhbCBhbmQgc3R5bGUgZ3VpZGUuIDwvcD4NCgkNCgk8cD5NYXliZSB5b3UgYWxyZWFkeSBsb29rZWQgYXQgdGhlIGNvZGUgeW91IGp1c3QgaW5zdGFsbGVkIC0gdGhhdCdzIG9rLiBXaGVuIGxvb2tpbmcgYXQgdGhlIHNvdXJjZXMgb2YgdGhlIGV4YW1wbGVzLCBwbGVhc2Uga2VlcCB0aGUgZm9sbG93aW5nIHRoaW5ncyBpbiBtaW5kOjwvcD4NCgkNCgk8b2w+DQoJCTxsaT5UaGlzIGlzIG5vdCBhIHJlYWwgYXBwbGljYXRpb24gYnV0IG1lcmVseSBhIGNvbGxlY3Rpb24gb2YgZXhhbXBsZXMgYW5kIHR1dG9yaWFscy48L2xpPg0KCQk8bGk+TWFueSBmdW5jdGlvbmFsaXRpZXMgYXJlIHVzZWQgaW4gYSBkdW1iZWQgZG93biB3YXkuIFRoZXNlIGFyZSBleGFtcGxlcyBhZnRlciBhbGwuIEhvd2V2ZXIgd2UgdHJ5IHRvIHByZXNlbnQgYmVzdCBwcmFjdGlzZXMgZm9yIGVhY2ggZnJhbWV3b3JrIG1vZHVsZS48L2xpPg0KCTwvb2w+DQoJDQoJPHVsPg0KCQk8bGk+QmFzaWNzPC9saT4NCgkJPGxpPkV2ZW50IEhhbmRsZXI8L2xpPg0KCQk8bGk+VGVtcGxhdGUgRW5naW5lPC9saT4NCgk8L3VsPg0KCQkNCg0KPCEtLSBJbnN0YW5jZUVuZEVkaXRhYmxlIC0tPg0KDQoJPC9kaXY+DQoNCjwvYm9keT4NCjwhLS0gSW5zdGFuY2VFbmQgLS0+PC9odG1sPg0KIjtzOjY6ImJsb2NrcyI7YTo0OntzOjExOiJ1c2VybWVzc2FnZSI7TzoxNToiemdUZW1wbGF0ZUJsb2NrIjo0OntzOjE0OiJjdXJyZW50Q29udGVudCI7czo1NjoiDQoJCTxoMyBjbGFzcz0idXNlcm1lc3NhZ2UiPjwhLS1AdXNlcm1lc3NhZ2VALS0+PC9oMz4NCgkiO3M6MTU6Im9yaWdpbmFsQ29udGVudCI7czo1NjoiDQoJCTxoMyBjbGFzcz0idXNlcm1lc3NhZ2UiPjwhLS1AdXNlcm1lc3NhZ2VALS0+PC9oMz4NCgkiO3M6MTE6ImJsb2NrUGFyZW50IjtOO3M6MTQ6ImJsb2NrVmFyaWFibGVzIjthOjE6e3M6MTE6InVzZXJtZXNzYWdlIjtzOjIwOiI8IS0tQHVzZXJtZXNzYWdlQC0tPiI7fX1zOjEyOiJ1c2Vyd2FybmluZ3MiO086MTU6InpnVGVtcGxhdGVCbG9jayI6NDp7czoxNDoiY3VycmVudENvbnRlbnQiO3M6NTY6Ig0KCQk8aDMgY2xhc3M9InVzZXJ3YXJuaW5nIj48IS0tQHVzZXJ3YXJuaW5nQC0tPjwvaDM+DQoJIjtzOjE1OiJvcmlnaW5hbENvbnRlbnQiO3M6NTY6Ig0KCQk8aDMgY2xhc3M9InVzZXJ3YXJuaW5nIj48IS0tQHVzZXJ3YXJuaW5nQC0tPjwvaDM+DQoJIjtzOjExOiJibG9ja1BhcmVudCI7TjtzOjE0OiJibG9ja1ZhcmlhYmxlcyI7YToxOntzOjExOiJ1c2Vyd2FybmluZyI7czoyMDoiPCEtLUB1c2Vyd2FybmluZ0AtLT4iO319czoxMDoidXNlcmVycm9ycyI7TzoxNToiemdUZW1wbGF0ZUJsb2NrIjo0OntzOjE0OiJjdXJyZW50Q29udGVudCI7czo1NDoiDQoJCTxoMyBjbGFzcz0idXNlcndhcm5pbmciPjwhLS1AdXNlcmVycm9yQC0tPjwvaDM+DQoJIjtzOjE1OiJvcmlnaW5hbENvbnRlbnQiO3M6NTQ6Ig0KCQk8aDMgY2xhc3M9InVzZXJ3YXJuaW5nIj48IS0tQHVzZXJlcnJvckAtLT48L2gzPg0KCSI7czoxMToiYmxvY2tQYXJlbnQiO047czoxNDoiYmxvY2tWYXJpYWJsZXMiO2E6MTp7czo5OiJ1c2VyZXJyb3IiO3M6MTg6IjwhLS1AdXNlcmVycm9yQC0tPiI7fX1zOjQ6InJvb3QiO086MTU6InpnVGVtcGxhdGVCbG9jayI6NDp7czoxNDoiY3VycmVudENvbnRlbnQiO047czoxNToib3JpZ2luYWxDb250ZW50IjtOO3M6MTE6ImJsb2NrUGFyZW50IjtOO3M6MTQ6ImJsb2NrVmFyaWFibGVzIjtOO319czo5OiJ2YXJpYWJsZXMiO2E6Mzp7czoxMToidXNlcm1lc3NhZ2UiO086MTg6InpnVGVtcGxhdGVWYXJpYWJsZSI6Mjp7czoxNDoiY3VycmVudENvbnRlbnQiO047czoxNDoiZGVmYXVsdENvbnRlbnQiO047fXM6MTE6InVzZXJ3YXJuaW5nIjtPOjE4OiJ6Z1RlbXBsYXRlVmFyaWFibGUiOjI6e3M6MTQ6ImN1cnJlbnRDb250ZW50IjtOO3M6MTQ6ImRlZmF1bHRDb250ZW50IjtOO31zOjk6InVzZXJlcnJvciI7TzoxODoiemdUZW1wbGF0ZVZhcmlhYmxlIjoyOntzOjE0OiJjdXJyZW50Q29udGVudCI7TjtzOjE0OiJkZWZhdWx0Q29udGVudCI7Tjt9fX0=');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `trafficlog`
--

CREATE TABLE IF NOT EXISTS `trafficlog` (
  `trafficlog_id` int(12) NOT NULL AUTO_INCREMENT,
  `trafficlog_module` int(12) NOT NULL,
  `trafficlog_action` int(12) NOT NULL,
  `trafficlog_user` int(12) NOT NULL,
  `trafficlog_ip` int(10) unsigned DEFAULT NULL,
  `trafficlog_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`trafficlog_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Daten für Tabelle `trafficlog`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `trafficlog_parameters`
--

CREATE TABLE IF NOT EXISTS `trafficlog_parameters` (
  `trafficparameters_id` int(12) NOT NULL AUTO_INCREMENT,
  `trafficparameters_trafficid` int(12) NOT NULL,
  `trafficparameters_key` varchar(64) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `trafficparameters_value` text CHARACTER SET latin1 COLLATE latin1_general_ci,
  PRIMARY KEY (`trafficparameters_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Daten für Tabelle `trafficlog_parameters`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `userconfirmation`
--

CREATE TABLE IF NOT EXISTS `userconfirmation` (
  `userconfirmation_id` int(12) NOT NULL AUTO_INCREMENT,
  `userconfirmation_user` int(12) NOT NULL,
  `userconfirmation_key` varchar(100) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  PRIMARY KEY (`userconfirmation_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

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
  `userroleaction_id` int(12) NOT NULL AUTO_INCREMENT,
  `userroleaction_userrole` int(12) NOT NULL DEFAULT '0',
  `userroleaction_action` int(12) NOT NULL DEFAULT '0',
  PRIMARY KEY (`userroleaction_id`),
  KEY `userroleright_userrole` (`userroleaction_userrole`),
  KEY `userroleright_userright` (`userroleaction_action`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Daten für Tabelle `userroles_to_actions`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `userroles_to_users`
--

CREATE TABLE IF NOT EXISTS `userroles_to_users` (
  `userroleuser_id` int(12) NOT NULL AUTO_INCREMENT,
  `userroleuser_userrole` int(12) NOT NULL DEFAULT '0',
  `userroleuser_user` int(12) NOT NULL DEFAULT '0',
  PRIMARY KEY (`userroleuser_id`),
  KEY `userroleuser_userrole` (`userroleuser_userrole`),
  KEY `userroleuser_user` (`userroleuser_user`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

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

