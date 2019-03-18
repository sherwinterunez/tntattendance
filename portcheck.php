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

//require_once('gsmsms.class.inc.php');
//require_once('Pdu/Pdu.php');
//require_once('Pdu/PduFactory.php');
//require_once('Utf8/Utf8.php');

define ("DEVICE_NOTSET", 0);
define ("DEVICE_SET", 1);
define ("DEVICE_OPENED", 2);

class APP_SMS extends SMS {

	public function deviceInit($device=false,$baudrate=115200) {

		if(!($this->deviceSet($device)&&$this->deviceOpen('w+')&&$this->setBaudRate($baudrate))) {
			return false;
		}

		return true;
	}

}

function rfidReaderCheck($dev=false,$localIP=false) {
	global $appdb;

	//$dev = '/dev/ttyS3';

	if(!empty($dev)&&!empty($localIP)) {
	} else return false;

	$bypass = getOption('$TTY_BYPASS',false);

	if(!empty($bypass)) {
		$arr = explode('|',$bypass);

		if(!empty($arr)&&is_array($arr)) {
			foreach($arr as $k=>$v) {
				$v = strtoupper(trim($v));
				$x = strtoupper(trim($dev));

				if(preg_match('/'.$v.'/si',$dev)) {
					return false;
				}
			}
		}
	}

	//echo "\nportCheck starting ($dev).\n";

	$sms = new APP_SMS;

	//$portdevice = "/dev/cu.usbserial";

	//echo "\nInitializing port ($dev).\n";

	if(!@$sms->deviceInit($dev)) {
		//echo "\nError initializing device ($dev)!\n";
		return false;
	}

	$sms->deviceClose();

	return true;
}

