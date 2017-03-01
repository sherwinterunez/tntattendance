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
			//$apptemplate->add_css('styles',$apptemplate->templates_urlpath().'css/login.css');
		}

		function add_script() {
			global $apptemplate;

			$apptemplate->add_script($apptemplate->templates_urlpath().'js/moment.min.js');
			$apptemplate->add_script($apptemplate->templates_urlpath().'js/jquery.marquee.min.js');
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

		function tapped($vars) {
			global $appdb, $appaccess;

			if(!empty($vars['post']['rfid'])&&!empty($vars['post']['unixtime'])&&is_numeric($vars['post']['unixtime'])) {

				$post = $vars['post'];

				if(!($result = $appdb->query("select * from tbl_studentprofile where studentprofile_rfid='".$post['rfid']."'"))) {
					json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
					die;				
				}

				if(!empty($result['rows'][0]['studentprofile_id'])) {
					$vars['studentinfo'] = $result['rows'][0];
				}

				if(!empty($vars['studentinfo']['studentprofile_id'])) {

					$month = intval(date('m', $post['unixtime']));
					$day = intval(date('d', $post['unixtime']));
					$year = intval(date('Y', $post['unixtime']));
					$hour = intval(date('H', $post['unixtime']));
					$minute = intval(date('i', $post['unixtime']));
					$second = intval(date('s', $post['unixtime']));

					$from = date2timestamp("$month/$day/$year 00:00:00",'m/d/Y H:i:s');
					$to = date2timestamp("$month/$day/$year 23:59:59",'m/d/Y H:i:s');

					if(!($result = $appdb->query("select * from tbl_studentdtr where studentdtr_studentid=".$vars['studentinfo']['studentprofile_id']." and studentdtr_unixtime >= $from and studentdtr_unixtime <= $to order by studentdtr_id desc limit 1"))) {
						json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
						die;				
					}

					$type = 'IN';

					$bypass = false;

					if(!empty($result['rows'][0]['studentdtr_id'])) {
						$vars['studentdtr'] = $result['rows'][0];
						$vars['$appdb'] = $appdb;

						if($vars['studentdtr']['studentdtr_type']=='IN') {
							$type = 'OUT';
						} else {
							$type = 'IN';
						}

						$settings_rfidinterval = getOption('$SETTINGS_RFIDINTERVAL',0) * 60;

						$interval = $post['unixtime'] - $vars['studentdtr']['studentdtr_unixtime'];

						if($interval>$settings_rfidinterval) {

						} else {
							$bypass = true;
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

						if(!($result = $appdb->insert("tbl_studentdtr",$content,"studentdtr_id"))) {
							json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
							die;				
						}

						$vars['studentdtr_result'] = $result;

						if(!empty($result['returning'][0]['studentdtr_id'])) {

							$content = array();
							$content['studentprofile_db'] = $studentprofile_db = $vars['studentinfo']['studentprofile_db'] + 1;

							if($type=='IN') {
								$content['studentprofile_in'] = $studentprofile_in = $vars['studentinfo']['studentprofile_in'] + 1;
								$studentprofile_out = $vars['studentinfo']['studentprofile_out'];
							} else {
								$content['studentprofile_out'] = $studentprofile_out = $vars['studentinfo']['studentprofile_out'] + 1;
								$studentprofile_in = $vars['studentinfo']['studentprofile_in'];
							}

							$late = false;

							if($late) {
								$content['studentprofile_late'] = $studentprofile_late = $vars['studentinfo']['studentprofile_late'] + 1;
							} else {
								$studentprofile_late = $vars['studentinfo']['studentprofile_late'];
							}

							if(!($result = $appdb->update("tbl_studentprofile",$content,"studentprofile_id=".$vars['studentinfo']['studentprofile_id']))) {
								json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
								die;				
							}

							$retval = array();
							$retval['db'] = intval($studentprofile_db);
							$retval['in'] = intval($studentprofile_in);
							$retval['out'] = intval($studentprofile_out);
							$retval['late'] = intval($studentprofile_late);
							$retval['type'] = $type;
							$retval['image'] = '/studentphoto.php?pid='.$vars['studentinfo']['studentprofile_id'];

							header_json();
							json_encode_return($retval);
							die;

							//$vars['sql'] = $appdb;

						}
					}

				}

			}

			pre(array('$vars'=>$vars));
			die;
		}

		function fullname() {
			return $_SESSION['USER']['content']['user_fname'].' '.$_SESSION['USER']['content']['user_lname'];
		}

		function js($vars) {
			require_once('tap.mod.inc.js');
		}

		function render($vars) {
			global $apptemplate, $appform, $current_page;
			
			$this->check_url();

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