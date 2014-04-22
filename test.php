<?php

require 'Connector.php';

$phpsock = new PHPsock\Connector();
$phpsock->connect(7070);
var_dump($phpsock->getOnlineClients());
$phpsock->publish('http://ebrana.cz/news', 'nejaka novinka');
var_dump($phpsock->call('jan-dvorak', 'test', "jak se mas?"));