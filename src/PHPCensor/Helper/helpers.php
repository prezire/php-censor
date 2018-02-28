<?php
function config($key, $default = null)
{
  return \b8\Config::getInstance()->get($key, $default);
}
function tokenize()
{
  return sha1(date("Y-m-d H:i:s", mt_rand(1, 99999)));
}
function tokenizeUrl($tokenize)
{
  return $tokenize ? '?' . tokenize() : '';
}
function vendor($url, $tokenizeUrl = false)
{
  return APP_URL .
    'assets/vendor/' .
    $url .
    tokenizeUrl($tokenizeUrl);
}
function js($url, $tokenizeUrl = false)
{
  print '<script src="' .
    APP_URL .
    'assets/js/' .
    $url .
    tokenizeUrl($tokenizeUrl) . 
    '" type="text/javascript"></script>';
}
function css($url, $tokenizeUrl = false)
{
  print '<link href="' .
    APP_URL .
    'assets/css/' .
    $url .
    tokenizeUrl($tokenizeUrl) . 
    '" rel="stylesheet" type="text/css" />';
}