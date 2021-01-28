<?php

/*
##### Created a shell script names starlink.update. make sure to change the path to your install
#### I then run this in screen in the background.

#/bin/bash
while true ; do php /var/www/html/btnwi/scripts/cron/php/dishy.cron.php & printf . & sleep 1; done

*/

require(dirname(__FILE__).'/../../../config.inc.php');
if(strtoupper($_CONFIG["dishy"]['update_method']) == "CLI"){
	
	$t = shell_exec($_CONFIG['dishy']['get_status']);
	// This appends maxspeeds (peak) to the dishy json result along with method used
	
	$maxspeedfile = $_CONFIG['files']['maxspeed'];
	$maxspeeds = unserialize(file_get_contents($maxspeedfile));
	$status = json_decode($t,true);

	$max["down"] = $status["dishGetStatus"]["downlinkThroughputBps"];
	$max["up"] = $status["dishGetStatus"]["uplinkThroughputBps"];

	if($maxspeeds){		
		if($maxspeeds['down'] < $max["down"]){
			$maxspeeds['down'] = $max['down'];
		}
		if($maxspeeds['up'] < $max["up"]){
			$maxspeeds['up'] = $max['up'];
		}
		file_put_contents($maxspeedfile,serialize($maxspeeds));
	} else {
		file_put_contents($maxspeedfile,serialize($max));
	}

	$status['dishGetStatus']['deviceInfo']['id'] = 'private';
	file_put_contents($_CONFIG['files']['dishy'],json_encode($status, JSON_PRETTY_PRINT));
}