<?php
/*
*
* Author: Sherwin R. Terunez
* Contact: sherwinterunez@yahoo.com
*
* Date Created: September 23, 2017
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

function RFIDProcess() {
	global $appdb;

	if(!($result = $appdb->query("select * from tbl_rfidqueue where rfidqueue_deleted=0 order by rfidqueue_id asc"))) {
		json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
		die;
	}

	$settings_uhfrfidprocessdelay = getOption('$SETTINGS_UHFRFIDPROCESSDELAY',2);
	$settings_uhfrfidprocessurl = getOption('$SETTINGS_UHFRFIDPROCESSURL','http://127.0.0.1:8080/');
	$settings_uhfrfidserverip = getOption('$SETTINGS_UHFRFIDSERVERIP','127.0.0.1');

	$curl = new MyCurl;

	if(!empty($result['rows'][0]['rfidqueue_rfid'])) {
		foreach($result['rows'] as $k=>$v) {
			$id = $v['rfidqueue_id'];

			//$curl->get($settings_uhfrfidprocessurl.'rfidreader/'.$v['rfidqueue_rfid'].'/');

			$url = 'http://'.$settings_uhfrfidserverip.'/tap/tapped/';

			$vars = array();
			$vars['rfid'] = $v['rfidqueue_rfid'];
			$vars['unixtime'] = time();
			$vars['imagesize'] = 350;

			$mtime = explode( ' ', microtime() );
			$start = $mtime[1] + $mtime[0];

			$cont = $curl->post($url,$vars);

			$mtime = explode( ' ', microtime() );
			$end = $mtime[1] + $mtime[0];
			$total = $end - $start;

			$vars['time'] = $total;

			pre(array('$vars'=>$vars));

			if(!($res = $appdb->update("tbl_rfidqueue",array('rfidqueue_deleted'=>1),"rfidqueue_id=".$id))) {
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

$settings_useuhfrfidreader = getOption('$SETTINGS_USEUHFRFIDREADER',false);

$settings_useinfraredbeam = getOption('$SETTINGS_USEINFRAREDBEAM',false);

if($settings_useuhfrfidreader&&!$settings_useinfraredbeam) {
	RFIDProcess();
}


//
