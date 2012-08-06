<?php
date_default_timezone_set('PRC');
define('DEBUG', 'DEBUG');
define('INFO', 'INFO');
define('ERROR', 'ERROR');
define('FATAL', 'FATAL');
define('LOG_FILE', dirname(__FILE__).'/../log/background.log');
function logging($level, $message, $log_file) {
	
    $now = date('Y-m-d H:i:s');
    $line = "<$level> [$now] $message\n";
    if ($log_file) {
        error_log($line, 3, $log_file);
    }
    // echo $line;
}

?>
