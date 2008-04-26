-- phpMyAdmin SQL Dump
-- version 2.11.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Erstellungszeit: 26. April 2008 um 14:11
-- Server Version: 5.0.45
-- PHP-Version: 5.2.4

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Datenbank: `feedkun`
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=83 ;

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
  `messagecache_content` text collate latin1_general_ci NOT NULL,
  PRIMARY KEY  (`messagecache_user`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=16 ;

--
-- Daten für Tabelle `modules`
--

INSERT INTO `modules` (`module_id`, `module_name`, `module_description`, `module_active`) VALUES
(1, 'main', 'Main module', 1),
(3, 'dataserver', 'The usual XML stream stuff', 1);

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
  `sessiondata_id` varchar(32) NOT NULL,
  `sessiondata_created` int(11) NOT NULL default '0',
  `sessiondata_lastupdate` int(11) NOT NULL default '0',
  `sessiondata_content` text character set utf8 NOT NULL,
  `sessiondata_ip` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`sessiondata_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Daten für Tabelle `sessiondata`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `templatecache`
--

CREATE TABLE `templatecache` (
  `templatecache_id` int(12) NOT NULL auto_increment,
  `templatecache_name` varchar(128) collate latin1_general_ci NOT NULL,
  `templatecache_timestamp` varchar(32) collate latin1_general_ci NOT NULL,
  `templatecache_content` text collate latin1_general_ci NOT NULL,
  PRIMARY KEY  (`templatecache_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=1 ;

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=1 ;

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
  `trafficparameters_key` varchar(64) collate latin1_general_ci NOT NULL,
  `trafficparameters_value` text collate latin1_general_ci,
  PRIMARY KEY  (`trafficparameters_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=1 ;

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
  `userconfirmation_key` varchar(100) collate latin1_general_ci NOT NULL,
  PRIMARY KEY  (`userconfirmation_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=1 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=73 ;

--
-- Daten für Tabelle `userdata`
--

INSERT INTO `userdata` (`userdata_id`, `userdata_user`, `userdata_firstname`, `userdata_lastname`, `userdata_url`, `userdata_address1`, `userdata_address2`, `userdata_city`, `userdata_zip`, `userdata_country`, `userdata_im`, `userdata_timestamp`) VALUES
(63, 1, 'Adam', 'Admin', 'http://www.taskkun.de', 'Musterstraße 1', 'Kaff 2c', 'Darmstadt', '64283', '', '', '2008-04-23 08:48:02'),
(64, 2, 'Mandy', 'Manager', 'http://www.taskkun.de', 'Musterstraße 2', 'Kaff 2b', 'Darmstadt', '64283', NULL, NULL, '2008-04-13 09:20:15'),
(65, 3, 'Ben', 'Benutzer', 'http://www.taskkun.de', 'Nutzerstraße 17', '', 'Benutzerstadt', '65432', '', '', '2008-03-31 22:47:31');

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=32 ;

--
-- Daten für Tabelle `userrights`
--

INSERT INTO `userrights` (`userright_id`, `userright_action`, `userright_user`) VALUES
(31, 8, 1),
(30, 6, 1);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `userroles`
--

CREATE TABLE `userroles` (
  `userrole_id` int(12) NOT NULL auto_increment,
  `userrole_name` varchar(30) NOT NULL default '',
  `userrole_description` text,
  PRIMARY KEY  (`userrole_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

--
-- Daten für Tabelle `userroles`
--

INSERT INTO `userroles` (`userrole_id`, `userrole_name`, `userrole_description`) VALUES
(1, 'Administrator', 'Administrator-Account'),
(2, 'Manager', 'Manager-Account'),
(3, 'Benutzer', 'Standard Benutzer-Account');

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=687 ;

--
-- Daten für Tabelle `userroles_to_actions`
--

INSERT INTO `userroles_to_actions` (`userroleaction_id`, `userroleaction_userrole`, `userroleaction_action`) VALUES
(682, 1, 78),
(681, 1, 74),
(680, 1, 76),
(679, 1, 77),
(678, 1, 75),
(677, 1, 70),
(676, 1, 69),
(675, 1, 68),
(674, 1, 67),
(673, 1, 66),
(672, 1, 65),
(671, 1, 46),
(670, 1, 47),
(669, 1, 48),
(644, 2, 66),
(643, 2, 65),
(642, 2, 46),
(641, 2, 47),
(640, 2, 48),
(639, 2, 49),
(638, 2, 50),
(637, 2, 51),
(636, 2, 52),
(635, 2, 40),
(634, 2, 41),
(633, 2, 59),
(632, 2, 42),
(631, 2, 43),
(372, 3, 46),
(371, 3, 47),
(370, 3, 48),
(369, 3, 52),
(368, 3, 40),
(367, 3, 42),
(366, 3, 43),
(365, 3, 44),
(364, 3, 45),
(363, 3, 3),
(668, 1, 49),
(667, 1, 50),
(666, 1, 51),
(665, 1, 52),
(664, 1, 53),
(663, 1, 54),
(662, 1, 40),
(661, 1, 41),
(660, 1, 55),
(659, 1, 56),
(630, 2, 44),
(629, 2, 60),
(628, 2, 61),
(627, 2, 62),
(658, 1, 57),
(657, 1, 58),
(626, 2, 45),
(625, 2, 3),
(373, 3, 65),
(374, 3, 66),
(656, 1, 59),
(655, 1, 42),
(654, 1, 43),
(653, 1, 44),
(652, 1, 60),
(651, 1, 61),
(650, 1, 62),
(649, 1, 45),
(648, 1, 3),
(645, 2, 76),
(646, 2, 74),
(647, 2, 78),
(683, 1, 79),
(684, 1, 80),
(685, 1, 81),
(686, 1, 82);

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=29 ;

--
-- Daten für Tabelle `userroles_to_users`
--

INSERT INTO `userroles_to_users` (`userroleuser_id`, `userroleuser_userrole`, `userroleuser_user`) VALUES
(1, 1, 1),
(2, 2, 2),
(3, 3, 3);

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
  `user_instance` int(12) NOT NULL default '0',
  PRIMARY KEY  (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=35 ;

--
-- Daten für Tabelle `users`
--

INSERT INTO `users` (`user_id`, `user_username`, `user_password`, `user_key`, `user_active`, `user_instance`) VALUES
(1, 'admin@taskkun.de', '098f6bcd4621d373cade4e832627b4f6', '17d815d30b840a283ef10c5c1f32db', 1, 1),
(2, 'manager@taskkun.de', '098f6bcd4621d373cade4e832627b4f6', '5d733ba0a5e2bb097f22776cb1775d', 1, 1),
(3, 'benutzer@taskkun.de', '098f6bcd4621d373cade4e832627b4f6', 'e49b436b0a10981670fc28c7e187fa', 1, 1);
