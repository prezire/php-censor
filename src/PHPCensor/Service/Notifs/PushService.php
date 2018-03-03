<?php namespace PHPCensor\Service\Notifs;
use Exception;
use Ratchet\ConnectionInterface;
use Ratchet\Wamp\WampServerInterface;
use PHPCensor\Service\Notifs\PushInterface;
/**
 * Class PushService.
 * Processes the filters and broadcasts of push
 * notifications to all listeners during data entry.
 */
class PushService implements WampServerInterface, 
                             PushInterface
{
  protected $topics;
  public function __construct()
  {
    $this->topics = array();
  }
  public function onUnSubscribe
  (
    ConnectionInterface $conn, 
    $topic
  )
  {
    /*Do nothing.*/
  }
  public function onOpen(ConnectionInterface $conn)
  {
    /*Do nothing.*/
  }
  public function onClose(ConnectionInterface $conn)
  {
    /*Do nothing.*/
  }
  public function onError
  (
    ConnectionInterface $conn, 
    Exception $e
  )
  {
    /*Do nothing.*/
  }
  public function onCall
  (
    ConnectionInterface $conn,
    $id,
    $topic,
    array $params
  ) 
  {
    //In this application if clients send data it's 
    //because the user hacked around in console.
    $conn->callError
    (
        $id,
        $topic,
        'You are not allowed to make calls.'
    )->close();
  }
  public function onPublish
  (
    ConnectionInterface $conn,
    $topic,
    $event,
    array $exclude,
    array $eligible
  ) 
  {
    //In this application if clients send data
    //it's because the user hacked around in console
    $conn->close();
  }
  public function onSubscribe(ConnectionInterface $conn, $topic) 
  {
    $this->topics[$topic->getId()] = $topic;
  }
  /**
   * @param  string  $entry  JSON'ified string received from ZMQ.
   */
  public function onDataEntry($entry) 
  {
    $entryData = json_decode($entry, true);
    //If the lookup topic object isn't 
    //set there is no one to publish to.
    if(!array_key_exists($entryData['topic'], $this->topics)) 
    {
      return;
    }
    $topic = $this->topics[$entryData['topic']];
    //Re-send the data to all the clients 
    //subscribed to that topic.
    $topic->broadcast($entryData);
  }
}