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
	require ('config.php');
	$profiles = array();
	$cursor   = -1;

	while ($cursor != 0) {
		$connection = new TwitterOAuth($consumer_key, $consumer_secret, $oauth_access_token, $oauth_access_token_secret);
		$cursor     = '&cursor='.$cursor;
		$ids        = $connection->get('https://api.twitter.com/1.1/followers/list.json?'.$cursor.'screen_name='.$usr);
		$cursor     = $ids->next_cursor;

		if (!is_array($ids->ids)) {break;}

		// Chunk the ids in to arrays of 100.
		$ids_arrays = array_chunk($ids->ids, 100);
		echo "Raw IDs: ".print_r($ids).'<br />';

		foreach ($ids_arrays as $implode) {
			$user_ids = implode('%2C', $implode);
			$results  = $connection->get('users/lookup', array('user_id' => implode(',', $implode)));
			foreach ($results as $profile) {
				$profiles[$profile->name] = $profile;
			}
		}
	}
	// write out data
	return var_dump($profiles);
}

/* Run the application */
$app->run();

?>
