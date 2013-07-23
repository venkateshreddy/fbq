<?php
include("../MessageSpec.class.php");

$url 			= 	"https://graph.facebook.com/act_368811234";
$request_type 	=	"GET";
$access_token	=	"kjhakjasdjasdhakduuwqiueqwemnamndbadjkasd";
$method_name	=	"adgroups";
$data			=	"";

$spec = new MessageSPec($url, $request_type, $access_token, $method_name, $data);
$message = $spec->getMessage();
echo $message;
?>