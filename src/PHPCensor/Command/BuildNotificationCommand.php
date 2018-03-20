<?php namespace PHPCensor;
use b8\Config;
use PHPCensor\Service\Notifs\PushService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use PHPCI\Service\PushNotifService;
use React\EventLoop\Factory;
use React\ZMQ\Context;
use React\Socket\Server;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use Ratchet\Wamp\WampServer;
use Monolog\Logger;
/**
 * Class BuildNotificationCommand.
 * A Push Notification WebSocket Server that listens and
 * broadcasts real-time updates for a build for active projects.
 * @example  ./bin/console php-censor:build-notifications
 */
final class BuildNotificationCommand extends Command
{
  //TODO: Logger.
  protected $logger
  public function __construct(Logger $logger)
  {
    parent::__construct();
    $this->logger = $logger;
  }
  protected function configure()
  {
    $this
      ->setName('php-censor:build-notifications')
      ->setDescription('A Push Notification WebSocket Server that listens and broadcasts real-time Project Builds.');
  }
  protected function execute
  (
    InputInterface $input,
    OutputInterface $output
  )
  {
    /*
      @var  string  $uri  The URI for WebSocket. 
      Binding to 0.0.0.0 allows remotes to connect.
      Default is 127.0.0.1:8080.
    */
    echo 'xxx';exit;
    $uri = config('phpci.notifs.uri');
    /*
      @var  string  $bindDns  Binding to 127.0.0.1 
      means the only client that can connect is itself.
      Set to null or empty string to disable. The
      default is tcp://127.0.0.1:5555.
     */
    $bindDns = config('phpci.notifs.bindDns');
    $loop = Factory::create();
    $pushSvc = new PushService();
    //Listen for the web server to make a ZeroMQ 
    //push after an ajax request.
    $context = new Context($loop);
    $pull = $context->getSocket(\ZMQ::SOCKET_PULL);
    if(!is_null($bindDns) || !empty($bindDns))
      $pull->bind($bindDns);
    $pushSvcHandler = 'onDataEntry';
    $pull->on('message', array($pushSvc, $pushSvcHandler));
    //Set up our WebSocket server for clients wanting 
    //real-time updates.
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