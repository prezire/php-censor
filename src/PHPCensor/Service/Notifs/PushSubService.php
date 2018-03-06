<?php namespace PHPCensor\Service\Notifs;
use PHPCI\Service\PushNotifService;
use React\EventLoop\Factory;
use React\ZMQ\Context;
use React\Socket\Server;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use Ratchet\Wamp\WampServer;
/**
 * PushSubService class.
 * Manages WebSocket sub / subscribe / listeners.
 * TODO: Run as daemon and run once via shell 
 * like: php PushSubService.php.
 * @see \PHPCensor\BuildPushNotification.php.
 */
final class PushSubService
{
  /**
   * Construct.
   * @param  string  $uri  The URI for WebSocket. 
   * Binding to 0.0.0.0 allows remotes to connect.
   * Default is 127.0.0.1:8080.
   * @param  string  $bindDns  Binding to 127.0.0.1 
   * means the only client that can connect is itself.
   * Set to null or empty string to disable. The
   * default is tcp://127.0.0.1:5555.
   */
  public function __construct
  (
    $uri = '127.0.0.1:8080',
    $bindDns = 'tcp://127.0.0.1:5555'
  )
  {
    $loop = Factory::create();
    $pushSvc = new PushService();

    //Listen for the web server to 
    //make a ZeroMQ push after an ajax request.
    $context = new Context($loop);
    $pull = $context->getSocket(\ZMQ::SOCKET_PULL);
    if(!is_null($bindDns) || !empty($bindDns))
    {
      $pull->bind($bindDns); 
    }
    $pushSvcHandler = 'onDataEntry';
    $pull->on('message', array($pushSvc, $pushSvcHandler));

    //Set up our WebSocket server for clients 
    //wanting real-time updates.
    $webSock = new Server($uri, $loop); 
    $webServer = new IoServer
    (
      new HttpServer
      (
        new WsServer
        (
          new WampServer
          (
            $pushSvc
          )
        )
      ), 
      $webSock
    );
    $loop->run();
  }
}