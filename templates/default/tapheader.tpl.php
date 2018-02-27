<?php
/*
*
* Author: Sherwin R. Terunez
* Contact: sherwinterunez@yahoo.com
*
* Description:
*
* Header template
*
*/

if(!defined('APPLICATION_RUNNING')) {
	header("HTTP/1.0 404 Not Found");
	die('access denied');
}

if(defined('ANNOUNCE')) {
	echo "\n<!-- loaded: ".__FILE__." -->\n";
}

$settings_verticaldisplay = getOption('$SETTINGS_VERTICALDISPLAY',false);

if($settings_verticaldisplay) {
	require_once('tapheader-vertical.tpl.php');
} else {
	require_once('tapheader-horizontal.tpl.php');
}

//
