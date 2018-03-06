<?php namespace PHPCensor;
use b8\Config;
use PHPCensor\Service\Notifs\PushPubService;
use PHPCensor\Service\Notifs\PushSubService;
use PHPCensor\Service\Notifs\PushService;
/**
 * Class BuildPushNotification.
 * TODO: Delete this file and daemonize pushsubservice.php.
 */
final class BuildPushNotification
{
  public function __construct()
  {
    $uri = config('phpci.notifs.uri');
    $bindDns = config('phpci.notifs.bindDns');
    new PushSubService($uri, $bindDns);
  }
}