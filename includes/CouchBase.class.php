<?php
/*
Author: venkateshk@ybrantdigital.com
Description: This class acts as the interface between the couch base and php 
*/

class  PangeaCouchBase{
	
	private $connection;

	/**
     *
     * @param array $hosts An array of hostnames[:port] where the
     *                     Couchbase cluster is running. The port number is
     *                     optional (and only needed if you're using a non-
     *                     standard port).
     * @param string $user The username used for authentication to
     *                     the cluster
     * @param string $password The password used to authenticate to
     *                       the cluster
     * @param string $bucket The name of the bucket to connect to
     * @param boolean $persistent If a persistent object should be used or not
     */
	function __construct($host, $username, $password, $bucket,$persistent = true){

		$this->connection = new Couchbase($host, $username, $password, $bucket, $persistent);
	
	}

	
	/**
	 * Retrieve a document from the cluster.
	 *
     * @param string $id identifies the object to retrieve
     * @param callable $callback a callback function to call for missing
     *                 objects. The function signature looks like:
     *                 <code>bool function($res, $id, &$val)</code>
     *                 if the function returns <code>true</code> the value
     *                 returned through $val is returned. Please note that
     *                 the cas field is not updated in these cases.
     * @param string $cas where to store the cas identifier of the object
     * @return object The document from the cluster
     * @throws CouchbaseException if an error occurs
     */
    function getDocument($id) {
    	return $this->connection->get($id);
    }

    
    /**
     * Retrieve multiple documents from the cluster.
     *
     * @param array $ids an array containing all of the document identifiers
     * @param array $cas an array to store the cas identifiers of the documents
     * @param int $flags may be 0 or COUCHBASE_GET_PRESERVE_ORDER
     * @return array an array containing the documents
     * @throws CouchbaseException if an error occurs
     */
    function getMultipleDocuments($ids) {
    	return $this->connection->getMulti($ids);
    }
	

	/**
     * Add a document to the cluster.
     *
     * The add operation adds a document to the cluster only if no document
     * exists in the cluster with the same identifier.
     *
     * @param string $id the identifier to store the document under
     * @param object $document the document to store
     * @param integer $expiry the lifetime of the document (0 == infinite)
     * @param integer $persist_to wait until the document is persisted to (at least)
     *                            this many nodes
     * @param integer $replicate_to wait until the document is replicated to (at least)
     *                            this many nodes
     * @return string the cas value of the object if success
     * @throws CouchbaseException if an error occurs
     */
	function insertDocument($id, $document, $expiry = 0, $persist_to = 0, $replicate_to = 0)
	{
		 return $this->connection->add($id, $document, $expiry, $persist_to, $replicate_to);
	}

	
	/**
     * Store a document in the cluster.
     *
     * The set operation stores a document in the cluster. It differs from add and replace in that it does 
     * not care for the presence of the identifier in the cluster.
     *
     * If the $cas field is specified, set will only succeed if the identifier exists in the cluster with the exact same 
     * cas value as the one specified in this request.
     *
     * @param string $id the identifier to store the document under
     * @param object|string $document the document to store
     * @param integer $expiry the lifetime of the document (0 == infinite)
     * @param string $cas a cas identifier to restrict the store operation
     * @param integer $persist_to wait until the document is persisted to (at least)
     *                            this many nodes
     * @param integer $replicate_to wait until the document is replicated to (at least)
     *                            this many nodes
     * @return string the cas value of the object if success
     * @throws CouchbaseException if an error occurs
     */
	function setDocument($id, $document, $expiry = 0, $cas = "", $persist_to = 0, $replicate_to = 0)
	{
		return $this->connection->set($id, $document, $expiry, $cas, $persist_to, $replicate_to);
	}

	
	/**
     * Store multiple documents in the cluster.
     *
     * @param array $documents an array containing "id" =&gt; "document" pairs
     * @param integer $expiry the lifetime of the document (0 == infinite)
     * @param integer $persist_to wait until the document is persisted to (at least)
     *                            this many nodes
     * @param integer $replicate_to wait until the document is replicated to (at least)
     *                            this many nodes
     * @return boolean true if success
     * @throws CouchbaseException if an error occurs
     */
	function setMultipleDocuments($documents, $expiry = 0, $persist_to = 0, $replicate_to = 0) {
		return $this->connection->setMulti($documents, $expiry, $persist_to, $replicate_to);
    }

    
    /**
     * Delete a document from the cluster.
     *
     * @param string $id the document identifier
     * @param string $cas a cas identifier to restrict the store operation
     * @param integer $persist_to wait until the document is persisted to (at least)
     *                            this many nodes
     * @param integer $replicate_to wait until the document is replicated to (at least)
     *                            this many nodes
     * @return string the cas value representing the delete document if success
     * @throws CouchbaseException if an error occurs
     */
	function deleteDocument($id, $cas = "", $persist_to = 0, $replicate_to = 0)
	{
		return $this->connection->delete($id, $cas, $persist_to, $replicate_to);
	}

}
?>