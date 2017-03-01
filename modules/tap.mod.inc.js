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
* Date: November 27, 2015
*
*/

if(!defined('APPLICATION_RUNNING')) {
	header("HTTP/1.0 404 Not Found");
	die('access denied');
}

if(defined('ANNOUNCE')) {
	echo "\n<!-- loaded: ".__FILE__." -->\n";
}

global $apptemplate;

//echo '/* ';
//echo "app.mod.inc.js";
//echo ' */';

//echo "\n\n/*\n\n";
//pre($vars);
//echo "\n\n*/\n\n";

?>
/*
*
* Author: Sherwin R. Terunez
* Contact: sherwinterunez@yahoo.com
*
* Description:
*
* Javascript Utilities
*
* Created: November 27, 2015
*
*/

var loginForm = [
	{type: "settings", position: "label-left", labelWidth: 90, inputWidth: 220, offsetLeft: 10, offsetTop: 5},
	{type: "input", label: "rfid", value: "", offsetTop: 20, name:"rfid", required:true},
	{type: "input", label: "unixtime", value: "", offsetTop: 20, name:"unixtime", required:true},
];

srt.checkFocus = function() {
	setTimeout(function(){
		if(!jQuery("input[name='rfid']").is(":focus")) {
			jQuery("input[name='rfid']").focus();
			//console.log('set focus');
		}
		srt.checkFocus();
		//console.log('Checking focus...');
	},100);
};

srt.checkTime = function() {
	setTimeout(function(){
		srt.myForm.setItemValue('unixtime',moment().format('X'));
		srt.checkTime();
	},1000);
}

srt.etap = function() {

	srt.myForm = myForm = new dhtmlXForm("myForm", loginForm);

	myForm.enableLiveValidation(true);

	srt.checkFocus();
	srt.checkTime();

	myForm.attachEvent("onEnter", function(){
		var rfid = myForm.getItemValue("rfid");
		var unixtime = myForm.getItemValue("unixtime");
	    console.log("Enter key has been pressed!");
	    console.log("Value: "+rfid);
	    myForm.setItemValue("rfid","");

	    //console.log($(this.base));

		postData('/'+settings.router_id+'/tapped/','rfid='+rfid+'&unixtime='+unixtime,function(data){
			//if(data.return_code) {
			//	if(data.return_code=='SUCCESS') {
			//		showMessage(data.return_message,5000);
			//	}
			//}

			//if(data) {

			//}

			//console.log(typeof(data.db));

			//console.log(data.in);

			if(typeof(data)!='object') {
				return false;
			}

			if(typeof data.db != 'undefined' ) {
				jQuery('#db').html(data.db);
			}

			if(typeof data.in != 'undefined' ) {
				jQuery('#in').html(data.in);
			}

			if(typeof data.out != 'undefined' ) {
				jQuery('#out').html(data.out);
			}

			if(typeof data.late != 'undefined' ) {
				jQuery('#late').html(data.late);
			}

			if(typeof data.type != 'undefined' ) {
				jQuery('#type').html(data.type);
			}

			if(typeof data.image != 'undefined' ) {
				console.log(typeof(data.image));
				jQuery('#studentphoto').html('<img src="'+data.image+'" />');
			}
		});


	});

};

jQuery(document).ready(function($) {
	srt.etap();
});
