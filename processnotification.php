<?php
/*
*
* Author: Sherwin R. Terunez
* Contact: sherwinterunez@yahoo.com
*
* Date Created: January 13, 2017
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

/*
http://obis101.terunez.com/fcmsendtotopic.php?topic=tapntxt09493621618&msg=Hello&title=Tap%20N%20Txt
*/

define('REMOTE_FCMSENDTOTOPIC_URL','http://obis101.terunez.com/fcmsendtotopic.php');

date_default_timezone_set('Asia/Manila');

//if(!getOption('$SETTINGS_SENDPUSHNOTIFICATION',false)) {
//	die;
//}

global $appdb;

if(!($result = $appdb->query("select * from tbl_studentdtr where studentdtr_notified=0 order by studentdtr_id asc limit 1"))) {
	json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
	die;
}

if(!empty($result['rows'][0]['studentdtr_id'])) {
	$notifications = $result['rows'];
}

if(!empty($notifications)) {

	print_r($notifications);

	foreach($notifications as $k=>$v) {

		$ch = new MyCURL;

		$msgin = getOption('$SETTINGS_TIMEINNOTIFICATION');
		$msgout = getOption('$SETTINGS_TIMEOUTNOTIFICATION');

		$fullname = getStudentFullName($v['studentdtr_studentid']);

		$profile = getStudentProfile($v['studentdtr_studentid']);

		if(!empty($fullname)) {
			$msgin = str_replace('%STUDENTFULLNAME%',strtoupper($fullname),$msgin);
			$msgout = str_replace('%STUDENTFULLNAME%',strtoupper($fullname),$msgout);
		}

		$msgin = str_replace('%FIRSTNAME%',strtoupper($v['studentprofile_firstname']),$msgin);
		$msgin = str_replace('%LASTNAME%',strtoupper($v['studentprofile_lastname']),$msgin);
		$msgin = str_replace('%MIDDLENAME%',strtoupper($v['studentprofile_middlename']),$msgin);

		$msgout = str_replace('%FIRSTNAME%',strtoupper($v['studentprofile_firstname']),$msgout);
		$msgout = str_replace('%LASTNAME%',strtoupper($v['studentprofile_lastname']),$msgout);
		$msgout = str_replace('%MIDDLENAME%',strtoupper($v['studentprofile_middlename']),$msgout);

		$msgin = str_replace('%d%',date('d',$v['studentdtr_unixtime']),$msgin);
		$msgin = str_replace('%F%',date('F',$v['studentdtr_unixtime']),$msgin);
		$msgin = str_replace('%m%',date('m',$v['studentdtr_unixtime']),$msgin);
		$msgin = str_replace('%M%',date('M',$v['studentdtr_unixtime']),$msgin);
		$msgin = str_replace('%n%',date('n',$v['studentdtr_unixtime']),$msgin);
		$msgin = str_replace('%y%',date('y',$v['studentdtr_unixtime']),$msgin);
		$msgin = str_replace('%Y%',date('Y',$v['studentdtr_unixtime']),$msgin);
		$msgin = str_replace('%a%',date('a',$v['studentdtr_unixtime']),$msgin);
		$msgin = str_replace('%A%',date('A',$v['studentdtr_unixtime']),$msgin);
		$msgin = str_replace('%g%',date('g',$v['studentdtr_unixtime']),$msgin);
		$msgin = str_replace('%G%',date('G',$v['studentdtr_unixtime']),$msgin);
		$msgin = str_replace('%h%',date('h',$v['studentdtr_unixtime']),$msgin);
		$msgin = str_replace('%H%',date('H',$v['studentdtr_unixtime']),$msgin);
		$msgin = str_replace('%i%',date('i',$v['studentdtr_unixtime']),$msgin);
		$msgin = str_replace('%s%',date('s',$v['studentdtr_unixtime']),$msgin);
		$msgin = str_replace('%r%',date('r',$v['studentdtr_unixtime']),$msgin);

		$msgout = str_replace('%d%',date('d',$v['studentdtr_unixtime']),$msgout);
		$msgout = str_replace('%F%',date('F',$v['studentdtr_unixtime']),$msgout);
		$msgout = str_replace('%m%',date('m',$v['studentdtr_unixtime']),$msgout);
		$msgout = str_replace('%M%',date('M',$v['studentdtr_unixtime']),$msgout);
		$msgout = str_replace('%n%',date('n',$v['studentdtr_unixtime']),$msgout);
		$msgout = str_replace('%y%',date('y',$v['studentdtr_unixtime']),$msgout);
		$msgout = str_replace('%Y%',date('Y',$v['studentdtr_unixtime']),$msgout);
		$msgout = str_replace('%a%',date('a',$v['studentdtr_unixtime']),$msgout);
		$msgout = str_replace('%A%',date('A',$v['studentdtr_unixtime']),$msgout);
		$msgout = str_replace('%g%',date('g',$v['studentdtr_unixtime']),$msgout);
		$msgout = str_replace('%G%',date('G',$v['studentdtr_unixtime']),$msgout);
		$msgout = str_replace('%h%',date('h',$v['studentdtr_unixtime']),$msgout);
		$msgout = str_replace('%H%',date('H',$v['studentdtr_unixtime']),$msgout);
		$msgout = str_replace('%i%',date('i',$v['studentdtr_unixtime']),$msgout);
		$msgout = str_replace('%s%',date('s',$v['studentdtr_unixtime']),$msgout);
		$msgout = str_replace('%r%',date('r',$v['studentdtr_unixtime']),$msgout);

		$dt = date('m/d/Y H:i:s',$v['studentdtr_unixtime']);

		$msgin = str_replace('%DATETIME%',$dt,$msgin);
		$msgout = str_replace('%DATETIME%',$dt,$msgout);

		$mobileno = getGuardianMobileNo($v['studentdtr_studentid']);

		if(getOption('$SETTINGS_SENDPUSHNOTIFICATION',false)) {

			$post = array();
			$post['topic'] = 'tapntxt'.$mobileno;

			if($v['studentdtr_type']=='IN') {
				$post['msg'] = $msgin;
			} else {
				$post['msg'] = $msgout;
			}

			$post['title'] = 'Tap N Txt';

			if(!($retcont = $ch->post(REMOTE_FCMSENDTOTOPIC_URL,$post))) {
				print_r(array('error'=>$retcont));
			}

			print_r(array('$retcont'=>$retcont));

			if(!empty($retcont['content'])) {
				$retval = json_decode($retcont['content'],true);
			}

			if(!empty($retval['message_id'])) {

				$content = array();
				$content['studentdtr_notified'] = $retval['message_id'];
				$content['studentdtr_notifystamp'] = 'now()';

				if(!($result = $appdb->update("tbl_studentdtr",$content,"studentdtr_id=".$v['studentdtr_id']))) {
					json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
					die;
				}

			} else {

				$content = array();
				$content['studentdtr_notified'] = 1;
				$content['studentdtr_notifystamp'] = 'now()';

				if(!($result = $appdb->update("tbl_studentdtr",$content,"studentdtr_id=".$v['studentdtr_id']))) {
					json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
					die;
				}

			}

		}

		$asim = getAllSims(3);

		pre(array('$asim'=>$asim));

		if(!empty($asim)) {

			shuffle($asim);

			foreach($asim as $m=>$n) {

				if($v['studentdtr_type']=='IN') {
					pre(array('$mobileno'=>$mobileno,'$m'=>$n['sim_number'],'$msgin'=>$msgin));
					sendToOutBox($mobileno,$n['sim_number'],$msgin);
				} else {
					pre(array('$mobileno'=>$mobileno,'$m'=>$n['sim_number'],'$msgin'=>$msgout));
					sendToOutBox($mobileno,$n['sim_number'],$msgout);
				}

				break;
			}

		}

	}

}