function portCheck($dev=false,$localIP=false) {
	global $appdb;

	//$dev = '/dev/ttyS3';

	if(!empty($dev)&&!empty($localIP)) {
	} else return false;

	$bypass = getOption('$TTY_BYPASS',false);

	if(!empty($bypass)) {
		$arr = explode('|',$bypass);

		if(!empty($arr)&&is_array($arr)) {
			foreach($arr as $k=>$v) {
				$v = strtoupper(trim($v));
				$x = strtoupper(trim($dev));

				if(preg_match('/'.$v.'/si',$dev)) {
					return false;
				}
			}
		}
	}

	//echo "\nportCheck starting ($dev).\n";

	$sms = new APP_SMS;

	//$portdevice = "/dev/cu.usbserial";

	//echo "\nInitializing port ($dev).\n";

	if(!@$sms->deviceInit($dev)) {
		//echo "\nError initializing device ($dev)!\n";
		return false;
	}

	//echo "\nInitializing done.\n";

	//echo "\nSending AT Command.\n";

	$mobileNo = false;

	/*if($sms->sendMessageOk("AT\r\n",10)) {
		//print_r($sms->history);
		$sms->clearHistory();
	} else {
		//print_r($sms->history);
		return false;
		//die('An error has occured!');
	}*/

	if($sms->at()) {
		$sms->clearHistory();
	} else {
		return false;
	}

	if(!($result=$appdb->query('select * from tbl_port where port_device=\''.$dev.'\''))) {
		return false;
	}

	if(!empty($result['rows'][0]['port_id'])) {
		$content = array();
		$content['port_device'] = $dev;
		$content['port_updatestamp'] = 'now()';

		$appdb->update('tbl_port',$content,'port_id='.$result['rows'][0]['port_id']);

		$portid = $result['rows'][0]['port_id'];
	} else {
		$content = array();
		$content['port_device'] = $dev;
		$content['port_name'] = 'PORT'.str_replace('/','_',$dev);

		$result = $appdb->insert('tbl_port',$content,'port_id');

		if(!empty($result['returning'][0]['port_id'])) {
			$portid = $result['returning'][0]['port_id'];
		}
	}

	// [0] => +CNUM: "","639465937842",129
	// [0] => +CNUM: " ","639287710253",129

	//if($sms->sendMessageReadPort("AT+CNUM\r\n", "\+CNUM\:\s+\".+?\"\,\"(.+?)\"\,.+?\r\nOK\r\n")) {

	//if($sms->sendMessageReadPort("AT+CNUM\r\n", "\+CNUM\:\s+.+?(\d+).+?\r\nOK\r\n")) {

	//if($sms->sendMessageReadPort("AT+CNUM\r\n", "\+CNUM\:\s+(.+?)\r\nOK\r\n")) {

	if($sms->sendMessageReadPort("AT+CNUM\r\n", "\+CNUM\:\s+(.+?)\r\n")) {
		$result = $sms->getResult();
		//print_r(array('$result'=>$result));
		$sms->clearHistory();

		$cnum = explode(',', $result[1]);

		foreach($cnum as $v) {
			$v = str_replace('"', '', $v);
			if(($res=parseMobileNo($v))) {
				//print_r(array('$res'=>$res));
				$mobileNo = '0'.$res[2].$res[3];
				$mobileNetwork = getNetworkName($mobileNo);
			}
		}

		//if(preg_match()) {

		//}

	} else
	if($sms->sendMessageReadPort("AT+CNUM\r\n", "OK\r\n")) {
		$result = $sms->getResult();
		//print_r(array('$result'=>$result));
		$sms->clearHistory();
		$mobileNo = 'UNKNOWN';
	} else {
		//print_r($sms->history);
		return false;
		//die('An error has occured!');
	}

	/*if($sms->sendMessageOk("AT+COPS?\r\n")) {
		print_r($sms->history);
		$sms->clearHistory();
	} else {
		print_r($sms->history);
		return false;
		//die('An error has occured!');
	}

	if($sms->sendMessageOk("AT+COPS=?\r\n")) {
		print_r($sms->history);
		$sms->clearHistory();
	} else {
		print_r($sms->history);
		return false;
		//die('An error has occured!');
	}*/

	$mobileNetwork = getNetworkName($mobileNo);

	//print_r(array('$mobileNo'=>$mobileNo,'$mobileNetwork'=>$mobileNetwork));

	if($mobileNo=='UNKNOWN') {
		$sql = "select * from tbl_sim where sim_number='$mobileNo' and sim_device='$dev'";
	} else {
		$sql = "select * from tbl_sim where sim_number='$mobileNo'";
	}

	//if(!($result=$appdb->query('select * from tbl_sim where sim_number=\''.$mobileNo.'\''))) {
	//	return false;
	//}

	if(!($result=$appdb->query($sql))) {
		return false;
	}

	//trigger_error($sql,E_USER_NOTICE);

	if(!empty($result['rows'][0]['sim_id'])) {
		$content = array();
		$content['sim_device'] = $dev;
		$content['sim_updatestamp'] = 'now()';
		$content['sim_network'] = $mobileNetwork;
		$content['sim_online'] = 1;
		$content['sim_ip'] = $localIP;

		if($mobileNo=='UNKNOWN') {
			$content['sim_disabled'] = 1;
		}

		$appdb->update('tbl_sim',$content,'sim_id='.$result['rows'][0]['sim_id']);
	} else {
		$content = array();
		$content['sim_device'] = $dev;
		$content['sim_name'] = 'SIM'.$mobileNo;
		$content['sim_number'] = $mobileNo;
		$content['sim_network'] = $mobileNetwork;
		$content['sim_online'] = 1;
		$content['sim_ip'] = $localIP;

		if($mobileNo=='UNKNOWN') {
			$content['sim_disabled'] = 1;
		}

		$result = $appdb->insert('tbl_sim',$content,'sim_id');

		setSetting('TIME_MODEMINIT_'.$dev,1);

		//if(!empty($result['returning'][0]['sim_id'])) {
		//}
	}

	if(!empty($portid)) {
		$appdb->update('tbl_port',array('port_simnumber'=>$mobileNo),'port_id='.$portid);
	}


	//echo "\nSending done.\n";

	$sms->deviceClose();

	//$tstop = timer_stop();

	//echo "\nportCheck done. (".$tstop." secs).\n";

	if($mobileNo=='UNKNOWN') {
		return false;
	}

	return $mobileNo;
}

function updateSimcards($textassistips) {

	$gas = getGroupAssignedSim(0);

	if(!empty($gas)&&is_array($gas)) {

		$ch = new MyCURL;

		foreach($textassistips as $k=>$ip) {
			$dt = array();
			$dt['sims'] = base64_encode(serialize($gas));

			$url = 'http://'.$ip.'/app/simcards/';

			$ret = $ch->post($url,$dt);
		}
	}

}

$settings_disableportcheck = getOption('$SETTINGS_DISABLEPORTCHECK',false);

if(!empty($settings_disableportcheck)) {
	$arr = array();
	$arr['disable'] = true;
	echo json_encode($arr);
	die;
}

