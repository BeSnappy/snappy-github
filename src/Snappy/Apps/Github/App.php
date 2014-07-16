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
	 * Any notes about this application
	 *
	 * @var string
	 */
	public $notes = '<p>You can generate a token from your <a href="https://github.com/settings/applications" target="_blank">GitHub Settings</a> under "Personal Access Token"</p>';

	/**
	 * The application's icon filename.
	 *
	 * @var string
	 */
	public $icon = 'github.png';

	/**
	 * The application service's main website.
	 *
	 * @var string
	 */
	public $website = 'https://github.com';

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
		array('name' => 'token', 'type' => 'text', 'help' => 'Your Github API Token', 'validate' => 'required'),
		array('name' => 'owner', 'type' => 'text', 'help' => 'The repository owner', 'validate' => 'required'),
		array('name' => 'repository', 'type' => 'text', 'help' => 'The repository name', 'validate' => 'required'),
		array('name' => 'tag', 'label' => 'Watch for tag', 'type' => 'text', 'placeholder' => '#github', 'help' => 'Tickets with this tag will create an issue in GitHub.', 'validate' => 'required'),
	);

	/**
	 * Handle tags changed.
	 *
	 * @param  array  $ticket
	 * @param  array  $added
	 * @param  array  $removed
	 * @return void
	 */
	public function handleTagsChanged(array $ticket, array $added, array $removed)
	{
		if (in_array($this->config['tag'], $added))
		{
			$client = $this->getClient();

			$link = 'URL: https://app.besnappy.com/#ticket/'.$ticket['id'];
			$body = last($ticket['notes']);
			$body = $body['content'];

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

		$client->authenticate($this->config['token'], null, \Github\Client::AUTH_HTTP_TOKEN);

		return $client;
	}

}
