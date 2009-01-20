-- phpMyAdmin SQL Dump
-- version 2.11.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Erstellungszeit: 21. August 2008 um 22:00
-- Server Version: 5.0.45
-- PHP-Version: 5.2.4

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Datenbank: `wellnesswelt`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `actions`
--

CREATE TABLE `actions` (
  `action_id` int(12) NOT NULL auto_increment,
  `action_module` int(12) NOT NULL default '0',
  `action_name` varchar(30) NOT NULL default '',
  `action_description` text NOT NULL,
  `action_requiresuserright` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`action_id`),
  KEY `action_module` (`action_module`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=93 ;

--
-- Daten für Tabelle `actions`
--

INSERT INTO `actions` (`action_id`, `action_module`, `action_name`, `action_description`, `action_requiresuserright`) VALUES
(1, 1, 'login', 'Login user', 0),
(2, 1, 'logout', 'Logout user', 0),
(3, 1, 'index', 'Main index action', 1);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `configurationcache`
--

CREATE TABLE `configurationcache` (
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
-- Tabellenstruktur für Tabelle `messagecache`
--

CREATE TABLE `messagecache` (
  `messagecache_user` int(12) NOT NULL,
  `messagecache_content` text character set latin1 collate latin1_general_ci NOT NULL,
  PRIMARY KEY  (`messagecache_user`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `messagecache`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `modules`
--

CREATE TABLE `modules` (
  `module_id` int(12) NOT NULL auto_increment,
  `module_name` varchar(30) NOT NULL default '',
  `module_description` text NOT NULL,
  `module_active` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`module_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=18 ;

--
-- Daten für Tabelle `modules`
--

INSERT INTO `modules` (`module_id`, `module_name`, `module_description`, `module_active`) VALUES
(1, 'main', 'Main module', 1);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `preferences`
--

CREATE TABLE `preferences` (
  `preference_id` int(12) NOT NULL auto_increment,
  `preference_key` varchar(30) NOT NULL default '',
  `preference_value` varchar(30) NOT NULL default '',
  `preference_description` text,
  `preference_order` int(5) NOT NULL default '0',
  PRIMARY KEY  (`preference_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Daten für Tabelle `preferences`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `preferences_to_users`
--

CREATE TABLE `preferences_to_users` (
  `preferencesusers_id` int(12) NOT NULL auto_increment,
  `preferencesusers_user` int(12) NOT NULL default '0',
  `preferencesusers_preference` int(12) NOT NULL default '0',
  PRIMARY KEY  (`preferencesusers_id`),
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

CREATE TABLE `sessiondata` (
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

CREATE TABLE `templatecache` (
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
-- Tabellenstruktur für Tabelle `trafficlog`
--

CREATE TABLE `trafficlog` (
  `trafficlog_id` int(12) NOT NULL auto_increment,
  `trafficlog_module` int(12) NOT NULL,
  `trafficlog_action` int(12) NOT NULL,
  `trafficlog_user` int(12) NOT NULL,
  `trafficlog_ip` int(10) unsigned default NULL,
  PRIMARY KEY  (`trafficlog_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Daten für Tabelle `trafficlog`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `trafficlog_parameters`
--

CREATE TABLE `trafficlog_parameters` (
  `trafficparameters_id` int(12) NOT NULL auto_increment,
  `trafficparameters_trafficid` int(12) NOT NULL,
  `trafficparameters_key` varchar(64) character set latin1 collate latin1_general_ci NOT NULL,
  `trafficparameters_value` text character set latin1 collate latin1_general_ci,
  PRIMARY KEY  (`trafficparameters_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Daten für Tabelle `trafficlog_parameters`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `userconfirmation`
--

CREATE TABLE `userconfirmation` (
  `userconfirmation_id` int(12) NOT NULL auto_increment,
  `userconfirmation_user` int(12) NOT NULL,
  `userconfirmation_key` varchar(100) character set latin1 collate latin1_general_ci NOT NULL,
  PRIMARY KEY  (`userconfirmation_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Daten für Tabelle `userconfirmation`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `userdata`
--

CREATE TABLE `userdata` (
  `userdata_id` int(12) NOT NULL auto_increment,
  `userdata_user` int(12) NOT NULL default '0',
  `userdata_firstname` varchar(100) default NULL,
  `userdata_lastname` varchar(100) default NULL,
  `userdata_url` varchar(100) default NULL,
  `userdata_address1` varchar(100) default NULL,
  `userdata_address2` varchar(100) default NULL,
  `userdata_city` varchar(100) default NULL,
  `userdata_zip` varchar(10) default NULL,
  `userdata_country` varchar(30) default NULL,
  `userdata_im` varchar(255) default NULL,
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

CREATE TABLE `userrights` (
  `userright_id` int(12) NOT NULL auto_increment,
  `userright_action` int(12) NOT NULL default '0',
  `userright_user` int(12) NOT NULL default '0',
  PRIMARY KEY  (`userright_id`),
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

CREATE TABLE `userroles` (
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

CREATE TABLE `userroles_to_actions` (
  `userroleaction_id` int(12) NOT NULL auto_increment,
  `userroleaction_userrole` int(12) NOT NULL default '0',
  `userroleaction_action` int(12) NOT NULL default '0',
  PRIMARY KEY  (`userroleaction_id`),
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

CREATE TABLE `userroles_to_users` (
  `userroleuser_id` int(12) NOT NULL auto_increment,
  `userroleuser_userrole` int(12) NOT NULL default '0',
  `userroleuser_user` int(12) NOT NULL default '0',
  PRIMARY KEY  (`userroleuser_id`),
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

CREATE TABLE `users` (
  `user_id` int(12) NOT NULL auto_increment,
  `user_username` varchar(255) NOT NULL,
  `user_password` varchar(255) NOT NULL default '',
  `user_key` varchar(64) default NULL,
  `user_active` tinyint(1) NOT NULL,
  PRIMARY KEY  (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Daten für Tabelle `users`
--

