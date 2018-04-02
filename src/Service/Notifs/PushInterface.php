<?php namespace PHPCensor\Service\Notifs;
use Exception;
use Ratchet\ConnectionInterface;
interface PushInterface
{
  public function onOpen(ConnectionInterface $conn);
  public function onClose(ConnectionInterface $conn);
  public function onError(ConnectionInterface $conn, Exception $e);
  public function onDataEntry($entry);
}