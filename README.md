# PHP Script for WordPress Backup and Restore.

This PHP script allows you to generate a backup of your WordPress website and restore it when needed. Please note that shell execution needs to be enabled for PHP.

## How to use:

To use this script, please follow these steps:

1. Upload the `backup.php` file to your WordPress instance directory.
2. In the terminal, run the following command: `php backup.php`. Alternatively, you can visit `YourDomain.com/backup.php`.
3. Once the backup file is generated, download it to your local computer.

To restore the backup to a new host, please follow these steps:

4. Install a fresh WordPress on your new host. This will allow the `restore.php` file to use the database details to restore the backup.
5. Upload the locally downloaded backup file to the same directory where you installed the new WordPress. Alternatively, you can use `wget` to download the backup file.
6. Upload the `restore.php` file to the same directory.
7. In the terminal, run the following command: `php restore.php`.

**Your restoration should now be complete.** 

Please note that you will need to update your DNS records to point to the new host. Once the changes have propagated globally, your site should be accessible.

If you find this script useful, please feel free to share it with your friends.

#
#
#

## How it works:

### What `backup.php` does

When executed, `backup.php` performs the following tasks:

1. Extracts the database credentials from the `wp-config.php` file.
2. Exports the database with the file name `backup-wp-db-$random_string.sql`.
3. Compresses all files with the file name `backup-wp-files-$random_string.zip` in the same directory. 
4. Deletes the SQL file, as it is no longer needed and is already compressed under the zip file.

### What `restore.php` does

When executed, `restore.php` performs the following tasks:

1. Renames the `wp-config.php` file to `old-wp-config.php`.
2. Deletes all files and folders that start with `wp-`, as well as the `index.php` and `xmlrpc.php` files.
3. Extracts `backup-wp-files-$random_string.zip` to the same directory.
4. Updates the `wp-config.php` file with the database credentials from the `old-wp-config.php` file.
5. Drops all tables from the fresh WordPress instance database and imports the new database SQL with the file name `backup-wp-db-$random_string.sql`.

That's it! Although code to remove the zip and SQL files is not included, you can manually remove them as they are no longer needed.

# Credits

This code is created using ChatGPT and inspired from the article by [Manoj](https://manojlk.work): Read article: [How to migrate WordPress manually but faster & better.](https://wpzonify.com/how-to-migrate-your-wordpress-site-manually/)
