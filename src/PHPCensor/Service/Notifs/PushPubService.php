<?php namespace PHPCensor\Service\Notifs;
use ZMQContext;
use ZMQ;
/**
 * PushPubService class.
 * Manages WebSocket pub / publish / broadcasts.
 * Send messages to subscribers via HTML POST Request.
 */
final class PushPubService
{
  /**
   * Constructor.
   * Broadcasts status of builds via Push Notification.
   * @param  string  $projectTitle  Project title.
   * @param  string  $type  Build NOTIF_TYPE 
   * constants.
   * @see   \PHPCensor\Model\Build
   */
  public function __construct($projectTitle, $type)
  {
    $data = array
    (
      'topic'        => config('php-censor.notifs.topic'),
      'projectTitle' => $projectTitle,
      'type'         => $type,
      'sentOn'       => date('Ymdhis')
    );
    $context = new ZMQContext();
    $socket  = $context->getSocket
    (
      ZMQ::SOCKET_PUSH, 
      'PHPCensor Push Notification Server'
    );
    $socket->connect(config('php-censor.notifs.bindDns'));
    $socket->send(json_encode($data));
  }
}