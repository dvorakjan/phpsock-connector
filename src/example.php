<?php

require 'Connector.php';
// require 'vendor/autoload.php';

$phpsock = new PHPsock\Connector();
$phpsock->connect(7070);
$phpsock->getOnlineClients(function($users){
	var_dump($users);
});
$phpsock->publish('http://ebrana.cz/news', ['nejaka novinka']);
var_dump($phpsock->callClient('jan-dvorak', 'test', ["jak se mas?"]));