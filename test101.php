<?php
/*
*
* Author: Sherwin R. Terunez
* Contact: sherwinterunez@yahoo.com
*
* Date Created: February 23, 2011
*
* Description:
*
* Application entry point.
*
*/

//define('ANNOUNCE', true);

error_reporting(E_ALL);

ini_set("max_execution_time", 300);

define('APPLICATION_RUNNING', true);

define('ABS_PATH', dirname(__FILE__) . '/');

if(defined('ANNOUNCE')) {
	echo "\n<!-- loaded: ".__FILE__." -->\n";
}

//define('INCLUDE_PATH', ABS_PATH . 'includes/');

require_once(ABS_PATH.'includes/index.php');
//require_once(ABS_PATH.'modules/index.php');

/*require_once(INCLUDE_PATH.'config.inc.php');
require_once(INCLUDE_PATH.'miscfunctions.inc.php');
require_once(INCLUDE_PATH.'functions.inc.php');
require_once(INCLUDE_PATH.'errors.inc.php');
require_once(INCLUDE_PATH.'error.inc.php');
require_once(INCLUDE_PATH.'db.inc.php');
require_once(INCLUDE_PATH.'pdu.inc.php');
require_once(INCLUDE_PATH.'pdufactory.inc.php');
require_once(INCLUDE_PATH.'utf8.inc.php');
require_once(INCLUDE_PATH.'sms.inc.php');
require_once(INCLUDE_PATH.'userfuncs.inc.php');*/

date_default_timezone_set('Asia/Manila');

global $appdb;

//$tb = $appdb->isTableExist('tbl_studentprofile');

/*if(!$appdb->isColumnExist('tbl_studentprofile','studentprofile_schoolyear')) {
	$appdb->query("alter table tbl_studentprofile add column studentprofile_schoolyear text DEFAULT ''::text NOT NULL");
}

pre(array('$appdb'=>$appdb));*/

/*$ch = new MyCurl;

$ch->setopt(CURLOPT_ENCODING,"gzip");

$cont = $ch->get('http://tntattendance.dev/app/getoutbox/waiting/10');

$info = $ch->getinfo();

pre(array('$cont'=>$cont,'$info'=>$info));*/

//isServerLicense();


/*$host = '10.1.2.6';
$port = 80;
$waitTimeoutInSeconds = 1;
if($fp = @fsockopen($host,$port,$errCode,$errStr,$waitTimeoutInSeconds)){
   // It worked
	 echo "\nit worked!\n";
} else {
   // It didn't work
	 echo "\nit didn't worked!\n";
}
@fclose($fp);*/

//$host = '10.1.2.5';

/*$host = 'tntattendance.dev';

if(pingDomain($host)>0) {

	$ch = new MyCurl;

	$ch->setopt(CURLOPT_ENCODING,"gzip");

	$cont = $ch->get('http://'.$host.'/app/getoutbox/waiting/10');

	$info = $ch->getinfo();

	//pre(array('$cont'=>$cont,'$info'=>$info));

	pre('success!');

}*/

//$test = 'hello';

//$x = explode('|',$test);

//pre(array('$x'=>$x));

//$r = pingDomain('10.1.2.86');

//pre(array('$r'=>$r));


/*
$url = 'http://172.16.170.20/tap/tapped/';

$vars = array();
$vars['rfid'] = 1;
$vars['unixtime'] = time();
$vars['imagesize'] = 350;

$ch = new MyCurl;

$cont = $ch->post($url,$vars);

pre(array('$cont'=>$cont));


$url = 'http://172.16.170.20/tap/refresh/';

$vars = array();
$vars['unixtime'] = time();
$vars['imagesize'] = 350;

$ch = new MyCurl;

$cont = $ch->post($url,$vars);

pre(array('$cont'=>$cont));
*/

$str = '1100EE00E2001620040111909FB7E100EE00E200001604017911909F16E11100EE000016200401791190B716E11100EE0000001620040179909FB716E111EE00E200001620017911909FB716E11100EE00E2001620040111909FB716E100EE00E200162001791190B716E11100EEE20000162001791190B716E11100EEE2001620040179909FB7E11100EEE2001620040179909FB716E100EEE2000020040179909F16E11100EE0000001604017911909F16E11100EEE2000016040179909FB7E11100EE00E2000016200111909FB716E111EE00E200001620017911909FB716E1';

$out = explode('1100EE',$str);

pre(array('$out'=>$out));


//
