<?php
/*
*
* Author: Sherwin R. Terunez
* Contact: sherwinterunez@yahoo.com
*
* Date Created: January 5, 2018 1:30AM
*
* Description:
*
* Application entry point.
*
*/

//define('ANNOUNCE', true);

error_reporting(E_ALL);

ini_set("max_execution_time", 600);

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

global $appdb;

function getNodeStatus($ip=false,$data=false) {
	global $appdb;

	if(!empty($ip)&&!empty($data)) {
	} else {
		return false;
	}

	if(filter_var($ip, FILTER_VALIDATE_IP)) {
	} else {
		return false;
	}

	$ch = new MyCurl;

	$ch->setopt(CURLOPT_ENCODING,"gzip");

	if(($cont=$ch->get('http://'.$ip.'/app/getstatus/all/'))&&!empty($cont['content'])) {
		$info = $ch->getinfo();

		$json = json_decode($cont['content'],true);

		if(!empty($json)&&is_array($json)) {
			if(isset($json['smsinbox_count'])&&isset($json['smsoutbox_count'])&&isset($json['smssent_count'])&&isset($json['queued'])&&isset($json['waiting'])&&isset($json['failed'])&&isset($json['sending'])&&isset($json['sent'])) {
				pre(array('$json'=>$json,'$data'=>$data));

				$content = array();
				$content['monitor_clientnodeid'] = $data['clientnode_id'];
				$content['monitor_clientid'] = $data['clientnode_clientid'];
				$content['monitor_codename'] = $data['clientnode_codename'];
				$content['monitor_name'] = $data['clientnode_name'];
				$content['monitor_info'] = $data['clientnode_info'];
				$content['monitor_url'] = $data['clientnode_url'];
				$content['monitor_vpnip'] = $data['clientnode_vpnip'];
				$content['monitor_vpnport'] = $data['clientnode_vpnport'];
				$content['monitor_publicip'] = $data['clientnode_publicip'];
				$content['monitor_localip'] = $data['clientnode_localip'];
				$content['monitor_active'] = 1;
				$content['monitor_queued'] = $json['queued'];
				$content['monitor_waiting'] = $json['waiting'];
				$content['monitor_sending'] = $json['sending'];
				$content['monitor_sent'] = $json['sent'];
				$content['monitor_failed'] = $json['failed'];
				$content['monitor_updatestampunix'] = '#extract(epoch from now())::integer#';

				if(!($result = $appdb->insert("tbl_monitor",$content,"monitor_id"))) {
					if(preg_match('/duplicate\s+key\s+value/si',$appdb->lasterror)) {
						unset($content['monitor_clientnodeid']);
						$content['monitor_updatestamp'] = 'now()';
						if(!($result = $appdb->update("tbl_monitor",$content,"monitor_clientnodeid=".$data['clientnode_id']))) {
							json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
							die;
						}
					} else {
						json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
						die;
					}
				}

			}
			return true;
		}
	}
	return false;
}

$sql = "select * from tbl_clientnode where clientnode_active>0";

if(!($result = $appdb->query($sql))) {
	json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
	die;
}

if(!empty($result['rows'][0]['clientnode_id'])) {
	$rows = $result['rows'];
	pre(array('$result'=>$result));
	foreach($rows as $k=>$v) {
		getNodeStatus($v['clientnode_vpnip'],$v);
	}
}

//getNodeStatus('10.1.2.11');

//pre(array('$cont'=>$cont,'$info'=>$info));



// eof
