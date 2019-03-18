<?php

/* INCLUDES_START */

$memcached = false;

if(class_exists('Memcached')) {
	$memcached = new Memcached();

	if($memcached->addServer('127.0.0.1', 11211)) {
	} else {
		$memcached = false;
	}
}

/* INCLUDES_END */
