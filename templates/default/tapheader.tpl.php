<?php
/*
*
* Author: Sherwin R. Terunez
* Contact: sherwinterunez@yahoo.com
*
* Description:
*
* Header template
*
*/

if(!defined('APPLICATION_RUNNING')) {
	header("HTTP/1.0 404 Not Found");
	die('access denied');
}

if(defined('ANNOUNCE')) {
	echo "\n<!-- loaded: ".__FILE__." -->\n";
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no"/>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
<meta http-equiv="X-UA-Compatible" content="IE=edge"/>
<title><?php $this->title(); ?></title>
<script>if (typeof module === 'object') {window.module = module; module = undefined;}</script>
<?php
do_action('action_meta_content_type');
do_action('action_meta_description');
do_action('action_meta_author');
do_action('action_meta_robots');
do_action('action_stylesheets');
do_action('action_scripts');
do_action('action_settings');
do_action('action_header_bottom');
?>
<?php /*
<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
<![endif]-->
*/ ?>
</head>
<body id="body" style="opacity:0">
<div id="myForm" style="margin-top:-10000px;margin-left:-10000px;height:100;"></div>
<style>

	body {
		/*background: #ccc url("/templates/default/tap/bg.jpg") no-repeat -100px -113px;*/
		background: #f7f7f7;
	}

	#contenttop {
		display: block;
		width: 100%;
		height: 100px;
		/*border: 1px solid #0f0;*/
		clear: both;
		margin: 0 auto;
		background: #14ade3;
	}

	#contentbottom {
		display: block;
		position: absolute;
		bottom: 0;
		width: 100%;
		height: 60px;
		/*border: 1px solid #0f0;*/
		margin: 0;
		padding: 0;
		background: #14ade3;
		opacity: 0.8;
	}

	#marquee {
		display: block;
		position: absolute;
		bottom: 0;
		width: 100%;
		height: auto;
		font-size: 40px;
		color: #ffffff;
		opacity: 1;
		z-index: 10000;
	}

	#contentmiddle {
		display: block;
		width: 100%;
		height: 100%;
		/*border: 1px solid #f00;*/
		clear: both;
		margin: 0 auto;
	}

	#contentleft {
		display: block;
		width: 35%;
		/*border: 1px solid #00f;*/
		float: left;
		height: 100%;
	}

	#contentmid {
		display: block;
		width: 35%;
		height: 100%;
		/*border: 1px solid #f00;*/
		float: left;
		/*padding: 30px 0 0 0;*/
	}

	#contentright {
		display: block;
		width: 30%;
		height: 100%;
		/*border: 1px solid #00f;*/
		float: right;
		/*margin-top: 35px;*/
	}

	#studentphotobg {
		display: block;
		width: 350px;
		height: 350px;
		/*border: 1px solid #f00;*/
		/*margin: 100px 0 0 50px;*/
		margin: 0 auto;
		background: url("/templates/default/tap/studentphoto.png?<?php echo time(); ?>") no-repeat 0px 0px;
		/*border: 1px solid #00f;*/
		overflow: hidden;
		-moz-box-shadow: 3px 7px 28px -2px #212121;
		-webkit-box-shadow: 3px 7px 28px -2px #212121;
		box-shadow: 3px 7px 28px -2px #212121;
	}

	#studentphoto {
		display: block;
		width: 350px; /* 351px by 350px */
		height: 350px;
		/*margin: 25px 0 0 29px;*/
		overflow: hidden;
		/*border: 1px solid #f00;*/
	}

	#studentphoto img {
		max-width: 350px; /* 351px by 350px */
		max-height: 350px;
	}

	#sidenumber {
		display: block;
		width: 266px;
		height: 180px;
		background: url("/templates/default/tap/sidenumber.png") no-repeat 0px 0px;
		overflow: hidden;
		/*border: 1px solid #f00;*/
		margin-top: -35px;
	}

	#sidenumber #title {
		display: block;
		width: 207px;
		height: 52px;
		line-height: 52px;
		/*border: 1px solid #f00;*/
		margin: 16px 0 0 23px;
		font-size: 30px;
		text-align: center;
		color: #ffffff;
	}

	/*#db,
	#in,
	#out,
	#late {
		display: block;
		width: 207px;
		height: 73px;
		line-height: 73px;
		margin: 3px 0 0 23px;
		font-size: 60px;
		text-align: center;
		color: #0b3c4d;
	}*/

	#type {
		display: block;
		height: auto;
	}

	#studentname {
		display: block;
		font-size: 45px;
		margin: 10px;
		clear: both;
		/*border: 1px solid #f00;*/
	}

	#studentyearsection {
		display: block;
		font-size: 35px;
		margin: 10px 10px;
	}

	#studentremarks {
		display: block;
		font-size: 35px;
		margin: 20px 10px 10px 10px;
	}

	#contenttopleft {
		display: block;
		width: 50%;
		height: auto;
		float: left;
	}

	#contenttopright {
		display: block;
		width: 50%;
		height: auto;
		float: right;
	}

	#currentdatetime {
		display: block;
		font-size: 25px;
		line-height: 100px;
		float: right;
		margin-right: 20px;
	}

	#schoolname {
		display: block;
		font-size: 30px;
		line-height: 100px;
		margin-left: 20px;
	}

	#contentprevious {
		display: block;
		border: 1px solid #f00;
		height: 110px;
		margin: 0 0 10px 0;
	}

	#studentprev {
		display: block;
		width: 100px;
		height: 100px;
		float: left;
		border: 1px solid #f00;
		margin: 5px;
	}

	#info {
		display: block;
		width: 350px;
		border: 1px solid #f00;
		clear:both;
		margin: 0 auto;
	}

	#info .label {
		display: block;
		float: left;
		width: auto;
		font-size: 20px;
		color: #000;
	}

	#info #db,
	#info #in,
	#info #out,
	#info #late {
		display: block;
		float: left;
		width: auto;
		font-size: 20px;
		color: #000;
	}

