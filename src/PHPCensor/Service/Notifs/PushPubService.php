<?php namespace PHPCensor\Service\Notifs;
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
   * @param  string  $title  Build title.
   * @param  string  $buildNotifType  Build NOTIF_TYPE 
   * constants.
   * @see   \PHPCensor\Model\Build
   */
  public function __construct($title, $buildNotifType)
  {
    $data = array
    (
      'topic' => config('phpci.notifs.topic'),
      'title' => $title,
      'buildNotifType' => $buildNotifType,
      'sentOn' => $time
    );
    $context = new ZMQContext();
    $socket = $context->getSocket
    (
      ZMQ::SOCKET_PUSH,
      'PHPCensor Push Notification Server'
    );
    $socket->connect(config('phpci.notifs.bindDns'));
    $socket->send(json_encode($data));
  }
}