<?php
ini_set('max_execution_time', 600);
ini_set('memory_limit', '256M');

require ('../vendor/autoload.php');
use Abraham\TwitterOAuth\TwitterOAuth;

$app          = new Silex\Application();
$app['debug'] = true;

// Register the monolog logging service
$app->register(new Silex\Provider\MonologServiceProvider(), array(
		'monolog.logfile' => 'php://stderr',
	));

// Our web handlers
$app->get('/',

function () use ($app) {
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
				echo get_followers($usr);
				echo '</div>';
			}
			return '<h3>That\'s all the data!</h3>';
		} else {
			return 'ERROR: Users were not supplied correctly.';
		}
	});

//  HELPER FUNCTIONS
function get_followers($usr) {
	$profiles = array();
	$cursor   = -1;
	// Access tokens are stored as environment varibales on Heroku server.
	$consumer_key        = getenv('consumer_key');
	$consumer_secret     = getenv('consumer_key_secret');
	$access_token        = getenv('access_token');
	$access_token_secret = getenv('access_token_secret');

	$connection = new TwitterOAuth($consumer_key, $consumer_secret, $access_token, $access_token_secret);
	// Empty array that will be used to store followers.
	$profiles = array();
	// Get the ids of all followers.
	$ids = $connection->get('followers/ids');
	// Chunk the ids in to arrays of 100.
	$ids_arrays = array_chunk($ids->ids, 100);

	// Loop through each array of 100 ids.
	foreach ($ids_arrays as $implode) {
		// Perform a lookup for each chunk of 100 ids.
		$results = $connection->get('users/lookup', array('user_id' => implode(',', $implode)));
		// Loop through each profile result.
		foreach ($results as $profile) {
			// Use screen_name as key for $profiles array.
			$profiles[$profile->screen_name] = $profile;
		}
	}
	// Array of user objects.
	return var_dump($profiles);
}
/* Run the application */
$app->run();

?>
