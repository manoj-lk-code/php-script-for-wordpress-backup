<?php
// Set current working directory
chdir(__DIR__);

// Get database credentials from wp-config.php
$wp_config_path = 'wp-config.php';

if (!file_exists($wp_config_path)) {
    exit("Error: $wp_config_path not found");
}

$wp_config_content = file_get_contents($wp_config_path);

preg_match("/define\(\s*['\"]DB_NAME['\"]\s*,\s*['\"](.*?)['\"]\s*\)/", $wp_config_content, $matches);
$db_name = $matches[1];

preg_match("/define\(\s*['\"]DB_USER['\"]\s*,\s*['\"](.*?)['\"]\s*\)/", $wp_config_content, $matches);
$db_user = $matches[1];

preg_match("/define\(\s*['\"]DB_PASSWORD['\"]\s*,\s*['\"](.*?)['\"]\s*\)/", $wp_config_content, $matches);
$db_password = $matches[1];

// Generate random string for file names
$random_string = uniqid();

// Generate file names
$db_file = "backup-wp-db-$random_string.sql";
$zip_file = "backup-wp-files-$random_string.zip";

// Start logging
$log = "Script started at " . date('Y-m-d H:i:s') . "\n";

// Export database
$cmd = "mysqldump --no-tablespaces -u $db_user -p$db_password $db_name > $db_file";
$log .= "Running WP database export command:\n" . preg_replace('/-p(.*?)\s+/', '-pxxxxxxxx ', $cmd) . "\n";
$output = shell_exec($cmd);
$log .= $output . "\n";

// Create zip backup of files
$cmd = "zip -r $zip_file *";
$log .= "Running zip backup command:\n$cmd\n";
$output = shell_exec($cmd);
$log .= $output . "\n";

// Remove database backup file
$cmd = "rm $db_file";
$log .= "Removing database backup file:\n$cmd\n";
$output = shell_exec($cmd);
$log .= $output . "\n";

// End logging
$log .= "Script finished at " . date('Y-m-d H:i:s') . "\n";
$log .= "Extract the zip file to see files and db sql file. \n";

// Output log to browser
echo "<pre>$log</pre>";
?>
