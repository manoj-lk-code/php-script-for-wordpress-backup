<?php

ini_set('max_execution_time', 0);
date_default_timezone_set('UTC');
$log_file = 'restore.log';

// Rename wp-config.php to old-wp-config.php
rename('wp-config.php', 'old-wp-config.php');

// Delete unwanted files and folders
exec('rm -rf index.php xmlrpc.php wp-*');

// Extract zip file
exec('unzip backup-wp-files-*.zip');

// Read database details from old-wp-config.php
$old_config = file_get_contents('old-wp-config.php');
preg_match("/define\(\s*'DB_NAME',\s*'(.+)'\s*\);/", $old_config, $db_name_match);
preg_match("/define\(\s*'DB_USER',\s*'(.+)'\s*\);/", $old_config, $db_user_match);
preg_match("/define\(\s*'DB_PASSWORD',\s*'(.+)'\s*\);/", $old_config, $db_password_match);
preg_match("/define\(\s*'DB_HOST',\s*'(.+)'\s*\);/", $old_config, $db_host_match);

// Update wp-config.php with database details
$config_file = file_get_contents('wp-config.php');
$config_file = preg_replace("/define\(\s*'DB_NAME',\s*'(.+)'\s*\);/", "define('DB_NAME', '{$db_name_match[1]}');", $config_file);
$config_file = preg_replace("/define\(\s*'DB_USER',\s*'(.+)'\s*\);/", "define('DB_USER', '{$db_user_match[1]}');", $config_file);
$config_file = preg_replace("/define\(\s*'DB_PASSWORD',\s*'(.+)'\s*\);/", "define('DB_PASSWORD', '{$db_password_match[1]}');", $config_file);
$config_file = preg_replace("/define\(\s*'DB_HOST',\s*'(.+)'\s*\);/", "define('DB_HOST', '{$db_host_match[1]}');", $config_file);
file_put_contents('wp-config.php', $config_file);

// Drop all tables in database
$drop_cmd = "mysql -u {$db_user_match[1]} -p{$db_password_match[1]} -h {$db_host_match[1]} -e 'show tables' --silent {$db_name_match[1]} | xargs -I\"@@\" mysql -u {$db_user_match[1]} -p{$db_password_match[1]} -h {$db_host_match[1]} -e 'DROP TABLE IF EXISTS @@' {$db_name_match[1]} 2>&1";
$drop_output = shell_exec($drop_cmd);
file_put_contents($log_file, "Drop Command Output: \n{$drop_output}\n\n", FILE_APPEND);

// Import database from SQL file
$sql_file = shell_exec('ls backup-wp-db-*.sql');
$import_cmd = "mysql -u {$db_user_match[1]} -p{$db_password_match[1]} -h {$db_host_match[1]} {$db_name_match[1]} < {$sql_file} 2>&1";
$import_output = shell_exec($import_cmd);
file_put_contents($log_file, "Database has been imported. Please ingore MySQL insecure password warning. \n{$import_output}\n\n", FILE_APPEND);

// Log execution
$timestamp = date('Y-m-d H:i:s');
$log_message = "Executed restore script on {$timestamp}\n";
file_put_contents($log_file, $log_message, FILE_APPEND);

echo "Restoration completed.\n";

?>
