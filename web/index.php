<?php

require('../vendor/autoload.php');
require_once('TwitterAPIExchange.php');
// import access tokens from a config file
require('../config.php');

$app = new Silex\Application();
$app['debug'] = true;

// Register the monolog logging service
$app->register(new Silex\Provider\MonologServiceProvider(), array(
  'monolog.logfile' => 'php://stderr',
));

// Our web handlers
$app->get('/', function() use($app) {
  $app['monolog']->addDebug('logging output.');
  return 'Hello';
});

$app->get('/twitter', function() use($app) {
    $app['monolog']->addDebug('logging Twitter page output.');
    return 'On the Twitter tracking page.';
});

$app->run();

?>
