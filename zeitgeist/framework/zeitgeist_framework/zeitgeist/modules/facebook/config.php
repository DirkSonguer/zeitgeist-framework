<?php

/*
db292443319
Hostname 	db1976.1und1.de
Port 	3306
Benutzername 	dbo292443319
Passwort 	FfRXfc5Z
*/

// Get these from http://developers.facebook.com
$api_key = '20f3de2ab32df9b073cc878e8e8bf59a';
$secret  = '9f3e1e08709f0d61e8f22782e2237029';
/* While you're there, you'll also want to set up your callback url to the url
 * of the directory that contains Footprints' index.php, and you can set the
 * framed page URL to whatever you want.  You should also swap the references
 * in the code from http://apps.facebook.com/footprints/ to your framed page URL. */

// The IP address of your database
$db_ip = 'db1976.1und1.de';           

$db_user = 'dbo292443319';
$db_pass = 'FfRXfc5Z';

// the name of the database that you create for footprints.
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
