<?php

require ('../vendor/autoload.php');
require_once ('TwitterAPIExchange.php');

$app          = new Silex\Application();
$app['debug'] = true;

// Register the monolog logging service
$app->register(new Silex\Provider\MonologServiceProvider(), array(
		'monolog.logfile' => 'php://stderr',
	));

// Our web handlers
$app->get('/', function () use ($app) {
		$app['monolog']->addDebug('logging output.');
		return 'Hello';
	});

$app->get('/twitter', function () use ($app) {
		$app['monolog']->addDebug('logging GET Twitter page output.');
		return 'On the Twitter tracking page.';
	});

$app->post('/twitter', function () use ($app) {
		echo '<h3>Here are the results of your API call</h3><br /><p>'.$data.'</p>';
		$data = array();
		$app['monolog']->addDebug('logging POST Twitter page output.');
		$users = $_POST['users'];
		if ($users != null && $users != '') {
			$users = explode(',', $users);
			foreach ($users as $usr) {
				echo '<div><p>Followers of: '.$usr.'</p>';
				array_push($data, get_followers($usr));
				echo '</div>';
			}
			return '<h3>That\'s all the data!</h3>';
		} else {
			return 'ERROR: Users were not supplied correctly.';
		}
	});

/* HELPER FUNCTIONS */
function get_followers($usr) {
	// Empty array to store all Jsonified follower data
	$followers = array();
	// Access tokens are stored as environment varibales on Heroku server.
	$settings = array(
		'consumer_key'              => getenv('consumer_key'),
		'consumer_secret'           => getenv('consumer_key_secret'),
		'oauth_access_token'        => getenv('access_token'),
		'oauth_access_token_secret' => getenv('access_token_secret')
	);

	// setup for twitter API request
	$twitter       = new TwitterAPIExchange($settings);
	$url           = urlencode('https://api.twitter.com/1.1/followers/list.json');
	$requestMethod = urlencode('GET');
	$getField      = '?count=200&screen_name=';
	$cursor        = -1;
	$getField_u    = urlencode($getField.$usr.'skip_status=1');
	echo $usr;
	do {
		// grab the data from the API and store as associative array
		$res = json_decode($twitter->setGetfield($getField_u)
			->buildOauth($url, $requestMethod)->performRequest()
			, $assoc = TRUE);
		array_push($followers, $res);
		$cursor = $res['next_cursor'];
		// write out data
		echo '<pre>';
		print_r($res);
		echo '</pre>';
	} while ($cursor != 0);
	return $followers;
}

/* Run the application */
$app->run();

?>
