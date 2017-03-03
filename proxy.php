<?php
if($_SERVER['REQUEST_METHOD']=='GET') {
	header("Location: index.php");
	exit();
}

$dest_host = base64_decode($_POST['dest_host']);
$dest_port = $_POST['dest_port'];
$is_ssl = $_POST['is_ssl'];
$key = $_POST['key'];

if($is_ssl == 'true') {
	if(in_array('ssl', stream_get_transports())) {
		$dest_host="ssl://".$dest_host;
	} else {
		$dest_port=80;
	}
}
$request = base64_decode($_POST['request_data']);

header("Content-type: application/octet-stream");
$fp = fsockopen($dest_host, $dest_port, $errno, $errstr, 30);
if (!$fp) {
	echo "$errstr ($errno)<br />\n";
} else {
	fwrite($fp, $request);
	while (!feof($fp)) {
		$buffer = fread($fp, 1024);
		$encryped_buffer='';
		for($i = 0; $i < strlen($buffer); $i++){
			$encryped_buffer.= chr(ord($buffer[$i]) + $key);
		}
		echo $encryped_buffer;
	}
	fclose($fp);
}
?>
