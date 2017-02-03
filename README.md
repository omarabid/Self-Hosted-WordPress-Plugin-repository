# Self-Hosted-WordPress-Plugin-repository

Create your own self-hosted WordPress Plugin repository for pushing automatic updates.

For integration with Composer, please use [wp-autoupdate](https://github.com/wpplex/wp-autoupdate)

## Quick Start

1) Place the `wp_autoupdate.php` file somewhere in your plugin directory and require it.
```php
require_once( 'wp_autoupdate.php' );
```
2) Hook the [init](https://codex.wordpress.org/Plugin_API/Action_Reference/init) function to initiatilize the update function when your plugin loads. Best put in your main `plugin.php` file:
```php
	function snb_activate_au()
	{
		// set auto-update params
		$plugin_current_version = '<your current version> e.g. "0.6"';
		$plugin_remote_path     = '<remote path to your update server> e.g. http://update.example.com';
		$plugin_slug            = plugin_basename(__FILE__);
		$license_user           = '<optional license username>';
		$license_key            = '<optional license key>';

		// only perform Auto-Update call if a license_user and license_key is given
		if ( $license_user && $license_key && $plugin_remote_path )
		{
			new wp_autoupdate ($plugin_current_version, $plugin_remote_path, $plugin_slug, $license_user, $license_key);
		}
	}

	add_action('init', 'snb_activate_au');
```

The `license_user` and `license_key` fields are optional. You can use these to implement an auto-update functionility for specified customers only. It's left to the developer to implement this if needed.

Note that it's possible to store certain settings as a Wordpress [option](https://codex.wordpress.org/Options_API) like the `plugin_remote_path` version.
If you do so, you can use `get_option()` to get fields like `plugin_remote_path`, `license_user`, `license_key` directly from your plugin. This increases maintainability.

3) Create your server back-end to handle the update requests. You are fee to implement this any way you want, with any framework you want. 
The idea is that when Wordpress loads your plugin, it will check the given remote path to see if an update is availabe through the returned transient. For a basic implementation see the example below. 

Note however this example does not provide any protection or security, it serves as a demonstration purpose only.

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
      $obj->download_link = 'http://localhost/repository/update.zip';
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

4) Make sure the `download_link` points to a `*.zip` file that holds the new version of your plugin. This `*.zip` file must have the same name as your WordPress plugin does. Also the `*.zip` file must NOT contain the plugin files directly, but must have a subfolder with the same name as your plugin to make WordPress play nicely with it.
e.g.:
```php
my-plugin.zip
     │
     └ my-plugin
           │
           ├ my-plugin.php
           ├ README.txt
           ├ uninstall.php
           ├ index.php
           ├ ..
           └ etc.
```

# More information 

You could find detailed explanation and example of usage [here](http://code.tutsplus.com/tutorials/a-guide-to-the-wordpress-http-api-automatic-plugin-updates--wp-25181)
