<?php 
    
    /*
    * mysqli database handle
    * delete, input, view, view all hashtag
    * masih sangat sederhana sekali
        keterangan 
        - nama database adalah = bot
        - dan nama tabel adalah = tags
    */ 

    if (!defined('HS')) {
        die('Tidak boleh diakses langsung.');
    }

	// koneksi
$conn = mysqli_connect('localhost', 'root', '', 'bot' /*nama tabel*/);
	if (!$conn) {
		echo "Gagal"; // jika gagal terkoneksi ke database
	}

    // menampilkan semua tag
    function viewAllTag($chatid)
    {
        global $conn;

        $sql = "SELECT * FROM tags WHERE chat_id = '$chatid' ORDER BY `tag_id` DESC";
        $query = mysqli_query($conn, $sql);
        $rows = [];
        while ($row = mysqli_fetch_assoc($query)) {
            $rows[] = $row['tag'];
        }

        return $rows;
    }

	// funngsi input ke database
    function inputTag($tag_id, $chatid, $tag, $type, $pesan, $file_id)
    {
    	global $conn;

    	$cek = mysqli_query($conn, "SELECT tag FROM tags WHERE chat_id = '$chatid' AND tag = '$tag'");
    	if (mysqli_affected_rows($conn) > 0) {
    		$query = mysqli_query($conn, "UPDATE tags SET type = '$type', pesan = '$pesan', file_id = '$file_id' WHERE tag = '$tag' AND chat_id = '$chatid'");
    		return mysqli_affected_rows($conn);
            // return mysqli_error($conn);
    	} else {		
	    	$sql = "INSERT INTO tags VALUES ('$tag_id', '$chatid', '$tag', '$type', '$pesan', '$file_id')";
	    	$query = mysqli_query($conn, $sql);
	    	return mysqli_affected_rows($conn);
            // return mysqli_error($conn);
    	}

    }
	
	// fungsi melihat hashtag
    function viewTag($tag, $chatid) 
    {
    	global $conn;
    	
    	$result = mysqli_query($conn, "SELECT * FROM tags WHERE tag = '$tag' AND chat_id = '$chatid'");
	    $rows = mysqli_fetch_assoc($result);

		return $rows;
    }

	// function menghapus tag
	function deleteTag($tag, $chatid)
    {
    	global $conn;

    	$result = mysqli_query($conn, "DELETE FROM tags WHERE chat_id = '$chatid' AND tag = '$tag'");
		return mysqli_affected_rows($conn);
    }