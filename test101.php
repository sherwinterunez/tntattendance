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

require_once(ABS_PATH.'includes/memcached.inc.php');

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

global $appdb;

//$tb = $appdb->isTableExist('tbl_studentprofile');

/*if(!$appdb->isColumnExist('tbl_studentprofile','studentprofile_schoolyear')) {
	$appdb->query("alter table tbl_studentprofile add column studentprofile_schoolyear text DEFAULT ''::text NOT NULL");
}

pre(array('$appdb'=>$appdb));*/

/*$ch = new MyCurl;

$ch->setopt(CURLOPT_ENCODING,"gzip");

$cont = $ch->get('http://tntattendance.dev/app/getoutbox/waiting/10');

$info = $ch->getinfo();

pre(array('$cont'=>$cont,'$info'=>$info));*/

//isServerLicense();


/*$host = '10.1.2.6';
$port = 80;
$waitTimeoutInSeconds = 1;
if($fp = @fsockopen($host,$port,$errCode,$errStr,$waitTimeoutInSeconds)){
   // It worked
	 echo "\nit worked!\n";
} else {
   // It didn't work
	 echo "\nit didn't worked!\n";
}
@fclose($fp);*/

//$host = '10.1.2.5';

/*$host = 'tntattendance.dev';

if(pingDomain($host)>0) {

	$ch = new MyCurl;

	$ch->setopt(CURLOPT_ENCODING,"gzip");

	$cont = $ch->get('http://'.$host.'/app/getoutbox/waiting/10');

	$info = $ch->getinfo();

	//pre(array('$cont'=>$cont,'$info'=>$info));

	pre('success!');

}*/

//$test = 'hello';

//$x = explode('|',$test);

//pre(array('$x'=>$x));

//$r = pingDomain('10.1.2.86');

//pre(array('$r'=>$r));


/*
$url = 'http://172.16.170.20/tap/tapped/';

$vars = array();
$vars['rfid'] = 1;
$vars['unixtime'] = time();
$vars['imagesize'] = 350;

$ch = new MyCurl;

$cont = $ch->post($url,$vars);

pre(array('$cont'=>$cont));


$url = 'http://172.16.170.20/tap/refresh/';

$vars = array();
$vars['unixtime'] = time();
$vars['imagesize'] = 350;

$ch = new MyCurl;

$cont = $ch->post($url,$vars);

pre(array('$cont'=>$cont));
*/

/*$str = '1100EE00E2001620040111909FB7E100EE00E200001604017911909F16E11100EE000016200401791190B716E11100EE0000001620040179909FB716E111EE00E200001620017911909FB716E11100EE00E2001620040111909FB716E100EE00E200162001791190B716E11100EEE20000162001791190B716E11100EEE2001620040179909FB7E11100EEE2001620040179909FB716E100EEE2000020040179909F16E11100EE0000001604017911909F16E11100EEE2000016040179909FB7E11100EE00E2000016200111909FB716E111EE00E200001620017911909FB716E1';

$out = explode('1100EE',$str);

pre(array('$out'=>$out));*/

