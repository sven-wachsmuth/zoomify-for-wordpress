/* ZOOMIFY - functionality for Wordpress child themes ****
author: Sven Wachsmuth
references: Plugin "Zoom-Image-Shortcode", zoomify pro documentation
*/

// Scripte, die unterhalb des Child-Themes liegen, registrieren
function zoomify_register_scripts_styles() {
	$root = get_stylesheet_directory_uri();
	wp_enqueue_script( 'zoomify-js', "{$root}/zoomify/my-zoomify-engine-scriptfile-min.js", '', '1.0', false );
	wp_enqueue_style('zoomify-css', "{$root}/zoomify/zoomify-styles.css", '', '1.0');
}
add_action('wp_enqueue_scripts', 'zoomify_register_scripts_styles');

// the shortcode processor for [zoomify option=value option=value...]
function zoomify_shortcode( $atts ) {
	$image_root = "https://mysite.com/my-zoom-image-root/";  // << CHANGE THIS
	// here you'll find all option names with their default values. 
	// if these names will be changed in the engine in future, you only need to adjust this here.
	// parameters always need to be lowercase - https://codex.wordpress.org/Shortcode_API
	$defaults = array (
		'file' => 'nopic.zif',
		'zInitialX' => null,
		'zInitialY'=> null,
		'zInitialZoom' => null,
		'zMinZoom' => null,
		'zMaxZoom' => null,
		'zNavigatorVisible' => 0,
		'zToolbarVisible' => 1,
		'zSliderVisible' => 1,
		'zLogoVisible' => 0,
		'zFullPageVisible' => 1,
		'zInitialFullPage' => 0,
		'zProgressVisible' => 1,
		'zTooltipsVisible' => 1,
		'zToolbarBackgroundVisible' => null,
		'zCoordinatesVisible' => 0,           // tool for copying coordinates
		'zComparisonPath' => null,
		'zSkinPath' => get_stylesheet_directory_uri() . "/zoomify/Skins/Default/",
		'zKeys' => 0,
		'zPanButtonsVisible' => 0,
		'zHotspotPath' => "",
		'zWatermarkPath' => "https://mysite.com/zoom/mywatermark.png"
	);

	// create a new array out of the defaults-array where the keys are lowercase and the values are the same
	// because shortcode options always needs to be lowercase.
	$lowkeys = array_change_key_case( $defaults );
	foreach( $atts as $key => $value ) {
		if( isset( $lowkeys[ strtolower($key) ] ) ) {
			$lowkeys[ strtolower($key) ] = $value;
		}
	}
  // cleaning and update values from the attributes
	foreach( $defaults as $key => $value ) {
		if( is_null( $lowkeys[ strtolower($key) ] ) ){
			unset( $defaults[$key] );
		} else {
			$defaults[$key] = $lowkeys[ strtolower($key) ];
		}
	}

	// initialize file parameter and start building the script block
	$fileUrl = $image_root . $defaults['file'];
	$divId = 'zoomify-' . strtolower( str_replace('.zif', '', substr($fileUrl, strrpos($fileUrl, '/') + 1)));
	unset( $defaults['file'] );

	// add parameters to the script block
	$output = "<script type=\"text/javascript\">Z.showImage(\"{$divId}\", \"{$fileUrl}\", \"";
	$amp_sign = ""; // vor das erste Paar noch kein "&"
	foreach ($defaults as $key => $value) {
		$output .= "{$amp_sign}{$key}={$value}";
		$amp_sign = "&";
	}
	$output .= "\");</script>";
	
  // zoomify-Container 
	$output .= "<div id=\"{$divId}\" class=\"zoomify-wrapper\"></div>";   
	return $output;
}
add_shortcode( 'zoomify', 'zoomify_shortcode' );
