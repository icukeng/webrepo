<?php
	$content = json_decode(file_get_contents($argv[1]), true);
	$result = shell_exec('ls');
	$fp = fopen($content['name'] . '-status.log', 'w');
	fwrite($fp, $result);
	// test
	$test = shell_exec('sleep 5s; ls -la');
	fwrite($fp, $test);
	fclose($fp);