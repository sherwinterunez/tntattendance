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

//MESSENGER PUSH NOTIFICATION
$facebook = new FacebookMessengerSendApi\SendAPI();

//MESSENGER - PAU
$settings_sendmessenger  = getOption('$SETTINGS_SENDMESSENGER',false);
$settings_messengertoken = getOption('$SETTINGS_MESSENGERTOKEN',false);

$settings_sendtimeinnotification  = getOption('$SETTINGS_SENDTIMEINNOTIFICATION',true);
$settings_sendtimeoutnotification  = getOption('$SETTINGS_SENDTIMEOUTNOTIFICATION',true);
$settings_sendlatenotification  = getOption('$SETTINGS_SENDLATENOTIFICATION',false);
$settings_sendabsentnotification  = getOption('$SETTINGS_SENDABSENTNOTIFICATION',false);
$settings_sendpushnotification  = getOption('$SETTINGS_SENDPUSHNOTIFICATION',false);
$settings_sendsmsnotification  = getOption('$SETTINGS_SENDSMSNOTIFICATION',true);

$settings_timeinnotification = getOption('$SETTINGS_TIMEINNOTIFICATION');
$settings_timeoutnotification = getOption('$SETTINGS_TIMEOUTNOTIFICATION');

$settings_latenotification = getOption('$SETTINGS_LATENOTIFICATION',false);
$settings_absentnotification = getOption('$SETTINGS_ABSENTNOTIFICATION',false);

if(!empty($settings_messengertoken)) {
} else {
	$settings_sendmessenger = false;
}

///////////////////////////////////////////////////////////////////////////////

// send facebook messenger notification

$limit = 5;

$notifications = false;

if(!($result = $appdb->query("select * from tbl_smsoutbox where smsoutbox_sendfb>0 and smsoutbox_sendfbstatus=1 order by smsoutbox_id asc limit $limit"))) {
	json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
	die;
}

if(!empty($result['rows'][0]['smsoutbox_id'])) {
	$notifications = $result['rows'];
}

if(!empty($notifications)) {

	foreach($notifications as $k=>$v) {

		$smsoutbox_message = $v['smsoutbox_message'];
		$smsoutbox_id = $v['smsoutbox_id'];

		if(!empty($settings_messengertoken)&&!empty($v['smsoutbox_fbid'])&&IsInternet()) {

			print_r(array('outbox facebook notification'=>$v));

			$access_token = trim($settings_messengertoken);
			//MESSENGER PUSH NOTIFICATION
			$recipient = explode(" ",(trim($v['smsoutbox_fbid'])));
			$facebook->setAccessToken($access_token);

			$statusCode = 100;

			for($i = 0; $i < count($recipient); $i++){

				$message = $facebook->contentType->text->text($smsoutbox_message);

				try {

					$ret = $facebook
						->setRecipientId($recipient[$i])
						->sendMessage($message);

					if(!empty($ret)) {
						$statusCode = $ret->getStatusCode();
						$body = $ret->getBody();
						print_r(array('$i'=>$i,'$statusCode'=>$statusCode,'$body'=>$body));
					}

				}
				//catch exception
				catch(Exception $e) {
				 // echo 'Message: ' .$e->getMessage();
					print_r(array('Exception'=>$e->getMessage()));
				}

			}

			/*$facebook->setAccessToken($access_token);
			$message = $facebook->contentType->text->text($smsoutbox_message);
			$facebook
				->setRecipientId(trim($v['smsoutbox_fbid']))
				->sendMessage($message);*/

			$content = array();
			$content['smsoutbox_sendfbstatus'] = $statusCode;

			if(!($result = $appdb->update("tbl_smsoutbox",$content,"smsoutbox_id=".$smsoutbox_id))) {
				json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
				die;
			}

		}

	}

}



#eof
