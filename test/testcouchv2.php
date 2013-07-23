<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');


require_once("../fbmessagepublisher.php");
require_once("../includes/couch_db_vars.php");
require_once("../includes/amqp_vars.php");

//test details
$details['post']['url']				= 	"https://graph.facebook.com/act_105496672875561";
$details['post']['request_type'] 	=	"post";
$details['post']['account_id'] 		=	"act_105496672875561";
$details['post']['access_token']	=	"CAABbC2IvUFsBAEwDOeOZCZConVfEoSzDA7T0BF5sZAn1EGdZBZBQLcygLOIZBZB4ZAxfRi04BDSfpvusZCOftqkAZAGWJwBzE4QbBWJ3aaLKaneOAJwQRqPRC11eJvlxA1mSpAZCvVfwFALRmlaBgAt9zDlCJNMkuWgLr4ZD";
$details['post']['method_name']		=	"adcampaigns";
$details['post']['data']			=	"name=testcampaign-23-05&campaign_status=2&redownload=1&daily_budget=1000";

//test details
$details['get']['url']				= 	"https://graph.facebook.com/act_105496672875561";
$details['get']['account_id'] 		=	"act_105496672875561";
$details['get']['request_type'] 	=	"get";
$details['get']['access_token']		=	"CAABbC2IvUFsBAEwDOeOZCZConVfEoSzDA7T0BF5sZAn1EGdZBZBQLcygLOIZBZB4ZAxfRi04BDSfpvusZCOftqkAZAGWJwBzE4QbBWJ3aaLKaneOAJwQRqPRC11eJvlxA1mSpAZCvVfwFALRmlaBgAt9zDlCJNMkuWgLr4ZD";
$details['get']['method_name']		=	"adcampaigns";
$details['get']['data']				=	"";


$request = "get";
extract($_REQUEST);
extract($details[$request]);

$message_publisher = FacebookMessagePublisher::getInstance($couchbase['test'],$amqp['test']); 

$token = $message_publisher->publishMessage($details[$request]);

$response = null;
$i = 1;
do{
	sleep(3);
	$docWithResponse = json_decode($message_publisher->retrieveResponse($token));
	if(is_object($docWithResponse) && isset($docWithResponse->response))
		$response = $docWithResponse->response;
}while(is_null($response));
echo "<pre>";
print_r($response);
echo "</pre>";
?>
