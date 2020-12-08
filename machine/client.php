<?php

$client = new Swoole\Client(SWOOLE_SOCK_TCP);
$status = 0;
$prot = null;
$data = null;
if ($status == 0) {
   $port = 9505;
   $data  = [
        'method' => 'machineinfo',
        //'method' => 'machinestop',
        'msg' => 'rainrun',
        'data' => '666',
        'code' => 9
    ];
} else {
    $port = 9501;
    $data  = [
        'method' => 'machinestop',
        'msg' => 'rainrun',
        'data' => '666',
        'code' => 4
    ];
}

if (!$client->connect('172.26.240.108', $port, -1)) { //连接到172.26.240.108
    exit("connect failed. Error: {$client->errCode}\n");
}


//$client->send(json_encode($data));
$client->send(json_encode($data));
echo $client->recv();
$client->close();