$ports = array();
/*$ports[] = '/dev/ttyS0';
$ports[] = '/dev/ttyS1';
$ports[] = '/dev/ttyS2';
$ports[] = '/dev/ttyS3';
$ports[] = '/dev/ttyUSB0';
$ports[] = '/dev/ttyUSB1';
$ports[] = '/dev/ttyUSB2';
$ports[] = '/dev/ttyUSB3';
$ports[] = '/dev/ttyUSB4';
$ports[] = '/dev/ttyUSB5';*/

$files = scandir('/dev');

//print_r(array('$files'=>$files));

if(!empty($files)&&is_array($files)) {
	foreach($files as $file) {
		if(preg_match('/ttyS|ttyU|cu\.usb/s',$file)) {
			$ports[] = '/dev/'.$file;
		}
	}
}

//print_r(array('$ports'=>$ports));

$localIP = getMyLocalIP();

if(trim($localIP)=='') {
	$localIP = '127.0.0.1';
}

$timeout = 120;

$timeoutat = time() + $timeout;

$flag = false;

do {

	$localIP = getMyLocalIP();

	if(trim($localIP)=='') {
		$localIP = '127.0.0.1';
	} else
	if(trim($localIP)=='0.0.0.0'||trim($localIP)=='127.0.0.1') {
	} else {
		break;
	}

	sleep(1);

} while ($timeoutat > time());

$appdb->query('delete from tbl_port');

$okport = array();
$mobileNos = array();
$devices = array();
$rfidreader = array();

$appdb->update('tbl_sim',array('sim_online'=>0,'sim_device'=>'','sim_ip'=>''));

$settings_useuhfrfidreader = getOption('$SETTINGS_USEUHFRFIDREADER',false);

foreach($ports as $dev) {
	if($mno=portCheck($dev,$localIP)) {

		setSetting('TIME_MODEMINIT_'.$mno,1);

		$devices[] = array('port'=>$dev,'sim'=>$mno,'ip'=>$localIP);
		$mobileNos[] = "'".$mno."'";
		$okport[] = $dev;
	} else
	if($settings_useuhfrfidreader&&rfidReaderCheck($dev,$localIP)) {
		$rfidreader[] = array('port'=>$dev,'ip'=>$localIP);
	}
}

//$appdb->update('tbl_sim',array('sim_online'=>0),'sim_number not in ('.implode(',', $mobileNos).')');

$settings_textassist = getOption('$SETTINGS_TEXTASSIST',false);

$settings_textassistip = getOption('$SETTINGS_TEXTASSISTIP','');

//print_r(array('$settings_textassist'=>$settings_textassist,'$settings_textassistip'=>$settings_textassistip));

if(!empty($settings_textassist)&&!empty($settings_textassistip)) {
	$textassistip = isValidIps($settings_textassistip,true);
	//print_r(array('$textassistip'=>$textassistip));
	if(!empty($textassistip)) {
		$textassistips = explode(',',$textassistip);
		//print_r(array('$textassistips'=>$textassistips));
	}
}

$settings_relay = getOption('$SETTINGS_RELAY',false);

$settings_relayip = getOption('$SETTINGS_RELAYIP','');

if(!empty($settings_relay)&&!empty($settings_relayip)) {
	$relayip = isValidIps($settings_relayip,true);
	//print_r(array('$textassistip'=>$textassistip));
	if(!empty($relayip)) {
		//$relayipx = explode(',',$relayip);
		//$relayips = array();
		//$relayips[] = $relayipx[0];
		//print_r(array('$textassistips'=>$textassistips));
		$relayips = array($relayip);
	}
}

$settings_sendmessenger  = getOption('$SETTINGS_SENDMESSENGER',false);

$settings_messengertoken = getOption('$SETTINGS_MESSENGERTOKEN',false);

$tstop = timer_stop();

$arr = array('ports'=>$okport,'devices'=>$devices,'rfidreader'=>$rfidreader,'time'=>$tstop);

if(!empty($textassistips)&&is_array($textassistips)) {
	@updateSimcards($textassistips);
	$arr['textassistips'] = $textassistips;
}

if(!empty($relayips)&&is_array($relayips)) {
	$arr['relayips'] = $relayips;
}

if(!empty($settings_sendmessenger)&&!empty($settings_messengertoken)) {
	$arr['fb'] = 1;
} else {
	$arr['fb'] = 0;
}

echo json_encode($arr);

//print_r(array('$okport '=>$okport ));

//echo "\nportCheck done. (".$tstop." secs).\n";
