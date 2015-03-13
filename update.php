<?php
/**
 * The remote host file to process update requests
 *
 */
if ( !isset( $_POST['action'] ) ) {
	echo '0';
	exit;
}

//set up the properties common to both requests 
$obj = new stdClass();
$obj->slug = 'plugin.php';  
$obj->name = 'Plugin';
$obj->plugin_name = 'plugin.php';
$obj->new_version = '1.1';
// the url for the plugin homepage
$obj->url = 'http://www.example.com/plugins/my-plugin';
//the download location for the plugin zip file (can be any internet host)
$obj->package = 'http://mybucket.s3.amazonaws.com/plugin/plugin.zip';

switch ( $_POST['action'] ) {

case 'version':  
	echo serialize( $obj );
	break;  
case 'info':   
	$obj->requires = '4.0';  
	$obj->tested = '4.0';  
	$obj->downloaded = 12540;  
	$obj->last_updated = '2012-10-17';  
	$obj->sections = array(  
		'description' => 'The new version of the Auto-Update plugin',  
		'another_section' => 'This is another section',  
		'changelog' => 'Some new features'  
	);
	$obj->download_link = $obj->package;  
	echo serialize($obj);  
case 'license':  
	echo serialize( $obj );  
	break;  
}  

?>
