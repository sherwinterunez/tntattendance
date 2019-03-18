<?php
/*
*
* Author: Sherwin R. Terunez
* Contact: sherwinterunez@yahoo.com
*
* Date Created: April 23, 2017 12:32 AM
*
* Description:
*
* User Database Updates
*
*/

if(!defined('APPLICATION_RUNNING')) {
	header("HTTP/1.0 404 Not Found");
	die('access denied');
}

if(defined('ANNOUNCE')) {
	echo "\n<!-- loaded: ".__FILE__." -->\n";
}

/* INCLUDES_START */

global $appdb;

//MESSENGER - PAU
if(!$appdb->isColumnExist('tbl_studentprofile','studentprofile_messengerid')) {
	$appdb->query("alter table tbl_studentprofile add column studentprofile_messengerid text DEFAULT ''::text NOT NULL");
}
//MESSENGER - PAU
if(!$appdb->isColumnExist('tbl_studentprofile','studentprofile_verification')) {
	$appdb->query("alter table tbl_studentprofile add column studentprofile_verification text DEFAULT ''::text NOT NULL");
}

if(!$appdb->isColumnExist('tbl_studentprofile','studentprofile_schoolyear')) {
	$appdb->query("alter table tbl_studentprofile add column studentprofile_schoolyear text DEFAULT ''::text NOT NULL");
}

if(!$appdb->isColumnExist('tbl_studentprofile','studentprofile_schoolyearstart')) {
	$appdb->query("alter table tbl_studentprofile add column studentprofile_schoolyearstart integer NOT NULL DEFAULT 0");
}

if(!$appdb->isColumnExist('tbl_studentprofile','studentprofile_schoolyearend')) {
	$appdb->query("alter table tbl_studentprofile add column studentprofile_schoolyearend integer NOT NULL DEFAULT 0");
}

if(!$appdb->isColumnExist('tbl_studentprofile','studentprofile_studentmobileno')) {
	$appdb->query("alter table tbl_studentprofile add column studentprofile_studentmobileno text DEFAULT ''::text NOT NULL");
}

if(!$appdb->isColumnExist('tbl_smsoutbox','smsoutbox_latenoti')) {
	$appdb->query("alter table tbl_smsoutbox add column smsoutbox_latenoti integer NOT NULL DEFAULT 0");
}

if(!$appdb->isColumnExist('tbl_smsoutbox','smsoutbox_absentnoti')) {
	$appdb->query("alter table tbl_smsoutbox add column smsoutbox_absentnoti integer NOT NULL DEFAULT 0");
}

if(!$appdb->isColumnExist('tbl_smsoutbox','smsoutbox_failedcount')) {
	$appdb->query("alter table tbl_smsoutbox add column smsoutbox_failedcount integer NOT NULL DEFAULT 0");
}

if(!$appdb->isColumnExist('tbl_smsoutbox','smsoutbox_fbid')) {
	$appdb->query("alter table tbl_smsoutbox add column smsoutbox_fbid text DEFAULT ''::text NOT NULL");
	$appdb->query("alter table tbl_smsoutbox add column smsoutbox_sendfb integer NOT NULL DEFAULT 0");
	$appdb->query("alter table tbl_smsoutbox add column smsoutbox_sendfbstatus integer NOT NULL DEFAULT 0");
}

if(!$appdb->isTableExist('tbl_rfidqueue')) {
	$appdb->query("CREATE SEQUENCE tbl_rfidqueue_seq START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1");
	$appdb->query("CREATE TABLE tbl_rfidqueue (rfidqueue_id bigint DEFAULT nextval(('tbl_rfidqueue_seq'::text)::regclass) NOT NULL,rfidqueue_rfid text DEFAULT ''::text NOT NULL,rfidqueue_active integer NOT NULL DEFAULT 0,rfidqueue_deleted integer NOT NULL DEFAULT 0,rfidqueue_flag integer NOT NULL DEFAULT 0,rfidqueue_createstamp timestamp with time zone DEFAULT now())");
	$appdb->query("ALTER TABLE ONLY tbl_rfidqueue ADD CONSTRAINT tbl_rfidqueue_primary_key PRIMARY KEY (rfidqueue_id)");
}

if(!$appdb->isTableExist('tbl_rfidnotfound')) {
	$appdb->query("CREATE SEQUENCE tbl_rfidnotfound_seq START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1");
	$appdb->query("CREATE TABLE tbl_rfidnotfound (rfidnotfound_id bigint DEFAULT nextval(('tbl_rfidnotfound_seq'::text)::regclass) NOT NULL,rfidnotfound_rfid text DEFAULT ''::text NOT NULL,rfidnotfound_ip text DEFAULT ''::text NOT NULL,rfidnotfound_active integer NOT NULL DEFAULT 0,rfidnotfound_deleted integer NOT NULL DEFAULT 0,rfidnotfound_flag integer NOT NULL DEFAULT 0,rfidnotfound_createstamp timestamp with time zone DEFAULT now())");
	$appdb->query("ALTER TABLE ONLY tbl_rfidnotfound ADD CONSTRAINT tbl_rfidnotfound_primary_key PRIMARY KEY (rfidnotfound_id)");
}

