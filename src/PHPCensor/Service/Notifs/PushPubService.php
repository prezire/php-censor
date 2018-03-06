<?php namespace PHPCensor\Service\Notifs;
/**
 * PushPubService class.
 * Manages WebSocket pub / publish / broadcasts.
 * Send messages to subscribers via HTML POST Request.
 */
final class PushPubService
{
  //TODO: Find the aggregated value of message key,
  //which in this case is the total build.
  public function __construct($title, $message, $url)
  {
    $data = array
    (
      'topic' => config('phpci.notifs.topic'),
      'title' => $title,
      'message' => $message,
      'url' => $url,
      'sentOn' => $time
    );
    $context = new ZMQContext();
    $socket = $context->getSocket
    (
      ZMQ::SOCKET_PUSH,
      'PHPCI Push Notification Server'
    );
    $socket->connect(config('phpci.notifs.bindDns'));
    $socket->send(json_encode($data));
  }
}