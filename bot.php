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

/* ----------------- MULAI LOOPING
    Jika tidak ada pesan masuk ditandai -
    Jika ada pesan masuk pada console ditandai +
    credit halimlab - faisal halim
*/

// metode loong poling
function myloop()
{
    global $debug;

    $idfile = 'botposesid.txt';
    $update_id = 0;

    if (file_exists($idfile)) {
        $update_id = (int) file_get_contents($idfile);
        echo '-';
    }

    $updates = getApiUpdate($update_id);
    
    foreach ($updates as $message) {
        $update_id = prosesApiMessage($message);
        echo '+';
    }
    file_put_contents($idfile, $update_id + 1);
}

while (true) {
    myloop();
}
