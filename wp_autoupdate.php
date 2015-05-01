<?php

class WP_AutoUpdate 
{
	/**
	 * The plugin current version
	 * @var string
	 */
	private $current_version;

	/**
	 * The plugin remote update path
	 * @var string
	 */
	private $update_path;

	/**
	 * Plugin Slug (plugin_directory/plugin_file.php)
	 * @var string
	 */
	private $plugin_slug;

	/**
	 * Plugin name (plugin_file)
	 * @var string
	 */
	private $slug;

	/**
	 * License User
	 * @var string
	 */
	private $license_user;

	/**
	 * License Key 
	 * @var string
	 */
	private $license_key;

	/**
	 * Initialize a new instance of the WordPress Auto-Update class
	 * @param string $current_version
	 * @param string $update_path
	 * @param string $plugin_slug
	 */
	public function __construct( $current_version, $update_path, $plugin_slug, $license_user = '', $license_key = '' )
	{
		// Set the class public variables
		$this->current_version = $current_version;
		$this->update_path = $update_path;

		// Set the License
		$this->license_user = $license_user;
		$this->license_key = $license_key;

		// Set the Plugin Slug	
		$this->plugin_slug = $plugin_slug;
		list ($t1, $t2) = explode( '/', $plugin_slug );
		$this->slug = str_replace( '.php', '', $t2 );		

		// define the alternative API for updating checking
		add_filter( 'pre_set_site_transient_update_plugins', array( &$this, 'check_update' ) );

		// Define the alternative response for information checking
		add_filter( 'plugins_api', array( &$this, 'check_info' ), 10, 3 );
	}

	/**
	 * Add our self-hosted autoupdate plugin to the filter transient
	 *
	 * @param $transient
	 * @return object $ transient
	 */
	public function check_update( $transient )
	{
		if ( empty( $transient->checked ) ) {
			return $transient;
		}

		// Get the remote version
		$remote_version = $this->getRemote_version();

		// If a newer version is available, add the update
		if ( version_compare( $this->current_version, $remote_version->new_version, '<' ) ) {
			$obj = new stdClass();
			$obj->slug = $this->slug;
			$obj->new_version = $remote_version->new_version;
			$obj->url = $remote_version->url;
			$obj->plugin = $this->plugin_slug;
			$obj->package = $remote_version->package;
			$transient->response[$this->plugin_slug] = $obj;
		}
		return $transient;
	}

	/**
	 * Add our self-hosted description to the filter
	 *
	 * @param boolean $false
	 * @param array $action
	 * @param object $arg
	 * @return bool|object
	 */
	public function check_info($false, $action, $arg)
	{
		if (isset($arg->slug) && $arg->slug === $this->slug) {
			$information = $this->getRemote_information();
			return $information;
		}
		return false;
	}

	/**
	 * Return the remote version
	 * @return string $remote_version
	 */
	public function getRemote_version()
	{
		$params = array(
			'body' => array(
				'action' => 'version',
				'license_user' => $this->license_user,
				'license_key' => $this->license_key,
			),
		);
		$request = wp_remote_post ($this->update_path, $params );
		if ( !is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) === 200 ) {
			return unserialize( $request['body'] );
		}
		return false;
	}

	/**
	 * Get information about the remote version
	 * @return bool|object
	 */
	public function getRemote_information()
	{
		$params = array(
			'body' => array(
				'action' => 'info',
				'license_user' => $this->license_user,
				'license_key' => $this->license_key,
			),
		);
		$request = wp_remote_post( $this->update_path, $params );
		if (!is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) === 200 ) {
			return unserialize( $request['body'] );
		}
		return false;
	}

	/**
	 * Return the status of the plugin licensing
	 * @return boolean $remote_license
	 */
	public function getRemote_license()
	{
		$params = array(
			'body' => array(
				'action' => 'license',
				'license_user' => $this->license_user,
				'license_key' => $this->license_key,
			),
		);
		$request = wp_remote_post( $this->update_path, $params );
		if ( !is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) === 200 ) {
			return unserialize( $request['body'] );
		}
		return false;
	}
}
