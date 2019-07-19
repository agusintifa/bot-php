<?php 
	
	/*
	* file untuk handle hashtag
	* menampilkan, input ke database, delete, view all hashtag
	*/ 

	if (!defined('HS')) {
	    die('Tidak boleh diakses langsung.');
	}

	// menghapus hashtag
	function delHashtag($tag, $chatid, $msg_reply_id)
	{
		sendApiAction($chatid);
		if (deleteTag($tag, $chatid) >= 0) {
			$text = "☑️ Tag $tag Terhapus";
			sendApiMsg($chatid, $text, $msg_reply_id, 'HTML');
		}
	}

	// menampilkan hashtag command #nama, #pesan_penting
	function viewHashtag($tag, $chatid, $msg_reply_id)
	{

	    global $conn; // didapat dari file database 

	    $sumber = $tag;

	    if (isset($sumber)) {

	        if (viewTag($sumber, $chatid)['type'] == "hashtag") {
	            sendApiAction($chatid);
	            $text = viewTag($sumber, $chatid)['pesan'];
	            sendApiMsg($chatid, $text, $msg_reply_id, 'HTML');
	        } 
	        if (viewTag($sumber, $chatid)['type'] == "photo") {
	            sendApiAction($chatid, 'upload_photo');
	            $file_id = viewTag($sumber, $chatid)['file_id'];
	            $text = viewTag($sumber, $chatid)['pesan'];
	            sendApiPhoto($chatid, $file_id, $text, $msg_reply_id);
	        } 
	        if (viewTag($sumber, $chatid)['type'] == "audio") {
	            sendApiAction($chatid, 'upload_audio');
	            $file_id = viewTag($sumber, $chatid)['file_id'];
	            $text = viewTag($sumber, $chatid)['pesan'];
	            sendApiAudio($chatid, $file_id, $text, $msg_reply_id);
	        }
	        if (viewTag($sumber, $chatid)['type'] == "application") {
	            sendApiAction($chatid, 'upload_document');
	            $file_id = viewTag($sumber, $chatid)['file_id'];
	            $text = viewTag($sumber, $chatid)['pesan'];
	            sendApiDocument($chatid, $file_id, $text, $msg_reply_id);
	        }
	        if (viewTag($sumber, $chatid)['type'] == "video") {
	            sendApiAction($chatid, 'upload_video');
	            $file_id = viewTag($sumber, $chatid)['file_id'];
	            $text = viewTag($sumber, $chatid)['pesan'];
	            sendApiVideo($chatid, $file_id, $text, $msg_reply_id);
	        }
			if (viewTag($sumber, $chatid)['type'] == "animation") {
	            sendApiAction($chatid, 'upload_video');
	            $file_id = viewTag($sumber, $chatid)['file_id'];
	            $text = viewTag($sumber, $chatid)['pesan'];
	            sendApiAnimation($chatid, $file_id, $text, $msg_reply_id);
	        }
	    }

	}

	// get nama tag
	function getTag($hasil_regex)
	{
	    $tag = explode(' ', $hasil_regex);
	    $tag = $tag[0]; // untag nama tag #namatag
	    $tag = strtolower($tag);
	    return $tag;
	}

	// type hashtag sebelum nanti di input atau di tampilkan
	function getTagType($pesan_reply)
	{
	    if (isset($pesan_reply['video'])) { // video
	        $type = "video"; // type untuk database
	    }
	    if (isset($pesan_reply['document'])) { // dokumen
	        $type = "application"; // type untuk database
	    }
	    if (isset($pesan_reply['animation'])) { // animasi gif
	        $type = "animation"; // type untuk database
	    }
	    if (isset($pesan_reply['audio'])) { // audio
	        $type = "audio"; // type untuk database
	    }
	    if (isset($pesan_reply['photo'])) { // photo
	        $type = "photo"; // type untuk database
	    }
	    if (isset($pesan_reply['text'])) { // text
	        $type = "hashtag"; // type untuk database
	    }
	    return $type;
	}

	// file id jika mereply media di group dan di input
	function getFile_id($pesan_reply)
	{
	    $file_id = '0';

	    if (isset($pesan_reply['photo'])) { // untuk gambar
	        $file_id = end($pesan_reply['photo']);
	        $file_id = $file_id['file_id'];
	    }
	    if (isset($pesan_reply['audio'])) { // untuk audio
	        $file_id = $pesan_reply['audio'];
	        $file_id = $file_id['file_id'];
	    }
	    if (isset($pesan_reply['animation'])) { // untuk animation
	        $file_id = $pesan_reply['animation'];
	        $file_id = $file_id['file_id'];
	    }
	    if (isset($pesan_reply['document'])) { // untuk document
	        $file_id = $pesan_reply['document'];
	        $file_id = $file_id['file_id'];
	    }
	    if (isset($pesan_reply['video'])) { // untuk video
	        $file_id = $pesan_reply['video'];
	        $file_id = $file_id['file_id'];
	    }
	    return $file_id;
	}

	// mendapatkan pesan hashtag sebelum di input
	function getTagMessage($tag, $pesan, $pesan_reply)
	{
        $tag_name = '/tag '.$tag;
        $pecah = explode($tag_name, $pesan);
		$tag_isi = $pecah[1];

		if (isset($pesan_reply['text'])) { // untuk reply text
			if ($tag_isi == '') {
				$tag_isi = $pesan_reply['text'];
			} elseif(!$tag_isi == '') {
				$tag_isi = $tag_isi;
			}
		}

		if (isset($pesan_reply['caption'])) { // untuk reply media yang ada captionnya
			if ($tag_isi == '') {
				$tag_isi = $pesan_reply['caption'];
			} else {
				$tag_isi = $tag_isi;
			}
		}

	    return $tag_isi;
	}