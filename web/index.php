<?php
ini_set('max_execution_time', 600);
ini_set('memory_limit', '1024M');

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
		$app['monolog']->addDebug('logging POST Twitter page output.');
		$users = $_POST['users'];
		if ($users != null && $users != '') {
			$users = explode(',', $users);
			foreach ($users as $usr) {
				echo '<div><p>Followers of: '.$usr.'</p>';
				get_followers($usr);
				echo '</div>';
			}
			return '<h3>That\'s all the data!</h3>';
		} else {
			return 'ERROR: Users were not supplied correctly.';
		}
	});

/* HELPER FUNCTIONS */
function get_followers($usr) {
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
	$count         = 'count=200';
	$cursor        = '-1';
	$sn_pre        = 'screen_name=';
	$skip          = 'skip_status=1';
	do {
		// grab the data from the API and store as associative array
		$res = json_decode($twitter->setGetfield(urlencode('?'.$count.'&'.$cursor.'&'.$sn_pre.$usr.'&'.$skip))
			->buildOauth($url, $requestMethod)->performRequest(),
			$assoc = TRUE);
		// write out data
		echo '<pre>Results for: ';
		print_r($cursor);
		echo '<br />';
		print_r($res);
		echo '</pre>';
		$cursor = $res['next_cursor'];
	} while ($cursor != 0);
	return $followers;
}

/* Run the application */
$app->run();

?>
