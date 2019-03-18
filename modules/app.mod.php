<?php
/*
*
* Author: Sherwin R. Terunez
* Contact: sherwinterunez@yahoo.com
*
* Description:
*
* App Module
*
* Date: November 13, 2015
*
*/

if(!defined('APPLICATION_RUNNING')) {
	header("HTTP/1.0 404 Not Found");
	die('access denied');
}

if(defined('ANNOUNCE')) {
	echo "\n<!-- loaded: ".__FILE__." -->\n";
}

if(!class_exists('APP_App')) {

	class APP_App extends APP_Base {

		var $pathid = 'app';
		var $desc = 'App';
		var $post = false;
		var $vars = false;

		var $cls_ajax = false;

		var $usermod = false;

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
		}

		function add_script() {
			global $apptemplate;

			$apptemplate->add_script('/'.$this->pathid.'/js/');
		}

		function add_rules() {
			global $appaccess;
		}

		function add_route() {
			global $approuter;
		}

		function js($vars) {
			require_once('app.mod.inc.js');
		}

		function dosimcards($vars) {
			global $approuter, $applogin, $toolbars, $forms, $apptemplate, $appdb;

			//pre(array('$vars'=>$vars));

			if(!empty($vars['post']['sims'])) {
				$sims = @unserialize(base64_decode($vars['post']['sims']));

				if(!empty($sims)&&is_array($sims)) {
					//pre(array('$sims'=>$sims));

					$appdb->query('delete from tbl_sim');

					foreach($sims as $k=>$v) {
						if(!empty($v['sim_number'])) {
							unset($v['sim_id']);
							unset($v['sim_timestamp']);
							unset($v['sim_updatestamp']);

							$appdb->insert('tbl_sim',$v,'sim_id');
						}
					}
				}

			}
		}

		function dosetsmsstatus($vars) {
			global $approuter, $applogin, $toolbars, $forms, $apptemplate, $appdb;

			if(!empty($vars['params'])) {

				$params = explode(',',$vars['params']);

				$ids = array();

				if(!empty($params)&&is_array($params)) {
					foreach($params as $k=>$v) {
						if(is_numeric($v)) {
							$ids[] = intval($v);
						}
					}
				}

				//pre(array('$params'=>$params));

				//pre(array('$ids'=>$ids));

				if(!empty($ids)&&is_array($ids)) {
					$sids = implode(',',$ids);

					//pre(array('$sids'=>$sids));

					if(!($result = $appdb->update("tbl_smsoutbox",array('smsoutbox_sent'=>1,'smsoutbox_status'=>4),"smsoutbox_id in ($sids)"))) {
						json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
						die;
					}

					$retval = array();
					$retval['success'] = 'Success';
					$retval['ids'] = $ids;

					json_encode_return($retval);
				}

				/*
				*/

			}

			pre(array('$vars'=>$vars));
		}

		function dogetstatus($vars) {
			global $approuter, $applogin, $toolbars, $forms, $apptemplate, $appdb;

			$smsinbox_count = 0;
			$smsoutbox_count = 0;
			$smssent_count = 0;
			$contact_count = 0;
			$smsoutbox_queued = 0;
			$smsoutbox_waiting = 0;
			$smsoutbox_sending = 0;
			$smsoutbox_sent = 0;
			$smsoutbox_failed = 0;
/////
			if(!($result = $appdb->query("select count(smsinbox_id) as count from tbl_smsinbox where smsinbox_unread=1 and smsinbox_deleted=0 and smsinbox_eload=0"))) {
				json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
				die;
			}

			if(!empty($result['rows'][0]['count'])) {
				$smsinbox_count = intval($result['rows'][0]['count']);
			}
/////
			if(!($result = $appdb->query("select count(smsoutbox_id) as count from tbl_smsoutbox where smsoutbox_eload=0 and smsoutbox_sent=0 and smsoutbox_deleted=0 and smsoutbox_delay=0"))) {
				json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
				die;
			}

			if(!empty($result['rows'][0]['count'])) {
				$smsoutbox_count = intval($result['rows'][0]['count']);
			}
/////
			if(!($result = $appdb->query("select count(smsoutbox_id) from tbl_smsoutbox where smsoutbox_sent!=0 and smsoutbox_deleted=0"))) {
				json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
				die;
			}

			if(!empty($result['rows'][0]['count'])) {
				$smssent_count = intval($result['rows'][0]['count']);
			}
/////
/*
if(strtolower($match[0])=='queued') {
	$where = 'smsoutbox_status=0 and ';
} else
if(strtolower($match[0])=='waiting') {
	$where = 'smsoutbox_status=1 and ';
} else
if(strtolower($match[0])=='sending') {
	$where = 'smsoutbox_status=3 and ';
} else
if(strtolower($match[0])=='sent') {
	$where = 'smsoutbox_status=4 and ';
} else
if(strtolower($match[0])=='failed') {
	$where = 'smsoutbox_status=5 and ';
}
*/
/////
			if(!($result = $appdb->query("select count(smsoutbox_id) as count from tbl_smsoutbox where smsoutbox_status=0 and smsoutbox_deleted=0 and smsoutbox_delay=0"))) {
				json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
				die;
			}

			if(!empty($result['rows'][0]['count'])) {
				$smsoutbox_queued = intval($result['rows'][0]['count']);
			}
/////
			if(!($result = $appdb->query("select count(smsoutbox_id) as count from tbl_smsoutbox where smsoutbox_status=1 and smsoutbox_deleted=0 and smsoutbox_delay=0"))) {
				json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
				die;
			}

			if(!empty($result['rows'][0]['count'])) {
				$smsoutbox_waiting = intval($result['rows'][0]['count']);
			}
/////
			if(!($result = $appdb->query("select count(smsoutbox_id) as count from tbl_smsoutbox where smsoutbox_status=3 and smsoutbox_deleted=0 and smsoutbox_delay=0"))) {
				json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
				die;
			}

			if(!empty($result['rows'][0]['count'])) {
				$smsoutbox_sending = intval($result['rows'][0]['count']);
			}
/////
			if(!($result = $appdb->query("select count(smsoutbox_id) as count from tbl_smsoutbox where smsoutbox_status=4 and smsoutbox_deleted=0 and smsoutbox_delay=0"))) {
				json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
				die;
			}

			if(!empty($result['rows'][0]['count'])) {
				$smsoutbox_sent = intval($result['rows'][0]['count']);
			}
/////
			if(!($result = $appdb->query("select count(smsoutbox_id) as count from tbl_smsoutbox where smsoutbox_status=5 and smsoutbox_deleted=0 and smsoutbox_delay=0"))) {
				json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
				die;
			}

			if(!empty($result['rows'][0]['count'])) {
				$smsoutbox_failed = intval($result['rows'][0]['count']);
			}

/*
$smsoutbox_queued = 0;
$smsoutbox_waiting = 0;
$smsoutbox_sending = 0;
$smsoutbox_sent = 0;
$smsoutbox_failed = 0;
*/
			header_json();
			json_encode_return(array('vars'=>$vars,'smsinbox_count'=>$smsinbox_count,'smsoutbox_count'=>$smsoutbox_count,'smssent_count'=>$smssent_count,'queued'=>$smsoutbox_queued,'waiting'=>$smsoutbox_waiting,'failed'=>$smsoutbox_failed,'sending'=>$smsoutbox_sending,'sent'=>$smsoutbox_sent));
		}

		function dogetinbox($vars) {
			global $approuter;

			pre(array('$vars'=>$vars));
		}

		function dogetoutbox($vars) {
			global $approuter, $applogin, $toolbars, $forms, $apptemplate, $appdb;

			//pre(array('$vars'=>$vars));

			if(!empty($vars['params'])) {
				$params = explode('/',$vars['params']);

				if(!empty($params[0])&&preg_match('/^(all|queued|waiting|sending|sent|failed|unsent|forsending)$/si',$params[0],$match)&&!empty($match[0])) {

					$limit = '';
					$count = false;

					if(!empty($params[1])&&is_numeric($params[1])&&intval(trim($params[1]))>0) {
						$limit = ' limit '.intval(trim($params[1]));
					} else
					if(!empty($params[1])&&strtolower(trim($params[1]))=='count') {
						$count = true;
					}

					$where = '';

					if(strtolower($match[0])=='all') {
					} else
					if(strtolower($match[0])=='queued') {
						$where = 'smsoutbox_status=0 and ';
					} else
					if(strtolower($match[0])=='waiting') {
						$where = 'smsoutbox_status=1 and ';
					} else
					if(strtolower($match[0])=='sending') {
						$where = 'smsoutbox_status=3 and ';
					} else
					if(strtolower($match[0])=='sent') {
						$where = 'smsoutbox_status=4 and ';
					} else
					if(strtolower($match[0])=='failed') {
						$where = 'smsoutbox_status=5 and ';
					} else
					if(strtolower($match[0])=='unsent') {
						$where = 'smsoutbox_status<>4 and ';
					} else
					if(strtolower($match[0])=='forsending') {
						$where = 'smsoutbox_status in (0,1,3) and ';
					}

					//smsoutbox_status

					$sql = "select *,(extract(epoch from now()) - extract(epoch from smsoutbox_failedstamp)) as elapsedtime from tbl_smsoutbox where $where smsoutbox_deleted=0 and smsoutbox_delay=0 order by smsoutbox_id desc $limit";

					if(!empty($count)) {
						$sql = "select count(*) as total from ($sql) as A";
					} else {
						$sql = "select * from ($sql) as A order by elapsedtime desc";
					}

					//pre(array('$params'=>$params,'$match'=>$match,'$sql'=>$sql));

					if(!($result = $appdb->query($sql))) {
						json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
						die;
					}

					if(!empty($result['rows'][0]['smsoutbox_id'])) {
						header_json();
						json_encode_return(array('rows'=>$result['rows'],'$_SERVER'=>$_SERVER,'$params'=>$params,'$match'=>$match,'$sql'=>$sql));
						die;
					} else
					if(!empty($result['rows'][0]['total'])) {
						header_json();
						json_encode_return(array('rows'=>$result['rows'],'$_SERVER'=>$_SERVER,'$params'=>$params,'$match'=>$match,'$sql'=>$sql));
					}

				}
			}
		}

		function render($vars) {
			global $applogin, $apptemplate, $appform, $current_page;

			if(!$applogin->is_loggedin()) {
				redirect301('/'.$applogin->pathid.'/');
			}

			$this->check_url();

			$apptemplate->header($this->desc.' | '.getOption('$APP_NAME',APP_NAME),'appheader');

			//$apptemplate->page('topnavbar');

			//$apptemplate->page('topnav');

			//$apptemplate->page('topmenu');

			//$apptemplate->page('workarea');

			//$apptemplate->page('app');

			$apptemplate->footer();

		} // render

	} // class APP_App

	$appapp = new APP_App;
}

# eof modules/app
