<?php

class PangeaAmqp{

	private $connection;
	private $channel;
	private $exchange;
	private $queue;
	private $routing_key;


	/**
	 * This function gets the Amqp connection and creates a new channel. As we dont need any user inputs for these.. We 
	 * can create them in constructor 
	*/
	function __construct()
	{
		$this->connection = new AMQPConnection();
		$this->connection->connect();

		$this->channel = new AMQPChannel($this->connection);
	}

	/**
	 * 
	 * This function declares an exchange if not existed.
	 * @param $exchange_name is the name of the exchange to e declared
	 * @return void
	 */
	function declareExchange($exchange_name)
	{
		$this->exchange = new AMQPExchange($this->channel);
		$this->exchange->setName($exchange_name);
		$this->exchange->setType(AMQP_EX_TYPE_DIRECT);
		$this->exchange->declare();
	}

	/**
	 * This function declares a queue
	* @param $queue_name is the name of the queue to be declared
	*/
	function declareQueue($queue_name)
	{
		$this->queue = new AMQPQueue($this->channel);
		$this->queue->setName($queue_name);
		$this->queue->setFlags(AMQP_DURABLE);
		//$this->queue->setFlags(AMQP_PASSIVE);
		$this->queue->declare();
	}

	/**
	 * This function binds the exchange with the routing key
	* @param $routing_key
	*/
	function bindExchange($routing_key)
	{
		$this->routing_key = $routing_key;
		// Bind it on the exchange to routing.key
		$this->exchange -> bind($this->exchange->getName(), $routing_key);
	}

	/**
	 * This function inserts a message in to the queue
	* @param $message is the message that needs to be sent.
	*/
	function insertMessage($message)
	{
		$this->exchange -> publish($message, $this->routing_key);
	}
}

$amqp = new PangeaAmqp();
$amqp -> declareExchange("pangea");
$amqp -> declareQueue("myqueue2");
$amqp -> bindExchange("facebook1");
$amqp -> insertMessage("this is the sample message generated at ".time());

?>