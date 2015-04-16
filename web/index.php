<?php

require('../vendor/autoload.php');
require_once('TwitterAPIExchange.php');
// Access tokens are stored as environment varibales on Heroku server.
$settings = array(
    'oauth_access_token' => getenv('access_token'),
    'access_token_secret' => getenv('access_token_secret'),
    'consumer_key' => getenv('consumer_key'),
    'consumer_key_secret' => getenv('consumer_key_secret')
)

// setup for twitter API request
$url = 'https://api.twitter.com/1.1/followers/list.json';
$getfield = '?skip_status=true&count=200&username=';
$requestMethod = 'GET';
$twitter = new TwitterAPIExchange($settings);

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
    if ($_POST['users']) {
        users = array();
        for (usr in users) {
            get_followers(usr)
        }
        return 'Processing your Twitter POST request.';
    } else {
        return 'Users were not supplied correctly.';
    }
});

/* HELPER FUNCTIONS */
function get_followers(user) {
    followers = array();
    do {
        // grab and append users to array
        res = $twitter ->setGetfield($getfield)
                       ->buildOauth($url, $requestMethod)
                       ->performRequest();
    } while (pages.next_cursor != null);
}

/* Run the application */
$app->run();

?>
