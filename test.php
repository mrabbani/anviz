<?php

require 'Anviz.php';

$client = new Anviz('client', 1, 5010, '192.168.1.185');

//$client->setDateTime();

echo date('Y-m-d H:i:s', $client->getDateTime());