<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once("includes/MessageSpec.class.php");
require_once("includes/CouchBase.class.php");
require_once("includes/PangeaAmqp.class.php");
require_once("includes/KLogger.php");

class FacebookMessagePublisher
{
    
    public $couchConnection;
    public $amqpConnection;
    public $logger;
    /**
	 * @param $couch_details contain all the couch conection details
	 * @return FacebookMessagePublisher instance
     */
    public static function getInstance($couch_details, $amqp_details)
    {
        static $obj = null;
        if ($obj === null) {
            $obj = new FacebookMessagePublisher($couch_details, $amqp_details);
        }
        return $obj;
    }

    /**
     * this is private constructor.. it ca be called from out side
	 * @param $couch_details contain all the couch conection details
	 * @return nothing 
     */
    private function __construct($couch_details, $amqp_details)
    {
    	$this->couchConnection = new PangeaCouchBase($couch_details['host'], 
								$couch_details['username'], 
								$couch_details['password'], 
								$couch_details['bucket']);
    	
    	$this->amqpConnection = new PangeaAmqp($amqp_details['host'], 
                                $amqp_details['port'], 
                                $amqp_details['username'], 
                                $amqp_details['password']);

	$this->logger = KLogger::instance("/var/www/fbq/logs/", KLogger::DEBUG);
    }

    /**
     * this is publish message function.. this perfomrs the main functionality
	 * @param $message_details is an array contaning url, request_type, access_token, method_name, data
	 * @return unique token 
     */
    public function publishMessage($message_details)
    {
    	extract($message_details);

    	$data_obj = $this->getJsonMessage($url, $request_type, $access_token, $method_name, $data);
    	
    	$token = $this->getMessageToken();
	
    	$couch_message = $this->insertMessageToCouch($token, $data_obj);
	
	$logger_object = json_encode(array("url"=>$url, "request_type"=>$request_type, "method_name"=>$method_name, "parameters"=>$data));

	$this->logger->logInfo("Token : ".$token." => Inserted to couch => with data =>", $logger_object);
    	//message that has to be sent to Queue
	$message = json_encode(array("token" => $token, "message" => $couch_message));
	
	if($this->insertMessageToQueue("pangea", $account_id, $message))
	{
		$this->logger->logInfo("Token : ".$token." => Inserted to queue => with message =>", $message);
		return $token;			
	}	
	else
		return null;

    }


    /**
     * this function performs the message validation and gives back the json string of message
	 * @param $url is the publisher url
	 * @param $request_type is type of the request
	 * @param $access_token authentication token
	 * @param $method_name is name of the method
	 * @param $data is the params list sent to publisher
	 * @return json object 
     */
    private function getJsonMessage($url, $request_type, $access_token, $method_name, $data)
    {
    	//validating the message by applying to the message spec
    	$spec = new MessageSpec($url, $request_type, $access_token, $method_name, $data);
		
    	//get the json object of all the required details
		$data_obj = $spec->getMessage();

		return $data_obj;
    }

    /*
	 * this is the token generator function
    */
    private function getMessageToken()
    {
    	//generating the token
    	$token = md5(time());

    	return $token;
    }

    /**
     * this function inserts the message in to couchbase
	 * @param $token is the unique identifier of the document
	 * @param $data_obj is the message that has to be published
	 * @return inserted doc 
     */
    private function insertMessageToCouch($token, $data_obj)
    {
    	//inserting the message in to the couchbase.
		$this->couchConnection->insertDocument($token, $data_obj);
		$couch_doc = $this->couchConnection->getDocument($token);
		return $couch_doc;
    }


    /**
     * this function inserts the message in to queue
	 * @param $exchangeName is the name of the exchange to be declared
	 * @param $account_id is the account id used for creating the queue
	 * @param $message is the json object that contain data and token
	 * @return boolen 
     */
    private function insertMessageToQueue($exchangeName, $account_id, $message)
    {
    	//performing the rabbitmq operations
		$this->amqpConnection -> declareExchange($exchangeName);
		$res = $this->amqpConnection -> declareQueue($account_id);
        $this->amqpConnection -> bindExchange($account_id);
        sleep(1);
		$this->amqpConnection -> insertMessage($message);
        return true;
    }

    /**
     * this function inserts the message in to queue
	 * @param $token is the unique id to get the response
	 * @return couchbase document 
     */
    public function retrieveResponse($token)
    {
    	if(trim($token) == "")
    		return null;
    	$docWithResponse = $this->couchConnection->getDocument($token);
    	return $docWithResponse;
    }
}
?>
