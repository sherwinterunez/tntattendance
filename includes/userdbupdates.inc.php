<?php
/*
*
* Author: Sherwin R. Terunez
* Contact: sherwinterunez@yahoo.com
*
* Date Created: April 23, 2017 12:32 AM
*
* Description:
*
* User Database Updates
*
*/

if(!defined('APPLICATION_RUNNING')) {
	header("HTTP/1.0 404 Not Found");
	die('access denied');
}

if(defined('ANNOUNCE')) {
	echo "\n<!-- loaded: ".__FILE__." -->\n";
}

/* INCLUDES_START */

global $appdb;

if(!$appdb->isColumnExist('tbl_studentprofile','studentprofile_schoolyear')) {
	$appdb->query("alter table tbl_studentprofile add column studentprofile_schoolyear text DEFAULT ''::text NOT NULL");
}

if(!$appdb->isColumnExist('tbl_studentprofile','studentprofile_schoolyearstart')) {
	$appdb->query("alter table tbl_studentprofile add column studentprofile_schoolyearstart integer NOT NULL DEFAULT 0");
}

if(!$appdb->isColumnExist('tbl_studentprofile','studentprofile_schoolyearend')) {
	$appdb->query("alter table tbl_studentprofile add column studentprofile_schoolyearend integer NOT NULL DEFAULT 0");
}

if(!$appdb->isColumnExist('tbl_smsoutbox','smsoutbox_latenoti')) {
	$appdb->query("alter table tbl_smsoutbox add column smsoutbox_latenoti integer NOT NULL DEFAULT 0");
}

if(!$appdb->isColumnExist('tbl_smsoutbox','smsoutbox_absentnoti')) {
	$appdb->query("alter table tbl_smsoutbox add column smsoutbox_absentnoti integer NOT NULL DEFAULT 0");
}

/* INCLUDES_END */


#eof ./includes/userdbupdates.inc.php
