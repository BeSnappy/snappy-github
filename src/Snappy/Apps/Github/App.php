<?php namespace Snappy\Apps\Github;

use Snappy\Apps\App as BaseApp;
use Snappy\Apps\TagsChangedHandler;

class App extends BaseApp implements TagsChangedHandler {

	/**
	 * The name of the application.
	 *
	 * @var string
	 */
	public $name = 'Github';

	/**
	 * The application description.
	 *
	 * @var string
	 */
	public $description = 'Convert tickets into Github issues.';

	/**
	 * The application's icon filename.
	 *
	 * @var string
	 */
	public $icon = 'github.png';

	/**
	 * The application author name.
	 *
	 * @var string
	 */
	public $author = 'UserScape, Inc.';

	/**
	 * The application author e-mail.
	 *
	 * @var string
	 */
	public $email = 'it@userscape.com';

	/**
	 * The settings required by the application.
	 *
	 * @var array
	 */
	public $settings = array(
		array('name' => 'token', 'type' => 'text', 'help' => 'Enter your Github API Token'),
		array('name' => 'owner', 'type' => 'text', 'help' => 'Enter the repository owner'),
		array('name' => 'repository', 'type' => 'text', 'help' => 'Enter the repository name'),
	);

	/**
	 * Handle the creation of a new contact.
	 *
	 * @param  array  $ticket
	 * @param  array  $added
	 * @param  array  $removed
	 * @return void
	 */
	public function handleTagsChanged(array $ticket, array $added, array $removed)
	{
		if (in_array('#github', $added))
		{
			$client = $this->getClient();

			$link = 'URL: https://app.besnappy.com/home#ticket/'.$ticket['id'];
			$body = head($ticket['notes']);

			$client->api('issues')->create($this->config['owner'], $this->config['repository'], array(
				'title' => $ticket['default_subject'],
				'body' => $link.PHP_EOL.PHP_EOL.$body,
			));
		}
	}

	/**
	 * Get the Github client instance.
	 *
	 * @return \Github\Client
	 */
	public function getClient()
	{
		$client = new \Github\Client;

		$client->authenticate($this->config['token']);

		return $client;
	}

}