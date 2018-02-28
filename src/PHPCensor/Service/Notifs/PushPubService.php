<?php namespace PHPCensor\Service\Notifs;
/**
 * PushPubService class.
 * Manages WebSocket pub / publish / broadcasts.
 * Send messages to subscribers via HTML POST Request.
 */
final class PushPubService
{
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
    $socket = $context->getSocket(ZMQ::SOCKET_PUSH, 'PHPCI Notification Push Server');
    $socket->connect(config('phpci.notifs.bindDns'));
    $socket->send(json_encode($data));
  }
}