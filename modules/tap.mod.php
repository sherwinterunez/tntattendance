<?php
/*
*
* Author: Sherwin R. Terunez
* Contact: sherwinterunez@yahoo.com
*
* Description:
*
* Utilities Module Class
*
* Date: Jan 12, 2017 8:57AM +0800
*
*/

if(!defined('APPLICATION_RUNNING')) {
	header("HTTP/1.0 404 Not Found");
	die('access denied');
}

if(defined('ANNOUNCE')) {
	echo "\n<!-- loaded: ".__FILE__." -->\n";
}

if(!class_exists('APP_Tap')) {

	class APP_Tap extends APP_Base {

		var $pathid = 'tap';
		var $desc = 'Tap';
		var $post = false;
		var $vars = false;

		var $cls_ajax = false;

		function __construct() {
			parent::__construct();
		}

		function __destruct() {
			parent::__destruct();
		}

		function modulespath() {
			return str_replace(basename(__FILE__),'',__FILE__);
		}

		function add_css() {
			global $apptemplate;

			//$apptemplate->add_css('styles','http://fonts.googleapis.com/css?family=Open+Sans:400,700');
			$apptemplate->add_css('styles',$apptemplate->templates_urlpath().'css/fonts.css');
		}

		function add_script() {
			global $apptemplate;

			$apptemplate->add_script($apptemplate->templates_urlpath().'js/moment.min.js');
			$apptemplate->add_script($apptemplate->templates_urlpath().'js/jquery.marquee.min.js');
			//$apptemplate->add_script('//'.$_SERVER['SERVER_ADDR'].':8080/socket.io/socket.io.js');
			$apptemplate->add_script('//127.0.0.1:8080/socket.io/socket.io.js');
			//$apptemplate->add_script($apptemplate->templates_urlpath().'js/jquery.marquee.js?t='.time());
			$apptemplate->add_script('/'.$this->pathid.'/js/');
			//$apptemplate->add_script('/'.$this->pathid.'/js/?t='.time());
			//$apptemplate->add_script($apptemplate->templates_urlpath().'js/login.js');

		}

		function add_rules() {
			global $appaccess;

			//$appaccess->rules($this->pathid,'login','User login');
		}

		function add_route() {
			global $approuter;

			$approuter->addroute(array('^/'.$this->pathid.'/tapped/$' => array('id'=>$this->pathid,'param'=>'action='.$this->pathid, 'callback'=>array($this,'tapped'))));
			$approuter->addroute(array('^/'.$this->pathid.'/refresh/$' => array('id'=>$this->pathid,'param'=>'action='.$this->pathid, 'callback'=>array($this,'refresh'))));
			$approuter->addroute(array('^/'.$this->pathid.'/getbulletin/$' => array('id'=>$this->pathid,'param'=>'action='.$this->pathid, 'callback'=>array($this,'getBulletin'))));
			$approuter->addroute(array('^/'.$this->pathid.'/getdatetime/$' => array('id'=>$this->pathid,'param'=>'action='.$this->pathid, 'callback'=>array($this,'getDateTime'))));
			$approuter->addroute(array('^/'.$this->pathid.'/getprevious/$' => array('id'=>$this->pathid,'param'=>'action='.$this->pathid, 'callback'=>array($this,'getPrevious'))));
			$approuter->addroute(array('^/'.$this->pathid.'/setprevious/$' => array('id'=>$this->pathid,'param'=>'action='.$this->pathid, 'callback'=>array($this,'setPrevious'))));
			$approuter->addroute(array('^/'.$this->pathid.'$' => array('id'=>$this->pathid,'param'=>'action='.$this->pathid, 'callback'=>array($this,'render'))));
			$approuter->addroute(array('^/'.$this->pathid.'/\?(.*)$' => array('id'=>$this->pathid,'param'=>'action='.$this->pathid, 'callback'=>array($this,'render'))));


			///$approuter->addroute(array('^/'.$this->pathid.'/session/$' => array('id'=>$this->pathid,'param'=>'action='.$this->pathid, 'callback'=>array($this,'session'))));
			//$approuter->addroute(array('^/'.$this->pathid.'/verify/$' => array('id'=>$this->pathid,'param'=>'action='.$this->pathid, 'callback'=>array($this,'verify'))));
			//$approuter->addroute(array('^/logout/$' => array('id'=>$this->pathid,'param'=>'action='.$this->pathid, 'callback'=>array($this,'logout'))));
		}

		function is_loggedin() {
			return !empty($_SESSION['USER']['user_id']);
		}

		function isSystemAdministrator() {
			if(!empty($_SESSION['USER']['role_id'])) {
				return $_SESSION['USER']['role_id']==1;
			}
			return false;
		}

		function getAccess() {
			if(!empty($_SESSION['ACCESS'])) {
				return $_SESSION['ACCESS'];
			}
			return array();
		}

		function getUserID() {
			if(!empty($_SESSION['USER']['user_id'])) {
				return $_SESSION['USER']['user_id'];
			}
			return false;
		}

		function getRoleID() {
			if(!empty($_SESSION['USER']['role_id'])) {
				return $_SESSION['USER']['role_id'];
			}
			return false;
		}

		function verify($vars) {
			global $appdb, $appaccess;

			if(!empty($vars['post'])&&!empty($vars['post']['user_hash'])&&!empty($vars['post']['username'])) {
				$this->vars = $vars;
				$this->post = $vars['post'];
			}

			if(!($result = $appdb->query("select * from tbl_users where user_login='".pgFixString($this->post['username'])."'"))) {
				json_error_return(1); // 1 => 'Error in SQL execution.'
			}


			if(!empty($result['rows'][0]['user_id'])) {
			} else {

				//if(!($result = $appdb->update('tbl_users',array('loginfailed'=>'#loginfailed + 1#','loginfailedstamp'=>'now()'),"user_login='".pgFixString($this->post['username'])."'"))) {
				//	json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.','$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
				//}

				json_error_return(2); // 2 => 'Invalid username/password.'
			}

			$userinfo = $result['rows'][0];

			if(!($result = $appdb->query("select * from tbl_roles where role_id='".$userinfo['role_id']."'"))) {
				json_error_return(1); // 1 => 'Error in SQL execution.'
			}

			if(!empty($result['rows'][0]['role_id'])) {
			} else {
				json_error_return(4); // 4 => 'Invalid Role ID.',
			}

			$roleinfo = $result['rows'][0];

			//pre(array('$this->post'=>$this->post,'$result'=>$result,'$_SESSION'=>$_SESSION));

			if($userinfo['flag']==255) {
				if(!($result = $appdb->update('tbl_users',array('loginfailed'=>'#loginfailed + 1#','loginfailedstamp'=>'now()'),"user_login='".pgFixString($this->post['username'])."'"))) {
					json_error_return(1); // 1 => 'Error in SQL execution.'
				}

				json_error_return(3); // 3 => 'Username has been disabled.'
			}

			if($userinfo['user_hash']!=$this->post['user_hash']) {

				if(!empty($userinfo['loginfailed'])&&intval($userinfo['loginfailed'])>7) {
					if(!($result = $appdb->update('tbl_users',array('loginfailed'=>'#loginfailed + 1#','loginfailedstamp'=>'now()','flag'=>'255'),"user_login='".pgFixString($this->post['username'])."'"))) {
						json_error_return(1); // 1 => 'Error in SQL execution.'
					}

					json_error_return(3); // 3 => 'Username has been disabled.'
				}

				if(!($result = $appdb->update('tbl_users',array('loginfailed'=>'#loginfailed + 1#','loginfailedstamp'=>'now()'),"user_login='".pgFixString($this->post['username'])."'"))) {
					json_error_return(1); // 1 => 'Error in SQL execution.'
				}

				json_error_return(2); // 2 => 'Invalid username/password.'
			}

			if(!empty($userinfo['content'])) {
				$userinfo['content'] = json_decode($userinfo['content'],true);
			}

			if(!empty($roleinfo['content'])) {
				$roleinfo['content'] = $_SESSION['ACCESS'] = json_decode($roleinfo['content'],true);
			}

			$_SESSION['USER'] = $userinfo;
			$_SESSION['ROLE'] = $roleinfo;

			if($this->isSystemAdministrator()) {
/////

				$arules = $appaccess->getAllRules();

				//pre(array('$arules'=>$arules));

				$rules = array();

				foreach($arules as $a=>$b) {
					foreach($b as $k=>$v) {
						$rules[] = $k;
					}
				}

				//pre(array('$rules'=>$rules));

				$roleinfo['content'] = $_SESSION['ACCESS'] = $rules;

				$_SESSION['ROLE'] = $roleinfo;
/////
			}

			if(!($result = $appdb->update('tbl_users',array('lastloginstamp'=>'now()','loginfailed'=>0),'user_id='.$userinfo['user_id']))) {
				json_error_return(1); // 1 => 'Error in SQL execution.'
			}

			//pre(array('$this->post'=>$this->post,'$result'=>$result,'$_SESSION'=>$_SESSION));

			json_error_return(0,'User successfully logged in.');

		}

		function getBulletin($vars) {
			global $appdb, $appaccess;

			//pre(array('$vars'=>$vars));

			$settings_electronicbulletindaily = getOption('$SETTINGS_ELECTRONICBULLETINDAILY',base64_encode(serialize(array())));

			if(!empty($settings_electronicbulletindaily)) {
				$settings_electronicbulletindaily = unserialize(base64_decode($settings_electronicbulletindaily));
			}

			$dtfrom = intval(getDbUnixDate());

			$month = intval(date('m', $dtfrom));
			$day = intval(date('d', $dtfrom));
			$year = intval(date('Y', $dtfrom));
			$hour = intval(date('H', $dtfrom));
			$minute = intval(date('i', $dtfrom));
			$second = intval(date('s', $dtfrom));

			$from = date2timestamp("$month/$day/$year 00:00:00",'m/d/Y H:i:s');

			$dtto = $dtfrom + 604800; // current up to 7 days

			$month = intval(date('m', $dtto));
			$day = intval(date('d', $dtto));
			$year = intval(date('Y', $dtto));
			$hour = intval(date('H', $dtto));
			$minute = intval(date('i', $dtto));
			$second = intval(date('s', $dtto));

			$to = date2timestamp("$month/$day/$year 23:59:59",'m/d/Y H:i:s');

			$bulletin = '';

			if(!empty($settings_electronicbulletindaily)&&is_array($settings_electronicbulletindaily)) {
				foreach($settings_electronicbulletindaily as $k=>$v) {
					if(intval($v['unixdate'])>=$from&&intval($v['unixdate'])<=$to) {
						$bulletin .= $v['msg'].' ';
					}
				}
			}

			if(!empty($bulletin)) {
			} else {
				$bulletin = getOption('$SETTINGS_ELECTRONICBULLETIN','DEMO UNIT... OBIS SOFTWARE TECHNOLOGY... OBIS SOFTWARE TECHNOLOGY... DEMO UNIT...');
			}

			$retval = array();
			$retval['vars'] = $vars;

			if(!empty(($license=checkLicense()))) {
			} else {
				$bulletin = 'THIS IS AN UNLICENSED COPY OF TAP N TXT. FOR DEMO ONLY. THIS IS AN UNLICENSED COPY OF TAP N TXT. FOR DEMO ONLY. THIS IS AN UNLICENSED COPY OF TAP N TXT. FOR DEMO ONLY. THIS IS AN UNLICENSED COPY OF TAP N TXT. FOR DEMO ONLY.';
			}

			$retval['bulletin'] = trim($bulletin);

			header_json();
			json_encode_return($retval);
		} // function getBulletin($vars) {

		function getDateTime($vars) {
			global $appdb, $appaccess;

			//pre(array('$vars'=>$vars));

			//$bulletin = getOption('$SETTINGS_ELECTRONICBULLETIN','The quick brown fox jump over the lazy dog besides the river bank.');

			if(!empty(($license=checkLicense()))) {
			} else {
				$license = array();
			}

			$retval = array();
			$retval['currentTime'] = intval(getDbUnixDate());
			$retval['currentTimeString'] = date('l, F d Y g:i A', $retval['currentTime']);
			$retval['localip'] = getMyLocalIP();
			$retval['load'] = sys_getloadavg();
			$retval['sysinfo'] = 'Server IP: '.$retval['localip'].' | Load: '.round($retval['load'][0],2).', '.round($retval['load'][1],2).', '.round($retval['load'][2],2);
			$retval['showadsinterval'] = intval(getOption('$SETTINGS_SHOWADSINTERVAL',30)) * 60 * 1000;
			$retval['showadsintervalenable'] = getOption('$SETTINGS_SHOWADSINTERVALENABLE',0);
			$retval['licenseexpiration'] = !empty($license['de']) ? $license['de'] : 0;

			//$load = sys_getloadavg();

			if(!empty($license['sc'])) {
				$retval['license'] = $license['sc'];
			} else {
				$retval['license'] = 'TAP N TXT DEMO UNIT (UNLICENSED)';
				//$retval['license'] = 'THE QUICK BROWN FOX';
			}

			header_json();
			json_encode_return($retval);
		} // function getDateTime($vars) {

		function getPrevious($vars) {
			global $appdb, $appaccess;

			if(!($result = $appdb->query("select * from tbl_studentdtr order by studentdtr_id desc limit 10"))) {
				json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
				die;
			}

			//pre(array('$result'=>$result));

			$retval = array();

			$previous = array();

			if(!empty($result['rows'][0]['studentdtr_id'])) {

				foreach($result['rows'] as $k=>$student) {

					$tmp = array();

					if(!empty(($profile = getStudentProfile($student['studentdtr_studentid'])))) {

						$fullname = '';

						if(!empty($profile['studentprofile_firstname'])) {
							$fullname .= trim($profile['studentprofile_firstname']).' ';
						}

						if(!empty($profile['studentprofile_middlename'])) {
							$fullname .= trim($profile['studentprofile_middlename']).' ';
						}

						if(!empty($profile['studentprofile_lastname'])) {
							$fullname .= trim($profile['studentprofile_lastname']).' ';
						}

						$tmp['dtrid'] = $student['studentdtr_id'];
						$tmp['studentid'] = $profile['studentprofile_id'];
						$tmp['fullname'] = strtoupper(trim($fullname));
						$tmp['image'] = '/studentphoto.php?size=150&pid='.$profile['studentprofile_id'];
						$tmp['yearlevel'] = !empty($profile['studentprofile_yearlevel']) ? getGroupRefName($profile['studentprofile_yearlevel']) : 'Year Level';
						$tmp['section'] = !empty($profile['studentprofile_section']) ? getGroupRefName($profile['studentprofile_section']) : 'Section';
						$tmp['html'] = '<img src="'.$tmp['image'].'" /><div id="studentprevlabel" class="bold">'.$tmp['fullname'].'</div><div id="studentprevlabel">'.$tmp['yearlevel'].' - '.$tmp['section'].'</div><br class="br" />';

						$previous[] = $tmp;
					}
				}

			}

			if(!empty($previous)) {
				$retval['previous'] = $previous;
			}

			header_json();
			json_encode_return($retval);
		} // function getPrevious($vars) {

		function setPrevious($vars) {
			global $appdb, $appaccess;

			//pre(array('$vars'=>$vars));

			/*
			if(!($result = $appdb->query("select * from tbl_studentdtr order by studentdtr_id desc limit 10"))) {
				json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
				die;
			}

			//pre(array('$result'=>$result));

			$retval = array();

			$previous = array();

			if(!empty($result['rows'][0]['studentdtr_id'])) {

				foreach($result['rows'] as $k=>$student) {

					$tmp = array();

					if(!empty(($profile = getStudentProfile($student['studentdtr_studentid'])))) {

						$fullname = '';

						if(!empty($profile['studentprofile_firstname'])) {
							$fullname .= trim($profile['studentprofile_firstname']).' ';
						}

						if(!empty($profile['studentprofile_middlename'])) {
							$fullname .= trim($profile['studentprofile_middlename']).' ';
						}

						if(!empty($profile['studentprofile_lastname'])) {
							$fullname .= trim($profile['studentprofile_lastname']).' ';
						}

						$tmp['dtrid'] = $student['studentdtr_id'];
						$tmp['studentid'] = $profile['studentprofile_id'];
						$tmp['fullname'] = strtoupper(trim($fullname));
						$tmp['image'] = '/studentphoto.php?size=150&pid='.$profile['studentprofile_id'];
						$tmp['yearlevel'] = !empty($profile['studentprofile_yearlevel']) ? getGroupRefName($profile['studentprofile_yearlevel']) : 'Year Level';
						$tmp['section'] = !empty($profile['studentprofile_section']) ? getGroupRefName($profile['studentprofile_section']) : 'Section';
						$tmp['html'] = '<img src="'.$tmp['image'].'" /><div id="studentprevlabel" class="bold">'.$tmp['fullname'].'</div><div id="studentprevlabel">'.$tmp['yearlevel'].' - '.$tmp['section'].'</div><br class="br" />';

						$previous[] = $tmp;
					}
				}

			}*/

			$total = 10;

			if(!empty($vars['post']['total'])&&intval($vars['post']['total'])>0) {
				$total = intval($vars['post']['total']);
			}

			$previous = array();

			for($i=0;$i<$total;$i++) {
				$tmp = array();
				$tmp['html'] = '<img src="/userphoto.php?size=200" /><div id="studentprevlabel" class="bold">&nbsp;</div><div id="studentprevlabel">&nbsp;</div><br class="br" />';
				$previous[] = $tmp;
			}

			if(!empty($previous)) {
				$retval['previous'] = $previous;
			}

			header_json();
			json_encode_return($retval);
		} // function setPrevious($vars) {

		function tapped($vars) {
			global $appdb, $appaccess, $memcached;

			//pre(array('$vars'=>$vars));

			if(!empty($vars['post']['rfid'])&&!empty($vars['post']['unixtime'])&&is_numeric($vars['post']['unixtime'])&&!empty($vars['post']['imagesize'])&&is_numeric($vars['post']['imagesize'])) {

				$xbypass = false;
				$kioskname = 'KIOSK';

				if(!empty($vars['post']['xbypass'])) {
					$xbypass = true;
				}

				if(!empty($vars['post']['kiosk'])) {
					$kioskname = trim($vars['post']['kiosk']);
				}

				$settings_servershutdownrfid = getOption('$SETTINGS_SERVERSHUTDOWNRFID',false);
				$settings_servershutdownrfidenable = getOption('$SETTINGS_SERVERSHUTDOWNRFIDENABLE',false);
				$settings_globaldisplay = getOption('$SETTINGS_GLOBALDISPLAY',false);

				if($settings_servershutdownrfidenable&&$settings_servershutdownrfid&&$vars['post']['rfid']==$settings_servershutdownrfid) {
					$curl = new MyCurl;
					$curl->get('http://127.0.0.1:8080/poweroff');

					$retval = array();
					$retval['return_code'] = 4544;
					$retval['return_message'] = 'Powering Off...';

					header_json();
					json_encode_return($retval);
					die;

				}

				$vars['post']['unixtime'] = intval(getDbUnixDate());
				$post = $vars['post'];

				if(!empty($post['facerec'])) {
					if(!($result = $appdb->query("select * from tbl_studentprofile where studentprofile_id='".$post['rfid']."'"))) {
						json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
						die;
					}
				} else {
					if(!($result = $appdb->query("select * from tbl_studentprofile where studentprofile_rfid='".$post['rfid']."'"))) {
						json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
						die;
					}
				}

				if(!empty($result['rows'][0]['studentprofile_id'])) {
					$vars['studentinfo'] = $result['rows'][0];
				} else {
					$retval = array();
					$retval['return_code'] = 4594;
					$retval['return_message'] = 'RFID '.$post['rfid'].' not found!';
					$retval['remote_ip'] = trim($_SERVER['REMOTE_ADDR']);
					$retval['notfound'] = true;

					$content = array();
					$content['rfidnotfound_rfid'] = $post['rfid'];
					$content['rfidnotfound_ip'] = trim($_SERVER['REMOTE_ADDR']);

					if(!($result = $appdb->insert("tbl_rfidnotfound",$content,"rfidnotfound_id"))) {
						json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
						die;
					}

					log_notice($retval);

					header_json();
					json_encode_return($retval);
					die;
				}

				$current_schoolyear = getCurrentSchoolYear();

				if(!empty($vars['studentinfo']['studentprofile_schoolyear'])&&$vars['studentinfo']['studentprofile_schoolyear']==$current_schoolyear) {
				} else {
					$retval = array();
					$retval['return_code'] = 4591;
					$retval['return_message'] = 'Invalid school year!';

					header_json();
					json_encode_return($retval);
					die;
				}

				//pre(array('$vars'=>$vars));

				if(!empty($vars['studentinfo']['studentprofile_id'])) {

					$month = intval(date('m', $post['unixtime']));
					$day = intval(date('d', $post['unixtime']));
					$year = intval(date('Y', $post['unixtime']));
					$hour = intval(date('H', $post['unixtime']));
					$minute = intval(date('i', $post['unixtime']));
					$second = intval(date('s', $post['unixtime']));

					$from = date2timestamp("$month/$day/$year 00:00:00",'m/d/Y H:i:s');
					$to = date2timestamp("$month/$day/$year 23:59:59",'m/d/Y H:i:s');

					$settings_tardinessgraceperiodminute = getOption('$SETTINGS_TARDINESSGRACEPERIODMINUTE',30) * 60;

					$startTime = getSectionStartTime($vars['studentinfo']['studentprofile_section']);
					$endTime = getSectionEndTime($vars['studentinfo']['studentprofile_section']);
					$startBreakTime = getSectionBreakStartTime($vars['studentinfo']['studentprofile_section']);
					$endBreakTime = getSectionBreakEndTime($vars['studentinfo']['studentprofile_section']);

					$maxInOut = getSectionMaxInOut($vars['studentinfo']['studentprofile_section']);

					if(!empty($startTime)) {
						$startTimeStamp = date2timestamp("$month/$day/$year $startTime",'m/d/Y H:i:s');
					}

					if(!empty($endTime)) {
						$endTimeStamp = date2timestamp("$month/$day/$year $endTime",'m/d/Y H:i:s');
					}

					if(!empty($startBreakTime)) {
						$startBreakTimeStamp = date2timestamp("$month/$day/$year $startBreakTime",'m/d/Y H:i:s');
					}

					if(!empty($endBreakTime)) {
						$endBreakTimeStamp = date2timestamp("$month/$day/$year $endBreakTime",'m/d/Y H:i:s');
					}

					$limit = 1;

					if(!empty($maxInOut)) {
						$limit = $maxInOut * 2;
					}

					$sql = "select *,(extract(epoch from now()) - extract(epoch from studentdtr_tappedstamp)) as elapsedtime, now() as now from tbl_studentdtr where studentdtr_studentid=".$vars['studentinfo']['studentprofile_id']." and studentdtr_unixtime >= $from and studentdtr_unixtime <= $to order by studentdtr_id desc limit $limit";

					log_notice(array('$sql'=>$sql,'unixtime'=>$post['unixtime'],'$startTimeStamp'=>$startTimeStamp,'$endTimeStamp'=>$endTimeStamp));

					if(!($result = $appdb->query($sql))) {
						json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
						die;
					}

					$late = false;
					$earlyBreak = false;
					$lateBreak = false;

					if(!empty($startTimeStamp)&&$post['unixtime']>($startTimeStamp+$settings_tardinessgraceperiodminute)) {
						$late = true;
					}

					if(!empty($startBreakTimeStamp)&&$post['unixtime']<$startBreakTimeStamp) {
						$earlyBreak = true;
					}

					if(!empty($endBreakTimeStamp)&&$post['unixtime']>$endBreakTimeStamp) {
						$lateBreak = true;
					}

					$type = 'IN';

					$bypass = false;

					if(!empty($result['rows'][0]['studentdtr_id'])) {

						$studentdtr_id = $result['rows'][0]['studentdtr_id'];
						$vars['studentdtr'] = $result['rows'][0];
						$vars['$appdb'] = $appdb;

						//pre(array('$vars'=>$vars));

						if($vars['studentdtr']['studentdtr_type']=='IN') {
							$type = 'OUT';
						} else {
							$type = 'IN';
						}

						if(!empty($maxInOut)) {
							$numIn = 0;
							$numOut = 0;

							foreach($result['rows'] as $k=>$v) {
								if($v['studentdtr_type']=='IN') {
									$numIn++;
								} else
								if($v['studentdtr_type']=='OUT') {
									$numOut++;
								}
							}

							if($type=='OUT'&&$numOut>=$maxInOut) {
								$vars['maxinoutalarm'] = 1;
							} else
							if($type=='IN'&&$numIn>=$maxInOut) {
								$vars['maxinoutalarm'] = 1;
							}
						}

						if(!empty($startBreakTimeStamp)&&!empty($endBreakTimeStamp)) {
							if($type=='OUT'&&!empty($earlyBreak)) {
								$vars['breakalarm'] = 1;
							} else
							if($type=='IN'&&!empty($lateBreak)) {
								$vars['breakalarm'] = 1;
							}
						}

						$settings_rfidinterval = getOption('$SETTINGS_RFIDINTERVAL',0) * 60;

						//$interval = $post['unixtime'] - $vars['studentdtr']['studentdtr_unixtime'];

						//$interval = $post['unixtime'] - $vars['studentdtr']['studentdtr_tappedstamp'];

						$interval = intval($vars['studentdtr']['elapsedtime']);

						log_notice(array('studentdtr'=>$vars['studentdtr'],'$settings_rfidinterval'=>$settings_rfidinterval,'$interval'=>$interval,'$post[unixtime]'=>$post['unixtime'],'$endTimeStamp'=>$endTimeStamp,'$type'=>$type));

						//pre(array('$interval'=>$interval,'$settings_rfidinterval'=>$settings_rfidinterval));

						$doalarm = false;

						if(!empty($post['maxinoutalarm'])&&!empty($vars['maxinoutalarm'])) {

						} else
						if(!empty($post['breakalarm'])&&!empty($vars['breakalarm'])) {

						} else
						if($post['unixtime']>=$endTimeStamp&&$type=='OUT') {

						} else
						if($post['unixtime']>=$endTimeStamp&&$type=='IN') {

							$bypass = true;

						} else
						if($interval>$settings_rfidinterval) {

						} else {

							$bypass = true;

							$appdb->update("tbl_studentdtr",array('studentdtr_tappedstamp'=>'now()'),"studentdtr_id=".$studentdtr_id);

						}

						$vars['interval'] = $interval;

					}

					if(!$bypass) {

						$vars['date'] = date('m/d/Y', $post['unixtime']);

						$content = array();
						$content['studentdtr_studentid'] = $vars['studentinfo']['studentprofile_id'];
						$content['studentdtr_studentrfid'] = $vars['studentinfo']['studentprofile_rfid'];
						$content['studentdtr_unixtime'] = $post['unixtime'];
						$content['studentdtr_active'] = 1;
						$content['studentdtr_type'] = $type;

						if(!empty($kioskname)) {
							$content['studentdtr_kiosk'] = trim($kioskname);
						}

						if(!empty($_SERVER['REMOTE_ADDR'])) {
							$content['studentdtr_ip'] = trim($_SERVER['REMOTE_ADDR']);
						}

						if($type=='IN'&&!empty($late)) {
							$content['studentdtr_late'] = 1;
						}

						if(!($result = $appdb->insert("tbl_studentdtr",$content,"studentdtr_id"))) {
							json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
							die;
						}

						$vars['studentdtr_result'] = $result;

						if(!empty($result['returning'][0]['studentdtr_id'])) {

							$content = array();
							$content['studentprofile_db'] = $vars['studentinfo']['studentprofile_db'] + 1;

							if($type=='IN') {
								$content['studentprofile_in'] = $vars['studentinfo']['studentprofile_in'] + 1;
								$studentprofile_out = $vars['studentinfo']['studentprofile_out'];

								if(!empty($late)) {
									$content['studentprofile_late'] = $vars['studentinfo']['studentprofile_late'] + 1;
								}
							} else {
								$content['studentprofile_out'] = $vars['studentinfo']['studentprofile_out'] + 1;
								$studentprofile_in = $vars['studentinfo']['studentprofile_in'];
							}

							/*$late = false;

							if($late) {
								$content['studentprofile_late'] = $studentprofile_late = $vars['studentinfo']['studentprofile_late'] + 1;
							} else {
								$studentprofile_late = $vars['studentinfo']['studentprofile_late'];
							}*/

							if(!($result = $appdb->update("tbl_studentprofile",$content,"studentprofile_id=".$vars['studentinfo']['studentprofile_id']))) {
								json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
								die;
							}

							//$from = date2timestamp("$month/$day/$year 00:00:00",'m/d/Y H:i:s');
							//$to = date2timestamp("$month/$day/$year 23:59:59",'m/d/Y H:i:s');

							$studentprofile_db = 0;
							$studentprofile_in = 0;
							$studentprofile_out = 0;
							$studentprofile_late = 0;

							//$studentprofile_schoolyear = getCurrentSchoolYear();

							//pre(array('$studentprofile_schoolyear'=>$studentprofile_schoolyear));

							if($type=='IN') {

								$statsvar = '$STATS_TIMEIN_'.$year.'_'.$month.'_'.$day;

								$stats_timein = getOption($statsvar, 0);

								$stats_timein = $stats_timein + 1;

								log_notice(array('$statsvar'=>$statsvar,'$stats_timein'=>$stats_timein,'$xbypass'=>$xbypass));

								setSetting($statsvar, $stats_timein);

								if(!empty($memcached)) {
									$memcached->set('STATSTIMEIN', $stats_timein);
								}

								if(!empty($late)) {

									$statsvar = '$STATS_TIMEINLATE_'.$year.'_'.$month.'_'.$day;

									$stats_timeinlate = getOption($statsvar, 0);

									$stats_timeinlate = $stats_timeinlate + 1;

									log_notice(array('$statsvar'=>$statsvar,'$stats_timeinlate'=>$stats_timeinlate,'$xbypass'=>$xbypass));

									setSetting($statsvar, $stats_timeinlate);

									if(!empty($memcached)) {
										$memcached->set('STATSTIMEINLATE', $stats_timeinlate);
									}

								}

							} else {

								$statsvar = '$STATS_TIMEOUT_'.$year.'_'.$month.'_'.$day;

								$stats_timeout = getOption($statsvar, 0);

								$stats_timeout = $stats_timeout + 1;

								log_notice(array('$statsvar'=>$statsvar,'$stats_timeout'=>$stats_timeout,'$xbypass'=>$xbypass));

								setSetting($statsvar, $stats_timeout);

								if(!empty($memcached)) {
									$memcached->set('STATSTIMEOUT', $stats_timeout);
								}

							}

							if(!empty($xbypass)) {
								$toretval = array();
								$toretval['rfid'] = $vars['studentinfo']['studentprofile_rfid'];
								$toretval['success'] = $vars['studentinfo']['studentprofile_rfid'].' Success!';
								$toretval['type'] = $type;

								if(!empty($vars['maxinoutalarm'])) {
									$toretval['maxinoutalarm'] = 1;
								}

								if(!empty($vars['breakalarm'])) {
									$toretval['breakalarm'] = 1;
								}

								if(!empty($memcached)) {
////////////////////////////////////////

									//pre(array('$vars'=>$vars));

									//log_notice(array('memcached'=>'Saving to memcached...'));

									//log_notice(array('$_SERVER'=>$_SERVER));

									$currentunixtime = intval(getDbUnixDate());

									$month = intval(date('m', $currentunixtime));
									$day = intval(date('d', $currentunixtime));
									$year = intval(date('Y', $currentunixtime));
									$hour = intval(date('H', $currentunixtime));
									$minute = intval(date('i', $currentunixtime));
									$second = intval(date('s', $currentunixtime));

									$from = date2timestamp("$month/$day/$year 00:00:00",'m/d/Y H:i:s');
									$to = date2timestamp("$month/$day/$year 23:59:59",'m/d/Y H:i:s');

									$post = $vars['post'];

									$current_schoolyear = getCurrentSchoolYear();

									$studentprofile_db = 0;
									$studentprofile_in = getOption('$STATS_TIMEIN_'.$year.'_'.$month.'_'.$day, 0);
									$studentprofile_late = getOption('$STATS_TIMEINLATE_'.$year.'_'.$month.'_'.$day, 0);
									$studentprofile_out = getOption('$STATS_TIMEOUT_'.$year.'_'.$month.'_'.$day, 0);

									if(!($result = $appdb->query("select count(studentprofile_id) as db from tbl_studentprofile where studentprofile_schoolyear='$current_schoolyear'"))) {
										json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
										die;
									}

									if(!empty($result['rows'][0]['db'])) {
										$studentprofile_db = $result['rows'][0]['db'];
									}

									$retval = array();
									$retval['db'] = intval($studentprofile_db);
									$retval['in'] = intval($studentprofile_in);
									$retval['out'] = intval($studentprofile_out);
									$retval['late'] = intval($studentprofile_late);

									$prevtotal = getOption('$SETTINGS_VERTICALDISPLAYNUMBEROFPREVIOUS',10);

									if(!empty($vars['post']['total'])&&is_numeric($vars['post']['total'])&&intval($vars['post']['total'])>0) {
										$prevtotal = intval($vars['post']['total']) + 1;
									}

									$where = '';

									if(empty($settings_globaldisplay)&&!empty($_SERVER['REMOTE_ADDR'])) {
										$where = " studentdtr_ip='".trim($_SERVER['REMOTE_ADDR'])."' and ";
									}

									$sql = "select * from tbl_studentdtr where $where studentdtr_unixtime >= $from and studentdtr_unixtime <= $to order by studentdtr_id desc limit $prevtotal";

									log_notice(array('$sql'=>$sql));

									if(!($result = $appdb->query($sql))) {
										json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
										die;
									}

									//pre(array('$result'=>$result));

									if(!empty($result['rows'][0]['studentdtr_id'])) {

										$previous = array();

										$first = true;

										foreach($result['rows'] as $k=>$student) {

											if($first) {

												if(!empty(($profile = getStudentProfile($student['studentdtr_studentid'])))) {

													$fullname = '';

													if(!empty($profile['studentprofile_firstname'])) {
														$fullname .= trim($profile['studentprofile_firstname']).' ';
													}

													if(!empty($profile['studentprofile_middlename'])) {
														$fullname .= trim($profile['studentprofile_middlename']).' ';
													}

													if(!empty($profile['studentprofile_lastname'])) {
														$fullname .= trim($profile['studentprofile_lastname']).' ';
													}

													$retval['recordid'] = $student['studentdtr_id'];
													$retval['type'] = $type = $student['studentdtr_type'];
													$retval['image'] = '/studentphoto.php?size='.$post['imagesize'].'&pid='.$profile['studentprofile_id'];
													//$retval['studentinfo'] = $vars['studentinfo'];
													//$retval['currentTimeStamp'] = $post['unixtime'];
													//$retval['currentTime'] = pgDateUnix($post['unixtime']);
													//$retval['startTimeStamp'] = $startTimeStamp;
													//$retval['startTime'] = pgDateUnix($startTimeStamp);
													//$retval['endTimeStamp'] = $endTimeStamp;
													//$retval['endTime'] = pgDateUnix($endTimeStamp);
													$retval['showadsinterval'] = intval(getOption('$SETTINGS_SHOWADSINTERVAL',30)) * 60 * 1000;
													$retval['showadsintervalenable'] = getOption('$SETTINGS_SHOWADSINTERVALENABLE',0);
													//$retval['studentdtr'] = $vars['studentdtr'];

													if($type=='IN') {
														$retval['remarks'] = getOption('$SETTINGS_TIMEINMESSAGE','Welcome to School! Have a nice day!');

														if(!empty($student['studentdtr_late'])) {
															$retval['remarks'] = getOption('$SETTINGS_LATEMESSAGE','Welcome to School! Have a nice day! Please be early next time!');
														}
													} else {
														$retval['remarks'] = getOption('$SETTINGS_TIMEOUTMESSAGE','Goodbye! See you later!');
													}

													$retval['fullname'] = strtoupper(trim($fullname));

													$retval['yearlevel'] = !empty($profile['studentprofile_yearlevel']) ? getGroupRefName($profile['studentprofile_yearlevel']) : 'Year Level';
													$retval['section'] = !empty($profile['studentprofile_section']) ? getGroupRefName($profile['studentprofile_section']) : 'Section';

												}

												$first = false;
												continue;
											}

											$tmp = array();

											if(!empty(($profile = getStudentProfile($student['studentdtr_studentid'])))) {

												$fullname = '';

												if(!empty($profile['studentprofile_firstname'])) {
													$fullname .= trim($profile['studentprofile_firstname']).' ';
												}

												if(!empty($profile['studentprofile_middlename'])) {
													$fullname .= trim($profile['studentprofile_middlename']).' ';
												}

												if(!empty($profile['studentprofile_lastname'])) {
													$fullname .= trim($profile['studentprofile_lastname']).' ';
												}

												$tmp['dtrid'] = $student['studentdtr_id'];
												$tmp['studentid'] = $profile['studentprofile_id'];
												$tmp['fullname'] = strtoupper(trim($fullname));
												$tmp['image'] = '/studentphoto.php?size=150&pid='.$profile['studentprofile_id'];
												$tmp['yearlevel'] = !empty($profile['studentprofile_yearlevel']) ? getGroupRefName($profile['studentprofile_yearlevel']) : 'Year Level';
												$tmp['section'] = !empty($profile['studentprofile_section']) ? getGroupRefName($profile['studentprofile_section']) : 'Section';
												$tmp['html'] = '<img src="'.$tmp['image'].'" /><div id="studentprevlabel" class="bold">'.$tmp['fullname'].'</div><div id="studentprevlabel">'.$tmp['yearlevel'].' - '.$tmp['section'].'</div><br class="br" />';

												$previous[] = $tmp;
											}
										}

									}

									if(!empty($previous)&&is_array($previous)) {
										$retval['previous'] = $previous;
									} else {
										$retval['previous'] = array();
									}

									if(empty($settings_globaldisplay)&&!empty($_SERVER['REMOTE_ADDR'])) {
										$memcached->set('DISPLAY_'.$_SERVER['REMOTE_ADDR'], json_encode($retval));
									} else {
										$memcached->set('DISPLAY_GLOBAL', json_encode($retval));
									}

									$retval = $toretval;

////////////////////////////////////////
								}

							} else {

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

								$retval = array();
								$retval['db'] = intval($studentprofile_db);
								$retval['in'] = intval($studentprofile_in);
								$retval['out'] = intval($studentprofile_out);
								$retval['late'] = intval($studentprofile_late);
								$retval['type'] = $type;
								$retval['image'] = '/studentphoto.php?size='.$post['imagesize'].'&pid='.$vars['studentinfo']['studentprofile_id'];
								$retval['studentinfo'] = $vars['studentinfo'];
								$retval['currentTimeStamp'] = $post['unixtime'];
								$retval['currentTime'] = pgDateUnix($post['unixtime']);
								$retval['startTimeStamp'] = $startTimeStamp;
								$retval['startTime'] = pgDateUnix($startTimeStamp);
								$retval['endTimeStamp'] = $endTimeStamp;
								$retval['endTime'] = pgDateUnix($endTimeStamp);
								$retval['showadsinterval'] = intval(getOption('$SETTINGS_SHOWADSINTERVAL',30)) * 60 * 1000;
								$retval['showadsintervalenable'] = getOption('$SETTINGS_SHOWADSINTERVALENABLE',0);
								//$retval['studentdtr'] = $vars['studentdtr'];

								if(!empty($vars['alarm'])) {
									$retval['alarm'] = 1;
								}

								if($type=='IN') {
									$retval['remarks'] = getOption('$SETTINGS_TIMEINMESSAGE','Welcome to School! Have a nice day!');

									if(!empty($late)) {
										$retval['remarks'] = getOption('$SETTINGS_LATEMESSAGE','Welcome to School! Have a nice day! Please be early next time!');
									}
								} else {
									$retval['remarks'] = getOption('$SETTINGS_TIMEOUTMESSAGE','Goodbye! See you later!');
								}

								$fullname = '';

								if(!empty($vars['studentinfo']['studentprofile_firstname'])) {
									$fullname .= trim($vars['studentinfo']['studentprofile_firstname']).' ';
								}

								if(!empty($vars['studentinfo']['studentprofile_middlename'])) {
									$fullname .= trim($vars['studentinfo']['studentprofile_middlename']).' ';
								}

								if(!empty($vars['studentinfo']['studentprofile_lastname'])) {
									$fullname .= trim($vars['studentinfo']['studentprofile_lastname']).' ';
								}

								$retval['fullname'] = strtoupper(trim($fullname));

								$retval['yearlevel'] = !empty($vars['studentinfo']['studentprofile_yearlevel']) ? getGroupRefName($vars['studentinfo']['studentprofile_yearlevel']) : 'Year Level';
								$retval['section'] = !empty($vars['studentinfo']['studentprofile_section']) ? getGroupRefName($vars['studentinfo']['studentprofile_section']) : 'Section';

								// get previous time-in/time-out for this day

								// studentdtr_unixtime >= $from and studentdtr_unixtime <= $to

								//if(!($result = $appdb->query("select * from tbl_studentdtr order by studentdtr_id desc limit 10"))) {

								$prevtotal = 10;

								if(!empty($vars['post']['total'])&&is_numeric($vars['post']['total'])&&intval($vars['post']['total'])>0) {
									$prevtotal = intval($vars['post']['total']) + 1;
								}

								$where = '';

								if(empty($settings_globaldisplay)&&!empty($_SERVER['REMOTE_ADDR'])) {
									$where = " studentdtr_ip='".trim($_SERVER['REMOTE_ADDR'])."' and ";
								}

								$sql = "select * from tbl_studentdtr where $where studentdtr_unixtime >= $from and studentdtr_unixtime <= $to order by studentdtr_id desc limit $prevtotal";

								//log_notice(array('$sql'=>$sql));

								if(!($result = $appdb->query($sql))) {
									json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
									die;
								}

								//pre(array('$result'=>$result));

								if(!empty($result['rows'][0]['studentdtr_id'])) {

									$previous = array();

									$first = true;

									foreach($result['rows'] as $k=>$student) {

										if($first) {
											$first = false;
											continue;
										}

										$tmp = array();

										if(!empty(($profile = getStudentProfile($student['studentdtr_studentid'])))) {

											$fullname = '';

											if(!empty($profile['studentprofile_firstname'])) {
												$fullname .= trim($profile['studentprofile_firstname']).' ';
											}

											if(!empty($profile['studentprofile_middlename'])) {
												$fullname .= trim($profile['studentprofile_middlename']).' ';
											}

											if(!empty($profile['studentprofile_lastname'])) {
												$fullname .= trim($profile['studentprofile_lastname']).' ';
											}

											$tmp['dtrid'] = $student['studentdtr_id'];
											$tmp['studentid'] = $profile['studentprofile_id'];
											$tmp['fullname'] = strtoupper(trim($fullname));
											$tmp['image'] = '/studentphoto.php?size=150&pid='.$profile['studentprofile_id'];
											$tmp['yearlevel'] = !empty($profile['studentprofile_yearlevel']) ? getGroupRefName($profile['studentprofile_yearlevel']) : 'Year Level';
											$tmp['section'] = !empty($profile['studentprofile_section']) ? getGroupRefName($profile['studentprofile_section']) : 'Section';
											$tmp['html'] = '<img src="'.$tmp['image'].'" /><div id="studentprevlabel" class="bold">'.$tmp['fullname'].'</div><div id="studentprevlabel">'.$tmp['yearlevel'].' - '.$tmp['section'].'</div><br class="br" />';

											$previous[] = $tmp;
										}
									}

								}

								$retval['previous'] = $previous;

							}
							//pre(array('$retval'=>$retval));

							header_json();
							json_encode_return($retval);
							die;

							//$vars['sql'] = $appdb;

						}
					} else {
						$retval = array();
						$retval['return_code'] = 4595;
						$retval['return_message'] = 'You\'re done tapping!';

						header_json();
						json_encode_return($retval);
						die;
					}

				}

			}

			//pre(array('$vars'=>$vars));
			die;
		} // function tapped($vars) {

		function refresh($vars) {
			global $appdb, $appaccess, $memcached;

			//pre(array('$vars'=>$vars));

			if(!empty($vars['post'])) {
			} else {
				header_json();
				json_encode_return(array());
				die;
			}

			log_notice(array('$vars'=>$vars));

			$currentunixtime = intval(getDbUnixDate());

			$month = intval(date('m', $currentunixtime));
			$day = intval(date('d', $currentunixtime));
			$year = intval(date('Y', $currentunixtime));
			$hour = intval(date('H', $currentunixtime));
			$minute = intval(date('i', $currentunixtime));
			$second = intval(date('s', $currentunixtime));

			$from = date2timestamp("$month/$day/$year 00:00:00",'m/d/Y H:i:s');
			$to = date2timestamp("$month/$day/$year 23:59:59",'m/d/Y H:i:s');

			$post = $vars['post'];

			$current_schoolyear = getCurrentSchoolYear();

			$studentprofile_db = 0;
			$studentprofile_in = 0;
			$studentprofile_out = 0;
			$studentprofile_late = 0;

			$settings_globaldisplay = getOption('$SETTINGS_GLOBALDISPLAY',false);

			//$studentprofile_schoolyear = getCurrentSchoolYear();

			//pre(array('$studentprofile_schoolyear'=>$studentprofile_schoolyear));

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

			$retval = array();
			$retval['db'] = intval($studentprofile_db);
			$retval['in'] = intval($studentprofile_in);
			$retval['out'] = intval($studentprofile_out);
			$retval['late'] = intval($studentprofile_late);

			$prevtotal = 10;

			if(!empty($vars['post']['total'])&&is_numeric($vars['post']['total'])&&intval($vars['post']['total'])>0) {
				$prevtotal = intval($vars['post']['total']) + 1;
			}

			$where = '';

			if(empty($settings_globaldisplay)&&!empty($_SERVER['REMOTE_ADDR'])) {
				$where = " studentdtr_ip='".trim($_SERVER['REMOTE_ADDR'])."' and ";
			}

			$sql = "select * from tbl_studentdtr where $where studentdtr_unixtime >= $from and studentdtr_unixtime <= $to order by studentdtr_id desc limit $prevtotal";

			//log_notice(array('$sql'=>$sql));

			if(!($result = $appdb->query($sql))) {
				json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
				die;
			}

			//pre(array('$result'=>$result));

			if(!empty($result['rows'][0]['studentdtr_id'])) {

				$previous = array();

				$first = true;

				foreach($result['rows'] as $k=>$student) {

					if($first) {

						if(!empty(($profile = getStudentProfile($student['studentdtr_studentid'])))) {

							$fullname = '';

							if(!empty($profile['studentprofile_firstname'])) {
								$fullname .= trim($profile['studentprofile_firstname']).' ';
							}

							if(!empty($profile['studentprofile_middlename'])) {
								$fullname .= trim($profile['studentprofile_middlename']).' ';
							}

							if(!empty($profile['studentprofile_lastname'])) {
								$fullname .= trim($profile['studentprofile_lastname']).' ';
							}

							$retval['recordid'] = $student['studentdtr_id'];
							$retval['type'] = $type = $student['studentdtr_type'];
							$retval['image'] = '/studentphoto.php?size='.$post['imagesize'].'&pid='.$profile['studentprofile_id'];
							//$retval['studentinfo'] = $vars['studentinfo'];
							//$retval['currentTimeStamp'] = $post['unixtime'];
							//$retval['currentTime'] = pgDateUnix($post['unixtime']);
							//$retval['startTimeStamp'] = $startTimeStamp;
							//$retval['startTime'] = pgDateUnix($startTimeStamp);
							//$retval['endTimeStamp'] = $endTimeStamp;
							//$retval['endTime'] = pgDateUnix($endTimeStamp);
							$retval['showadsinterval'] = intval(getOption('$SETTINGS_SHOWADSINTERVAL',30)) * 60 * 1000;
							$retval['showadsintervalenable'] = getOption('$SETTINGS_SHOWADSINTERVALENABLE',0);
							//$retval['studentdtr'] = $vars['studentdtr'];

							if($type=='IN') {
								$retval['remarks'] = getOption('$SETTINGS_TIMEINMESSAGE','Welcome to School! Have a nice day!');

								if(!empty($student['studentdtr_late'])) {
									$retval['remarks'] = getOption('$SETTINGS_LATEMESSAGE','Welcome to School! Have a nice day! Please be early next time!');
								}
							} else {
								$retval['remarks'] = getOption('$SETTINGS_TIMEOUTMESSAGE','Goodbye! See you later!');
							}

							$retval['fullname'] = strtoupper(trim($fullname));

							$retval['yearlevel'] = !empty($profile['studentprofile_yearlevel']) ? getGroupRefName($profile['studentprofile_yearlevel']) : 'Year Level';
							$retval['section'] = !empty($profile['studentprofile_section']) ? getGroupRefName($profile['studentprofile_section']) : 'Section';

						}

						$first = false;
						continue;
					}

					$tmp = array();

					if(!empty(($profile = getStudentProfile($student['studentdtr_studentid'])))) {

						$fullname = '';

						if(!empty($profile['studentprofile_firstname'])) {
							$fullname .= trim($profile['studentprofile_firstname']).' ';
						}

						if(!empty($profile['studentprofile_middlename'])) {
							$fullname .= trim($profile['studentprofile_middlename']).' ';
						}

						if(!empty($profile['studentprofile_lastname'])) {
							$fullname .= trim($profile['studentprofile_lastname']).' ';
						}

						$tmp['dtrid'] = $student['studentdtr_id'];
						$tmp['studentid'] = $profile['studentprofile_id'];
						$tmp['fullname'] = strtoupper(trim($fullname));
						$tmp['image'] = '/studentphoto.php?size=150&pid='.$profile['studentprofile_id'];
						$tmp['yearlevel'] = !empty($profile['studentprofile_yearlevel']) ? getGroupRefName($profile['studentprofile_yearlevel']) : 'Year Level';
						$tmp['section'] = !empty($profile['studentprofile_section']) ? getGroupRefName($profile['studentprofile_section']) : 'Section';
						$tmp['html'] = '<img src="'.$tmp['image'].'" /><div id="studentprevlabel" class="bold">'.$tmp['fullname'].'</div><div id="studentprevlabel">'.$tmp['yearlevel'].' - '.$tmp['section'].'</div><br class="br" />';

						$previous[] = $tmp;
					}
				}

			}

			if(!empty($previous)&&is_array($previous)) {
				$retval['previous'] = $previous;
			} else {
				$retval['previous'] = array();
			}

			//pre(array('$retval'=>$retval));

			if(!empty($memcached)) {
				if(empty($settings_globaldisplay)&&!empty($_SERVER['REMOTE_ADDR'])) {
					$memcached->set('DISPLAY_'.$_SERVER['REMOTE_ADDR'], json_encode($retval));
				} else {
					$memcached->set('DISPLAY_GLOBAL', json_encode($retval));
				}
			}

			header_json();
			json_encode_return($retval);
			die;

		} // function refresh($vars) {

		function fullname() {
			return $_SESSION['USER']['content']['user_fname'].' '.$_SESSION['USER']['content']['user_lname'];
		}

		function js($vars) {

			$settings_verticaldisplay = getOption('$SETTINGS_VERTICALDISPLAY',false);

			if($settings_verticaldisplay) {
				require_once('tap-vertical.mod.inc.js');
			} else {
				require_once('tap.mod.inc.js');
			}
		}

		function render($vars) {
			global $apptemplate, $appform, $current_page;

			//$this->check_url();

			$apptemplate->header($this->desc.' | '.getOption('$APP_NAME',APP_NAME),'tapheader');

			//$apptemplate->page('topnavbar');

			//$apptemplate->page('topnav');

			//$apptemplate->page('topmenu');

			//$apptemplate->page('workarea');

			//$apptemplate->page('login');

			$apptemplate->footer();

		} // render

	} // class APP_Tap

	$apptap = new APP_Tap;
}

# eof modules/tap/index.php
