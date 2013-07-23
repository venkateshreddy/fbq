<?php
require_once("../includes/MessageSpec.class.php");
require_once("../includes/CouchBase.class.php");
require_once("../includes/PangeaAmqp.class.php");
require_once("../includes/couch_db_vars.php");
require_once("../includes/amqp_vars.php");


//test details
$details['post']['url']				= 	"https://graph.facebook.com/act_105496672875561";
$details['post']['request_type'] 	=	"post";
$details['post']['account_id'] 		=	"act_105496672875561";
$details['post']['access_token']	=	"CAABbC2IvUFsBAEwDOeOZCZConVfEoSzDA7T0BF5sZAn1EGdZBZBQLcygLOIZBZB4ZAxfRi04BDSfpvusZCOftqkAZAGWJwBzE4QbBWJ3aaLKaneOAJwQRqPRC11eJvlxA1mSpAZCvVfwFALRmlaBgAt9zDlCJNMkuWgLr4ZD";
$details['post']['method_name']		=	"adcampaigns";
$details['post']['data']			=	urlencode("name=campaign-".date('Ymd')."&daily_budget=1000&campaign_status=2");

//test details
$details['get']['url']				= 	"https://graph.facebook.com/act_105496672875561";
$details['get']['account_id'] 		=	"act_105496672875561";
$details['get']['request_type'] 	=	"get";
$details['get']['access_token']		=	"CAABbC2IvUFsBAEwDOeOZCZConVfEoSzDA7T0BF5sZAn1EGdZBZBQLcygLOIZBZB4ZAxfRi04BDSfpvusZCOftqkAZAGWJwBzE4QbBWJ3aaLKaneOAJwQRqPRC11eJvlxA1mSpAZCvVfwFALRmlaBgAt9zDlCJNMkuWgLr4ZD";
$details['get']['method_name']		=	"adcampaigns";
$details['get']['data']				=	urlencode("");


if(count($argv) < 2)
	die("please specify the request type \n");

if($argv[1] != "get" && $argv[1] != "post")
{
	die("not valid input \n");
}

extract($details[$argv[1]]);

echo date('Y:m:d H:i:s')."\n";
//***************** getting the message in json format ******************
$spec = new MessageSpec($url, $request_type, $access_token, $method_name, $data);
$message = $spec->getMessage();
//**************************************************************************


//****************************** Couchbase Part ****************************
$pcb = new PangeaCouchBase($couchbase['test']['host'], 
					$couchbase['test']['username'], 
					$couchbase['test']['password'], 
					$couchbase['test']['bucket']);

$token = md5(time());
$pcb->insertDocument($token, $message);
$a = $pcb->getDocument($token);
//****************************************************************************

//message that ha to be sent to Queue
$message = json_encode(array("token" => $token, "message" => $a));

//****************************** RabbitMQ Part********************************
$amqp = new PangeaAmqp();
$amqp -> declareExchange("pangea");
$amqp -> declareQueue($account_id);
$amqp -> bindExchange($account_id);
$amqp -> insertMessage($message);
$doc = $pcb->getDocument($token);

$response = null;
$i = 1;
do{
	sleep(3);
	$docWithResponse = $pcb->getDocument($token);
	if(is_object($docWithResponse) && isset($docWithResponse->response))
		$response = $docWithResponse->response;
}while(is_null($response));
echo $response."\n";
echo date('Y:m:d H:i:s')."\n";
?>