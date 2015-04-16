<?php

require('../vendor/autoload.php');
require_once('TwitterAPIExchange.php');
// Access tokens are stored as environment varibales on Heroku server.
$access_token = getenv('access_token');
$access_token_secret = getenv('access_token_secret');
$consumer_key = getenv('consumer_key');
$consumer_key_secret = getenv('consumer_key_secret');

$app = new Application();
$app['debug'] = true;

// Register the monolog logging service
$app->register(new MonologServiceProvider(), array(
    'monolog.logfile' => 'php://stderr',
));

// Our web handlers
$app->get('/', function() use($app) {
    $app['monolog']->addDebug('logging output.');
    return 'Hello';
});

$app->get('/twitter', function() use($app) {
    $app['monolog']->addDebug('logging GET Twitter page output.');
    return 'On the Twitter tracking page.';
});

$app->post('/twitter', function() use($app) {
    $app['monolog']->addDebug('logging POST Twitter page output.');
    users = array($data);
    for (usr in users) {
        get_followers(usr)
    }
    return 'On Twitter POST page.';
});

$app->run();

/* HELPER FUNCTIONS */
function get_followers(user) {
    followers = array();
    pages = http_get('https://api.twitter.com/1.1/followers/list.json?
        screen_name='.$usr.'&count=200&skip_status=true');
    do {
      // grab and append users to array
    } while (pages.next_cursor != null);
}

?>
