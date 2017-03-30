<?php
/*
*
* Author: Sherwin R. Terunez
* Contact: sherwinterunez@yahoo.com
*
* Description:
*
* App User Module
*
* Date: June 9, 2016
*
*/

if(!defined('APPLICATION_RUNNING')) {
	header("HTTP/1.0 404 Not Found");
	die('access denied');
}

if(defined('ANNOUNCE')) {
	echo "\n<!-- loaded: ".__FILE__." -->\n";
}

if(!class_exists('APP_app_setting')) {

	class APP_app_setting extends APP_Base_Ajax {

		var $desc = 'setting';

		var $pathid = 'setting';
		var $parent = false;

		/*function __construct($mypathid,$myparent) {
			$this->pathid = $mypathid;
			$this->parent = $myparent;
			$this->init();
		}*/

		function __construct() {
			$this->init();
		}

		function __destruct() {
		}

		function init() {
			$this->add_rules();
		}

		function add_rules() {
			global $appaccess;

			$appaccess->rules($this->desc,'Setting Module');
			$appaccess->rules($this->desc,'Setting Module New');
			$appaccess->rules($this->desc,'Setting Module Edit');
			$appaccess->rules($this->desc,'Setting Module Delete');

			//$appaccess->rules($this->desc,'User Account');
			/*$appaccess->rules($this->desc,'User Account New Role');
			$appaccess->rules($this->desc,'User Account Edit Role');
			$appaccess->rules($this->desc,'User Account Delete Role');
			$appaccess->rules($this->desc,'User Account New User');
			$appaccess->rules($this->desc,'User Account Edit User');
			$appaccess->rules($this->desc,'User Account Delete User');
			$appaccess->rules($this->desc,'User Account Manage All');
			$appaccess->rules($this->desc,'User Account Change Role');
			$appaccess->rules($this->desc,'User Account Change User Login');*/
		}

		function _form_setting($routerid=false,$formid=false) {
			global $applogin, $toolbars, $forms, $apptemplate, $appdb;

			if(!empty($routerid)&&!empty($formid)) {

				//pre(array($routerid,$formid));

				$post = $this->vars['post'];

				$params = array();

				$readonly = true;

				$default_bulletin = 'DEMO UNIT... OBIS SOFTWARE TECHNOLOGY... OBIS SOFTWARE TECHNOLOGY... DEMO UNIT...';
				$default_timeinnotification = '%STUDENTFULLNAME% has timed-in at %DATETIME%';
				$default_timeoutnotification = '%STUDENTFULLNAME% has timed-out at %DATETIME%';
				$default_timeinmessage = 'Welcome to School! Have a nice day!';
				$default_timeoutmessage = 'Goodbye! See you later!';
				$default_latemessage = 'Welcome to School! Have a nice day! Please be early next time!';

				$settings_electronicbulletin = getOption('$SETTINGS_ELECTRONICBULLETIN',$default_bulletin);
				$settings_loginnotificationschooladmin = getOption('$SETTINGS_LOGINNOTIFICATIONSCHOOLADMIN','');
				$settings_loginnotificationschooladminsendsms = getOption('$SETTINGS_LOGINNOTIFICATIONSCHOOLADMINSENDSMS',false);
				$settings_loginnotificationostrelationshipmanager = getOption('$SETTINGS_LOGINNOTIFICATIONOSTRELATIONSHIPMANAGER','');
				$settings_loginnotificationostrelationshipmanagersendsms = getOption('$SETTINGS_LOGINNOTIFICATIONOSTRELATIONSHIPMANAGERSENDSMS',false);

				$settings_rfidinterval = getOption('$SETTINGS_RFIDINTERVAL',false);

				$settings_synctoserver = getOption('$SETTINGS_SYNCTOSERVER',false);

				$settings_sendpushnotification  = getOption('$SETTINGS_SENDPUSHNOTIFICATION',false);

				$settings_timeinnotification = getOption('$SETTINGS_TIMEINNOTIFICATION',$default_timeinnotification);
				$settings_timeoutnotification = getOption('$SETTINGS_TIMEOUTNOTIFICATION',$default_timeoutnotification);

				$settings_timeinmessage = getOption('$SETTINGS_TIMEINMESSAGE',$default_timeinmessage);
				$settings_timeoutmessage = getOption('$SETTINGS_TIMEOUTMESSAGE',$default_timeoutmessage);
				$settings_latemessage = getOption('$SETTINGS_LATEMESSAGE',$default_latemessage);

				$settings_licensekey = getOption('$SETTINGS_LICENSEKEY',false);

				if(!empty($post['method'])&&($post['method']=='settingedit')) {
					$readonly = false;
				}

				if(!empty($post['method'])&&$post['method']=='settingsave') {
					$retval = array();
					$retval['return_code'] = 'SUCCESS';
					$retval['return_message'] = 'Setting successfully saved!';
					//$retval['post'] = $post;

					//pre(array('$post',$post));

					setSetting('$SETTINGS_ELECTRONICBULLETIN',!empty($post['settings_electronicbulletin'])?$post['settings_electronicbulletin']:$default_bulletin);

					setSetting('$SETTINGS_LOGINNOTIFICATIONSCHOOLADMIN',!empty($post['settings_loginnotificationschooladmin'])?$post['settings_loginnotificationschooladmin']:'');

					setSetting('$SETTINGS_LOGINNOTIFICATIONSCHOOLADMINSENDSMS',!empty($post['settings_loginnotificationschooladminsendsms'])?true:false);

					setSetting('$SETTINGS_LOGINNOTIFICATIONOSTRELATIONSHIPMANAGER',!empty($post['settings_loginnotificationostrelationshipmanager'])?$post['settings_loginnotificationostrelationshipmanager']:'');

					setSetting('$SETTINGS_LOGINNOTIFICATIONOSTRELATIONSHIPMANAGERSENDSMS',!empty($post['settings_loginnotificationostrelationshipmanagersendsms'])?true:false);

					setSetting('$SETTINGS_RFIDINTERVAL',!empty($post['settings_rfidinterval'])?intval($post['settings_rfidinterval']):0);

					setSetting('$SETTINGS_SYNCTOSERVER',!empty($post['settings_synctoserver'])?true:false);

					setSetting('$SETTINGS_SENDPUSHNOTIFICATION',!empty($post['settings_sendpushnotification'])?true:false);

					setSetting('$SETTINGS_TIMEINNOTIFICATION',!empty($post['settings_timeinnotification'])?$post['settings_timeinnotification']:$default_timeinnotification);

					setSetting('$SETTINGS_TIMEOUTNOTIFICATION',!empty($post['settings_timeoutnotification'])?$post['settings_timeoutnotification']:$default_timeoutnotification);

					setSetting('$SETTINGS_TIMEINMESSAGE',!empty($post['settings_timeinmessage'])?$post['settings_timeinmessage']:$default_timeinmessage);

					setSetting('$SETTINGS_TIMEOUTMESSAGE',!empty($post['settings_timeoutmessage'])?$post['settings_timeoutmessage']:$default_timeoutmessage);

					setSetting('$SETTINGS_LATEMESSAGE',!empty($post['settings_latemessage'])?$post['settings_latemessage']:$default_latemessage);

					setSetting('$SETTINGS_LICENSEKEY',!empty($post['settings_licensekey'])?$post['settings_licensekey']:'');

					json_encode_return($retval);
					die;
				}

				$params['hello'] = 'Hello, Sherwin!';

				$newcolumnoffset = 50;

				$position = 'right';

				$params['tbElectronicBulletin'] = array();
				$params['tbLoginNotification'] = array();
				$params['tbNotifications'] = array();
				$params['tbGeneral'] = array();
				$params['tbServer'] = array();
				$params['tbLicense'] = array();

				$params['tbElectronicBulletin'][] = array(
					'type' => 'input',
					'label' => 'BULLETIN',
					'inputWidth' => 500,
					'rows' => 5,
					//'labelWidth' => 250,
					'name' => 'settings_electronicbulletin',
					'readonly' => $readonly,
					//'required' => !$readonly,
					'value' => !empty($settings_electronicbulletin) ? $settings_electronicbulletin : '',
				);

				$params['tbLoginNotification'][] = array(
					'type' => 'input',
					'label' => 'TYPE',
					//'inputWidth' => 500,
					//'rows' => 5,
					'labelWidth' => 200,
					'name' => 'settings_loginnotificationtype',
					'readonly' => true,
					//'required' => !$readonly,
					'value' => 'SEND SMS USING MODEM',
				);

				$block = array();

				$block[] = array(
					'type' => 'input',
					'label' => 'SCHOOL ADMINISTRATOR',
					//'inputWidth' => 500,
					//'rows' => 5,
					'labelWidth' => 200,
					'name' => 'settings_loginnotificationschooladmin',
					'readonly' => $readonly,
					//'required' => !$readonly,
					'value' => !empty($settings_loginnotificationschooladmin) ? $settings_loginnotificationschooladmin : '',
				);

				$block[] = array(
					'type' => 'newcolumn',
					'offset' => 5,
				);

				$block[] = array(
					'type' => 'checkbox',
					'label' => 'SEND SMS',
					'labelWidth' => 360,
					'name' => 'settings_loginnotificationschooladminsendsms',
					'readonly' => $readonly,
					'checked' => !empty($settings_loginnotificationschooladminsendsms) ? true : false,
					'position' => 'label-right',
				);

				$params['tbLoginNotification'][] = array(
					'type' => 'block',
					'width' => 1000,
					'blockOffset' => 0,
					'offsetTop' => 5,
					'list' => $block,
				);

				$block = array();

				$block[] = array(
					'type' => 'input',
					'label' => 'OST RELATIONSHIP MANAGER',
					//'inputWidth' => 500,
					//'rows' => 5,
					'labelWidth' => 200,
					'name' => 'settings_loginnotificationostrelationshipmanager',
					'readonly' => $readonly,
					//'required' => !$readonly,
					'value' => !empty($settings_loginnotificationostrelationshipmanager) ? $settings_loginnotificationostrelationshipmanager : '',
				);

				$block[] = array(
					'type' => 'newcolumn',
					'offset' => 5,
				);

				$block[] = array(
					'type' => 'checkbox',
					'label' => 'SEND SMS',
					'labelWidth' => 360,
					'name' => 'settings_loginnotificationostrelationshipmanagersendsms',
					'readonly' => $readonly,
					'checked' => !empty($settings_loginnotificationostrelationshipmanagersendsms) ? true : false,
					'position' => 'label-right',
				);

				$params['tbLoginNotification'][] = array(
					'type' => 'block',
					'width' => 1000,
					'blockOffset' => 0,
					'offsetTop' => 5,
					'list' => $block,
				);

				$params['tbNotifications'][] = array(
					'type' => 'input',
					'label' => 'TIME-IN NOTIFICATION',
					'inputWidth' => 500,
					'rows' => 5,
					'labelWidth' => 200,
					'name' => 'settings_timeinnotification',
					'readonly' => $readonly,
					//'numeric' => true,
					//'required' => !$readonly,
					'value' => !empty($settings_timeinnotification) ? $settings_timeinnotification : '',
				);

				$params['tbNotifications'][] = array(
					'type' => 'input',
					'label' => 'TIME-OUT NOTIFICATION',
					'inputWidth' => 500,
					'rows' => 5,
					'labelWidth' => 200,
					'name' => 'settings_timeoutnotification',
					'readonly' => $readonly,
					//'numeric' => true,
					//'required' => !$readonly,
					'value' => !empty($settings_timeoutnotification) ? $settings_timeoutnotification : '',
				);

				$params['tbNotifications'][] = array(
					'type' => 'checkbox',
					'label' => 'SEND PUSH NOTIFICATION',
					'labelWidth' => 360,
					'name' => 'settings_sendpushnotification',
					'readonly' => $readonly,
					'checked' => !empty($settings_sendpushnotification) ? true : false,
					'position' => 'label-right',
				);

				$params['tbGeneral'][] = array(
					'type' => 'input',
					'label' => 'RFID INTERVAL (minutes)',
					//'inputWidth' => 500,
					//'rows' => 5,
					'labelWidth' => 200,
					'name' => 'settings_rfidinterval',
					'readonly' => $readonly,
					'numeric' => true,
					//'required' => !$readonly,
					'value' => !empty($settings_rfidinterval) ? $settings_rfidinterval : '5',
				);

				$params['tbGeneral'][] = array(
					'type' => 'input',
					'label' => 'TIME-IN MESSAGE',
					'inputWidth' => 500,
					'rows' => 5,
					'labelWidth' => 200,
					'name' => 'settings_timeinmessage',
					'readonly' => $readonly,
					//'numeric' => true,
					//'required' => !$readonly,
					'value' => !empty($settings_timeinmessage) ? $settings_timeinmessage : '',
				);

				$params['tbGeneral'][] = array(
					'type' => 'input',
					'label' => 'TIME-OUT MESSAGE',
					'inputWidth' => 500,
					'rows' => 5,
					'labelWidth' => 200,
					'name' => 'settings_timeoutmessage',
					'readonly' => $readonly,
					//'numeric' => true,
					//'required' => !$readonly,
					'value' => !empty($settings_timeoutmessage) ? $settings_timeoutmessage : '',
				);

				$params['tbGeneral'][] = array(
					'type' => 'input',
					'label' => 'LATE MESSAGE',
					'inputWidth' => 500,
					'rows' => 5,
					'labelWidth' => 200,
					'name' => 'settings_latemessage',
					'readonly' => $readonly,
					//'numeric' => true,
					//'required' => !$readonly,
					'value' => !empty($settings_latemessage) ? $settings_latemessage : '',
				);

				$params['tbServer'][] = array(
					'type' => 'checkbox',
					'label' => 'SYNC ALL CONTACTS TO NOTIFICATION SERVER',
					'labelWidth' => 360,
					'name' => 'settings_synctoserver',
					'readonly' => $readonly,
					'checked' => !empty($settings_synctoserver) ? true : false,
					'position' => 'label-right',
				);

				$params['tbLicense'][] = array(
					'type' => 'input',
					'label' => 'LICENSE KEY',
					'inputWidth' => 500,
					'rows' => 5,
					'labelWidth' => 100,
					'name' => 'settings_licensekey',
					'readonly' => $readonly,
					//'numeric' => true,
					//'required' => !$readonly,
					'value' => !empty($settings_licensekey) ? $settings_licensekey : '',
				);

				if(!empty(($license=checkLicense()))) {
					//pre(array('$license'=>$license));
					$settings_licenseinfo = ''; //prebuf($license);

					if(!empty($license['sc'])) {
						$settings_licenseinfo .= 'LICENSED TO '.$license['sc']."\n";
						$settings_licenseinfo .= 'DATE: '.$license['dt']."\n";
						$settings_licenseinfo .= 'EXPIRATION: '.$license['de']."\n";
						$settings_licenseinfo .= 'TOTAL DAYS: '.$license['dd']."\n";
						$settings_licenseinfo .= 'TOTAL STUDENTS: '.$license['ns']."\n";
					}
				} else {
					$settings_licenseinfo = 'UNLICENSED VERSION. UNAUTHORIZED USE IS PROHIBITED.';
				}

				$params['tbLicense'][] = array(
					'type' => 'input',
					'label' => 'LICENSE INFO',
					'inputWidth' => 500,
					'rows' => 5,
					'labelWidth' => 100,
					'name' => 'settings_licenseinfo',
					'readonly' => $readonly,
					//'numeric' => true,
					//'required' => !$readonly,
					'value' => !empty($settings_licenseinfo) ? $settings_licenseinfo : '',
				);

				$templatefile = $this->templatefile($routerid,$formid);

				//pre(array($routerid,$formid,$params,$templatefile));

				if(file_exists($templatefile)) {
					return $this->_form_load_template($templatefile,$params);
				}
			}

			return false;

		} // _form_group

		function router() {
			global $applogin, $toolbars, $forms, $apptemplate, $appdb;

			$retflag=false;

			header_json();

			if(!empty($this->post['routerid'])&&!empty($this->post['action'])) {

				if( $this->post['action']=='toolbar' && !empty($this->post['toolbarid']) ) {

					if(!empty($toolbar = $this->_toolbar($this->post['routerid'], $this->post['toolbarid']))) {
						$jsonval = json_encode($toolbar,JSON_OBJECT_AS_ARRAY);
						if($retflag===false) {
							die($jsonval);
						} else
						if($retflag==1) {
							return $toolbar;
						} else
						if($retflag==2) {
							return $jsonval;
						}
					}
				} else
				if( $this->post['action']=='form' && !empty($this->post['buttonid']) ) {

					if(!empty($form = $this->_form($this->post['routerid'], $this->post['buttonid']))) {

						$jsontoolbar = $this->_toolbar($this->post['routerid'], $this->post['buttonid']);

						$formid = $this->post['buttonid'];

						if(!empty($this->post['tabid'])) {
							$formid = $this->post['tabid'];
						}

						$formval = sha1($this->post['routerid'].$form.$formid);

						$sform = str_replace('%formval%',$formval,$form);

						$sform = '<div class="srt_cell_cont_tabbar">'.$sform.'</div>';

						$retval = array('html'=>$sform,'formval'=>$formval);

						$_SESSION['FORMS'][$formval] = array('since'=>time(),'formid'=>(!empty($this->post['tabid']) ? $this->post['tabid'] : $this->post['buttonid']),'routerid'=>$this->post['routerid']);

						//$prebuf = prebuf($_SESSION);

						//$retval['html'] .= '<br /><br />' . $prebuf;;

						if(!empty($jsontoolbar)) {
							$retval['toolbar'] = $jsontoolbar;
						}

						$jsonval = json_encode($retval,JSON_OBJECT_AS_ARRAY);

						if($retflag===false) {
							die($jsonval);
						} else
						if($retflag==1) {
							return $form;
						} else
						if($retflag==2) {
							return $jsonval;
						}
					}

				} else
				if( $this->post['action']=='form' && !empty($this->post['formid']) ) {

					$formval = sha1($this->post['routerid'].$this->post['formid'].time());

					$this->vars['post']['formval'] = $this->post['formval'] = $formval;

					$form = $this->_form($this->post['routerid'], $this->post['formid']);

					//pre($this->post); die;

					$jsontoolbar = $this->_toolbar($this->post['routerid'], $this->post['formid']);

					$jsonlayout = $this->_layout($this->post['routerid'], $this->post['formid']);

					$jsonxml = $this->_xml($this->post['routerid'], $this->post['formid']);

					if(empty($form)&&empty($jsontoolbar)&&empty($jsonlayout)) return false;

					$formid = $this->post['formid'];

					if(!empty($this->post['tabid'])) {
						$formid = $this->post['tabid'];
					}

					if(!empty($form)) {
						//$formval = sha1($this->post['routerid'].$form.$formid);

						$sform = str_replace('%formval%',$formval,$form);

						$sform = '<div class="srt_cell_cont_tabbar">'.$sform.'</div>';

						$retval = array('html'=>$sform,'formval'=>$formval);

						$_SESSION['FORMS'][$formval] = array('since'=>time(),'formid'=>(!empty($this->post['tabid']) ? $this->post['tabid'] : $this->post['formid']),'routerid'=>$this->post['routerid']);
					} else {
						$retval = array();
					}

					if(!empty($jsontoolbar)) {
						$retval['toolbar'] = $jsontoolbar;
					}

					if(!empty($jsonxml)) {
						$retval['xml'] = $jsonxml;
					}

					if(!empty($jsonlayout)) {

						$formval = sha1($this->post['routerid'].json_encode($jsonlayout).$formid);

						$_SESSION['FORMS'][$formval] = array('since'=>time(),'formid'=>(!empty($this->post['tabid']) ? $this->post['tabid'] : $this->post['formid']),'routerid'=>$this->post['routerid']);

						$retval['formval'] = $formval;
						$retval['layout'] = $jsonlayout;
					}

					$jsonval = json_encode($retval,JSON_OBJECT_AS_ARRAY);

					if($retflag===false) {
						die($jsonval);
					} else
					if($retflag==1) {
						return $form;
					} else
					if($retflag==2) {
						return $jsonval;
					}
				} else
				if( $this->post['action']=='formonly' && !empty($this->post['formid']) ) {

					//pre(array('post'=>$this->post));

					$toolbar = false;

					if(!empty($this->post['wid'])) {
						if(!empty($toolbar = $this->_toolbar($this->post['routerid'], $this->post['module']))) {
						}
					}

					$form = $this->_form($this->post['routerid'], $this->post['formid']);

					$jsonxml = $this->_xml($this->post['routerid'], $this->post['formid']);

					if(!empty($this->post['formval'])) {
						$form = str_replace('%formval%',$this->post['formval'],$form);
					}

					$retval = array('html'=>$form);

					if(!empty($toolbar)) {
						$retval['toolbar'] = $toolbar;
					}

					if(!empty($jsonxml)) {
						$retval['xml'] = $jsonxml;
					}

					//pre(array('$retval'=>$retval));

					$jsonval = json_encode($retval,JSON_OBJECT_AS_ARRAY);

					if($retflag===false) {
						die($jsonval);
					} else
					if($retflag==1) {
						return $form;
					} else
					if($retflag==2) {
						return $jsonval;
					}
				} else
				if( $this->post['action']=='grid' && !empty($this->post['formid']) && !empty($this->post['table']) ) {

					$retval = array();

					//pre(array($this->post));

					if($this->post['table']=='modemcommands') {
						if(!($result = $appdb->query("select * from tbl_modemcommands order by modemcommands_id asc"))) {
							json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
							die;
						}
						//pre(array('$result'=>$result));

						if(!empty($result['rows'][0]['modemcommands_id'])) {
							$rows = array();

							foreach($result['rows'] as $k=>$v) {
								$rows[] = array('id'=>$v['modemcommands_id'],'data'=>array(0,$v['modemcommands_id'],$v['modemcommands_name'],$v['modemcommands_desc']));
							}

							$retval = array('rows'=>$rows);
						}

					}

					$jsonval = json_encode($retval,JSON_OBJECT_AS_ARRAY);

					if($retflag===false) {
						die($jsonval);
					} else
					if($retflag==1) {
						return $form;
					} else
					if($retflag==2) {
						return $jsonval;
					}

				}
			}

			return false;
		} // router($vars=false,$retflag=false)

	}

	$appappsetting = new APP_app_setting;
}

# eof modules/app.user
