-- phpMyAdmin SQL Dump
-- version 3.1.3.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Erstellungszeit: 12. September 2009 um 08:29
-- Server Version: 5.1.33
-- PHP-Version: 5.2.9

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Datenbank: `zg_test`
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Daten für Tabelle `actions`
--


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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Daten für Tabelle `configurationcache`
--


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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Daten für Tabelle `modules`
--


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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Daten für Tabelle `templatecache`
--


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
  `userdata_firstname` varchar(100) DEFAULT NULL,
  `userdata_lastname` varchar(100) DEFAULT NULL,
  `userdata_sex` varchar(5) DEFAULT NULL,
  `userdata_locale` varchar(5) DEFAULT NULL,
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

