<?php
namespace PHPsock;

/**
 * PHP-side part of PHPsock layer for realtime communication between client-side JS and PHP application.
 *
 * @author     Jan Dvorak <dvorakj@gmail.com>
 * @since      2014-06-20
 * @license    MIT License
 */
class Connector {

    protected $_port;
    protected $_host;
    protected $_dnode;
	protected $_isConnected = false;
	protected $_connection;

    const DEFAULT_PORT = 7070;
    const DEFAULT_HOST = 'localhost';

    /**
     * @param null|int $port
     * @param null|string $host
     * @return Connector
     */
    public function __construct($port = null, $host = null) {
        $this->_port = empty($port) ? static::DEFAULT_PORT : $port;
        $this->_host = empty($host) ? static::DEFAULT_HOST : $host;

		$this->_dnode = new \DnodeSyncClient\Dnode();

        if ($this->_connection = $this->_dnode->connect($this->_host, $this->_port)) {
            $this->_isConnected = true;
        }
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
        // TODO try to reconnect
		if (!$this->_isConnected) {
			throw new Exception('Not connected to PHPsock server.');
		}
	}

}