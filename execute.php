<?php
	$result = preg_split("#[\r\n]+#", shell_exec('ls'));
	$fp = fopen('status.json', 'w');
	fwrite($fp, json_encode($result));
	fclose($fp);