/*
$thisbuf = "\r\n\r\n\r\n1100EE00E2001620040111909FB\r\n\r\n\r\n\r\n\r\n7E100EE00E200001604017911909F16E11100EE00001620040\r\n1791190B716E11100EE0000001620040179909FB716E111EE00E200001620017911909FB716E11100EE00E20\r\n01620040111909FB716E100EE00E200162001791190B716E11100EEE20000162001791190B716E11100EEE2001620040179909FB7E11100EEE2001620040179909FB716E100EEE2000020040179909F16E11100EE0000001604017911909F16E11100EEE2000016040179909FB7E11100EE00E2000016200111909FB716E111EE00E200001620017911909FB716E1";

while(1) {
	if(strpos($thisbuf,"\r\n\r\n")!==false) {
		$thisbuf = str_replace("\r\n\r\n", "\r\n", $thisbuf);
	} else break;
}

pre(array('$thisbuf'=>$thisbuf,'tocrlf'=>tocrlf($thisbuf)));

while(1) {
	if(isset($thisbuf[1])&&ord($thisbuf[0])==13&&ord($thisbuf[1])==10) {
		//echo $this->tocrlf('$this->buf => '. $this->buf)."\r\n";
		$thisbuf = substr($thisbuf, 2);
		//echo $this->tocrlf('$this->buf => ['. $this->buf.']')."\r\n";

		//if(isset($this->buf[1])&&$this->buf[0]=='>'&&$this->buf[1]==' ') {
		//	echo "\n\n>>>>>>>>>>>>\n\n";
		//	$this->buf = ">\r\n";
		//}

	} else break;
}

$thisbuffer = '';

$rfid = array();

while(1) {
	if(strpos($thisbuf,"\r\n")!==false) {
		for($i=0;$i<strlen($thisbuf);$i++) {
			if(isset($thisbuf[$i+1])&&ord($thisbuf[$i])==13&&ord($thisbuf[$i+1])==10) {

				$str = substr($thisbuf, 0, ($i+2));

				$rfid[] = trim($str);

				pre(array('$str'=>'['.trim($str).']'));

				$thisbuf = substr($thisbuf,($i+2));

				pre(array('$thisbuf'=>'['.$thisbuf.']'));

				$thisbuffer .= $str;
			}
		}
	} else break;
}


pre(array('$thisbuffer'=>$thisbuffer));

pre(array('$thisbuf'=>$thisbuf));

pre(array('$rfid'=>$rfid));

*/

/*
$sms = new SMS;

$thisbuffer = 'EE00E20000193114017615307C0C5EC71100EE00E20000193114017615307C0C5EC71100EE00E20000193114017615307C0C5EC71100EE00E20000193114017615307C0C5EC71100EE00E20000193114017615307C0C5EC71100EE00E20000193114017615307C0C5EC71100EE00E20000193114017615307C0C5EC71100EE00E20000193114017615307C0C5EC71100EE00E20000193114017615307C0C5EC71100EE00E20000193114017615307C0C5EC71100EE00E20000193114017615307C0C5EC71100EE00E20000193114017615307C0C5EC71100EE00E20000193114017615307C0C5EC71100EE00E20000193114017615307C0C5EC71100EE00E20000193114017615307C0C5EC71100EE00E20000193114017615307C0C5EC71100EE00E20000193114017615307C0C5EC71100EE00E20000193114017615307C0C5EC71100EE00E20000193114017615307C0C5EC71100EE00E20000193114017615307C0C5EC71100EE00E20000193114017615307C0C5EC71100EE00E20000193114017615307C0C5EC71100EE00E20000193114017615307C0C5EC71100EE00E20000193114017615307C0C5EC71100EE00E20000193114017615307C0C5EC71100EE00E20000193114017615307C0C5EC71100EE00E20000193114017615307C0C5EC71100EE00E20000193114017615307C0C5EC710';

pre(array('strlen'=>strlen($thisbuffer),'mod'=>strlen($thisbuffer)%2));

$mod = strlen($thisbuffer) % 2;

if(!empty($mod)) {
	$thisbuffer = $thisbuffer . '0';
}

$bin = hex2bin($thisbuffer);

$hex = $sms->str2hex2($bin);

pre(array('$hex'=>$hex,'$thisbuffer'=>$thisbuffer));
*/

//$tohex = '1100EE00E20000193114018924701A981387';

