<?php

require('../vendor/autoload.php');

use Utility\FileLock;


$log = get_logger('database');


$database_url = getenv('DATABASE_URL');
if ($database_url === false) {
    $log->error("aborting: database url is not defined");
    exit(1);
}


$update_lock = new FileLock(Config::get('repository')['update_lock_file']);
if ($update_lock->is_locked()) {
    $log->warning("aborting: an update seems to be in progress already");
    exit(1);
}

$update_lock->lock();
if (count($argv) == 1) {
    Db\update_database($database_url);
    Db\update_image_urls();
}
else if (in_array("--database", $argv)) {
    Db\update_database($database_url);
}
else if (in_array("--image-urls", $argv)) {
    Db\update_image_urls();
}
$update_lock->unlock();
