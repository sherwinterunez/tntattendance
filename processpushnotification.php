<?php
/*
*
* Author: Sherwin R. Terunez
* Contact: sherwinterunez@yahoo.com
*
* Date Created: December 18, 2018 9:55:15AM
*
* Description:
*
* Application entry point.
*
*/

//define('ANNOUNCE', true);

error_reporting(E_ALL);

ini_set("max_execution_time", 300);

ini_set('precision',30);

define('APPLICATION_RUNNING', true);

define('ABS_PATH', dirname(__FILE__) . '/');

if(defined('ANNOUNCE')) {
	echo "\n<!-- loaded: ".__FILE__." -->\n";
}

//define('INCLUDE_PATH', ABS_PATH . 'includes/');
require_once(ABS_PATH.'/vendor/autoload.php'); //MESSENGER APP
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

/*
http://obis101.terunez.com/fcmsendtotopic.php?topic=tapntxt09493621618&msg=Hello&title=Tap%20N%20Txt
*/

//define('REMOTE_FCMSENDTOTOPIC_URL','http://obis101.terunez.com/fcmsendtotopic.php');
define('REMOTE_FCMSENDTOTOPIC_URL','https://tntserver.obisph.com/fcmsendtotopic.php');

date_default_timezone_set('Asia/Manila');

if(!empty(($license=checkLicense()))) {
} else {
	print_r(array('ERROR'=>'Invalid or expired license!'));
	sleep(10);
	return false;
}

global $appdb;

///////////////////////////////////////////////////////////////////////////////

// send push notification

$settings_disablepushnoti = getOption('$SETTINGS_DISABLEPUSHNOTI',false);

if(!empty($settings_disablepushnoti)) {
	$retval = array();
	$retval['$SETTINGS_DISABLEPUSHNOTI'] = $settings_disablepushnoti;
	$retval['disabled'] = true;
	echo json_encode($retval);
	die;
}

$limit = 5;

$notifications = false;

if(!($result = $appdb->query("select * from tbl_smsoutbox where smsoutbox_sendpush>0 and smsoutbox_pushstatus=1 and smsoutbox_pushid=0 order by smsoutbox_id asc limit $limit"))) {
	json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
	die;
}

if(!empty($result['rows'][0]['smsoutbox_id'])) {
	$notifications = $result['rows'];
}

if(!empty($notifications)) {

	foreach($notifications as $k=>$v) {

		print_r(array('outbox push notifications'=>$v));

		$ch = new MyCURL;

		//curl_setopt($ch->ch, CURLOPT_SSL_VERIFYPEER, true);
		//curl_setopt($ch->ch, CURLOPT_SSL_VERIFYHOST, 0);
		//curl_setopt($ch->ch, CURLOPT_CAINFO, ABS_PATH . "cacert/cacert.pem");

		curl_setopt($ch->ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch->ch, CURLOPT_SSL_VERIFYHOST, false);

		$smsoutbox_message = $v['smsoutbox_message'];

		$fullname = getStudentFullName($v['smsoutbox_contactid']);

		$profile = getStudentProfile($v['smsoutbox_contactid']);

		$yearlevel = getStudentYearLevelID($v['smsoutbox_contactid']);

		if(!empty($fullname)) {
			$smsoutbox_message = str_replace('%STUDENTFULLNAME%',strtoupper($fullname),$smsoutbox_message);
		}

		$smsoutbox_message = str_replace('%FIRSTNAME%',strtoupper($profile['studentprofile_firstname']),$smsoutbox_message);
		$smsoutbox_message = str_replace('%LASTNAME%',strtoupper($profile['studentprofile_lastname']),$smsoutbox_message);
		$smsoutbox_message = str_replace('%MIDDLENAME%',strtoupper($profile['studentprofile_middlename']),$smsoutbox_message);

		//$dt = date('m/d/Y H:i:s',$profile['studentdtr_unixtime']);

		//$smsoutbox_message = str_replace('%DATETIME%',$dt,$smsoutbox_message);

		$mobileno = getGuardianMobileNo($profile['studentprofile_id']);

		if(!empty($mobileno)) {
		} else {

			$content = array();
			$content['smsoutbox_pushid'] = 1;
			$content['smsoutbox_pushstatus'] = 5;
			$content['smsoutbox_pushsentstamp'] = 'now()';

			if(!($result = $appdb->update("tbl_smsoutbox",$content,"smsoutbox_id=".$v['smsoutbox_id']))) {
				json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
				die;
			}

			continue;
		}

		$content = array();
		$content['smsoutbox_pushstatus'] = 3;

		if(!($result = $appdb->update("tbl_smsoutbox",$content,"smsoutbox_id=".$v['smsoutbox_id']))) {
			json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
			die;
		}

		$post = array();
		$post['topic'] = 'tapntxt'.$mobileno;
		$post['msg'] = $smsoutbox_message;
		$post['title'] = 'TAP N TXT';

		//pre(array('$post'=>$post,'$profile'=>$profile));

		if(!($retcont = $ch->post(REMOTE_FCMSENDTOTOPIC_URL,$post))) {
			print_r(array('error'=>$retcont));
		}

		//print_r(array('$retcont'=>$retcont,'$post'=>$post));

		if(!empty($retcont['content'])) {
			$retval = json_decode($retcont['content'],true);
		}

		if(!empty($retval['message_id'])) {

			$content = array();
			$content['smsoutbox_pushid'] = floatval($retval['message_id']);
			$content['smsoutbox_pushstatus'] = 4;
			$content['smsoutbox_pushsentstamp'] = 'now()';

			//print_r(array('$retval'=>$retval,'$content'=>$content));

			if(!($result = $appdb->update("tbl_smsoutbox",$content,"smsoutbox_id=".$v['smsoutbox_id']))) {
				json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
				die;
			}

		} else {

			$content = array();
			$content['smsoutbox_pushid'] = 1;
			$content['smsoutbox_pushstatus'] = 5;
			$content['smsoutbox_pushsentstamp'] = 'now()';

			if(!($result = $appdb->update("tbl_smsoutbox",$content,"smsoutbox_id=".$v['smsoutbox_id']))) {
				json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
				die;
			}

		}

	}

}



#eof
