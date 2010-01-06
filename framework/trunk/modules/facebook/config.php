<?php

/*
db292443319
Hostname 	db1976.1und1.de
Port 	3306
Benutzername 	dbo292443319
Passwort 	FfRXfc5Z
*/

// Get these from http://developers.facebook.com
$api_key = 'f819f8bbef2c69ab5e80f326c7c083a1';
$secret  = 'd1ae93aa94f9a6218e8f2271cc66b8a3';
/* While you're there, you'll also want to set up your callback url to the url
 * of the directory that contains Footprints' index.php, and you can set the
 * framed page URL to whatever you want.  You should also swap the references
 * in the code from http://apps.facebook.com/footprints/ to your framed page URL. */

// The IP address of your database
$db_ip = 'db1976.1und1.de';           

$db_user = 'dbo292443319';
$db_pass = 'FfRXfc5Z';
$db_name = 'db292443319';

/* create this table on the database:
CREATE TABLE `footprints` (
  `from` int(11) NOT NULL default '0',
  `to` int(11) NOT NULL default '0',
  `time` int(11) NOT NULL default '0',
  KEY `from` (`from`),
  KEY `to` (`to`)
)
*/
