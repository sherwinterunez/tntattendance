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

$settings_showadsinterval = getOption('$SETTINGS_SHOWADSINTERVAL',30);

$settings_showadsinterval = intval($settings_showadsinterval) * 60 * 1000;

$settings_showadsintervalenable = getOption('$SETTINGS_SHOWADSINTERVALENABLE',0);

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
	{type: "hidden", value: "350", name:"imagesize"},
	{type: "hidden", value: "<?php echo $settings_showadsinterval; ?>", name:"showadsinterval"},
	{type: "hidden", value: "<?php echo $settings_showadsintervalenable; ?>", name:"showadsintervalenable"},
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

//srt.checkTime = function() {
//	setTimeout(function(){
//		srt.myForm.setItemValue('unixtime',moment().format('X'));
//		srt.checkTime();
//	},1000);
//}

srt.etap = function() {

	srt.myForm = myForm = new dhtmlXForm("myForm", loginForm);

	myForm.enableLiveValidation(true);

	srt.checkFocus();
	//srt.checkTime();

	myForm.attachEvent("onEnter", function(){
		var rfid = myForm.getItemValue("rfid");
		var showadsinterval = parseInt(myForm.getItemValue("showadsinterval"));
		var unixtime = myForm.getItemValue("unixtime");
		var imagesize = myForm.getItemValue("imagesize");
	    //console.log("Enter key has been pressed!");
	    //console.log("Value: "+rfid);
	    myForm.setItemValue("rfid","");

			srt.doHideAds();

	    //console.log($(this.base));

			var studentprevTotal = 0;

			jQuery(".studentprev").each(function(idx){
				studentprevTotal++;
			});

		postData('/'+settings.router_id+'/tapped/','rfid='+rfid+'&unixtime='+unixtime+'&imagesize='+imagesize+'&total='+studentprevTotal,function(data){

			//if(data) {

			//}

			//console.log(typeof(data.db));

			//console.log(data.in);

			if(typeof(data)!='object') {
				return false;
			}

			if(data.return_code&&data.return_message) {
				showErrorMessage(data.return_message,2000);
				return false;
			}

			if(typeof data.showadsinterval != 'undefined' ) {
				//jQuery('#showadsinterval').html(data.showadsinterval);
				myForm.setItemValue("showadsinterval",data.showadsinterval);
			}

			if(typeof data.showadsintervalenable != 'undefined' ) {
				myForm.setItemValue("showadsintervalenable",data.showadsintervalenable);
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
				//console.log(typeof(data.image));
				//jQuery('#studentphoto').html('<img src="'+data.image+'" />');
				jQuery('#studentphoto').html('<img src="'+data.image+'" style="max-width:'+srt.studentphotobgHeight+'px;max-height:'+srt.studentphotobgHeight+'px" />');
			}

			if(typeof data.fullname != 'undefined' ) {
				//console.log(typeof(data.image));
				jQuery('#studentname').html(data.fullname);
			}

			var yearlevelsection = '';

			if(typeof data.yearlevel != 'undefined' ) {
				//console.log(typeof(data.image));
				//jQuery('#studentname').html(data.fullname);
				yearlevelsection += data.yearlevel + ' - ';
			}

			if(typeof data.section != 'undefined' ) {
				//console.log(typeof(data.image));
				//jQuery('#studentname').html(data.fullname);
				yearlevelsection += data.section;
			}

			jQuery('#studentyearsection').html(yearlevelsection);

			if(typeof data.remarks != 'undefined' ) {
				//console.log(typeof(data.image));
				jQuery('#studentremarks').html(data.remarks);
			}

			var contentrightHeight = jQuery("#contentright").height();
			var contentrightWidth = jQuery("#contentright").width();
			var studentcontentdivHeight = jQuery("#studentcontentdiv").height();
			var studentcontentHeight  = jQuery("#studentcontent").height();
			var studentcontentPaddingTop = (contentrightHeight - studentcontentdivHeight) / 2.5;

			jQuery("#studentcontent").css({height:studentcontentdivHeight,'paddingTop':studentcontentPaddingTop});

			var infoPaddingTop = (contentrightHeight - (studentcontentHeight + studentcontentPaddingTop)) / 2;

			var infoilabelWidth = 0;
			var infoilabelCtr = 0;

			jQuery("#info .ilabel").each(function(idx){
				infoilabelCtr++;
				infoilabelWidth = 10 + infoilabelWidth + jQuery(this).width();
			});

			jQuery("#info").css({height:infoPaddingTop,width:infoilabelWidth,'paddingTop':infoPaddingTop});

			if(typeof data.previous == 'object' &&  data.previous.length>0 ) {

				var obj = [];
				var max = 0;
				var ctr = 0;

				jQuery(".studentprev").each(function(idx){
					obj[max] = this;
					max++;
				});

				for(var prop in data.previous) {
					if(typeof obj[ctr] == 'object') {
						jQuery(obj[ctr]).html(data.previous[prop].html);
						//console.log(data.previous[prop]);
						ctr++;
					}
				}
			}

		},true);


	});

};

