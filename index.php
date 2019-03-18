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

define('APPLICATION_RUNNING', true);

define('ABS_PATH', dirname(__FILE__) . '/');

if(defined('ANNOUNCE')) {
	echo "\n<!-- loaded: ".__FILE__." -->\n";
}

date_default_timezone_set('Asia/Manila');

setlocale(LC_ALL,'en_US.UTF-8');

require_once(ABS_PATH.'includes/memcached.inc.php');

function _pre($data) {
	echo "\n\n<pre>\n\n";
	print_r($data);
	echo "\n\n</pre>\n\n";
}

function _prebuf($data) {
	ob_start();
	_pre($data);
	$output = ob_get_contents();
	ob_end_clean();
	return $output;
}

function _log_notice($str=false) {
	if(!empty($str)) {
		return trigger_error(_prebuf($str));
	}

	return false;
}

if(!empty($memcached)&&!empty($_SERVER['REMOTE_ADDR'])&&!empty($_SERVER['REQUEST_URI'])&&$_SERVER['REQUEST_URI']=='/tap/refresh/') {

	//require_once(ABS_PATH.'includes/functions.inc.php');

	$var = 'DISPLAY_'.$_SERVER['REMOTE_ADDR'];

	$data = $memcached->get($var);

	if(!empty($data)) {

		//_log_notice(array('/tap/refresh/'=>$data));

		$retval = json_decode($data, true);

		if(!empty($retval)&&is_array($retval)) {

			//$data['db'] = intval($studentprofile_db);

			$timein = $memcached->get('STATSTIMEIN');

			if(!empty($timein)) {
				$retval['in'] = intval($timein);
			}

			$timeout = $memcached->get('STATSTIMEOUT');

			if(!empty($timeout)) {
				$retval['out'] = intval($timeout);
			}

			$timelate = $memcached->get('STATSTIMEINLATE');

			if(!empty($timelate)) {
				$retval['late'] = intval($timelate);
			}

			//$retval['out'] = intval($memcached->get('STATSTIMEOUT'));
			//$retval['late'] = intval($memcached->get('STATSTIMEINLATE'));

			if(!empty($retval)) {
				header('Content-type: application/json');
				die(json_encode($retval));
			}

		}

	}

}

require_once(ABS_PATH.'includes/index.php');
//require_once(ABS_PATH.'includes.min/includes.inc.php');
//require_once(ABS_PATH.'includes.min/includes.encoded.php');
require_once(ABS_PATH.'modules/index.php');
require_once(ABS_PATH.'templates/default/index.php');

/*function check_login() {
	global $applogin;

	//pre($_SERVER);


	//if(!preg_match("#\/login(.+?)#si",$_SERVER['REQUEST_URI'])&&!$applogin->is_loggedin()) {

	if(preg_match("#\/app(.+?)#si",$_SERVER['REQUEST_URI'])&&!$applogin->is_loggedin()) {

		//if(!empty($_POST)) {
		//	json_return_error(255);
		//}

		redirect301('/'.$applogin->pathid.'/');
	}
}*/

function index() {
	echo "Hello World!";
}

function defaultroute() {
	global $approuter, $appindex;

	//$approuter->addroute(array('^/logout/$' => array('id'=>'logout','param'=>'action=logout', 'callback'=>'logout')));
	//$approuter->addroute(array('^/\?(.*)$' => array('id'=>'index','param'=>'action=index', 'callback'=>'index')));
	//$approuter->addroute(array('^/$' => array('id'=>'index','param'=>'action=index', 'callback'=>'index')));
	//$approuter->addroute(array('^/(.*)' => array('id'=>'defaultpage','param'=>'action=error&param=$1', 'callback'=>'notfound')));

	//$approuter->addroute(array('^/(.*)' => array('id'=>'index','param'=>'action=notfound&param=$1', 'callback'=>array($appindex,'notfound'))));
}

function notfound() {
	//header('Location: /');
	die('Not Found!');
}

function logout() {
	die("Logout!");
}

//add_action('router','check_login');

//pre(array('$_SERVER'=>$_SERVER));

$appsession->start();

//$_SESSION['timestamp'] = time();

$_SESSION['timestamp'] = getTimeFromServer();

$_SESSION['datestamp'] = date('l jS \of F Y h:i:s A',$_SESSION['timestamp']);

//log_notice(array('$_SESSION'=>$_SESSION));

add_action('routes','defaultroute',999);

$approuter->route();
