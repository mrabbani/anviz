<?php

//Server
set_time_limit(0);
$host = '192.168.1.185';
$port = 5010;
$socket = socket_create(AF_INET, SOCK_STREAM, 0);
socket_bind($socket, $host, $port);
socket_listen($socket);

$client = array($socket);

$realclient = array($socket);

while (true) {
//$read=$client;
    $cliid = 0;
    socket_select($client, $write = NULL, $except = NULL, $tv_sec = NULL);
    if (in_array($socket, $client)) {
        for ($i = 1; $i < 5; $i++) {
            if (!isset($client[$i])) {
                $tmp = socket_accept($socket);
                
                socket_write($tmp, hex2bin('A50000000038000093D6'));
                
                $request = bin2hex(socket_read($tmp, 2024));
                
                $realclient[$request[9]]['socket'] = $tmp;
                
                if(count($realclient) == 3){
                    break;
                }
            } //inside if
        } //for
        break;
    }
} //while

print_r($realclient);

socket_close($socket); //for accepting
?>