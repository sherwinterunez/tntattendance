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

// https://www.apptopus.ph/fb/webhook.php

error_reporting(E_ALL);

ini_set("max_execution_time", 300);

define('APPLICATION_RUNNING', true);

define('ABS_PATH', dirname(__FILE__) . '/../');

if(defined('ANNOUNCE')) {
	echo "\n<!-- loaded: ".__FILE__." -->\n";
}

date_default_timezone_set('Asia/Manila');

//define('INCLUDE_PATH', ABS_PATH . 'includes/');

require_once(ABS_PATH.'includes/index.php');
//require_once(ABS_PATH.'modules/index.php');

define('REMOTE_FBINTERFACE_URL','https://graph.facebook.com/v2.6/me/messages?access_token=');

if(!empty(($license=checkLicense()))) {
} else {
	print_r(array('ERROR'=>'Invalid or expired license!'));
	sleep(10);
	return false;
}

function facebookWebhook($access_token=false) {
  global $appdb;

  if(!empty($access_token)) {
  } else {
    return false;
  }

  $jsonData = false;

  $input = @json_decode(file_get_contents('php://input') , true);

  if(!empty($input)&&is_array($input)) {
    log_notice(array('webhook.php $input'=>$input));

    log_notice(array('webhook.php $input'=>$input['entry'][0]['messaging']));
  } else {
    log_notice(array('webhook.php $input'=>'invalid input'));
    return false;
  }

  if(!empty($input['entry'][0]['messaging'][0]['sender']['id'])) {

    $psid = $input['entry'][0]['messaging'][0]['sender']['id'];

    $code = array();

    if(!empty($input['entry'][0]['messaging'][0]['message']['text'])) {
      $message = trim($input['entry'][0]['messaging'][0]['message']['text']);
      $code = explode("-", $message);
    }

    $mids = array();

    if(!empty($message)&&!empty($code[0])) {

      if($code[0]=='SHOWINFO') {

        //$prebuf = prebuf($input['entry'][0]['messaging']);

        $rid = $input['entry'][0]['messaging'][0]['recipient']['id'];

        $info = 'SENDER: ['.$psid.'] RECEIVER: ['.$rid.']';

        $jsonData = "{
          'recipient': {
            'id': $psid
          },
          'message': {
            'text': '$info'
          }
        }";

        $bypass = true;

      } else {

        if(preg_match('/[a-zA-Z\-0-9]/i', $code[0])) {
          $profile = getStudentProfileByNumber($code[0]);
        }

        if(!empty($profile)) {
        } else {
          log_notice(array('webhook.php $profile'=>'invalid identification code'));

          $jsonData = "{
            'recipient': {
              'id': $psid
            },
            'message': {
              'text': 'You have entered an Invalid Identification code. Please enter your correct ID to continue registration.'
            }
          }";

          $bypass = true;
        }

        if(!empty($profile['studentprofile_messengerid'])) {
          $mids = explode(' ',$profile['studentprofile_messengerid']);

          if(in_array($psid,$mids)) {

            $jsonData = "{
              'recipient': {
                'id': $psid
              },
              'message': {
                'text': 'Your messenger ID is already registered.'
              }
            }";

            $bypass = true;
          }
        }

      }

    }

    if(!empty($bypass)) {
    } else
    if(!empty($code[1])&&!empty($profile)&&!empty($profile['studentprofile_verification'])) {

      if($code[1]==$profile['studentprofile_verification']) {

        $mids[] = $psid;

        $content = array();
        $content['studentprofile_messengerid'] = implode(' ',$mids);

        if(!($result = $appdb->update("tbl_studentprofile",$content,"studentprofile_id=".$profile['studentprofile_id']))) {
  				log_notice(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
  				die;
  			}

        $jsonData = "{
          'recipient': {
            'id': $psid
          },
          'message': {
            'text': 'Your messenger account has been registered. You will start to receive notification.'
          }
        }";

      } else {

        $jsonData = "{
          'recipient': {
            'id': $psid
          },
          'message': {
            'text': 'You have entered an Invalid verification code. Please enter the correct verification code.'
          }
        }";

      }

    } else
    if(!empty($code[0])) {

      $random_hash = substr(uniqid('', true), -5);

      $content = array();
      $content['studentprofile_verification'] = $random_hash;

      if(!($result = $appdb->update("tbl_studentprofile",$content,"studentprofile_id=".$profile['studentprofile_id']))) {
        log_notice(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
        die;
      }

      $msgin = "APPTOPUS MESSENGER YOUR VERIFICATION CODE IS : ";
  		$msgin .= $random_hash;
  		$mobileno = $profile['studentprofile_guardianmobileno'];

      if(!empty($mobileno)) {

    		$asim = getAllSims(3);

    		pre(array('$asim'=>$asim));

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

  					sendToOutBoxPriority($mobileno,$n['sim_number'],$msgin,0,1,1,0,0,$id); //verify

    				break;
    			}

    		} else {
    			// no sim card detected or no connected gsm modem
  				//pre(array('$mobileno'=>$mobileno,'$m'=>false,'$msgin'=>$msgin,'$license[sc]'=>$license['sc']));
  				sendToOutBoxPriority($mobileno,false,$msgin,0,1,1,0,0,$id); //verify
    		}
      }

      $jsonData ="{
        'recipient': {
          'id': $psid
        },
        'message': {
          'text': 'Thank you for sending your student number. A verification code was sent to your registered cellphone number. Please enter the verification code to validate this student number.'
      }
      }";

    }

  }

  if(!empty($jsonData)) {
    $crl = curl_init(REMOTE_FBINTERFACE_URL.$access_token);
    curl_setopt($crl, CURLOPT_POST, true);
    curl_setopt($crl, CURLOPT_POSTFIELDS, $jsonData);
    curl_setopt($crl, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
  	curl_exec($crl);
  }

  return false;
}

$settings_sendmessenger  = getOption('$SETTINGS_SENDMESSENGER',false);

$settings_messengertoken = getOption('$SETTINGS_MESSENGERTOKEN',false);

log_notice(array('$settings_sendmessenger'=>$settings_sendmessenger,'$settings_messengertoken'=>$settings_messengertoken));

if(isset($_GET['hub_verify_token'])) {
		if ($_GET['hub_verify_token'] === 'devdev1234') {
				echo $_GET['hub_challenge'];
				die;
		} else {
				echo 'Invalid Verify Token';
				die;
		}
}

if(!empty($settings_sendmessenger)&&!empty($settings_messengertoken)) {
  facebookWebhook($settings_messengertoken);
} else {
  log_notice(array('ERROR'=>'Facebook messenger bot is not configured properly'));
}

// eof fb/webhook.php
