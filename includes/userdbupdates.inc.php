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

if(!$appdb->isColumnExist('tbl_studentprofile','studentprofile_schoolyear')) {
	$appdb->query("alter table tbl_studentprofile add column studentprofile_schoolyear text DEFAULT ''::text NOT NULL");
}

if(!$appdb->isColumnExist('tbl_studentprofile','studentprofile_schoolyearstart')) {
	$appdb->query("alter table tbl_studentprofile add column studentprofile_schoolyearstart integer NOT NULL DEFAULT 0");
}

if(!$appdb->isColumnExist('tbl_studentprofile','studentprofile_schoolyearend')) {
	$appdb->query("alter table tbl_studentprofile add column studentprofile_schoolyearend integer NOT NULL DEFAULT 0");
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

if(!$appdb->isTableExist('tbl_rfidqueue')) {
	$appdb->query("CREATE SEQUENCE tbl_rfidqueue_seq START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1");
	$appdb->query("CREATE TABLE tbl_rfidqueue (rfidqueue_id bigint DEFAULT nextval(('tbl_rfidqueue_seq'::text)::regclass) NOT NULL,rfidqueue_rfid text DEFAULT ''::text NOT NULL,rfidqueue_active integer NOT NULL DEFAULT 0,rfidqueue_deleted integer NOT NULL DEFAULT 0,rfidqueue_flag integer NOT NULL DEFAULT 0,rfidqueue_createstamp timestamp with time zone DEFAULT now())");
	$appdb->query("ALTER TABLE ONLY tbl_rfidqueue ADD CONSTRAINT tbl_rfidqueue_primary_key PRIMARY KEY (rfidqueue_id)");
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

if(!$appdb->isColumnExist('tbl_monitor','monitor_clientnodeid')) {
	$appdb->query("alter table tbl_monitor add column monitor_clientnodeid integer NOT NULL DEFAULT 0");
	$appdb->query("CREATE UNIQUE INDEX indx_monitor_clientnodeid ON tbl_monitor USING btree (monitor_clientnodeid)");
}

if(!$appdb->isColumnExist('tbl_monitor','monitor_updatestampunix')) {
	$appdb->query("alter table tbl_monitor add column monitor_updatestampunix integer NOT NULL DEFAULT 0");
}

/* INCLUDES_END */


#eof ./includes/userdbupdates.inc.php
