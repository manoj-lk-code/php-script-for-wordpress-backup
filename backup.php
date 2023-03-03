<?php
ini_set('max_execution_time', 0); // set no timeout limit
$log_file = 'backup.log'; // set log file name and path

// get database credentials from wp-config.php file
$wp_config_file = 'wp-config.php';
$config = file_get_contents($wp_config_file);
preg_match("/'DB_NAME',\s*'([^']+)'/", $config, $db_name);
preg_match("/'DB_USER',\s*'([^']+)'/", $config, $db_user);
preg_match("/'DB_PASSWORD',\s*'([^']+)'/", $config, $db_pass);
$db_name = $db_name[1];
$db_user = $db_user[1];
$db_pass = $db_pass[1];

// define command to export WP database with current date and random hash in name
$backup_db_filename = 'Backup-wp-db-' . date('Y-m-d') . '-' . md5(uniqid()) . '.sql';
$export_cmd = "mysqldump --no-tablespaces -u $db_user -pxxxxxxxx $db_name > $backup_db_filename";
$export_cmd_display = "mysqldump --no-tablespaces -u $db_user -pxxxxxxxx $db_name > $backup_db_filename"; // command with password replaced for display

// define command to create backup archive with current date and random hash in name
$backup_zip_filename = 'backup-' . date('Y-m-d') . '-' . md5(uniqid()) . '.zip';
$zip_cmd = "zip -r $backup_zip_filename *";

// execute database export command and log output
$log = '';
$log .= "----- Starting backup at " . date('Y-m-d H:i:s') . " -----\n\n";
$log .= "Running WP database export command:\n";
$log .= "*masking the db password for security reasons...\n";
$log .= "$export_cmd_display\n\n";
$log .= str_replace($db_pass, 'xxxxxxxx', shell_exec($export_cmd . ' 2>&1')) . "\n"; // replace password with xxxxxxxx in output

// execute backup archive command and log output
$log .= "Running backup archive command:\n";
$log .= "$zip_cmd\n\n";
$log .= shell_exec($zip_cmd . ' 2>&1') . "\n";
$log .= "----- Backup completed at " . date('Y-m-d H:i:s') . " -----\n\n";

// write log to file and output to browser
file_put_contents($log_file, $log, FILE_APPEND); // append to log file
echo "<pre>$log</pre>";