if(!$appdb->isColumnExist('tbl_rfidqueue','rfidqueue_ip')) {
	$appdb->query("alter table tbl_rfidqueue add column rfidqueue_ip text DEFAULT ''::text NOT NULL");
}

if(!$appdb->isColumnExist('tbl_rfidqueue','rfidqueue_millitime')) {
	$appdb->query("alter table tbl_rfidqueue add column rfidqueue_millitime bigint NOT NULL DEFAULT 0");
}

if(!$appdb->isColumnExist('tbl_rfidqueue','rfidqueue_facerec')) {
	$appdb->query("alter table tbl_rfidqueue add column rfidqueue_facerec integer NOT NULL DEFAULT 0");
	$appdb->query("alter table tbl_rfidqueue add column rfidqueue_facehash text DEFAULT ''::text NOT NULL");
}

if(!$appdb->isColumnExist('tbl_studentdtr','studentdtr_ip')) {
	$appdb->query("alter table tbl_studentdtr add column studentdtr_ip text DEFAULT ''::text NOT NULL");
}

if(!$appdb->isColumnExist('tbl_studentdtr','studentdtr_kiosk')) {
	$appdb->query("alter table tbl_studentdtr add column studentdtr_kiosk text DEFAULT ''::text NOT NULL");
}

if(!$appdb->isColumnExist('tbl_upload','upload_facerec')) {
	$appdb->query("alter table tbl_upload add column upload_facerec integer NOT NULL DEFAULT 0");
}

if(!$appdb->isTableExist('tbl_client')) {
	$appdb->query("CREATE SEQUENCE tbl_client_seq START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1");
	$appdb->query("CREATE TABLE tbl_client (client_id bigint DEFAULT nextval(('tbl_client_seq'::text)::regclass) NOT NULL,client_name text DEFAULT ''::text NOT NULL,client_shortname text DEFAULT ''::text NOT NULL,client_info text DEFAULT ''::text NOT NULL,client_contactperson text DEFAULT ''::text NOT NULL,client_contactnumber text DEFAULT ''::text NOT NULL,client_population integer NOT NULL DEFAULT 0,client_license text DEFAULT ''::text NOT NULL,client_url text DEFAULT ''::text NOT NULL,client_vpnip text DEFAULT ''::text NOT NULL,client_publicip text DEFAULT ''::text NOT NULL,client_status integer NOT NULL DEFAULT 0,client_active integer NOT NULL DEFAULT 0,client_deleted integer NOT NULL DEFAULT 0,client_flag integer NOT NULL DEFAULT 0,client_createstamp timestamp with time zone DEFAULT now(),client_updatestamp timestamp with time zone DEFAULT now());");
	$appdb->query("ALTER TABLE ONLY tbl_client ADD CONSTRAINT tbl_client_primary_key PRIMARY KEY (client_id);");
}

if(!$appdb->isTableExist('tbl_clientnode')) {
	$appdb->query("CREATE SEQUENCE tbl_clientnode_seq START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1");
	$appdb->query("CREATE TABLE tbl_clientnode (clientnode_id bigint DEFAULT nextval(('tbl_clientnode_seq'::text)::regclass) NOT NULL,clientnode_clientid integer NOT NULL DEFAULT 0,clientnode_codename text DEFAULT ''::text NOT NULL,clientnode_name text DEFAULT ''::text NOT NULL,clientnode_info text DEFAULT ''::text NOT NULL,clientnode_url text DEFAULT ''::text NOT NULL,clientnode_vpnip text DEFAULT ''::text NOT NULL,clientnode_vpnport text DEFAULT ''::text NOT NULL,clientnode_publicip text DEFAULT ''::text NOT NULL,clientnode_localip text DEFAULT ''::text NOT NULL,clientnode_ccd text DEFAULT ''::text NOT NULL,clientnode_status integer NOT NULL DEFAULT 0,clientnode_active integer NOT NULL DEFAULT 0,clientnode_deleted integer NOT NULL DEFAULT 0,clientnode_flag integer NOT NULL DEFAULT 0,clientnode_createstamp timestamp with time zone DEFAULT now(),clientnode_updatestamp timestamp with time zone DEFAULT now());");
	$appdb->query("ALTER TABLE ONLY tbl_clientnode ADD CONSTRAINT tbl_clientnode_primary_key PRIMARY KEY (clientnode_id);");
}