srt.doMarquee = function() {
	postData('/'+settings.router_id+'/getbulletin/','marquee='+moment().format('X'),function(data){

		//console.log(typeof(data));

		if(typeof(data)!='object') {
			return false;
		}

		//console.log(data);

		if(typeof data.bulletin != 'undefined') {
			jQuery('#marquee').html(data.bulletin);
			jQuery('#marquee').marquee({duration: 10000, allowCss3Support:true});
		}
	},true);
}

srt.doShowDateTime = function() {
	//var dt = moment().format('LLLL');

	postData('/'+settings.router_id+'/getdatetime/','test=1',function(data){
		//console.log('doShowDateTime',data);

		jQuery('#currentdatetime').html(data.currentTimeString);
		//jQuery('#sysinfo').html('Server IP: '+data.localip);
		jQuery('#sysinfo').html(data.sysinfo);
		srt.myForm.setItemValue('unixtime',data.currentTime);

		if(typeof data.showadsinterval != 'undefined' ) {
			myForm.setItemValue("showadsinterval",data.showadsinterval);
		}

		if(typeof data.showadsintervalenable != 'undefined' ) {

			var showadsintervalenable = parseInt(myForm.getItemValue("showadsintervalenable"));

			if(showadsintervalenable==parseInt(data.showadsintervalenable)) {
			} else {
				myForm.setItemValue("showadsintervalenable",parseInt(data.showadsintervalenable));
				srt.doHideAds();
			}
		}

		if(typeof data.license != 'undefined' ) {
			jQuery('#schoolname').html(data.license);
		}

	},true);
}

srt.doShowAds = function() {

	var showadsintervalenable = parseInt(myForm.getItemValue("showadsintervalenable"));

	clearInterval(srt.adsInterval);

	srt.adsInterval = null;

	if(showadsintervalenable) {
		jQuery("#advertisement").css({opacity:1});
	}
}

srt.doHideAds = function() {

	var showadsinterval = parseInt(myForm.getItemValue("showadsinterval"));

	if(srt.adsInterval) {
		clearInterval(srt.adsInterval);
		srt.adsInterval = null;
	}

	srt.adsInterval = setInterval(function(){
		srt.doShowAds();
	},showadsinterval);

	jQuery("#advertisement").css({opacity:0});
}

srt.getPrevious = function() {
	postData('/'+settings.router_id+'/getprevious/','test=1',function(data){
		//console.log('getPrevious',data);

		if(typeof data.previous == 'object' &&  data.previous.length>0 ) {

			var obj = [];
			var max = 0;
			var ctr = 0;

			jQuery(".studentprev").each(function(idx){
				obj[max] = this;
				max++;
			});

			for(var prop in data.previous) {
				if(typeof obj[ctr] == 'object') {
					jQuery(obj[ctr]).html(data.previous[prop].html);
					//console.log(data.previous[prop]);
					ctr++;
				}
			}
		}

	},true);
}

