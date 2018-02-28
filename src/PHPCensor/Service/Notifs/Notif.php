<?php namespace PHPCensor\Service\Notifs;
use b8\Config;
use PHPCensor\Service\Notifs\PushPubService;
use PHPCensor\Service\Notifs\PushSubService;
use PHPCensor\Service\Notifs\PushService;
final class Notif
{
  public function __construct()
  {
    $uri = config('phpci.notifs.uri');
    $bindDns = config('phpci.notifs.bindDns');
    new PushSubService($uri, $bindDns);
  }
}