//$tohex = 'EE00E20000193114017615307C0C5EC71100EE00E20000193114017615307C0C5EC71100EE00E20000193114017615307C0C5EC71100EE00E20000193114017615307C0C5EC71100EE00E20000193114017615307C0C5EC71100EE00E20000193114017615307C0C5EC71100EE00E20000193114017615307C0C5EC71100EE00E20000193114017615307C0C5EC71100EE00E20000193114017615307C0C5EC71100EE00E20000193114017615307C0C5EC71100EE00E20000193114017615307C0C5EC71100EE00E20000193114017615307C0C5EC71100EE00E20000193114017615307C0C5EC71100EE00E20000193114017615307C0C5EC71100EE00E20000193114017615307C0C5EC71100EE00E20000193114017615307C0C5EC71100EE00E20000193114017615307C0C5EC71100EE00E20000193114017615307C0C5EC71100EE00E20000193114017615307C0C5EC71100EE00E20000193114017615307C0C5EC71100EE00E20000193114017615307C0C5EC71100EE00E20000193114017615307C0C5EC71100EE00E20000193114017615307C0C5EC71100EE00E20000193114017615307C0C5EC71100EE00E20000193114017615307C0C5EC71100EE00E20000193114017615307C0C5EC71100EE00E20000193114017615307C0C5EC71100EE00E20000193114017615307C0C5EC710';

/*
$tohex = '1100EE00E20000191100EE00E200001931140167133091E251641100EE00E200001931140167133091E251641100EE00E200001931140167133091E251641100EE00E200001931140167133091E251641100EE00E200001931140167133091E251641100EE00E200001931140167133091E251641100EE00E200001931140167133091E251641100EE00E200001931140167133091E251641100EE00E200001931140167133091E251641100EE00E200001931140167133091E251641100EE00E200001931140167133091E251641100EE00E200001931140167133091E251641100EE00E200001931140167133091E251641100EE00E200001931140167133091E251641100EE00E200001931140167133091E251641100EE00E200001931140167133091E251641100EE00E200001931140167133091E251641100EE00E200001931140167133091E251641100EE00E200001931140167133091E251641100EE00E200001931140167133091E251641100EE00E200001931140167133091E251641100EE00E200001931140167133091E251641100EE00E200001931140167133091E251641100EE00E200001931140167133091E251641100EE00E200001931140167133091E251641100EE00E200001931140167133091E251641100EE00E200001931140167133091E25164110';

pre(array('$tohex'=>$tohex,'$tohex[34]'=>$tohex[34],'$tohex[35]'=>$tohex[35]));

$idx = 0;

while(1) {

	if(isset($tohex[$idx+34])&&isset($tohex[$idx+35])) {
		$s = substr($tohex,$idx,8);
		//pre(array('$s'=>$s));
		if(substr($tohex,$idx,8)=='1100EE00') {
			$t = substr($tohex,$idx,36);
			pre(array('$t'=>$t));
			$tohex = str_replace($t,'',$tohex);
			$idx = 0;
		}
		$idx++;
	} else {
		$tail = substr($tohex,$idx);
		pre(array('$tail'=>$tail,'$tohex'=>$tohex));
		break;
	}

}*/

/*
$cont = array();
$cont['content'] = '{"rfid":"1100EE00E20000193114008321103AEF8180","success":"1100EE00E20000193114008321103AEF8180 Success!"}';

pre(array('$cont'=>$cont));

if(!empty($cont['content'])) {
	$content = @json_decode($cont['content'],true);
	if(!empty($content)&&is_array($content)&&!empty($content['success'])) {
		pre(array('sucess'=>'Success!'));
	}
}*/

/*
$ips = '192.168.1.2|192.168.1.3;192.168.1.4:,192.168.1.5';

if(($valid=isValidIps($ips,true))) {
	print_r(array('valid'=>$valid));
} else {
	print_r(array('invalid'=>$ips));
}*/

//$message = 'SRT12345-5493';

/*
$message = '   201720328x   ';

$message = trim($message);

$code = explode('-',$message);

print_r(array('$message'=>$message,'$code'=>$code));

if(!empty($code[0])) {
	$profile = getStudentProfileByNumber($code[0]);

	print_r(array('$profile'=>$profile));
}

$dt['data'] = <<<STUDENT
15 00 EE 00 E2 00 00 16 20 04 02 56 12 80 99 C2 A4 37,211720039,AJOSHUA ARIEL,MAPAYE,AGUILA,2017-2018,,,,CLARIBEL M AGUILA,09479619822,,1,,A,,,,GRADE 7,,,,
18 00 EE 00 E2 00 00 16 20 04 01 53 12 90 96 BB 96 F5,241720074,AJAMES AARON,TIANES,BALAN,2017-2018,,,,ANNIE T BALAN,09054167763,,1,,A,,,,GRADE 7,,,,
19 00 EE 00 E2 00 00 16 20 04 02 04 12 90 97 24 F3 C9,251720018,ALAWRENCE JAY,POBLETE,BAUTISTA,2017-2018,,,,LAARNI D POBLETE,09177546503,,1,,A,,,,GRADE 7,,,,
STUDENT;

*/

