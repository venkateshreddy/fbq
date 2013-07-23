<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

  require_once "includes/RestServer.php";
  require_once "fbmessagepublisher.php";
  require_once("includes/KLogger.php");

  class UpdateCouch
  {	  	
	    public static function insertResponse()
	    {
	    	//return $couchbase['remote'];
	    	$couchbase['test']['host'] 	= 	"localhost:8091";
		$couchbase['test']['username'] 	= 	"Administrator";
		$couchbase['test']['password'] 	= 	"password";	
		$couchbase['test']['bucket'] 	= 	"default";

		$amqp['test']['host'] 		= 	"localhost";
		$amqp['test']['username'] 	= 	"guest";
		$amqp['test']['password'] 	= 	"guest";	
		$amqp['test']['port'] 		= 	"5672";
	    	
	    	$message_publisher = FacebookMessagePublisher::getInstance($couchbase['test'], $amqp['test']);
	    	$con = $message_publisher->couchConnection;
		$logger	= $message_publisher->logger;
		
	        $doc = $con -> getDocument($_REQUEST['id']);
	        $arr = json_decode($doc,true);
	        $arr['response'] = json_decode($_REQUEST['response'],true);
	        $update = $con->setDocument($_REQUEST['id'], json_encode($arr));
		$logger->logInfo("Token : ".$_REQUEST['id']." => Writing response to couch =>", $_REQUEST['response']);
	        $doc = $con -> getDocument($_REQUEST['id']);
	        return array("updated"=>$doc);
	    }
  }
  $rest = new RestServer("UpdateCouch");
  $rest->handle();
?>
