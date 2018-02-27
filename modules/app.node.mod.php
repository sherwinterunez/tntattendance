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

if(!class_exists('APP_app_node')) {

	class APP_app_node extends APP_Base_Ajax {

		var $desc = 'node';

		var $pathid = 'node';
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

			$appaccess->rules($this->desc,'Node Module');
			$appaccess->rules($this->desc,'Node Module New');
			$appaccess->rules($this->desc,'Node Module Edit');
			$appaccess->rules($this->desc,'Node Module Delete');

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

		function _form_node($routerid=false,$formid=false) {
			global $applogin, $toolbars, $forms, $apptemplate, $appdb;

			if(!empty($routerid)&&!empty($formid)) {

				//pre(array($routerid,$formid));

				$post = $this->vars['post'];

				$params = array();

				$readonly = true;

				if(!empty($post['method'])&&($post['method']=='nodeedit')) {
					$readonly = false;
				}

				if(!empty($post['method'])&&$post['method']=='nodesave') {
					$retval = array();
					$retval['return_code'] = 'SUCCESS';
					$retval['return_message'] = 'Node successfully saved!';
					$retval['post'] = $post;

					//pre(array('$post',$post));

					json_encode_return($retval);
					die;
				}

				$params['hello'] = 'Hello, Sherwin!';

				$newcolumnoffset = 50;

				$position = 'right';

				$params['tbNodeRecords'] = array();

				/*$params['tbNodeRecords'][] = array(
					'type' => 'input',
					'label' => 'TARDINESS GRACE PERIOD (MINUTE)',
					'labelWidth' => 250,
					'name' => 'setting_tardinessgraceperiod',
					'readonly' => $readonly,
					//'required' => !$readonly,
					'value' => !empty($params['settinginfo']['setting_tardinessgraceperiod']) ? $params['settinginfo']['group_tardinessgraceperiod'] : '',
				);*/

				$params['tbNodeRecords'][] = array(
					'type' => 'container',
					'name' => 'node_grid',
					'inputWidth' => 400,
					'inputHeight' => 347,
					'className' => 'node_grid_'.$post['formval'],
				);

				$templatefile = $this->templatefile($routerid,$formid);

				//pre(array($routerid,$formid,$params,$templatefile));

				if(file_exists($templatefile)) {
					return $this->_form_load_template($templatefile,$params);
				}
			}

			return false;

		} // _form_node

		function _form_nodedetailnodes($routerid=false,$formid=false) {
			global $applogin, $toolbars, $forms, $apptemplate, $appdb, $appsession;

			if(!empty($routerid)&&!empty($formid)) {

				//pre(array($routerid,$formid));

				$post = $this->vars['post'];

				$params = array();

				$readonly = true;

				if(!empty($post['method'])&&($post['method']=='nodeedit'||$post['method']=='nodenew')) {
					$readonly = false;
				}

				if($post['method']=='nodenew') {
					$license = checkLicense();

					if(!empty($license)&&!empty($license['ns'])&&intval($license['ns'])>0&&intval($license['ns'])>getTotalStudentCurrentSchoolYear()) {
					} else {
						$retval = array();
						$retval['error_code'] = '345346';
						$retval['error_message'] = 'Invalid license or maximum number of allowed student for this school year has been reached!';

						json_encode_return($retval);
					}
				}

				if(!empty($post['method'])&&($post['method']=='onrowselect'||$post['method']=='nodeedit'||$post['method']=='noderefresh'||$post['method']=='nodecancel')) {
					if(!empty($post['rowid'])&&is_numeric($post['rowid'])&&$post['rowid']>0) {
						if(!($result = $appdb->query("select * from tbl_clientnode where clientnode_id=".$post['rowid']))) {
							json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
							die;
						}

						if(!empty($result['rows'][0]['clientnode_id'])) {
							$params['nodeinfo'] = $result['rows'][0];
						}
					}
				} else
				if(!empty($post['method'])&&$post['method']=='nodephotoget') {

					if(!empty($post['_method'])&&$post['_method']=='nodenew'&&empty($_GET['itemId'])) {
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
				if(!empty($post['method'])&&$post['method']=='nodephotoupload') {

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
				if(!empty($post['method'])&&$post['method']=='nodedelete') {
					$retval = array();
					$retval['return_code'] = 'SUCCESS';
					$retval['return_message'] = 'Node successfully deleted!';
					$retval['wid'] = $post['wid'];
					$retval['post'] = $post;

					if(!empty($post['rowid'])) {
						if(!($result = $appdb->query("delete from tbl_clientnode where clientnode_id=".$post['rowid']))) {
							json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
							die;
						}
					}

					json_encode_return($retval);
					die;
				} else
				if(!empty($post['method'])&&$post['method']=='nodesave') {
					$retval = array();
					$retval['return_code'] = 'SUCCESS';
					$retval['return_message'] = 'Node successfully saved!';
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
					$content['clientnode_clientid'] = !empty($post['clientnode_clientid'])&&is_numeric($post['clientnode_clientid'])&&intval($post['clientnode_clientid'])>0 ? $post['clientnode_clientid'] : 0;
					$content['clientnode_codename'] = !empty($post['clientnode_codename']) ? $post['clientnode_codename'] : '';
					$content['clientnode_active'] = !empty($post['clientnode_active']) ? 1 : 0;
					$content['clientnode_info'] = !empty($post['clientnode_info']) ? $post['clientnode_info'] : '';
					$content['clientnode_url'] = !empty($post['clientnode_url']) ? $post['clientnode_url'] : '';
					$content['clientnode_vpnip'] = !empty($post['clientnode_vpnip']) ? $post['clientnode_vpnip'] : '';
					$content['clientnode_vpnport'] = !empty($post['clientnode_vpnport']) ? $post['clientnode_vpnport'] : '';
					$content['clientnode_publicip'] = !empty($post['clientnode_publicip']) ? $post['clientnode_publicip'] : '';
					$content['clientnode_localip'] = !empty($post['clientnode_localip']) ? $post['clientnode_localip'] : '';

					if(!empty($content['clientnode_clientid'])) {
						$myClient = getClient($content['clientnode_clientid']);

						if(!empty($myClient['client_name'])) {
							$content['clientnode_name'] = $myClient['client_name'];
						}
					}

					if(!empty($post['rowid'])&&is_numeric($post['rowid'])&&$post['rowid']>0) {

						$retval['rowid'] = $post['rowid'];

						$content['clientnode_updatestamp'] = 'now()';

						if(!($result = $appdb->update("tbl_clientnode",$content,"clientnode_id=".$post['rowid']))) {
							json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
							die;
						}

					} else {

						if(!($result = $appdb->insert("tbl_clientnode",$content,"clientnode_id"))) {
							json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
							die;
						}

						if(!empty($result['returning'][0]['clientnode_id'])) {
							$retval['rowid'] = $result['returning'][0]['clientnode_id'];
						}

					}

					json_encode_return($retval);
					die;
				}

				$params['hello'] = 'Hello, Sherwin!';

				$newcolumnoffset = 50;

				$position = 'right';

				$params['tbNodeProfile'] = array();

				$block = array();

				$allClients = getAllClients();

				//pre(array('$allClients'=>$allClients));

				$opt = array();

				if(!empty($allClients)) {
					if(!$readonly) {
						$opt[] = array('text'=>'','value'=>'','selected'=>false);
					}

					foreach($allClients as $v) {
						$selected = false;
						if(!empty($params['nodeinfo']['clientnode_clientid'])&&$params['nodeinfo']['clientnode_clientid']==$v['client_id']) {
							$selected = true;
						}
						if($readonly) {
							if($selected) {
								$opt[] = array('text'=>$v['client_name'],'value'=>$v['client_id'],'selected'=>$selected);
							}
						} else {
							$opt[] = array('text'=>$v['client_name'],'value'=>$v['client_id'],'selected'=>$selected);
						}
					}
				}

				$block[] = array(
					'type' => 'combo',
					'label' => 'CLIENT',
					'labelWidth' => 100,
					'inputWidth' => 500,
					'name' => 'clientnode_clientid',
					'readonly' => $readonly,
					'required' => !$readonly,
					//'value' => !empty($params['nodeinfo']['client_name']) ? $params['nodeinfo']['client_name'] : '',
					'options' => $opt,
				);

				$block[] = array(
					'type' => 'newcolumn',
					'offset' => 50,
				);

				$block[] = array(
					'type' => 'checkbox',
					'label' => 'IS ACTIVE',
					'labelWidth' => 80,
					'name' => 'clientnode_active',
					'readonly' => $readonly,
					'checked' => !empty($params['nodeinfo']['clientnode_active']) ? true : false,
					'position' => 'label-right',
				);

				$params['tbNodeProfile'][] = array(
					'type' => 'block',
					'width' => 1000,
					'blockOffset' => 0,
					'offsetTop' => 5,
					'list' => $block,
				);

				$params['tbNodeProfile'][] = array(
					'type' => 'input',
					'label' => 'CODE NAME',
					'labelWidth' => 100,
					'inputWidth' => 200,
					'name' => 'clientnode_codename',
					'readonly' => $readonly,
					'required' => !$readonly,
					'value' => !empty($params['nodeinfo']['clientnode_codename']) ? $params['nodeinfo']['clientnode_codename'] : '',
				);

				$params['tbNodeProfile'][] = array(
					'type' => 'input',
					'label' => 'INFO',
					'labelWidth' => 100,
					'inputWidth' => 500,
					'rows' => 2,
					'name' => 'clientnode_info',
					'readonly' => $readonly,
					'required' => !$readonly,
					'value' => !empty($params['nodeinfo']['clientnode_info']) ? $params['nodeinfo']['clientnode_info'] : '',
				);

				$params['tbNodeProfile'][] = array(
					'type' => 'input',
					'label' => 'URL',
					'labelWidth' => 100,
					'inputWidth' => 500,
					'name' => 'clientnode_url',
					'readonly' => $readonly,
					//'required' => !$readonly,
					'value' => !empty($params['nodeinfo']['clientnode_url']) ? $params['nodeinfo']['clientnode_url'] : '',
				);

				$params['tbNodeProfile'][] = array(
					'type' => 'input',
					'label' => 'VPN IP',
					'labelWidth' => 100,
					'inputWidth' => 250,
					'name' => 'clientnode_vpnip',
					'readonly' => $readonly,
					'required' => !$readonly,
					'value' => !empty($params['nodeinfo']['clientnode_vpnip']) ? $params['nodeinfo']['clientnode_vpnip'] : '',
				);

				$params['tbNodeProfile'][] = array(
					'type' => 'input',
					'label' => 'VPN PORT',
					'labelWidth' => 100,
					'inputWidth' => 250,
					'name' => 'clientnode_vpnport',
					'readonly' => $readonly,
					'required' => !$readonly,
					'value' => !empty($params['nodeinfo']['clientnode_vpnport']) ? $params['nodeinfo']['clientnode_vpnport'] : '',
				);

				$params['tbNodeProfile'][] = array(
					'type' => 'input',
					'label' => 'LOCAL IP',
					'labelWidth' => 100,
					'inputWidth' => 250,
					'name' => 'clientnode_localip',
					'readonly' => $readonly,
					'required' => !$readonly,
					'value' => !empty($params['nodeinfo']['clientnode_localip']) ? $params['nodeinfo']['clientnode_localip'] : '',
				);

				$params['tbNodeProfile'][] = array(
					'type' => 'input',
					'label' => 'PUBLIC IP',
					'labelWidth' => 100,
					'inputWidth' => 250,
					'name' => 'clientnode_publicip',
					'readonly' => $readonly,
					//'required' => !$readonly,
					'value' => !empty($params['nodeinfo']['clientnode_publicip']) ? $params['nodeinfo']['clientnode_publicip'] : '',
				);

				$params['tbNodeProfile'][] = array(
					'type' => 'input',
					'label' => 'STATUS',
					'labelWidth' => 100,
					'inputWidth' => 200,
					'name' => 'clientnode_status',
					'readonly' => true,
					//'required' => !$readonly,
					'value' => !empty($params['nodeinfo']['clientnode_status']) ? $params['nodeinfo']['clientnode_status'] : '',
				);

				$templatefile = $this->templatefile($routerid,$formid);

				//pre(array($routerid,$formid,$params,$templatefile));

				if(file_exists($templatefile)) {
					return $this->_form_load_template($templatefile,$params);
				}
			}

			return false;

		} // _form_nodedetailnodes

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
					if($this->post['table']=='nodes') {

						if(!($result = $appdb->query("select * from tbl_clientnode order by clientnode_id asc"))) {
							json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.<br />'.$appdb->lasterror,'$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
							die;
						}

						//pre(array('$result'=>$result));

						if(!empty($result['rows'][0]['clientnode_id'])) {
							$rows = array();

							$seq = 1;

							foreach($result['rows'] as $k=>$v) {
								$rows[] = array('id'=>$v['clientnode_id'],'data'=>array(0,$seq,$v['clientnode_id'],$v['clientnode_codename'],$v['clientnode_name'],$v['clientnode_url'],$v['clientnode_vpnip'],$v['clientnode_vpnport'],$v['clientnode_localip'],$v['clientnode_status'],$v['clientnode_active'],$v['clientnode_createstamp'],$v['clientnode_updatestamp']));
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

	$appappnode = new APP_app_node;
}

# eof modules/app.user