srt.setPrevious = function() {

	var studentprevTotal = 0;

	jQuery(".studentprev").each(function(idx){
		studentprevTotal++;
	});

	postData('/'+settings.router_id+'/setprevious/','test=1&total='+studentprevTotal,function(data){
		//console.log('setPrevious',data);

		//jQuery('#currentdatetime').html(data.currentTimeString);
		//jQuery('#sysinfo').html('Server IP: '+data.localip);
		//jQuery('#sysinfo').html(data.sysinfo);
		//srt.myForm.setItemValue('unixtime',data.currentTime);

		var contentrightHeight = jQuery("#contentright").height();
		var contentrightWidth = jQuery("#contentright").width();
		var studentcontentdivHeight = jQuery("#studentcontentdiv").height();
		var studentcontentHeight  = jQuery("#studentcontent").height();
		var studentcontentPaddingTop = (contentrightHeight - studentcontentHeight) / 2.5;

		//alert('studentcontentPaddingTop: '+studentcontentPaddingTop);
		//alert('studentcontentHeight: '+studentcontentHeight);

		jQuery("#studentcontent").css({height:studentcontentHeight,'paddingTop':studentcontentPaddingTop});

		var infoPaddingTop = (contentrightHeight - (studentcontentHeight + studentcontentPaddingTop)) / 2;

		var infoilabelWidth = 0;
		var infoilabelCtr = 0;

		jQuery("#info .ilabel").each(function(idx){
			infoilabelCtr++;
			infoilabelWidth = 10 + infoilabelWidth + jQuery(this).width();
		});

		//alert('infoilabelCtr: '+infoilabelCtr);

		jQuery("#info").css({height:infoPaddingTop,width:infoilabelWidth,'paddingTop':infoPaddingTop});

		//showMessage('studentcontentHeight: '+studentcontentHeight,60000);
		//showMessage('studentcontentdivHeight: '+studentcontentdivHeight,60000);
		//showMessage('studentcontentdivPaddingTop: '+studentcontentdivPaddingTop,60000);

		//jQuery("#studentcontentdiv").css({paddingTop:studentcontentdivPaddingTop});

		var contentpreviousHeight  = jQuery("#contentprevious").height();
		var contentpreviousdivHeight = jQuery("#contentpreviousdiv").height();
		var contentpreviousdivPaddingTop = parseInt((contentpreviousHeight - contentpreviousdivHeight) / 2);

		//showMessage('contentpreviousHeight: '+contentpreviousHeight,60000);
		//showMessage('contentpreviousdivHeight: '+contentpreviousdivHeight,60000);
		//showMessage('contentpreviousdivPaddingTop: '+contentpreviousdivPaddingTop,60000);

		//jQuery("#contentpreviousdiv").css({paddingTop:contentpreviousdivPaddingTop});

		if(typeof data.previous == 'object' &&  data.previous.length>0 ) {

			var obj = [];
			var max = 0;
			var ctr = 0;

			jQuery(".studentprev").each(function(idx){
				obj[max] = this;
				max++;
			});

			for(var prop in data.previous) {
				if(typeof obj[ctr] == 'object') {
					jQuery(obj[ctr]).html(data.previous[prop].html);
					//console.log(data.previous[prop]);
					ctr++;
				}
			}
		}

	},true);
}