/*
$ch = new MyCURL;

$dt = array();
$dt['opt'] = 'DELETE';
$dt['data'] = <<<STUDENT
15 00 EE 00 E2 00 00 16 20 04 02 56 12 80 99 C2 A4 37,211720039,AJOSHUA ARIEL,MAPAYE,AGUILA,2017-2018,,,,CLARIBEL M AGUILA,09479619822,,1,,A,,,,GRADE 7,,,,
STUDENT;

$url = 'http://tntattendance.local/studentapi.php';

$ret = $ch->post($url,$dt);

print_r(array('$ret'=>$ret));
*/

/*
$ids = '211720039 241720074|251720018';

$ch = new MyCURL;

$dt = array();
$dt['id'] = $ids;
$dt['message'] = 'hello!';

$url = 'http://tntattendance.local/sendapi.php';

$ret = $ch->post($url,$dt);

print_r(array('$ret'=>$ret));
*/

/*
$img = __DIR__ . '/templates/default/tap/apptopuslogo.jpg';

//print_r(array('$img'=>$img));

if($hf=fopen($img,'r')) {

	$size = filesize($img);

	$fcontent = fread($hf,$size);

	fclose($hf);

	$img = new APP_SimpleImage;

	$img->loadfromstring($fcontent);

	//pre(array('mimetype'=>$img->mimetype()));

	$ch = new MyCURL;

	$dt = array();
	$dt['id'] = '241720074';
	$dt['photo'] = base64_encode($fcontent);

	$url = 'http://tntattendance.local/photoapi.php';

	$ret = $ch->post($url,$dt);

	print_r(array('$ret'=>$ret));

} else {
	@fclose($hf);
}
*/

/*
$fullname = getStudentFullName(11324);

$mobileno = getGuardianMobileNo(11324);

$profile = getStudentProfile(11324);

$yearlevel = getStudentYearLevelID(11324);

print_r(array('$fullname'=>$fullname,'$mobileno'=>$mobileno,'$profile'=>$profile,'$yearlevel'=>$yearlevel,'$appdb'=>$appdb));
*/

//$ret = getGroupAssignedSim(1762);

//$ret = getGroupAssignedSim(1);

//print_r(array('$ret'=>$ret));

