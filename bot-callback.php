<?php 

    /*
    * bot-callback
    * handle callback untuk button
    */ 

    if (!defined('HS')) {
        die('Tidak boleh diakses langsung.');
    }

    function prosesCallBackQuery($message)
    {
        // if ($GLOBALS['debug']) mypre($message);

        $message_id = $message['message']['message_id'];
        $chatid = $message['message']['chat']['id'];
        $data = $message['data'];

        // tombol random quotes
        // di generate oleh perintah /quote
        if ($data == 'Random') { // random button
            $inkeyboard = [
                        [
                            ['text' => 'Change 🔄', 'callback_data' => 'Random'],
                        ]
                    ];
            $text = randomQuotes()->quote->body . "\n";
            $text.= "`~ " . randomQuotes()->quote->author . "`";
        }
        
        // tombol random quotes
        // di generate oleh perintah /aniquote
        if ($data == 'RandomAnime') { // random button
            $inkeyboard = [
                    [
                        ['text' => 'Change 🔄', 'callback_data' => 'RandomAnime'],
                    ]
                ];
            $text = aniQuote()->body . "\n";
            $text.= "`~ " . aniQuote()->character . " - " . aniQuote()->anime . "`";
        }

        editMessageText($chatid, $message_id, $text, $inkeyboard, true);

        $messageupdate = $message['message'];
        $messageupdate['text'] = $data;

        prosesPesanTeks($messageupdate);
    }