jQuery(document).ready(function($) {
	srt.etap();
	srt.doMarquee();
	srt.doShowDateTime();

	var width = jQuery(window).width();
	var height = jQuery(window).height();
	var contentleftWidth = jQuery("#contentleft").width();
	var contentTopLeftWidth = jQuery("#contenttopleft").width();
	var contentTopRightWidth = jQuery("#contenttopright").width();
	var licenselogoWidth = jQuery("#licenselogo").width();
	var contenttopHeight = 100;
	var contentbottomHeight = 60;
	var contentmiddleHeight = height - (contenttopHeight+contentbottomHeight);
	var studentphotobgHeight = parseInt(contentleftWidth * 0.85);
	var studentphotobgMarginTop = (contentmiddleHeight - studentphotobgHeight) / 2;
	var infoMarginTop = (studentphotobgMarginTop / 2) - 10;
	var studentcontentHeight = contentmiddleHeight / 2 ; //jQuery("#studentcontent").height(); // + 100;
	var contentpreviousHeight = jQuery("#contentprevious").height(); // + 20;
	var contentpreviousdivHeight = jQuery("#contentpreviousdiv").height(); // + 20;
	//var studentcontentMarginTop = (contentmiddleHeight - (studentcontentHeight+contentpreviousHeight)) / 3;
	var contentpreviousWidth = jQuery("#contentprevious").width();
	var studentprevWidth = 0;
	var studentprevCtr = 0;
	var studentprevMargin = 0;

	var showadsinterval = parseInt(myForm.getItemValue("showadsinterval"));

	//alert('width: '+width+', height: '+height);

	//alert('contentTopLeftWidth: '+contentTopLeftWidth);

	//alert('contentTopRightWidth: '+contentTopRightWidth

	//alert('width: '+width+', contentpreviousWidth: '+contentpreviousWidth);

	var toph = 0.0538;
	var both = 0.0538;
	var midh = 1 - (toph+both);

	var contenttopHeightNew = height * toph;
	var contentbottomHeightNew = height * both;

	if(contenttopHeightNew<100) {
		contenttopHeightNew = 100;
	}

	if(contentbottomHeightNew<100) {
		contentbottomHeightNew = 100;
	}

	midh = 1 - ((contenttopHeightNew + contentbottomHeightNew) / height);

	var contentmiddleHeightNew = height * midh;

	//alert('height: '+height+', midh: '+midh+', contentmiddleHeightNew: '+contentmiddleHeightNew);

	var contentTopLeftWidthNew = parseInt((width - contentTopRightWidth));

	var contentleftHeightNew = contentmiddleHeightNew * 0.5;
	var contentrightHeightNew = contentmiddleHeightNew * 0.5;

	//alert('contentleftHeightNew: '+contentleftHeightNew+', contentrightHeightNew: '+contentrightHeightNew);

	//alert('contenttopHeightNew: '+contenttopHeightNew);

	//alert('contentTopLeftWidthNew: '+contentTopLeftWidthNew);

	jQuery("#contenttop").css({height:contenttopHeightNew});
	jQuery("#contentbottom").css({height:contentbottomHeightNew});
	jQuery("#marquee").css({height:contentbottomHeightNew*.8,fontSize:(contentbottomHeightNew*.8)+'px',paddingBottom:(contentbottomHeightNew*.2)+'px'});

	jQuery("#contenttopleft").css({width:contentTopLeftWidthNew});
	jQuery("#contentmiddles").css({height:contentmiddleHeightNew});

	//alert('contentmiddleHeightNew: '+contentmiddleHeightNew);

	jQuery("#contentleft").css({height:contentleftHeightNew});
	jQuery("#contentright").css({height:contentrightHeightNew});

	jQuery("#contentmid").css({height:contentleftHeightNew});
	jQuery("#contentprevious").css({height:contentleftHeightNew});
	jQuery("#contentpreviousdiv").css({height:contentleftHeightNew});

	var studentnameFontSizeNew = contentrightHeightNew * 0.06;
	var studentnameMargin = contentrightHeightNew * 0.01;
	var studentnameLineHeight = contentrightHeightNew * 0.065;

	jQuery("#studentname").css({fontSize:studentnameFontSizeNew+'px','line-height':studentnameLineHeight+'px','margin-top':0,'margin-right':studentnameMargin+'px','margin-bottom':studentnameMargin+'px','margin-left':studentnameMargin+'px'});

	var studentyearsectionFontSizeNew = contentrightHeightNew * 0.04;
	var studentyearsectionMargin = contentrightHeightNew * 0.05;
	var studentyearsectionLineHeight = contentrightHeightNew * 0.045;

	jQuery("#studentyearsection").css({fontSize:studentyearsectionFontSizeNew+'px','line-height':studentyearsectionLineHeight+'px','margin':studentyearsectionMargin+'px'});

	var studentremarksFontSizeNew = contentrightHeightNew * 0.05;
	var studentremarksMargin = contentrightHeightNew * 0.05;
	var studentremarksLineHeight = contentrightHeightNew * 0.055;

	jQuery("#studentremarks").css({fontSize:studentremarksFontSizeNew+'px','line-height':studentremarksLineHeight+'px','margin':studentremarksMargin+'px'});

	jQuery("#info").css({fontSize:studentyearsectionFontSizeNew+'px','line-height':studentyearsectionLineHeight+'px'});

	//console.log('showadsinterval',showadsinterval);

	//if(studentcontentMarginTop<0) {
		//studentcontentMarginTop = 20;
	//}

	//console.log('studentcontentMarginTop',studentcontentMarginTop);

	//console.log('contentleftWidth',contentleftWidth);

	//var studentPrevMaxCount = 3;

	var studentPrevMaxCount = parseInt(contentpreviousWidth / 150) - 1;

	//alert('studentPrevMaxCount: '+studentPrevMaxCount);

	jQuery(".studentprev").each(function(idx){
		studentprevCtr++;

		if(studentprevCtr<=studentPrevMaxCount) {
			studentprevWidth = studentprevWidth + jQuery(this).width();
		}
		//console.log('studentprevWidth',studentprevWidth);
		//console.log(studentprev,idx);
	});

	studentprevMargin = ((contentpreviousWidth - studentprevWidth) / studentPrevMaxCount) / 2;

	//alert('contentpreviousWidth: '+contentpreviousWidth+', studentprevWidth:'+studentprevWidth+', studentprevMargin: '+studentprevMargin);

	studentprevCtr = 0;

	var studentprevTotal = 0;

	jQuery(".studentprev").each(function(idx){
		studentprevTotal++;
		studentprevCtr++;
		if(studentprevCtr>studentPrevMaxCount) {
			jQuery(this).css({marginLeft:studentprevMargin,marginRight:studentprevMargin,clear:'left'});
			studentprevCtr = 1;
		} else {
			jQuery(this).css({marginLeft:studentprevMargin,marginRight:studentprevMargin});
		}
	});

	//var contentmidHeight = jQuery("#contentmid").height();

	//alert('studentprevTotal: '+studentprevTotal);
	//alert('contentTopLeftWidthNew: '+contentTopLeftWidthNew+', contentmidHeight: '+contentmidHeight);
	//alert('contentpreviousdivHeight: '+contentpreviousdivHeight);

	var studentphotobgPaddingTop = (contentleftHeightNew - studentphotobgHeight) / 2.5;

	//console.log('studentprev',jQuery("#studentprev").width());

	//console.log('studentprevWidth',studentprevWidth);

	/*jQuery("#info").html("width: "+width+", height: "+height);*/
	//jQuery("#contentmiddle").css({height:contentmiddleHeight});
	//jQuery("#studentphotobg").css({marginTop:studentphotobgMarginTop});
	//jQuery("#info").css({marginTop:infoMarginTop});
	//jQuery("#studentcontent").css({marginTop:studentcontentMarginTop});
	//jQuery("#contentprevious").css({marginTop:studentcontentMarginTop});
	//jQuery("#studentcontent").css({height:studentcontentHeight});
	//jQuery("#contentprevious").css({height:studentcontentHeight});
	jQuery("#studentphotobg").css({width:studentphotobgHeight,height:studentphotobgHeight,marginTop:studentphotobgPaddingTop});
	jQuery("#studentphoto").css({width:studentphotobgHeight,height:studentphotobgHeight});
	jQuery("#studentphoto img").css({width:studentphotobgHeight,height:studentphotobgHeight,"max-width":studentphotobgHeight,"max-height":studentphotobgHeight});

	var studentcontentdivHeight = jQuery("#studentcontentdiv").height();
	var studentcontentHeight  = jQuery("#studentcontent").height();
	var studentcontentdivPaddingTop = parseInt((studentcontentHeight - studentcontentdivHeight) / 2);

	//jQuery("#studentcontentdiv").css({paddingTop:studentcontentdivPaddingTop});

	srt.myForm.setItemValue('imagesize',studentphotobgHeight);

	jQuery('#studentphoto').html('<img src="/obislogo.php?size='+studentphotobgHeight+'" style="max-width:'+studentphotobgHeight+'px;max-height:'+studentphotobgHeight+'px" />');

	srt.studentphotobgHeight = studentphotobgHeight;

	setTimeout(function(){
		srt.setPrevious();
	},2000);

	setInterval(function(){
		//srt.setPrevious();
		jQuery("#body").css({opacity:1});
	},3000);

	setInterval(function(){
		srt.doMarquee();
	},90000);

	setInterval(function(){
		srt.doShowDateTime();
	},60000);

	srt.adsInterval = setInterval(function(){
		srt.doShowAds();
	},showadsinterval);

});
