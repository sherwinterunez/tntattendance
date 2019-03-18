<?php
/*
*
* Author: Sherwin R. Terunez
* Contact: sherwinterunez@yahoo.com
*
* Date Created: September 7, 2018 8:58PM
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

function TEXTAssist($ip='') {
	global $appdb;

	if(!empty($ip)) {
	} else return false;

  $curl = new MyCurl;

  $limit = 10;

  $url = 'http://'.$ip.'/app/getoutbox/forsending/'.$limit;

	pre(array('$url'=>$url));

  $cont = $curl->get($url);

  if(!empty($cont['content'])) {
    $content = @json_decode($cont['content'],true);

    if(!empty($content)&&is_array($content)&&!empty($content['rows'][0]['smsoutbox_id'])) {
      $rows = $content['rows'];

      $asim = getAllSims(3,true);

      //pre(array('$asim'=>$asim));

      if(!empty($asim)&&is_array($asim)) {

        foreach($rows as $k=>$v) {

          shuffle($asim);

          foreach($asim as $m=>$n) {

						// 09191234567

            $content = $v;

						log_notice(array('$content'=>$content));

            $content['smsoutbox_textassistip'] = $ip;
            $content['smsoutbox_textassistid'] = $content['smsoutbox_id'];

						if($content['smsoutbox_simnumber']=='') {
							$content['smsoutbox_simnumber'] = $n['sim_number'];
						} else
						if($content['smsoutbox_simnumber']=='09191234567') {
							$content['smsoutbox_simnumber'] = $n['sim_number'];
						}

            $content['smsoutbox_status'] = 1; // waiting status

            unset($content['smsoutbox_id']);
            unset($content['elapsedtime']);

            //pre(array('$content'=>$content));

						log_notice(array('$content'=>$content));

            if(!($result = $appdb->insert('tbl_smsoutbox',$content,'smsoutbox_id'))) {
              atLog('$appdb->lasterror ('.$appdb->lasterror.')','textassist','textassist','textassist',$ip,logdt());
            }

            break;
          }

          //break;
        }

        print_r(array('success'=>'yes!'));

      }

    }

    //print_r($content);
  }

	return true;
}

function TEXTAssistSync($ip='') {
	global $appdb;

	if(!empty($ip)) {
	} else return false;

	if(!($result = $appdb->query("select smsoutbox_textassistid from tbl_smsoutbox where smsoutbox_textassistid>0 and smsoutbox_textassistsynced=0 order by smsoutbox_id desc limit 10"))) {
		atLog('$appdb->lasterror ('.$appdb->lasterror.')','textassist','textassist','textassist',$ip,logdt());
		die;
	}

	if(!empty($result['rows'][0]['smsoutbox_textassistid'])) {
		$ids = array();

		foreach($result['rows'] as $k=>$v) {
			if(is_numeric($v['smsoutbox_textassistid'])) {
				$ids[] = intval($v['smsoutbox_textassistid']);
			}
		}

		if(!empty($ids)&&is_array($ids)) {
			$sids = implode(',',$ids);
		}

		if(!empty($sids)) {
			$curl = new MyCurl;

			$url = 'http://'.$ip.'/app/setsmsstatus/'.$sids;

			pre(array('$url'=>$url));

			$cont = $curl->get($url);

			pre(array('$cont'=>$cont));

			if(!empty($cont['content'])) {
				$content = @json_decode($cont['content'],true);

				if(!empty($content['success'])&&!empty($content['ids'])&&is_array($content['ids'])) {

					//pre(array('$content'=>$content));

					$ids = array();

					foreach($content['ids'] as $k=>$v) {
						if(is_numeric($v)&&intval($v)>0) {
							$ids[] = intval($v);
						}
					}

					if(!empty($ids)&&is_array($ids)) {
						pre(array('$ids'=>$ids));

						$sids = implode(',',$ids);

						//pre(array('$sids'=>$sids));

						if(!($result = $appdb->update("tbl_smsoutbox",array('smsoutbox_textassistsynced'=>1),"smsoutbox_textassistid in ($sids)"))) {
							json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
							die;
						}

					}

				}
			}
		}
	}

	return true;
}

if(getOption('$MAINTENANCE',false)) {
	die("\nTEXTAssist: Server under maintenance.\n");
}

$settings_textassist = getOption('$SETTINGS_TEXTASSIST',false);

//$_GET['ip'] = 'tntattendance.local';

if(!empty($settings_textassist)&&!empty($_GET['ip'])) {
  TEXTAssist($_GET['ip']);
	TEXTAssistSync($_GET['ip']);
}

///
