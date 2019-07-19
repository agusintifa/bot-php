<?php 

    /*
    * handle fitur yang ada didalam bot
    * bisa lihat sendiri berdasarkan fungsi dibawah ini
    */ 

    if (!defined('HS')) {
        die('Tidak boleh diakses langsung.');
    }

    // welcome message group group
    function welcomeMessage($message)
    {
        $chatid = $message['chat']['id'];
        $fromid = $message['from']['id'];
        $msg_reply_id = $message['message_id'];
        $namamu = $message['from']['first_name']; // variable penampung nama awal user
        // cek last name
        if (isset($message['from']['last_name'])) { 
            $namaakir = $message['from']['last_name']; // variable penampung nama akir user
            $namaLengkap = $namamu . $namaakir; // variable penampung nama lengkap user
        } else {
            // jika tidak ada last name maka
            // namaLengkap adalah nama depan anda
            $namaLengkap = $namamu;
        }

        // cek apakan ada user baru atau tidak
        if (isset($message['new_chat_member'])) {
            if (isset($message['new_chat_member']['username'])) { // pengecekan username
                sendApiAction($chatid);
                $text = "Hai <a href='tg://user?id=". $message['new_chat_member']['id'] ."'>". $message['new_chat_member']['first_name'] ."</a> 👋 \n";
                $text.= "Selamat datang di grup 👥 @". $message['chat']['username'];
                sendApiMsg($chatid, $text, $msg_reply_id, 'HTML');
            } else { // --------- jika username user belum ada tampilkan pesan dibawah ini
                sendApiAction($chatid);
                $text = "Hai <a href='tg://user?id=". $message['new_chat_member']['id'] ."'>". $message['new_chat_member']['first_name'] ."</a> 👋 \n";
                $text.= "Selamat datang di grup 👥 @". $message['chat']['username'];
                sendApiMsg($chatid, $text, $msg_reply_id, 'HTML');
            }

        }
    }


    // ping command /ping
    function pingMe($chatid, $msg_reply_id, $nama)
    {
        $time = explode(' ', microtime());
        $time = $time[1] + $time[0];
        $start = $time;
        sendApiAction($chatid);
        $time = explode(' ', microtime());
        $time = $time[1] + $time[0];
        $finish = $time;
        $total_time = round(($finish - $start), 2);
        $text = "🏓 Pong !\n";
        $text.= "Sekitar `$total_time Seconds`";
        sendApiMsg($chatid, $text, $msg_reply_id, 'Markdown');
    }

    // random anime quote
    function aniQuote()
    {
        $data = file_get_contents('https://myweb-api.000webhostapp.com/api/aniquote');
        $data = json_decode($data);
        return $data->data;
    }

    // fungsi top karakter
    function topCharacters() 
    {
        // fitur
        // mengambil karakter terbaik dari myanimelist
        // karakter yang di ambil tergantung pada user vote
        // user vote di web mynimelist
        $data = file_get_contents('https://api.jikan.moe/v3/top/characters');
        $data = json_decode($data, true);
        $data = array_slice($data['top'], 0, 10);

        return $data;
    }

    // fungsi top manga
    function topManga()
    {
        $data = file_get_contents('https://api.jikan.moe/v3/top/manga');
        $data = json_decode($data, true);
        $data = array_slice($data['top'], 0, 10);

        return $data;
    }

    // fungsi top anime
    function topAnime()
    {
        $data = file_get_contents('https://api.jikan.moe/v3/top/anime');
        $data = json_decode($data, true);
        $data = array_slice($data['top'], 0, 10);

        return $data;
    }

    // fungsi random quotes
    function randomQuotes()
    {
        $url = 'https://favqs.com/api/qotd'; // url api
        $data = file_get_contents($url);
        $data = json_decode($data);

        return $data;
    }

    // fungsi zodiak
    function zodiak($nama, $tgl)
    {
        // fitur
        // + melihat zodiak
        // + melihat hari jawa pada tanggal lahit [ ex : [ sabtu pon, jumat pahing, senin kliwon ]
        // + melihat ulangtahun kurang berapa hari
        // + melihat zodiak
        $nama = str_replace(" ", "-", $nama);
        $url = htmlspecialchars_decode('https://script.google.com/macros/exec?service=AKfycbw7gKzP-WYV2F5mc9RaR7yE3Ve1yN91Tjs91hp_jHSE02dSv9w&nama='.$nama.'&tanggal='.$tgl.'');
        $sumber = file_get_contents($url);
        $sumber = json_decode($sumber);
        $nama = str_replace("-", " ", $sumber->data->nama);

        $result = 'Nama <b>' . $nama . "</b>\n";
        $result.= 'Lahir pada  <b>' . $sumber->data->lahir . "</b>\n";
        $result.= 'Usia <b>' . $sumber->data->usia . "</b>\n";
        $result.= 'ulang tahun <b>' . $sumber->data->ultah . "</b> lagi \n";
        $result.= 'Zodiak <b>' . $sumber->data->zodiak . "</b>\n";

        if ($sumber->status == "success") {
            return $result;
        } else {
            return "Ops nampaknya ada yang salah\nPastikan anda sudah mengikuti format berikut\n<code>/zodiak Nama_Anda DD-MM-YYYY</code> atau\n <code>/zodiak Nama DD-MM-YYYY</code> atau\n <code>/zodiak Nama-Anda DD-MM-YYYY</code>";
        }
    }