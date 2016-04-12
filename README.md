# Self-Hosted-WordPress-Plugin-repository

Create your own self-hosted WordPress Plugin repository for pushing automatic updates.

For integration with Composer, please use [wp-autoupdate](https://github.com/wpplex/wp-autoupdate)

## How to use

1) Place the wp_autoupdate.php file somewhere in your plugin directory.
```php
require_once( 'wp_autoupdate.php' );
```
2) Hook the init function to initiatilize the update function when your plugin loads. Best put in your main plugin .php file:
```php
	function snb_activate_au()
	{
		// set auto-update params
		$plugin_current_version = '<your current version> e.g. "0.6"';
		$plugin_remote_path     = '<remote path to your update server> e.g. http://update.example.com'
		$plugin_slug            = plugin_basename(__FILE__);
		$license_user           = snb_opt('snb_license_user');
		$license_key            = snb_opt('snb_license_key');

		// only perform Auto-Update call if a license_user and license_key is given
		if ( $license_user && $license_key && $plugin_remote_path )
		{
			new wp_autoupdate ($plugin_current_version, $plugin_remote_path, $plugin_slug, $license_user, $license_key);
		}
	}

	add_action('init', 'snb_activate_au');
```

3) Create your server back-end to handle the update requests. When Wordpress loads your plugin, it will check the given remote path to see if an update is availabe through the returned transient. For a basic implementation see below. Note however this example does not provide any protection or security, it serves as a demonstration purpose only.

```php
if (isset($_POST['action'])) {
  switch ($_POST['action']) {
    case 'version':
      echo '1.1';
      break;
    case 'info':
      $obj                = new stdClass();
      $obj->slug          = 'plugin.php';
      $obj->plugin_name   = 'plugin.php';
      $obj->new_version   = '1.1';
      $obj->requires      = '3.0';
      $obj->tested        = '3.3.1';
      $obj->downloaded    = 12540;
      $obj->last_updated  = '2012-01-12';
      $obj->sections      = array(
          'description'     => 'The new version of the Auto-Update plugin',
          'another_section' => 'This is another section',
          'changelog'       => 'Some new features'
      );
      $obj->download_link = 'http://localhost/update.php';
      echo serialize($obj);
    case 'license':
      echo 'false';
      break;
  }
} else {
    header('Cache-Control: public');
    header('Content-Description: File Transfer');
    header('Content-Type: application/zip');
    readfile('update.zip');
}
```

You could find detailed explanation and example of usage [here](http://code.tutsplus.com/tutorials/a-guide-to-the-wordpress-http-api-automatic-plugin-updates--wp-25181)
