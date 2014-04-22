<?php
namespace PHPsock;

require 'vendor/autoload.php';

class Connector {

	const DEFAULT_PORT = 7070;

	protected $_dnode;
	protected $_isConnected = false;
	protected $_connection;

	public function __construct() {
		$this->_dnode = new \DnodeSyncClient\Dnode();
	}

	public function connect($port = 7070, $host = 'localhost') {
		$this->_connection = $this->_dnode->connect($host, $port);
		$this->_isConnected = true;
	}

	public function getOnlineClients() {
		$this->_checkConnection();
		return $this->_connection->call('getOnlineClients')[0];
	}

	public function call($clientAlias, $procedure, $params) {
		$this->_checkConnection();
		return $this->_connection->call('callClient', array($clientAlias, $procedure, $params));
	}

	public function publish($topic, $message) {
		$this->_checkConnection();
		return $this->_connection->call('publish', array($topic, $message));
	}

	protected function _checkConnection() {
		if (!$this->_isConnected) {
			throw new Exception('Not connected.');
		}
	}

}