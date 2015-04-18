<?php

require ('../vendor/autoload.php');
require_once ('TwitterAPIExchange.php');
// Access tokens are stored as environment varibales on Heroku server.
$settings = array(
	'oauth_access_token'        => getenv('access_token'),
	'oauth_access_token_secret' => getenv('access_token_secret'),
	'consumer_key'              => getenv('consumer_key'),
	'consumer_secret'           => getenv('consumer_key_secret')
);

// setup for twitter API request
$url           = 'https://api.twitter.com/1.1/followers/list.json';
$getfield      = '?skip_status=true&count=200&username=';
$requestMethod = 'GET';
$twitter       = new TwitterAPIExchange($settings);

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
		$data = array();
		$app['monolog']->addDebug('logging POST Twitter page output.');
		$users = $_POST['users'];
		if ($users != null && $users != '') {
			$users = explode(',', $users);
			echo 'Users passed in: '.$users;
			foreach ($users as $usr) {
				echo 'User processing: '.$usr;
				array_push($data, get_followers($usr));
			}
			return '<h3>Here are the results of your API call</h3><br /><p>'.$data.'</p>';
		} else {
			return 'Users were not supplied correctly.';
		}
	});

/* HELPER FUNCTIONS */
function get_followers($usr) {
	$cursor    = -1;
	$followers = array();
	do {
		$res_dict = json_decode($twitter->setGetfield($getfield.$usr.'&cursor='.$cursor)
			->buildOauth($url, $requestMethod)->performRequest(), $assoc = TRUE);
		$cursor = $res_dict['next_cursor'];
		if ($res_dict['errors'][0]['message'] != '') {
			return '<h3>Looks like there was there was a problem with your request.</h3>
                    <p>Twitter returned the following error message(code:'.$res_dict['errors'][0]['code'].
			'):</p><blockquote>'.$res_dict['errors'][0]['message'].'</blockquote>';
		} else {
			array_push($followers, $res_dict);
		}
	} while ($cursor != 0);
	return $followers;
}

/* Run the application */
$app->run();

?>
