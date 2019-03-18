<?php
/*
*
* Author: Sherwin R. Terunez
* Contact: sherwinterunez@yahoo.com
*
* Date Created: November 26, 2018 7:26:25PM
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
	//print_r(array('ERROR'=>'Invalid or expired license!'));
	//sleep(10);
	//return false;
	die(json_encode(array('ERROR'=>'Invalid or expired license!')));
}

global $appdb;

$settings_sendmessenger  = getOption('$SETTINGS_SENDMESSENGER',false);
$settings_messengertoken = getOption('$SETTINGS_MESSENGERTOKEN',false);
$settings_sendpushnotification  = getOption('$SETTINGS_SENDPUSHNOTIFICATION',false);

if(!empty($settings_messengertoken)) {
} else {
	$settings_sendmessenger = false;
}

//$id = true;

//$_POST['id'] = '201420934';
//$_POST['message'] = 'This is a sample message!';

if(!empty($_POST['id'])&&!empty($_POST['message'])) {

	$ids = trim($_POST['id']);

	$ids = str_replace(' ',';',$ids);
	$ids = str_replace('|',';',$ids);
	$ids = str_replace(',',';',$ids);

	$ida = explode(';',$ids);

	$queued = array();

	foreach($ida as $k=>$id) {
		$id = trim($id);

		$message = trim($_POST['message']);

		if(!($result = $appdb->query("select * from tbl_studentprofile where studentprofile_number ='$id'"))) {
			json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
			die;
		}

		if(!empty($result['rows'][0]['studentprofile_number'])) {

			$profile = $result['rows'][0];

			$mobileno = $profile['studentprofile_guardianmobileno'];

			$studentprofile_id = $profile['studentprofile_id'];

			$studentprofile_number = $profile['studentprofile_number'];

			//print_r(array('$profile'=>$profile));

			$fbmessenger = 0;

			$push = 0;

			if($settings_sendpushnotification) {
				$push = 1;
			}

			if($settings_sendmessenger&&!empty($profile['studentprofile_messengerid'])&&IsInternet()) {
				$fbmessenger = $profile['studentprofile_messengerid'];
			}

			$status = 1; // waiting

			if(!empty($fbmessenger)) {
				$status = 4;
				$push = 0;
			}

			$asim = getAllSims(3,true);

			//pre(array('$asim'=>$asim));

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

					//pre(array('$mobileno'=>$mobileno,'$m'=>$n['sim_number'],'$message'=>$message,'$license[sc]'=>$license['sc'],'$fbmessenger'=>$fbmessenger));
					sendToOutBoxPriority($mobileno,$n['sim_number'],$message,$push,1,$status,0,0,$studentprofile_id,$fbmessenger);
					$queued[] = $studentprofile_number;
					break;
				}

			} else {
				// no sim card detected or no connected gsm modem
				//pre(array('$mobileno'=>$mobileno,'$m'=>false,'$message'=>$message,'$license[sc]'=>$license['sc'],'$fbmessenger'=>$fbmessenger));
				sendToOutBoxPriority($mobileno,false,$message,$push,1,$status,0,0,$studentprofile_id,$fbmessenger);
				$queued[] = $studentprofile_number;
			}

		}
	}

	$ret = array();
	$ret['success'] = $queued;
	die(json_encode($ret));
}
