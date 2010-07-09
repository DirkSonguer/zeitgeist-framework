<?php

define( 'ZEITGEIST_ROOTDIRECTORY', './' );

define( 'DEBUGMODE', true );

// include core classes
require_once ( 'zeitgeist.php' );
require_once ( 'classes/database_pdo.class.php' );

$debug = zgDebug::init();

$debug->write("test");

try
{
    $test = new zgDatabasePDO("mysql:host=127.0.0.1;dbname=lineracer_new", 'root', '');
}
catch ( PDOException $e )
{
    echo $e->getMessage( );
}


$STH = $test->prepare("sELECT * FROM actions WHERE action_id = ?");
$actionid = "1";
$STH->bindParam(1, $actionid);
$STH->execute();

while ( $row = $STH->fetch( ) )
{
//    var_dump( $row );
}

$actionid = "2";
$STH->bindParam(1, $actionid);
$STH->execute();

$STH->setFetchMode( PDO::FETCH_ASSOC );

echo "<br /><br />";
while ( $row = $STH->fetch( ) )
{
//    var_dump( $row );
}

$debug->loadStylesheet( 'debug.css' );
//	$debug->showInnerLoops = true;
$debug->showMiscInformation();
$debug->showDebugMessages( );
$debug->showQueryMessages();
$debug->showGuardMessages();
?>