<?php
// http://www.lornajane.net/posts/2012/php-5-4-built-in-webserver
if (file_exists($_SERVER['SCRIPT_FILENAME'])) {
	return false; // serve the requested resource as-is.
}
include_once 'index.php';
