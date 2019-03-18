<?php
/*
*
* Author: Sherwin R. Terunez
* Contact: sherwinterunez@yahoo.com
*
* Date Created: December 2, 2018 9:47:05PM
*
* Description:
*
* Application entry point.
*
*/

//define('ANNOUNCE', true);

error_reporting(E_ALL);

ini_set("max_execution_time", 900);

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

//print_r(array('$_POST'=>$_POST));

if(!empty(($license=checkLicense()))) {
} else {
	//print_r(array('ERROR'=>'Invalid or expired license!'));
	//sleep(10);
	//return false;
	die(json_encode(array('ERROR'=>'Invalid or expired license!')));
}

global $appdb;

if(!empty($_POST['id'])&&!empty($_POST['photo'])) {

	$id = trim($_POST['id']);

	$photodata = trim($_POST['photo']);

	$photodata = base64_decode($photodata);

	if(!empty($photodata)) {
	} else {
		die(json_encode(array('ERROR'=>'Invalid base64 photo data.')));
	}

	if(!($result = $appdb->query("select * from tbl_studentprofile where studentprofile_number ='$id'"))) {
		json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
		die;
	}

	if(!empty($result['rows'][0]['studentprofile_number'])) {

		$profile = $result['rows'][0];

		$mobileno = $profile['studentprofile_guardianmobileno'];

		$studentprofile_id = $profile['studentprofile_id'];

		$studentprofile_number = $profile['studentprofile_number'];

		$img = new APP_SimpleImage;

		$img->loadfromstring($photodata);

		//pre(array('mimetype'=>$img->mimetype()));

		if(preg_match('/gif|jpeg|png/si',$img->mimetype())) {
		} else {
			die(json_encode(array('ERROR'=>'Invalid photo! Please upload photo with GIF/JPG/PNG type.')));
		}

		$b64content = base64_encode($photodata);

		//pre(array('$b64content'=>$b64content)); die;

		if($b64content) {

			$content = array();
			$content['upload_sid'] = sha1($b64content);
			$content['upload_type'] = $imgmimetype = $img->mimetype();
			$content['upload_studentprofileid'] = $studentprofile_id;
			$content['upload_content'] = $b64content;
			$content['upload_size'] = strlen($photodata);
			$content['upload_name'] = 'customer_photo';

			if(!($result = $appdb->query("select * from tbl_upload where upload_studentprofileid='$studentprofile_id'"))) {
				json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
				die;
			}

			if(!empty($result['rows'][0]['upload_id'])) {

				$upload_id = $result['rows'][0]['upload_id'];

				if(!($result = $appdb->update("tbl_upload",$content,"upload_id='".$upload_id."'"))) {
					json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror));
					die;
				}

				$ret = array();
				$ret['upload_id'] = $upload_id;
				$ret['id'] = $studentprofile_id;
				$ret['opt'] = 'update';
				$ret['mimetype'] = $imgmimetype;

				die(json_encode($ret));

			} else {

				if(!($result = $appdb->insert("tbl_upload",$content,"upload_id"))) {
					json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
					die;
				}

				if(!empty($result['returning'][0]['upload_id'])) {
					$ret = array();
					$ret['upload_id'] = $result['returning'][0]['upload_id'];
					$ret['id'] = $studentprofile_id;
					$ret['opt'] = 'insert';
					$ret['mimetype'] = $imgmimetype;

					die(json_encode($ret));
				}

			}

			die(json_encode(array('ERROR'=>'An unknown error has occured!')));
		}

  } else {
		die(json_encode(array('ERROR'=>'Invalid Student ID!')));
	}

}

/*
$ret = array();

if(!empty($invalid)) {
	$ret['INVALID'] = $invalid;
}

if(!empty($update)) {
	$ret['UPDATE'] = $update;
}

if(!empty($duplicate)) {
	$ret['DUPLICATE'] = $duplicate;
}

if(!empty($success)) {
	$ret['SUCCESS'] = $success;
}

if(!empty($delete)) {
	$ret['DELETE'] = $delete;
}

if(!empty($ret)) {
} else {
	$ret['ERROR'] = 'An error has occured while processing data.';
}

json_encode_return($ret);
*/

//
