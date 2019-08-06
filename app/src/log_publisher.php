<?php

require dirname(__DIR__) . '/vendor/autoload.php' ;
require dirname(__DIR__) . '/src/configs.php' ;
require dirname(__DIR__) . '/src/data.php' ;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Exchange\AMQPExchangeType;

$exchange = 'log';
$queue = 'ApplicationLog';

$connection = new AMQPStreamConnection(HOST, PORT, USER, PASS, VHOST);
$channel = $connection->channel();

$channel->queue_declare($queue, false, true, false, false);
$channel->exchange_declare($exchange, AMQPExchangeType::DIRECT, false, true, false);
$channel->queue_bind($queue, $exchange);

$faker = Faker\Factory::create();

$iterTimes = implode(' ', array_slice($argv, 1)) ;

if( !is_numeric($iterTimes) ){
  $iterTimes = 1 ;
}
else{
  $iterTimes = intval($iterTimes);
}

$i = 0 ;

while( $i < $iterTimes ){

  $messageBody = json_encode([
    'unique_id' => $faker->sha1 ,
    'ipv4' => $faker->ipv4 ,
    'date' => Date("Y-m-d H:i:s O") ,
    'agent' => $faker->userAgent ,
    'file' => '/' . implode('/', $faker->words($faker->numberBetween(1, 4))) . "." . $faker->fileExtension ,
    'server' => $server[$faker->numberBetween(1, count($server)-1)] ,
    'methods' => $methods[$faker->numberBetween(1, count($methods)-1)] ,
    'env' => $env[$faker->numberBetween(1, count($env)-1)] ,
    'app' => $app[$faker->numberBetween(1, count($app)-1)] ,
    'status_codes' => $status_codes[$faker->numberBetween(1, count($status_codes)-1)] ,
  ]);
  
  $message = new AMQPMessage($messageBody,[
    'content_type' => 'application/json',
    'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT
  ]);
  
  $channel->basic_publish($message, $exchange);

  $i++ ;
}

echo "Finished publishing to queue: " . $queue . PHP_EOL ;

$channel->close();
$connection->close();
