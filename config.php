<?php

$db['host'] = "localhost";
$db['port'] = 3306;
$db['name'] = "phpipam";
$db['user'] = "phpipam";
$db['pass'] = "secret";

$db['ssl']        = false;                           # true/false, enable or disable SSL as a whole
$db['ssl_key']    = "/path/to/cert.key";             # path to an SSL key file. Only makes sense combined with ssl_cert
$db['ssl_cert']   = "/path/to/cert.crt";             # path to an SSL certificate file. Only makes sense combined with ssl_key
$db['ssl_ca']     = "/path/to/ca.crt";               # path to a file containing SSL CA certs
$db['ssl_capath'] = "/path/to/ca_certs";             # path to a directory containing CA certs
$db['ssl_cipher'] = "DHE-RSA-AES256-SHA:AES128-SHA"; # one or more SSL Ciphers

$debugging = false;
$phpsessname = "phpipam";

// if(!defined('BASE')) define('BASE', "/");
// Change RewriteBase in /.htaccess, too.
if(!defined('BASE')) define('BASE', "/ipv/");

// Defines the default time zone, change e.g. to "Europe/London" when needed
if(function_exists("date_default_timezone_set")) {
  if(!ini_get('date.timezone')) {
    date_default_timezone_set('Europe/Berlin');
  }
}

?>
