<?php
function config($key, $default = null)
{
  return \b8\Config::getInstance()->get($key, $default);
}
function hasNotifs()
{
  $c = config('php-censor.notifs');
  return !empty($c) && !is_null($c) && count($c) > 0;
}
