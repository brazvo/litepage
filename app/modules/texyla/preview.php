<?php

$cfg = isset($_POST["cfg"]) ? $_POST["cfg"] : null;

if ($cfg === "admin") {
	require_once dirname(__FILE__) . "/AdminTexy.php";
	$texy = new AdminTexy;

} elseif ($cfg === "forum") {
	require_once dirname(__FILE__) . "/ForumTexy.php";
	$texy = new ForumTexy;

} else {
	require_once dirname(__FILE__) . "/../../libs/class.Texy.php";
	$texy = new Texy;
}

header("Content-Type: text/html; charset=UTF-8");
$code = get_magic_quotes_gpc() ? stripslashes($_POST["texy"]) : $_POST["texy"];
echo $texy->process($code);
