<?php
/*
*
* Author: Sherwin R. Terunez
* Contact: sherwinterunez@yahoo.com
*
* Date Created: October 28, 2016
*
* Description:
*
* Application entry point.
*
*/

//define('ANNOUNCE', true);

error_reporting(E_ALL);

//ini_set("max_execution_time", 300);

define('APPLICATION_RUNNING', true);

define('ABS_PATH', dirname(__FILE__) . '/');

if(defined('ANNOUNCE')) {
	echo "\n<!-- loaded: ".__FILE__." -->\n";
}

//define('INCLUDE_PATH', ABS_PATH . 'includes/');

require_once(ABS_PATH.'includes/index.php');

global $appdb, $appaccess, $appsession;

/*
$appsession->start();

$ret = array();
$ret['session'] = $_SESSION;
$ret['server'] = $_SERVER;

die(json_encode($ret));
*/

define('REMOTE_FCMSENDTOTOPIC_URL','https://tntserver.obisph.com/fcmsendtotopic.php');

$url = 'http://tntserver.obisph.com/fcmsendtotopic.php?from=sherwin&msg=hello&title=title&topic=tapntxt09088853095';

$ch = new MyCURL;

curl_setopt($ch->ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch->ch, CURLOPT_SSL_VERIFYHOST, false);
//curl_setopt($ch->ch, CURLOPT_CAINFO, ABS_PATH . "cacert/cacert.pem");

$mobileno = '09088853095';

$post = array();
$post['topic'] = 'tapntxt'.$mobileno;
$post['msg'] = 'Hello, world!';
$post['title'] = 'TAP N TXT';

if(!($retcont = $ch->post(REMOTE_FCMSENDTOTOPIC_URL,$post))) {
	print_r(array('error'=>$retcont));
}

print_r(array('$retcont'=>$retcont,'$post'=>$post,'curl_error($ch)'=>curl_error($ch->ch)));

///
