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

//pre(array('$_GET'=>$_GET));

global $appdb;

if(!empty($_GET['pid'])&&is_numeric($_GET['pid'])&&intval($_GET['pid'])>0) {

	if(!($result = $appdb->query("select * from tbl_upload where upload_studentprofileid=".intval($_GET['pid'])))) {
		json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
		die;
	}

	if(!empty($result['rows'][0]['upload_content'])) {
		//$retval['uploadid'] = $result['rows'][0]['upload_id'];
		$content = base64_decode($result['rows'][0]['upload_content']);
	}

	if(!empty($content)) {

		if(!empty($_GET['size'])&&is_numeric($_GET['size'])&&intval($_GET['size'])>0) {
			$size = intval($_GET['size']);
		}

		header("Content-Type: image/jpg");

		$img = new APP_SimpleImage;

		$img->loadfromstring($content);

		if(!empty($size)) {
			if($img->getWidth()<$img->getHeight()) {
				$img->resizeToWidth($size);
			} else {
				$img->resizeToHeight($size);				
			}
		}

		$img->output();

		//print_r($content);
	}

	die();

	//pre($result);

}
