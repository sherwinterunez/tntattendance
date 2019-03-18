<?php
/*
*
* Author: Sherwin R. Terunez
* Contact: sherwinterunez@yahoo.com
*
* Date Created: March 15, 2019 12:26AM
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

define ("DEVICE_NOTSET", 0);
define ("DEVICE_SET", 1);
define ("DEVICE_OPENED", 2);

class APP_SMS extends SMS {

	public function deviceInit($device=false,$baudrate=57600) {

		if(!($this->deviceSet($device)&&$this->deviceOpen('w+')&&$this->setBaudRate($baudrate))) {
			return false;
		}

		return true;
	}

}

function FACEProcess() {
	global $appdb;

	if(!($result = $appdb->query("select * from tbl_rfidqueue where rfidqueue_deleted=0 order by rfidqueue_id asc limit 10"))) {
		json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
		die;
	}

	//print_r(array('$result'=>$result));

	$settings_uhfrfidprocessdelay = getOption('$SETTINGS_UHFRFIDPROCESSDELAY',2);
	$settings_uhfrfidprocessurl = getOption('$SETTINGS_UHFRFIDPROCESSURL','http://127.0.0.1:8080/');
	$settings_uhfrfidserverip = getOption('$SETTINGS_UHFRFIDSERVERIP','127.0.0.1');
	$settings_globaldisplay = getOption('$SETTINGS_GLOBALDISPLAY',false);
	$settings_breakalarm = getOption('$SETTINGS_BREAKALARM',false);
	$settings_maxinoutalarm = getOption('$SETTINGS_MAXINOUTALARM',false);
	$settings_breakalarmip = getOption('$SETTINGS_BREAKALARMIP','127.0.0.1');
	$settings_maxinoutalarmip = getOption('$SETTINGS_MAXINOUTALARMIP','127.0.0.1');
	$settings_kioskname = getOption('$SETTINGS_KIOSKNAME','KIOSK');

	$curl = new MyCurl;

	if(!empty($result['rows'][0]['rfidqueue_id'])) {
		foreach($result['rows'] as $k=>$v) {

			$id = $v['rfidqueue_id'];

			if(!empty($v['rfidqueue_rfid'])) {
			} else {
				if(!($res = $appdb->update("tbl_rfidqueue",array('rfidqueue_deleted'=>1),"rfidqueue_id=".$id))) {
					json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
					die;
				}
				continue;
			}

			//print_r(array('$v'=>$v));

			//$curl->get($settings_uhfrfidprocessurl.'rfidreader/'.$v['rfidqueue_rfid'].'/');

			$url = 'http://'.$settings_uhfrfidserverip.'/tap/tapped/';

			$vars = array();
			$vars['rfid'] = $rfid = $v['rfidqueue_rfid'];
			$vars['facerec'] = 1;
			$vars['unixtime'] = time();
			$vars['imagesize'] = 350;
			$vars['xbypass'] = 1;
			$vars['kiosk'] = $settings_kioskname;

			if(!empty($v['rfidqueue_millitime'])&&floatval($v['rfidqueue_millitime'])>0) {
				$vars['unixtime'] = $v['rfidqueue_millitime'];
			}

			if(!empty($settings_globaldisplay)) {
				$vars['globaldisplay'] = 1;
			}

			if(!empty($settings_maxinoutalarm)) {
				$vars['maxinoutalarm'] = 1;
			}

			if(!empty($settings_breakalarm)) {
				$vars['breakalarm'] = 1;
			}

			$mtime = explode( ' ', microtime() );
			$start = $mtime[1] + $mtime[0];

			$cont = $curl->post($url,$vars);

			$mtime = explode( ' ', microtime() );
			$end = $mtime[1] + $mtime[0];
			$total = $end - $start;

			$vars['time'] = $total;
			$vars['id'] = $id;

			log_notice(array('$cont'=>$cont));

			log_notice(array('$vars'=>$vars));

			//pre(array('$vars'=>$vars));

			$rfidqueue_deleted = 1;

			if(!empty($cont['content'])) {
				$content = @json_decode($cont['content'],true);
				if(!empty($content)&&is_array($content)&&!empty($content['success'])) {
					$rfidqueue_deleted = 2;

					if(!empty($content['maxinoutalarm'])) {
						$url = 'http://'.$settings_maxinoutalarmip.':8080/rfidalarm';
						$cont = $curl->get($url);

						log_notice(array('alarm'=>'maxinoutalarm','url'=>$url));
					}

					if(!empty($content['breakalarm'])) {
						$url = 'http://'.$settings_breakalarmip.':8080/rfidalarm';
						$cont = $curl->get($url);

						log_notice(array('alarm'=>'breakalarm','url'=>$url));
					}
				} else
				if(!empty($content)&&is_array($content)&&!empty($content['notfound'])) {

					if(!($res = $appdb->update("tbl_rfidqueue",array('rfidqueue_deleted'=>321),"rfidqueue_rfid='".$rfid."' and rfidqueue_deleted=0"))) {
						json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
						die;
					}

					$url = 'http://127.0.0.1:8080/rfidnotfound/'.urlencode($content['return_message']).'/';
					$cont = $curl->get($url);

					//return false;
					continue;
				} else
				if(!empty($content)&&is_array($content)&&!empty($content['return_message'])) {

					if(!($res = $appdb->update("tbl_rfidqueue",array('rfidqueue_deleted'=>421),"rfidqueue_rfid='".$rfid."' and rfidqueue_deleted=0"))) {
						json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
						die;
					}

					$url = 'http://127.0.0.1:8080/rfidnotfound/'.urlencode($content['return_message']).'/';
					$cont = $curl->get($url);

					//return false;
					continue;
				}

			}

			if(!($res = $appdb->update("tbl_rfidqueue",array('rfidqueue_deleted'=>$rfidqueue_deleted),"rfidqueue_id=".$id))) {
				json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
				die;
			}

			//sleep($settings_uhfrfidprocessdelay);
		}
	}


	return true;
}

if(getOption('$MAINTENANCE',false)) {
	die("\nretrieve: Server under maintenance.\n");
}

$settings_usefacerecognition = getOption('$SETTINGS_USEFACERECOGNITION',false);

if($settings_usefacerecognition) {
	FACEProcess();
}


//
