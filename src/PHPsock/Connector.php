<?php
namespace PHPsock;

/**
 * PHP-side part of PHPsock layer for realtime communication between client-side JS and PHP application.
 *
 * @author     Jan Dvorak <dvorakj@gmail.com>
 * @since      2014-06-20
 * @license    MIT License
 */
class Connector
{
    const DEFAULT_PORT = 7070;
    const DEFAULT_HOST = 'localhost';

    protected $_port;
    protected $_host;
    protected $_dnode;
    protected $_loop;

    /**
     * @param null|int    $port
     * @param null|string $host
     * @return Connector
     */
    public function __construct($port = null, $host = null)
    {
        $this->_port = empty($port) ? static::DEFAULT_PORT : $port;
        $this->_host = empty($host) ? static::DEFAULT_HOST : $host;

        $this->_loop = new \React\EventLoop\StreamSelectLoop();
        $this->_dnode = new \DNode\DNode($this->_loop);
    }

    /**
     * @param string        $method
     * @param array         $params
     * @param callable|null $callback
     */
    public function callMethod($method, $params = [], $callback = null)
    {
        $this->_dnode->connect($this->_host, $this->_port, function ($remote, $connection) use ($method, $params, $callback) {
            call_user_func_array([$remote, $method], array_merge($params, [
                function ($return = []) use ($connection, $callback) {
                    if (is_callable($callback)) {
                        $callback($return);
                        $connection->end();
                    }
                }
            ]));

            // in case of no callback end connection immediately
            if (!is_callable($callback)) {
                $connection->end();
            }
        });
        $this->_loop->run();
    }

    /**
     * @param array  $clientAliases
     * @param string $procedure
     * @param array  $params
     */
    public function call($clientAliases, $procedure, $params = [])
    {
        return $this->callMethod('callClients', [$clientAliases, $procedure, $params]);
    }

    /**
     * @param string $topic
     * @param mixed  $message
     */
    public function publish($topic, $message)
    {
        return $this->callMethod('publish', [$topic, $message]);
    }

    /**
     * @param callable $callback
     */
    public function getOnlineClients($callback)
    {
        return $this->callMethod('getOnlineClients', [], function ($list = []) use ($callback) {
            $callback($list);
        });
    }

}
