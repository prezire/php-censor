<?php namespace PHPCensor;
use b8\Config;
use PHPCensor\Service\Notifs\PushPubService;
use PHPCensor\Service\Notifs\PushSubService;
use PHPCensor\Service\Notifs\PushService;
/**
 * Class BuildPushNotification.
 * TODO: Daemon.
 */
final class BuildPushNotification
{
  public function __construct()
  {
    $uri = 'tcp://127.0.0.1:5555'; //config('phpci.notifs.uri');
    $bindDns = 'some category'; //config('phpci.notifs.bindDns');
    new PushSubService($uri, $bindDns);
  }
}