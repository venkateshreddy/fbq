<?php
/**
Author: venkateshk@ybrantdigital.com
Description: This class Defines how the message should look like and what all the parameters
			 Should be there in the message that has to be sent by pangea to Facebook. 
*/

class  MessageSpec{
	
	private $url;				//the url that has to be called by erlang process
	private $request_type; 		//this must be GET or POST
	private $access_token;		//the Facebook access token required for authentication
	private $method_name;		//the method that has to be called ..... no need to mention in case of batch request
	private $data;				//the params that has to be passed.... this must be a key => value array


	/**
     This constructor will take all the inputs and validates them
     */
	function __construct($url, $request_type, $access_token, $method_name, $data) {
       
	    $this->url = $url;
	    $this->request_type = $request_type;
	    $this->access_token = $access_token;
	    $this->method_name = $method_name;
	    $this->data = $data;

	    if(!$this->validateparams())
	 	{
	 		throw new Exception("Invalid Params");
	 	}
   	}
   	/**
		Validate all the params in this function 
   	*/
   function validateparams()
   {
   		
		if(is_null($this->url) || trim($this->url) =="")
		{
			return false;
		}
   		else if(!filter_var($this->url, FILTER_VALIDATE_URL))
		{
		  	return false;
		}
		else if(is_null($this->request_type) || trim($this->request_type) =="")
		{
			return false;
		}
		else if($this->request_type != "get" && $this->request_type != "post")
		{
			return false;
		}
		else if(is_null($this->access_token) || trim($this->access_token) =="")
		{
			return false;
		}
		else if(is_null($this->method_name) || trim($this->method_name) =="")
		{
			return false;
		}
		//*************enable this when you want to validate the data************
		/*else if(is_null($this->data) || trim($this->data) =="")
		{
			return false;
		}*/
		return true;
   }

   /**
		This function gets the message in json format.
   */
   function getMessage()
   {
   		$message_arr = array(
   								"url" 			=> 	$this->url,
   								"request_type" 	=> 	$this->request_type,
   								"access_token" 	=> 	$this->access_token,
   								"method_name"  	=> 	$this->method_name,
   								"data"			=>	$this->data
   							);
   		return json_encode($message_arr);
   }
} 

?>