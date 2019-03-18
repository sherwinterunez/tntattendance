<?php
/*
*
* Author: Paulo Rojales
*
*
* Date Created: Oct 18 2018
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
//require_once(ABS_PATH.'/vendor/autoload.php'); //MESSENGER APK
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
//define('REMOTE_FCMSENDTOTOPIC_URL','https://tntserver.obisph.com/fcmsendtotopic.php');
define('REMOTE_FCMSENDVERIFY_URL','https://tntserver.obisph.com/fcmsendverify.php');

date_default_timezone_set('Asia/Manila');

//if(!getOption('$SETTINGS_SENDPUSHNOTIFICATION',false)) {
//	die;
//}

if(!empty(($license=checkLicense()))) {
} else {
	print_r(array('ERROR'=>'Invalid or expired license!'));
	sleep(10);
	return false;
}

global $appdb;

//$id = true;

if(!empty($_POST['id'])) {
	$id = trim($_POST['id']);

if(!($result = $appdb->query("select * from tbl_studentprofile where studentprofile_number ='$id'"))) {
	json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
	die;
}

	if(!empty($result['rows'][0])) {
		$notifications = $result['rows'];
	}

	if(!empty($notifications)) {

		foreach($notifications as $k=>$v) {
    $msgin .= "APPTOPUS MESSENGER YOUR VERIFICATION CODE IS : ";
		$msgin .= $v['studentprofile_verification'];
		$mobileno = $v['studentprofile_guardianmobileno'];
		$id = $v['studentprofile_id'];


      pre(array("mobileno" => $mobileno));

		if(empty($mobileno)) {
		} else {

		$asim = getAllSims(3);

		pre(array('$asim'=>$asim));

		if(!empty($asim)) {

			shuffle($asim);

			$unixtime = intval(getDbUnixDate());

			foreach($asim as $m=>$n) {

				$failed_stamp = getOption('FAILEDSTAMP_'.$n['sim_number'],false);

				if(!empty($failed_stamp)) {

					$tm = $unixtime - $failed_stamp;

					if($tm<300) {
						continue;
					}
				}

					if(!empty($license['sc'])) {
					}
					pre(array('$mobileno'=>$mobileno,'$m'=>$n['sim_number'],'$msgin'=>$msgin,'$license[sc]'=>$license['sc']));
					sendToOutBoxPriority($mobileno,$n['sim_number'],$msgin,0,1,1,0,0,$id); //verify


				break;
			}

		} else {
			// no sim card detected or no connected gsm modem
  				if(!empty($license['sc'])) {
  				}
  				pre(array('$mobileno'=>$mobileno,'$m'=>false,'$msgin'=>$msgin,'$license[sc]'=>$license['sc']));
  				sendToOutBoxPriority($mobileno,$n['sim_number'],$msgin,0,1,1,0,0,$id); //verify
    		}
      }
  	}
  }
}



// send push notification
/*
$notifications = false;

if(!($result = $appdb->query("select * from tbl_smsoutbox where smsoutbox_sendpush>0 and smsoutbox_pushstatus=1 and smsoutbox_pushid=0 order by smsoutbox_id asc limit 1"))) {
	json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
	die;
}

if(!empty($result['rows'][0]['smsoutbox_id'])) {
	$notifications = $result['rows'];
}

if(!empty($notifications)) {

	print_r(array('outbox notifications'=>$notifications));

	foreach($notifications as $k=>$v) {

		$ch = new MyCURL;

		curl_setopt($ch->ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch->ch, CURLOPT_SSL_VERIFYHOST, false);

		$smsoutbox_message = $v['smsoutbox_message'];

		$fullname = getStudentFullName($v['smsoutbox_contactid']);

		$profile = getStudentProfile($v['smsoutbox_contactid']);

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

		if(!($retcont = $ch->post(REMOTE_FCMSENDVERIFY_URL,$post))) {
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

}*/