if(!$appdb->isTableExist('tbl_monitor')) {
	$appdb->query("CREATE SEQUENCE tbl_monitor_seq START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1");
	$appdb->query("CREATE TABLE tbl_monitor (monitor_id bigint DEFAULT nextval(('tbl_monitor_seq'::text)::regclass) NOT NULL,monitor_clientid integer NOT NULL DEFAULT 0,monitor_codename text DEFAULT ''::text NOT NULL,monitor_name text DEFAULT ''::text NOT NULL,monitor_info text DEFAULT ''::text NOT NULL,monitor_url text DEFAULT ''::text NOT NULL,monitor_vpnip text DEFAULT ''::text NOT NULL,monitor_vpnport text DEFAULT ''::text NOT NULL,monitor_publicip text DEFAULT ''::text NOT NULL,monitor_localip text DEFAULT ''::text NOT NULL,monitor_ccd text DEFAULT ''::text NOT NULL,monitor_status integer NOT NULL DEFAULT 0,monitor_active integer NOT NULL DEFAULT 0,monitor_deleted integer NOT NULL DEFAULT 0,monitor_flag integer NOT NULL DEFAULT 0,monitor_createstamp timestamp with time zone DEFAULT now(),monitor_updatestamp timestamp with time zone DEFAULT now());");
	$appdb->query("ALTER TABLE ONLY tbl_monitor ADD CONSTRAINT tbl_monitor_primary_key PRIMARY KEY (monitor_id);");
}

if(!$appdb->isColumnExist('tbl_monitor','monitor_queued')) {
	$appdb->query("alter table tbl_monitor add column monitor_queued integer NOT NULL DEFAULT 0");
	$appdb->query("alter table tbl_monitor add column monitor_waiting integer NOT NULL DEFAULT 0");
	$appdb->query("alter table tbl_monitor add column monitor_sending integer NOT NULL DEFAULT 0");
	$appdb->query("alter table tbl_monitor add column monitor_sent integer NOT NULL DEFAULT 0");
	$appdb->query("alter table tbl_monitor add column monitor_failed integer NOT NULL DEFAULT 0");
}

if(!$appdb->isColumnExist('tbl_groupref','groupref_maxinout')) {
	$appdb->query("alter table tbl_groupref add column groupref_maxinout integer NOT NULL DEFAULT 0");
	$appdb->query("alter table tbl_groupref add column groupref_breakstarttime text DEFAULT ''::text NOT NULL");
	$appdb->query("alter table tbl_groupref add column groupref_breakendtime text DEFAULT ''::text NOT NULL");
}

if(!$appdb->isColumnExist('tbl_groupref','groupref_simassignment')) {
	$appdb->query("alter table tbl_groupref add column groupref_simassignment text DEFAULT ''::text NOT NULL");
}

if(!$appdb->isColumnExist('tbl_smsoutbox','smsoutbox_textassistid')) {
	$appdb->query("alter table tbl_smsoutbox add column smsoutbox_textassistid integer NOT NULL DEFAULT 0");
	$appdb->query("alter table tbl_smsoutbox add column smsoutbox_textassistip text DEFAULT ''::text NOT NULL");
}

if(!$appdb->isColumnExist('tbl_smsoutbox','smsoutbox_textassistsent')) {
	$appdb->query("alter table tbl_smsoutbox add column smsoutbox_textassistsent integer NOT NULL DEFAULT 0");
}

if(!$appdb->isColumnExist('tbl_smsoutbox','smsoutbox_textassistsynced')) {
	$appdb->query("alter table tbl_smsoutbox add column smsoutbox_textassistsynced integer NOT NULL DEFAULT 0");
}

if(!$appdb->isColumnExist('tbl_smsoutbox','smsoutbox_contactname')) {
	$appdb->query("alter table tbl_smsoutbox add column smsoutbox_contactname text DEFAULT ''::text NOT NULL");
}

if(!$appdb->isColumnExist('tbl_monitor','monitor_clientnodeid')) {
	$appdb->query("alter table tbl_monitor add column monitor_clientnodeid integer NOT NULL DEFAULT 0");
	$appdb->query("CREATE UNIQUE INDEX indx_monitor_clientnodeid ON tbl_monitor USING btree (monitor_clientnodeid)");
}

if(!$appdb->isColumnExist('tbl_monitor','monitor_updatestampunix')) {
	$appdb->query("alter table tbl_monitor add column monitor_updatestampunix integer NOT NULL DEFAULT 0");
}

/* INCLUDES_END */


#eof ./includes/userdbupdates.inc.php