</style>
<div id="contenttop">
	<div id="contenttopleft">
		<div id="schoolname">ABC Montessori</div>
	</div>
	<div id="contenttopright">
		<div id="currentdatetime"></div>
	</div>
</div>
<div id="contentmiddle">
	<div id="contentleft">
		<div id="studentphotobg"><div id="studentphoto"></div></div>
		<div id="info">
			<div class="label">Database:</div>
			<div id="db">&nbsp;</div>
			<div class="label">IN:</div>
			<div id="in">&nbsp;</div>
			<div class="label">OUT:</div>
			<div id="out">&nbsp;</div>
			<div class="label">LATE:</div>
			<div id="late">&nbsp;</div>
			<br style="clear:both;" />
		</div>
	</div>
	<div id="contentmid">
		<div id="contentprevious">
			<div id="studentprev">#studentprev</div>
			<div id="studentprev">#studentprev</div>
			<div id="studentprev">#studentprev</div>
			<div id="studentprev">#studentprev</div>
		</div>
		<div id="studentname">#studentname&nbsp;</div>
		<div id="studentyearsection">#studentyearsection&nbsp;</div>
		<div id="studentremarks">#studentremarks&nbsp;</div>
	</div>
	<div id="contentright">
		<?php /*
		<div id="sidenumber">
		<div id="title">DB</div>
		<div id="db">&nbsp;</div>
		</div>
		<div id="sidenumber">
		<div id="title">IN</div>
		<div id="in">&nbsp;</div>
		</div>
		<div id="sidenumber">
		<div id="title">OUT</div>
		<div id="out">&nbsp;</div>
		</div>
		<div id="sidenumber">
		<div id="title">LATE</div>
		<div id="late">&nbsp;</div>
		</div>*/ ?>
		<div id="type">&nbsp;</div>
	</div>
	<br style="clear:both;" />
</div>
<div id="marquee">&nbsp;</div>
<div id="contentbottom">&nbsp;</div>
