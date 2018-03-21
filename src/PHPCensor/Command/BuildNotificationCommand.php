<?php namespace PHPCensor\Command;
use b8\Config;
use Monolog\Logger;
use PHPCensor\Logging\OutputLogHandler;
use PHPCensor\Worker\BuildWorker;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use React\EventLoop\Factory;
use React\ZMQ\Context;
use React\Socket\Server;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use Ratchet\Wamp\WampServer;
use PHPCensor\Service\Notifs\PushService;
/**
 * Class BuildNotificationCommand.
 * A Push Notification WebSocket Server that listens and
 * broadcasts real-time updates for a build for active projects.
 * @example  nohup php bin/console php-censor:build-notifications &
 */
final class BuildNotificationCommand extends Command
{
  /**
   * @var OutputInterface
   */
  protected $output;

  /**
   * @var Logger
   */
  protected $logger;

  /**
   * @param \Monolog\Logger $logger
   * @param string $name
   */
  public function __construct(Logger $logger, $name = null)
  {
    parent::__construct();
    $this->logger = $logger;
  }
  protected function configure()
  {
    $this
      ->setName('php-censor:build-notification')
      ->addOption('debug', null, null, 'Run PHP Censor in Debug Mode')
      ->setDescription('A Push Notification WebSocket Server that listens and broadcasts real-time Project Builds.');
  }
  protected function execute
  (
    InputInterface $input,
    OutputInterface $output
  )
  {
    $this->output = $output;

    if ($input->hasOption('verbose') && $input->getOption('verbose')) {
      $this->logger->pushHandler(
        new OutputLogHandler($this->output, Logger::INFO)
      );
    }

    $debug = $input->hasOption('debug') && $input->getOption('debug');
    if ($debug) {
      $output->writeln('<comment>Debug mode enabled.</comment>');
      define('DEBUG_MODE', true);
    }

    /*
      @var  string  $uri  The URI for WebSocket. 
      Binding to 0.0.0.0 allows remotes to connect.
      Default is 127.0.0.1:8080.
    */
    $uri = config('php-censor.notifs.uri');
    /*
      @var  string  $bindDns  Binding to 127.0.0.1 
      means the only client that can connect is itself.
      Set to null or empty string to disable. The
      default is tcp://127.0.0.1:5555.
     */
    $bindDns = config('php-censor.notifs.bindDns');
    $loop = Factory::create();
    $pushSvc = new PushService();
    //Listen for the web server to make a ZeroMQ 
    //push after an ajax request.
    $context = new Context($loop);
    $pull = $context->getSocket(\ZMQ::SOCKET_PULL);
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