/*
$bsim = 'YToyOntzOjExOiIwOTQ3NDIyMDY1OSI7YToxNzp7czo2OiJzaW1faWQiO3M6MzoiMjY2IjtzOjEwOiJzaW1fZGV2aWNlIjtzOjE3OiIvZGV2L2N1LnVzYnNlcmlhbCI7czo4OiJzaW1fbmFtZSI7czoxNDoiU0lNMDkyMDI3NjUxODgiO3M6ODoic2ltX2Rlc2MiO3M6MDoiIjtzOjEwOiJzaW1fbnVtYmVyIjtzOjExOiIwOTQ3NDIyMDY1OSI7czoxMToic2ltX25ldHdvcmsiO3M6MTc6IlNtYXJ0L1RhbGsgTiBUZXh0IjtzOjEyOiJzaW1fZGlzYWJsZWQiO3M6MToiMCI7czoxMToic2ltX2RlbGV0ZWQiO3M6MToiMCI7czoxMDoic2ltX3N0YXR1cyI7czoxOiIwIjtzOjEwOiJzaW1fb25saW5lIjtzOjE6IjEiO3M6ODoic2ltX21lbnUiO3M6MToiMCI7czo4OiJzaW1fZmxhZyI7czoxOiIwIjtzOjEzOiJzaW1fdGltZXN0YW1wIjtzOjI5OiIyMDE4LTAxLTMxIDEzOjUzOjM5LjQ1MTQ5NSswOCI7czoxNToic2ltX3VwZGF0ZXN0YW1wIjtzOjI5OiIyMDE4LTAxLTMxIDE0OjI5OjU1LjQzNDY1NSswOCI7czoxMToic2ltX2hvdGxpbmUiO3M6MToiMCI7czo2OiJzaW1faXAiO3M6NzoiMC4wLjAuMCI7czo5OiJzaW1fZWxvYWQiO3M6MToiMCI7fXM6MTE6IjA5MjAyNzY1MTg4IjthOjE3OntzOjY6InNpbV9pZCI7czozOiIyNjciO3M6MTA6InNpbV9kZXZpY2UiO3M6MTg6Ii9kZXYvY3UudXNic2VyaWFsMiI7czo4OiJzaW1fbmFtZSI7czoxNDoiU0lNMDkyMDI3NjUxODgiO3M6ODoic2ltX2Rlc2MiO3M6MDoiIjtzOjEwOiJzaW1fbnVtYmVyIjtzOjExOiIwOTIwMjc2NTE4OCI7czoxMToic2ltX25ldHdvcmsiO3M6MTc6IlNtYXJ0L1RhbGsgTiBUZXh0IjtzOjEyOiJzaW1fZGlzYWJsZWQiO3M6MToiMCI7czoxMToic2ltX2RlbGV0ZWQiO3M6MToiMCI7czoxMDoic2ltX3N0YXR1cyI7czoxOiIwIjtzOjEwOiJzaW1fb25saW5lIjtzOjE6IjEiO3M6ODoic2ltX21lbnUiO3M6MToiMCI7czo4OiJzaW1fZmxhZyI7czoxOiIwIjtzOjEzOiJzaW1fdGltZXN0YW1wIjtzOjI5OiIyMDE5LTAxLTA4IDAxOjQ4OjIxLjAyMTQ1OCswOCI7czoxNToic2ltX3VwZGF0ZXN0YW1wIjtzOjI5OiIyMDE5LTAxLTA4IDAxOjQ4OjIxLjAyMTQ1OCswOCI7czoxMToic2ltX2hvdGxpbmUiO3M6MToiMCI7czo2OiJzaW1faXAiO3M6MDoiIjtzOjk6InNpbV9lbG9hZCI7czoxOiIwIjt9fQ==';

$sims = getGroupAssignedSim(0);

//print_r(array('$sims'=>$sims));

$ch = new MyCURL;

$dt = array();
//$dt['sims'] = base64_encode(serialize($sims));
$dt['sims'] = $bsim;

$url = 'http://tntattendance.local/app/simcards/';

$ret = $ch->post($url,$dt);

print_r(array('$ret'=>$ret));
*/

//if(!empty($memcached)) {

	//print_r(array('recordid'=>$memcached->get('recordid')));

	//$new = time();

	//print_r(array('new'=>$new));

	//$memcached->set('recordid', $new);

	/*
	$var = 'DISPLAY_192.168.27.11';

	$data = $memcached->get($var);

	print_r(array($var=>$data));

	$var = 'DISPLAY_192.168.27.13';

	$data = $memcached->get($var);

	print_r(array($var=>$data));

	$var = 'DISPLAY_192.168.27.31';

	$data = $memcached->get($var);

	print_r(array($var=>$data));

	$var = 'DISPLAY_192.168.27.12';

	$data = $memcached->get($var);

	print_r(array($var=>$data));
	*/

	function str2hex2($str) {
		$hex = '';

		for($j=0;$j<strlen($str);$j++) {
				$hex .= str_pad(dechex(ord($str[$j])), 2, '0', STR_PAD_LEFT);
		}
		return $hex;
	}

	$crlf = "\r\n";

	$str = str2hex2($crlf);

	print_r(array('$str'=>$str,'ord($crlf[0])'=>ord($crlf[0]),'ord($crlf[1])'=>ord($crlf[1])));

//}


//
