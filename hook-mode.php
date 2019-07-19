<?php

define('HS', true);

require_once 'bot-config.php';
require_once 'bot-fungsi.php';
require_once 'bot-database.php';
require_once 'bot-callback.php';
require_once 'bot-fitur.php';
require_once 'bot-hashtag.php';
require_once 'bot-msg-help.php';
require_once 'bot-proses.php';

$entityBody = file_get_contents('php://input');
$message = json_decode($entityBody, true);
prosesApiMessage($message);
