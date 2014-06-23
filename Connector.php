<?php
namespace Ebrana\PHPsock;

/**
 * Connector pro napojeni z PHP na PHPsock server, ktery umoznuje komunikaci v realnem case s klienty.
 *
 * @author     jd
 * @version    1.0.0
 * @since      2014-06-20
 *
 * @package    Ebrana\PHPsock
 * @copyright  Ebrana s.r.o. (http://ebrana.cz)
 * @license    Ebrana Licence
 */
class Connector {

    protected $_port;
    protected $_host;
    protected $_dnode;
	protected $_isConnected = false;
	protected $_connection;

    const DEFAULT_PORT = 7070;
    const DEFAULT_HOST = 'afrodita.ebrana.cz';

    /**
     * Místo konstruktoru je lepsi pouzit tovarnu create, ktera umznuje prepsani parametru z konfigurace
     *
     * @param null|int $port
     * @param null|string $host
     * @return Connector
     */
    public function __construct($port = null, $host = null) {
        $this->_port = empty($port) ? self::DEFAULT_PORT : $port;
        $this->_host = empty($host) ? self::DEFAULT_HOST : $host;

		$this->_dnode = new \DnodeSyncClient\Dnode();

        if ($this->_connection = $this->_dnode->connect($this->_host, $this->_port)) {
            $this->_isConnected = true;
        }
	}

    /**
     * Továrna pro vytvoření konektoru pro připojení k NodeJS realtime serveru
     *
     * @author Jan Dvořák
     * @param null|array $config
     *
     * @return Connector
     * @throws \Ebrana\Exception
     */
    public static function create($config = null)
    {
        $defaultConfig = array(
            'port' => self::DEFAULT_PORT,
            'host' => self::DEFAULT_HOST
        );

        if(is_null($config))
        {
            $app = \Zend_Registry::get('App')->getOption('app');
            if (isset($config['phpsock'])) {
                $config = $app['phpsock'];
                if (empty($config['port'])) $config['port'] = self::DEFAULT_PORT;
                if (empty($config['host'])) $config['host'] = self::DEFAULT_HOST;
            } else {
                $config = array();
            }
        }

        $config = array_merge($defaultConfig, $config);

        if(null === $config['port'] || null === $config['host'])
        {
            throw new \Ebrana\Exception(5001, 'Some required (port, host) parameter missing in config.');
        }

        return new self(
            $config['port'],
            $config['host']
        );
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