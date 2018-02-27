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

if(!class_exists('APP_app_client')) {

	class APP_app_client extends APP_Base_Ajax {

		var $desc = 'client';

		var $pathid = 'client';
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

			$appaccess->rules($this->desc,'Client Module');
			$appaccess->rules($this->desc,'Client Module New');
			$appaccess->rules($this->desc,'Client Module Edit');
			$appaccess->rules($this->desc,'Client Module Delete');

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

		function _form_client($routerid=false,$formid=false) {
			global $applogin, $toolbars, $forms, $apptemplate, $appdb;

			if(!empty($routerid)&&!empty($formid)) {

				//pre(array($routerid,$formid));

				$post = $this->vars['post'];

				$params = array();

				$readonly = true;

				if(!empty($post['method'])&&($post['method']=='clientedit')) {
					$readonly = false;
				}

				if(!empty($post['method'])&&$post['method']=='clientsave') {
					$retval = array();
					$retval['return_code'] = 'SUCCESS';
					$retval['return_message'] = 'Client successfully saved!';
					$retval['post'] = $post;

					//pre(array('$post',$post));

					json_encode_return($retval);
					die;
				}

				$params['hello'] = 'Hello, Sherwin!';

				$newcolumnoffset = 50;

				$position = 'right';

				$params['tbClientRecords'] = array();

				/*$params['tbClientRecords'][] = array(
					'type' => 'input',
					'label' => 'TARDINESS GRACE PERIOD (MINUTE)',
					'labelWidth' => 250,
					'name' => 'setting_tardinessgraceperiod',
					'readonly' => $readonly,
					//'required' => !$readonly,
					'value' => !empty($params['settinginfo']['setting_tardinessgraceperiod']) ? $params['settinginfo']['group_tardinessgraceperiod'] : '',
				);*/

				$params['tbClientRecords'][] = array(
					'type' => 'container',
					'name' => 'client_grid',
					'inputWidth' => 400,
					'inputHeight' => 347,
					'className' => 'client_grid_'.$post['formval'],
				);

				$templatefile = $this->templatefile($routerid,$formid);

				//pre(array($routerid,$formid,$params,$templatefile));

				if(file_exists($templatefile)) {
					return $this->_form_load_template($templatefile,$params);
				}
			}

			return false;

		} // _form_client

		function _form_clientdetailclients($routerid=false,$formid=false) {
			global $applogin, $toolbars, $forms, $apptemplate, $appdb, $appsession;

			if(!empty($routerid)&&!empty($formid)) {

				//pre(array($routerid,$formid));

				$post = $this->vars['post'];

				$params = array();

				$readonly = true;

				if(!empty($post['method'])&&($post['method']=='clientedit'||$post['method']=='clientnew')) {
					$readonly = false;
				}

				if($post['method']=='clientnew') {
					$license = checkLicense();

					if(!empty($license)&&!empty($license['ns'])&&intval($license['ns'])>0&&intval($license['ns'])>getTotalStudentCurrentSchoolYear()) {
					} else {
						$retval = array();
						$retval['error_code'] = '345346';
						$retval['error_message'] = 'Invalid license or maximum number of allowed student for this school year has been reached!';

						json_encode_return($retval);
					}
				}

				if(!empty($post['method'])&&($post['method']=='onrowselect'||$post['method']=='clientedit'||$post['method']=='clientrefresh'||$post['method']=='clientcancel')) {
					if(!empty($post['rowid'])&&is_numeric($post['rowid'])&&$post['rowid']>0) {
						if(!($result = $appdb->query("select * from tbl_client where client_id=".$post['rowid']))) {
							json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
							die;
						}

						if(!empty($result['rows'][0]['client_id'])) {
							$params['clientinfo'] = $result['rows'][0];
						}
					}
				} else
				if(!empty($post['method'])&&$post['method']=='clientphotoget') {

					if(!empty($post['_method'])&&$post['_method']=='clientnew'&&empty($_GET['itemId'])) {
						header("Content-Type: image/jpg");
						die();
					}

					/*$retval = array();
					$retval['vars'] = $this->vars;
					$retval['$_SESSION'] = $_SESSION;
					$retval['$_GET'] = $_GET;

					pre($retval);

					json_encode_return($retval);
					die;*/

					if(!empty($post['rowid'])) {
					} else {
						$post['rowid'] = 0;
					}

					if(!empty($_GET['itemId'])) {
						if(!($result = $appdb->query("select * from tbl_upload where upload_id=".$_GET['itemId']))) {
							json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
							die;
						}
					} else {
						if(!($result = $appdb->query("select * from tbl_upload where upload_name='".$post['name']."' and upload_studentprofileid=".$post['rowid']." order by upload_id desc limit 1"))) {
							json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
							die;
						}
						$pid = $post['rowid'];
					}

					if(!empty($result['rows'][0]['upload_content'])) {
						//$retval['uploadid'] = $result['rows'][0]['upload_id'];
						$content = base64_decode($result['rows'][0]['upload_content']);
					}

					$size = 500;

					$settings_autodetectface = getOption('$SETTINGS_AUTODETECTFACE',false);

					if(!empty($content)) {

						header("Content-Type: image/jpg");

						if($settings_autodetectface) {

							$detector = new FaceDetector;

							$detector->faceDetectString($content);
							//$detector->faceDetect('duterte101.jpg');

							//$detector->cropFaceToJpeg();
							$detector->cropFaceToJpeg2();

							$detector->resize($size,$size);

							if(!empty($pid)) {

								$imagefile = '/var/log/cache/'.$pid.'-'.$size.'.jpg';

								@$detector->output(IMAGETYPE_JPEG, $imagefile);

							}

							$detector->output();

						} else {

							$img = new APP_SimpleImage;

							$img->loadfromstring($content);

							$wd = $img->getWidth();
							$ht = $img->getHeight();

							if($wd>$ht) {
								$img->resizeToHeight($size);
							} else {
								$img->resizeToWidth($size);
							}

							//print_r($content);

							$img->output();

						}

						//print_r($content);

					} else {

						define('TAP_PATH', ABS_PATH . 'templates/default/tap');

						$defaultphoto = TAP_PATH.'/user.jpg';

						if(file_exists($defaultphoto)&&($hf=fopen($defaultphoto,'r'))) {

					    $content = fread($hf,filesize($defaultphoto));

							//pre(array('$defaultphoto'=>$defaultphoto,'$size'=>$size)); die;

							//pre($content); die;

					    fclose($hf);

							header("Content-Type: image/jpg");

							if(!empty($content)) {
								$img = new APP_SimpleImage;

								$img->loadfromstring($content);

								//if(!empty($size)) {
								//		$img->resize($size,$size);
								//}

								$img->output();
							}

							die;

						}
					}

					die();

				} else
				if(!empty($post['method'])&&$post['method']=='clientphotoupload') {

					$filename = $_FILES["file"]["name"];

					$retval = array();
					$retval['state'] = true;
					$retval['itemId'] = $post['itemId'];
					$retval['filename'] = str_replace("'","\\'",$filename);
					$retval['vars'] = $this->vars;
					$retval['$_FILES'] = $_FILES;

					$filepath = $_FILES['file']['tmp_name'];

					if(is_readable($filepath)&&($hf=fopen($filepath,'r'))) {

						$fcontent = fread($hf,filesize($filepath));
						fclose($hf);
						@unlink($filepath);

						$b64content = base64_encode($fcontent);

						if($b64content) {
							$content = array();
							$content['upload_sid'] = $appsession->id();
							$content['upload_type'] = $_FILES['file']['type'];
							$content['upload_temp'] = 1;
							$content['upload_content'] = $b64content;
							$content['upload_size'] = $_FILES['file']['size'];
							$content['upload_name'] = $post['itemId'];
							//$content['upload_customerid'] = $post['rowid'];

							if(!($result = $appdb->query("select * from tbl_upload where upload_studentprofileid=0 and upload_sid='".$content['upload_sid']."' and upload_name='".$post['itemId']."'"))) {
								json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
								die;
							}

							if(!empty($result['rows'][0]['upload_id'])) {

								$retval['uploadid'] = $result['rows'][0]['upload_id'];

								if(!($result = $appdb->update("tbl_upload",$content,"upload_id=".$retval['uploadid']))) {
									json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
									die;
								}

								if(!in_array($retval['uploadid'], $_SESSION['UPLOADS'])) {
									$_SESSION['UPLOADS'][] = $retval['uploadid'];
								}

							} else {
								if(!($result = $appdb->insert("tbl_upload",$content,"upload_id"))) {
									json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
									die;
								}

								if(!empty($result['returning'][0]['upload_id'])) {
									$retval['uploadid'] = $result['returning'][0]['upload_id'];
									$_SESSION['UPLOADS'][] = $retval['uploadid'];
								}
							}

							$retval['itemValue'] = $retval['uploadid'];
						}
					}



					//json_encode_return($retval);
					header("Content-Type: text/html");
					print_r(json_encode($retval));
					die;

				} else
				if(!empty($post['method'])&&$post['method']=='clientdelete') {
					$retval = array();
					$retval['return_code'] = 'SUCCESS';
					$retval['return_message'] = 'Client successfully deleted!';
					$retval['wid'] = $post['wid'];
					$retval['post'] = $post;

					if(!empty($post['rowid'])) {
						if(!($result = $appdb->query("delete from tbl_client where client_id=".$post['rowid']))) {
							json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
							die;
						}
					}

					json_encode_return($retval);
					die;
				} else
				if(!empty($post['method'])&&$post['method']=='clientsave') {
					$retval = array();
					$retval['return_code'] = 'SUCCESS';
					$retval['return_message'] = 'Client successfully saved!';
					$retval['post'] = $post;

					$license = checkLicense();

					if(!empty($post['rowid'])&&is_numeric($post['rowid'])&&$post['rowid']>0&&!empty($license)&&!empty($license['ns'])&&intval($license['ns'])>0&&intval($license['ns'])>=getTotalStudentCurrentSchoolYear()) {
					} else
					if(!empty($license)&&!empty($license['ns'])&&intval($license['ns'])>0&&intval($license['ns'])>getTotalStudentCurrentSchoolYear()) {
					} else {
						$retval = array();
						$retval['error_code'] = '345346';
						$retval['error_message'] = 'Invalid license or maximum number of allowed student for this school year has been reached!';

						json_encode_return($retval);
					}

					//pre(array('$post',$post));
					$content = array();
					$content['client_name'] = !empty($post['client_name']) ? $post['client_name'] : '';
					$content['client_shortname'] = !empty($post['client_shortname']) ? $post['client_shortname'] : '';
					$content['client_active'] = !empty($post['client_active']) ? 1 : 0;
					$content['client_info'] = !empty($post['client_info']) ? $post['client_info'] : '';
					$content['client_contactperson'] = !empty($post['client_contactperson']) ? $post['client_contactperson'] : '';
					$content['client_contactnumber'] = !empty($post['client_contactnumber']) ? $post['client_contactnumber'] : '';
					$content['client_population'] = !empty($post['client_population'])&&is_numeric($post['client_population'])&&intval($post['client_population'])>0 ? $post['client_population'] : 0;
					$content['client_license'] = !empty($post['client_license']) ? $post['client_license'] : '';
					$content['client_url'] = !empty($post['client_url']) ? $post['client_url'] : '';
					$content['client_vpnip'] = !empty($post['client_vpnip']) ? $post['client_vpnip'] : '';
					$content['client_publicip'] = !empty($post['client_publicip']) ? $post['client_publicip'] : '';

					if(!empty($post['rowid'])&&is_numeric($post['rowid'])&&$post['rowid']>0) {

						$retval['rowid'] = $post['rowid'];

						$content['client_updatestamp'] = 'now()';

						if(!($result = $appdb->update("tbl_client",$content,"client_id=".$post['rowid']))) {
							json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
							die;
						}

					} else {

						if(!($result = $appdb->insert("tbl_client",$content,"client_id"))) {
							json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
							die;
						}

						if(!empty($result['returning'][0]['client_id'])) {
							$retval['rowid'] = $result['returning'][0]['client_id'];
						}

					}

					json_encode_return($retval);
					die;
				}

				$params['hello'] = 'Hello, Sherwin!';

				$newcolumnoffset = 50;

				$position = 'right';

				$params['tbClientProfile'] = array();

				/*$params['tbStudentProfile'][] = array(
					'type' => 'input',
					'label' => 'TARDINESS GRACE PERIOD (MINUTE)',
					'labelWidth' => 250,
					'name' => 'setting_tardinessgraceperiod',
					'readonly' => $readonly,
					//'required' => !$readonly,
					'value' => !empty($params['settinginfo']['setting_tardinessgraceperiod']) ? $params['settinginfo']['group_tardinessgraceperiod'] : '',
				);*/

				$block = array();

				$block[] = array(
					'type' => 'input',
					'label' => 'CLIENT NAME',
					'labelWidth' => 150,
					'inputWidth' => 500,
					'name' => 'client_name',
					'readonly' => $readonly,
					'required' => !$readonly,
					'value' => !empty($params['clientinfo']['client_name']) ? $params['clientinfo']['client_name'] : '',
				);

				$block[] = array(
					'type' => 'newcolumn',
					'offset' => 50,
				);

				$block[] = array(
					'type' => 'checkbox',
					'label' => 'IS ACTIVE',
					'labelWidth' => 80,
					'name' => 'client_active',
					'readonly' => $readonly,
					'checked' => !empty($params['clientinfo']['client_active']) ? true : false,
					'position' => 'label-right',
				);

				$params['tbClientProfile'][] = array(
					'type' => 'block',
					'width' => 1000,
					'blockOffset' => 0,
					'offsetTop' => 5,
					'list' => $block,
				);

				$params['tbClientProfile'][] = array(
					'type' => 'input',
					'label' => 'SHORT NAME',
					'labelWidth' => 150,
					'inputWidth' => 200,
					'name' => 'client_shortname',
					'readonly' => $readonly,
					'required' => !$readonly,
					'value' => !empty($params['clientinfo']['client_shortname']) ? $params['clientinfo']['client_shortname'] : '',
				);

				$params['tbClientProfile'][] = array(
					'type' => 'input',
					'label' => 'POPULATION',
					'labelWidth' => 150,
					'inputWidth' => 200,
					'name' => 'client_population',
					'numeric' => true,
					'readonly' => $readonly,
					'required' => !$readonly,
					'value' => !empty($params['clientinfo']['client_population']) ? $params['clientinfo']['client_population'] : '',
				);

				$params['tbClientProfile'][] = array(
					'type' => 'input',
					'label' => 'INFO',
					'labelWidth' => 150,
					'inputWidth' => 500,
					'rows' => 2,
					'name' => 'client_info',
					'readonly' => $readonly,
					'required' => !$readonly,
					'value' => !empty($params['clientinfo']['client_info']) ? $params['clientinfo']['client_info'] : '',
				);

				$params['tbClientProfile'][] = array(
					'type' => 'input',
					'label' => 'CONTACT PERSON',
					'labelWidth' => 150,
					'inputWidth' => 500,
					'name' => 'client_contactperson',
					'readonly' => $readonly,
					'required' => !$readonly,
					'value' => !empty($params['clientinfo']['client_contactperson']) ? $params['clientinfo']['client_contactperson'] : '',
				);

				$params['tbClientProfile'][] = array(
					'type' => 'input',
					'label' => 'CONTACT NUMBER',
					'labelWidth' => 150,
					'inputWidth' => 500,
					'name' => 'client_contactnumber',
					'readonly' => $readonly,
					'required' => !$readonly,
					'value' => !empty($params['clientinfo']['client_contactnumber']) ? $params['clientinfo']['client_contactnumber'] : '',
				);

				$params['tbClientProfile'][] = array(
					'type' => 'input',
					'label' => 'URL',
					'labelWidth' => 150,
					'inputWidth' => 500,
					'name' => 'client_url',
					'readonly' => $readonly,
					//'required' => !$readonly,
					'value' => !empty($params['clientinfo']['client_url']) ? $params['clientinfo']['client_url'] : '',
				);

				$params['tbClientProfile'][] = array(
					'type' => 'input',
					'label' => 'VPN IP',
					'labelWidth' => 150,
					'inputWidth' => 250,
					'name' => 'client_vpnip',
					'readonly' => $readonly,
					//'required' => !$readonly,
					'value' => !empty($params['clientinfo']['client_vpnip']) ? $params['clientinfo']['client_vpnip'] : '',
				);

				$params['tbClientProfile'][] = array(
					'type' => 'input',
					'label' => 'PUBLIC IP',
					'labelWidth' => 150,
					'inputWidth' => 250,
					'name' => 'client_publicip',
					'readonly' => $readonly,
					//'required' => !$readonly,
					'value' => !empty($params['clientinfo']['client_publicip']) ? $params['clientinfo']['client_publicip'] : '',
				);

				$params['tbClientProfile'][] = array(
					'type' => 'input',
					'label' => 'STATUS',
					'labelWidth' => 150,
					'inputWidth' => 200,
					'name' => 'client_status',
					'readonly' => true,
					//'required' => !$readonly,
					'value' => !empty($params['clientinfo']['client_status']) ? $params['clientinfo']['client_status'] : '',
				);

				$templatefile = $this->templatefile($routerid,$formid);

				//pre(array($routerid,$formid,$params,$templatefile));

				if(file_exists($templatefile)) {
					return $this->_form_load_template($templatefile,$params);
				}
			}

			return false;

		} // _form_clientdetailclients

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

					} else
					if($this->post['table']=='clients') {

						if(!($result = $appdb->query("select * from tbl_client order by client_id asc"))) {
							json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
							die;
						}

						//pre(array('$result'=>$result));

						if(!empty($result['rows'][0]['client_id'])) {
							$rows = array();

							$seq = 1;

							foreach($result['rows'] as $k=>$v) {
								$rows[] = array('id'=>$v['client_id'],'data'=>array(0,$seq,$v['client_id'],$v['client_shortname'],$v['client_name'],$v['client_contactperson'],$v['client_contactnumber'],$v['client_population'],$v['client_url'],$v['client_status'],$v['client_active'],$v['client_createstamp'],$v['client_updatestamp']));
								$seq++;
							}

							$retval = array('rows'=>$rows);
						}

					}

					$jsonval = json_encode($retval,JSON_OBJECT_AS_ARRAY);

					//pre(array('$jsonval'=>$jsonval));

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

	$appappclient = new APP_app_client;
}

# eof modules/app.user
