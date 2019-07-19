<?php

if (!defined('HS')) {
    die('Tidak boleh diakses langsung.');
}
// set time zone jakarta
date_default_timezone_set('Asia/Jakarta');
ini_set('date.timezone', 'Asia/Jakarta');

function prosesApiMessage($sumber)
{
    $updateid = $sumber['update_id'];
   // if ($GLOBALS['debug']) mypre($sumber);
    if (isset($sumber['message'])) {
        $message = $sumber['message'];

        // Welcome Message new user group
        if (isset($message['new_chat_member'])) {
            welcomeMessage($message); 
        }
        
        // pesan masuk
        if (isset($message['text'])) {
            prosesPesanTeks($message);
        } elseif (isset($message['sticker'])) {
            prosesPesanSticker($message);
        } else {
            // gak di proses silakan dikembangkan sendiri
        }
    }

    if (isset($sumber['callback_query'])) {
        prosesCallBackQuery($sumber['callback_query']);
    }

    return $updateid;
}

function prosesPesanSticker($message)
{
    // if ($GLOBALS['debug']) mypre($message);
}

function prosesPesanTeks($message)
{
    // if ($GLOBALS['debug']) mypre($message);

    $pesan = $message['text'];
    $chatid = $message['chat']['id'];
    $fromid = $message['from']['id'];
    $msg_reply_id = $message['message_id'];
    $namamu = $message['from']['first_name']; // variable penampung nama awal user
    // cek last_name
    if (isset($message['from']['last_name'])) { 
        $namaakir = $message['from']['last_name']; // variable penampung nama akir user
        $lengkap = $namamu . $namaakir; // variable penampung nama lengkap user
    } else {
        // jika tidak ada last name maka
        // Lengkap adalah nama depan anda
        $lengkap = $namamu;
    }
    // status dan info member dalam groip
    $status = getChatMember($chatid, $fromid);
    // mengamil info chat
    $chat_info = getChat($chatid);

    switch (true) {

    // pesan hanya untuk admin ------------------------------------------------------------
        // unpin pesan [ GROUP ONLY ]
        case $pesan == '/unpin':
        case $pesan == '!unpin':
            if ($chat_info->result->type == 'private') {
                sendApiAction($chatid);
                $text = "📛 Fitur ini tersedia hanya pada grup.";
                sendApiMsg($chatid, $text, $msg_reply_id == false, 'Markdown');
                return;
            }
            $status_bot = getChatMember($chatid, $GLOBALS['bot_id']);
            if ($status_bot->result->status !== 'administrator') {
                sendApiAction($chatid);
                $text = "❌ Error\n";
                $text.= "Saya tidak memiliki akses\n";
                sendApiMsg($chatid, $text, $msg_reply_id, 'HTML');
                return;
            } else {
                if (isset($status->result->can_pin_messages)) {
                    if ($status->result->can_pin_messages === false) {
                        sendApiAction($chatid);
                        $text = "❌ Error\n";
                        $text.= "Anda tidak memiliki akses, Perintah ini hanya untuk admin Silahkan Hubungi Admin.\n";
                        $text.= "Anda seorang admin ?, coba periksa status akses anda !";
                        sendApiMsg($chatid, $text, $msg_reply_id, 'HTML');
                        return;
                    }
                }
                if ($status->result->status !== 'administrator' AND $status->result->status !== 'creator') {
                    sendApiAction($chatid);
                    $text = "❌ Error\n";
                    $text.= "Anda tidak memiliki akses, Perintah ini hanya untuk admin Silahkan Hubungi Admin.\n";
                    $text.= "Anda seorang admin ?, coba periksa status akses anda !";
                    sendApiMsg($chatid, $text, $msg_reply_id, 'HTML');
                    return;
                } else {
                    if (isset($chat_info->result->pinned_message)) {
                        unpinMessage($chatid);
                        $text = "✅ Berhasil";
                        sendApiMsg($chatid, $text, $msg_reply_id, 'HTML');
                    } else {
                        sendApiAction($chatid);
                        $text = "Tidak ada pesan tersemat";
                        sendApiMsg($chatid, $text, $msg_reply_id, 'HTML');
                    }
                }
            }
            break;

        // menghapus tag [ GROUP ONLY ]
        case preg_match('/^\/untag #(\S.\S*)/i', $pesan, $hasil) : // untuk admin dan jajarannya
            if ($chat_info->result->type !== 'private') {
                if ($status->result->status !== 'administrator' AND $status->result->status !== 'creator') { // untuk admin dan jajarannya
                    sendApiAction($chatid);
                    $text = "❌ Error\n";
                    $text.= "Anda tidak memiliki akses, Perintah ini hanya untuk admin Silahkan Hubungi admin.\n";
                    sendApiMsg($chatid, $text, $msg_reply_id, 'HTML');
                    return;
                }    
            } else {
                sendApiAction($chatid);
                $text = "📛 Fitur ini tersedia hanya pada grup.";
                sendApiMsg($chatid, $text, $msg_reply_id == false, 'Markdown');
                return;
            }
            $tag = '#'.strtolower($hasil[1]);
            delHashtag($tag, $chatid, $msg_reply_id);                
            break;

        // pin message [ GROUP ONLY ]
        case $pesan == '/pin':
        case $pesan == '!pin':
            $status_bot = getChatMember($chatid, $GLOBALS['bot_id']);
            if ($chat_info->result->type == 'private') {
                sendApiAction($chatid);
                $text = "📛 Fitur ini tersedia hanya pada grup.";
                sendApiMsg($chatid, $text, $msg_reply_id == false, 'Markdown');
            } else {
                if (isset($message['reply_to_message'])) {
                    if ($status_bot->result->can_pin_messages == false) {
                        sendApiAction($chatid);
                        $text = "📛 Saya tidak memiliki akses\nPastikan saya sudah menjadi admin dan memiliki akses yang cukup";
                        sendApiMsg($chatid, $text, $msg_reply_id, 'HTML');
                        return;
                    } else {
                        if (isset($status->result->can_pin_messages)) {
                            if ($status->result->can_pin_messages === false) {
                                sendApiAction($chatid);
                                $text = "❌ Error\n";
                                $text.= "Anda tidak memiliki akses, Perintah ini hanya untuk admin Silahkan Hubungi Admin.\n";
                                $text.= "Anda seorang admin ?, coba periksa status akses anda !";
                                sendApiMsg($chatid, $text, $msg_reply_id, 'HTML');
                                return;
                            }
                        }
                        if ($status->result->status !== 'administrator' AND $status->result->status !== 'creator') { // untuk admin dan jajarannya
                            sendApiAction($chatid);
                            $text = "❌ Error\n";
                            $text.= "Anda tidak memiliki akses, Perintah ini hanya untuk admin Silahkan Hubungi Admin.\n";
                            $text.= "Anda seorang admin ?, coba periksa status akses anda !";
                            sendApiMsg($chatid, $text, $msg_reply_id, 'HTML');
                        } else {
                            $reply_id = $message['reply_to_message']['message_id'];
                            sendApiAction($chatid);
                            pinMessage($chatid, $reply_id);
                            $text = "✅ Berhasil";
                            sendApiMsg($chatid, $text, $msg_reply_id, 'HTML');
                        }
                    }
                } else {
                    sendApiAction($chatid);
                    $text = "📛 Reply sebuah pesan";
                    sendApiMsg($chatid, $text, $msg_reply_id, 'HTML');
                }
            }
            break;
        
        // inputTag tag le database [ GROUP ONLY ]
        case preg_match('/^\/tag (.*)$/i', $pesan, $hasil):
            if ($chat_info->result->type !== 'private') {
                if ($status->result->status !== 'administrator' AND $status->result->status !== 'creator') { // untuk admin dan jajarannya
                    sendApiAction($chatid);
                    $text = "❌ Error\n";
                    $text.= "Anda tidak memiliki akses, Perintah ini hanya untuk admin Silahkan Hubungi admin.\n";
                    sendApiMsg($chatid, $text, $msg_reply_id, 'HTML');
                    return;
                }    
            } else {
                sendApiAction($chatid);
                $text = "📛 Fitur ini tersedia hanya pada grup.";
                sendApiMsg($chatid, $text, $msg_reply_id == false, 'Markdown');
                return;
            }
            sendApiAction($chatid);
            $tag_id = date('YdmHis'); // id tag masuk database
            $tag = getTag($hasil[1]); #hashtag
            $tag_isi = explode($tag, $hasil[1]); // pecah pesan tag
            if (strstr($tag, '#') == false) {
                $text = "❌ Tag $tag belum berhasil dipasang.\n\n";
                $text.= "coba beberapa metode berikut\n";
                $text.= "1. masukan <code>/tag #nama pesan untuk tag ini</code>\n";
                $text.= "2. anda juga bisa <b>mereply</b> pesan dan masukan <code>/tag #nama</code>\n";
                sendApiMsg($chatid, $text, $msg_reply_id, 'HTML');
            } else {                
                if (isset($message['reply_to_message'])) {
                    $type = getTagType($message['reply_to_message']);
                    $tag_isi = getTagMessage($tag, $pesan, $message['reply_to_message']);
                    $file_id = getFile_id($message['reply_to_message']);
                } else {
                    // hasil tag pesan 1 line ex - /tag #nama_tag isidari tag
                    $type = 'hashtag'; // type tag masuk database
                    $tag_isi = $tag_isi[1];
                    $file_id = '0';
                }
            }
            // proses dan konfirmasi inputTag
            if (inputTag($tag_id, $chatid, $tag, $type, $tag_isi, $file_id) >= 0) {
                $text = "✅ Tag $tag terpasang.\n\n";
                $text.= $tag_isi;
                sendApiMsg($chatid, $text, $msg_reply_id, 'HTML');
            } else {
                $text = "❌ Tag $tag belum berhasil dipasang.\n\n";
                $text.= "coba beberapa metode berikut\n";
                $text.= "1. masukan <code>/tag #nama pesan untuk tag ini</code>\n";
                $text.= "2. anda juga bisa <b>mereply</b> pesan dan masukan <code>/tag #nama</code>\n";
                sendApiMsg($chatid, $text, $msg_reply_id, 'HTML');
            }
            break;
    // Pesan hanya untuk admin sampai sini -------------------------------------------------

    // kebawah pesan bukan admin / umum -----------------------------------------------------
        // menampilkan semua tag [ GROUP ONLY ]
        case $pesan == '/tags':
            if ($chat_info->result->type == 'private') {
                sendApiAction($chatid);
                $text = "📛 Fitur ini tersedia hanya pada grup.";
                sendApiMsg($chatid, $text, $msg_reply_id == false, 'Markdown');
            } else {            
                sendApiAction($chatid);
                $data = viewAllTag($chatid);
                $text = implode(' ', $data);
                sendApiMsg($chatid, $text, $msg_reply_id, 'Markdown');
            }
            break;

        // start [ PRIVATE ONLY ]
        case $pesan == '/start':
            if ($chat_info->result->type !== 'private') { // hanya untuk private
                sendApiAction($chatid);
                $text = "Perintah ini hanya tersedia untuk private.";
                $url = "https://t.me/".$GLOBALS['username_bot'];
                $inkeyboard = [
                    [
                        ['text' => 'Private message', 'url' => $url] // tombol menuju chat private
                    ]
                ];
                sendApiKeyboard($chatid, $msg_reply_id, $text, $inkeyboard, true);
            } else {            
                sendApiAction($chatid);
                $text = "Halo $namamu 👋\n";
                $text.= "Apa kabar? mau tau apa yang saya bisa ketik /help. Semoga saya bisa membantu dan semoga anda puas terimakasih 🙏";
                sendApiMsg($chatid, $text, $msg_reply_id, 'HTML');
            }
            break;

        // ping bot [ GROUP & PRIVATE ]
        case $pesan == '!ping':
        case $pesan == '/ping':
            pingMe($chatid, $msg_reply_id, $lengkap);
            break;

        // nampilin tag dari database [ GROUP ONLY ]
        // private bisa tapi tidak ada response
        case preg_match('/^#(\S.\S*)/', $pesan, $hasil):
            sendApiAction($chatid);
            $tag_name = strtolower($hasil[0]);
            viewHashtag($tag_name, $chatid, $msg_reply_id);
            break;
        
        // panduan menggunakan /tag [ GROUP & PRIVATE ]
        case $pesan == '/tag':
            sendApiAction($chatid);
            $text = "❌ Tag belum berhasil dipasang.\n\n";
            $text.= "coba beberapa metode berikut\n";
            $text.= "1. masukan <code>/tag #nama pesan untuk tag ini</code>\n";
            $text.= "2. anda juga bisa <b>mereply</b> pesan dan masukan <code>/tag #nama</code>\n";
            sendApiMsg($chatid, $text, $msg_reply_id, 'HTML');
            break;

        // random anime quotes [ GROUP & PRIVATE ]
        case $pesan == '/aniquote': 
            sendApiAction($chatid);
            $text = aniQuote()->body . "\n";
            $text.= "`~ " . aniQuote()->character . " - " . aniQuote()->anime . "`";
            $inkeyboard = [
                [
                    ['text' => 'Change 🔄', 'callback_data' => 'RandomAnime'] // tombol generate
                ]
            ];
            sendApiKeyboard($chatid, $msg_reply_id, $text, $inkeyboard, true);
            break;

        // random quotes [ GROUP & PRIVATE ]
        case $pesan == '/quote': 
            sendApiAction($chatid);
            $text = randomQuotes()->quote->body . "\n";
            $text.= "`~ " . randomQuotes()->quote->author . "`";
            $inkeyboard = [
                [
                    ['text' => 'Change 🔄', 'callback_data' => 'Random'] // tombol generate
                ]
            ];
            sendApiKeyboard($chatid, $msg_reply_id, $text, $inkeyboard, true);
            break;

        // Panduan penggunakan perintah !top [ GROUP & PRIVATE ]
        case $pesan == '!top':
            sendApiAction($chatid);
            $text = "Halo $lengkap 👋 silahkan pilih opsi berikut ini !\n";
            $text.= "<code>!top anime</code> melihat anime terbaik\n";
            $text.= "<code>!top manga</code> melihat manga terbaik\n";
            $text.= "<code>!top characters</code> melihat characters terbaik\n";
            sendApiMsg($chatid, $text, $msg_reply_id, 'HTML');
            break;

        // melihat karakter terpopuler dari MAL [ GROUP & PRIVATE ]
        case $pesan == '!top characters': 
                sendApiAction($chatid);
                $i = 1;
                $text = "Berikut Adalah daftar top characters di <b>myanimelist</b>\n";
                foreach (topCharacters() as $characters) {
                    $text.= "\n".$i++.". <a href='https://myanimelist.net/character/".$characters['mal_id']."'>" . $characters['title'] . "</a>\n";
                    $text.= "📌 <a href='https://myanimelist.net/anime/".$characters['animeography'][0]['mal_id']."'>".$characters['animeography'][0]['name']."</a>\n";
                    if (isset($characters['animeography'][1])) {
                        $text.= "📌 <a href='https://myanimelist.net/anime/".$characters['animeography'][1]['mal_id']."'>".$characters['animeography'][1]['name']."</a>\n";
                    }
                    if (isset($characters['animeography'][2])) {
                        $text.= "📌 <a href='https://myanimelist.net/anime/".$characters['animeography'][2]['mal_id']."'>".$characters['animeography'][2]['name']."</a>\n";
                    }
                }
                sendApiMsg($chatid, $text, $msg_reply_id, 'HTML');
            break;

        // melihat anime terpopuler dari MAL [ GROUP & PRIVATE ]
        case $pesan == '!top anime': 
                sendApiAction($chatid);
                $i = 1;
                $text = "Berikut Adalah daftar top anime di <b>myanimelist</b>\n";
                foreach (array_slice(topAnime(), 0, 10) as $anime) {
                    $text.= $i++.". <a href='https://myanimelist.net/anime/".$anime['mal_id']."'>" . $anime['title'] . "</a>\n";
                    $text.= "Tayang dari " . $anime['start_date'] . " - " . $anime['end_date'] . "\n\n";
                }
                sendApiMsg($chatid, $text, $msg_reply_id, 'HTML');
            break;

        // melihat manga terpopuler dari MAL [ GROUP & PRIVATE ]
        case $pesan == '!top manga': 
                sendApiAction($chatid);
                $i = 1;
                $text = "Berikut Adalah daftar top manga di <b>myanimelist</b>\n";
                foreach (array_slice(topmanga(), 0, 10) as $manga) {
                    $text.= $i++.". <a href='https://myanimelist.net/manga/".$manga['mal_id']."'>" . $manga['title'] . "</a>\n";
                    $text.= "Tayang dari " . $manga['start_date'] . " - " . $manga['end_date'] . "\n";
                    $text.= "[ Score ".$manga['score']." ]\n\n";
                }
                sendApiMsg($chatid, $text, $msg_reply_id, 'HTML');
            break;

        // arahan perintah /zodiak [ GROUP & PRIVATE ]
        case $pesan == "/zodiak";
            sendApiAction($chatid);
            
            $text = "Format yang anda masukan salah, Silahkan ikuti format berikut\n <code>/zodiak Nama_Anda DD-MM-YYYY</code> atau\n <code>/zodiak Nama DD-MM-YYYY</code> atau\n <code>/zodiak Nama-Anda DD-MM-YYYY</code>";
            
            sendApiMsg($chatid, $text, $msg_reply_id, 'HTML');
            break;

        // melihat zodiak dan info lahir [ GROUP & PRIVATE ]
        case preg_match("/^\/zodiak (.*)/i", $pesan, $hasil): // --- /zodiak {nama} {tanggal_lahir}
            sendApiAction($chatid);
            if (isset($hasil[1])) {
                $pecah = explode(' ', $hasil[1]);
                $nama = $pecah[0];
                $tgl = end($pecah);
                
                $text = zodiak($nama, $tgl);

                sendApiMsg($chatid, $text, $msg_reply_id, 'HTML');
            }
            break;

        // melihat id user dan group [ GROUP & PRIVATE ]
        case $pesan == '/id': 
            sendApiAction($chatid);
            $text = "🆔 User <code>$fromid</code>\n"; 
            $text.= "└ $lengkap\n\n";
            $text.= "🆔 Grup: <code>$chatid</code>\n";
            $text.= "├ " . $message['chat']['type'] . "\n";
            $text.= "├ @" . $message['chat']['username'] . "\n";
            if (isset($message['chat']['title'])) {
                $text.= "└ " . $message['chat']['title'] . "\n";
            }
            sendApiMsg($chatid, $text, $msg_reply_id, 'HTML');
            break;

        // echo pesan [ GROUP & PRIVATE ]
        case preg_match("/\/echo (.*)/", $pesan, $hasil):
            sendApiAction($chatid);
            $text = '*Echo:* '.$hasil[1];
            sendApiMsg($chatid, $text, $msg_reply_id, 'Markdown');
            break;

        default:
            // code...
            break;
    }
}
