<?php
/*
*
* Author: Sherwin R. Terunez
* Contact: sherwinterunez@yahoo.com
*
* Date Created: January 17, 2019 12:28:10 PM
*
* Description:
*
* Application entry point.
*
*/

//define('ANNOUNCE', true);

error_reporting(E_ALL);

ini_set("max_execution_time", 0);

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

define ("DEVICE_NOTSET", 0);
define ("DEVICE_SET", 1);
define ("DEVICE_OPENED", 2);

class APP_SMS extends SMS {


	public function deviceInit($device=false,$baudrate=115200,$validParams=false) {

		//$validParams = 'ignbrk -icanon -isig -iexten -echo -icrnl ixon -ixany -imaxbel -brkint -opost -onlcr -igncr -inlcr -echoe -echok -echoctl -echoke time 5';

		//$validParams = 'ignbrk time 5';

		$validParams = 'raw ignbrk -brkint -icrnl -imaxbel -opost -onlcr -isig -icanon -iexten -echo -echoe -echok -echoctl -echoke time 5';

		if(!($this->deviceSet($device)&&$this->deviceOpen('rb')&&$this->setBaudRate($baudrate,$validParams,true))) {
			return false;
		}

		return true;
	}

}

function updateCounters() {
	global $appdb, $appaccess;

	$current_schoolyear = getCurrentSchoolYear();

	$unixtime = intval(getDbUnixDate());

	$month = intval(date('m', $unixtime));
	$day = intval(date('d', $unixtime));
	$year = intval(date('Y', $unixtime));
	$hour = intval(date('H', $unixtime));
	$minute = intval(date('i', $unixtime));
	$second = intval(date('s', $unixtime));

	$studentprofile_db = 0;
	$studentprofile_in = 0;
	$studentprofile_out = 0;
	$studentprofile_late = 0;

	$from = date2timestamp("$month/$day/$year 00:00:00",'m/d/Y H:i:s');
	$to = date2timestamp("$month/$day/$year 23:59:59",'m/d/Y H:i:s');

	if(!($result = $appdb->query("select count(studentprofile_id) as db from tbl_studentprofile where studentprofile_schoolyear='$current_schoolyear'"))) {
		json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
		die;
	}

	if(!empty($result['rows'][0]['db'])) {
		$studentprofile_db = $result['rows'][0]['db'];
	}

	if(!($result = $appdb->query("select count(studentdtr_id) as in from tbl_studentdtr where studentdtr_type='IN' and studentdtr_unixtime >= $from and studentdtr_unixtime <= $to"))) {
		json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
		die;
	}

	if(!empty($result['rows'][0]['in'])) {
		$studentprofile_in = $result['rows'][0]['in'];
	}

	if(!($result = $appdb->query("select count(studentdtr_id) as out from tbl_studentdtr where studentdtr_type='OUT' and studentdtr_unixtime >= $from and studentdtr_unixtime <= $to"))) {
		json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
		die;
	}

	if(!empty($result['rows'][0]['out'])) {
		$studentprofile_out = $result['rows'][0]['out'];
	}

	if(!($result = $appdb->query("select count(studentdtr_id) as late from tbl_studentdtr where studentdtr_type='IN'and studentdtr_late>0  and studentdtr_unixtime >= $from and studentdtr_unixtime <= $to"))) {
		json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
		die;
	}

	if(!empty($result['rows'][0]['late'])) {
		$studentprofile_late = $result['rows'][0]['late'];
	}

	//setSetting('$STATS_TIMEIN_'.$year.'_'.$month.'_'.$day, $studentprofile_in);

	//setSetting('$STATS_TIMEINLATE_'.$year.'_'.$month.'_'.$day, $studentprofile_late);

	//setSetting('$STATS_TIMEOUT_'.$year.'_'.$month.'_'.$day, $studentprofile_out);

	//setSetting('$STATS_TOTALDB_'.$year.'_'.$month.'_'.$day, $studentprofile_db);


	print_r(array('$studentprofile_db'=>$studentprofile_db,
								'$studentprofile_in'=>$studentprofile_in,
								'$studentprofile_out'=>$studentprofile_out,
								'$studentprofile_late'=>$studentprofile_late));

}

updateCounters();


//
