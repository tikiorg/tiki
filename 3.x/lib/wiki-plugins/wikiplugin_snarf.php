<?php
/* Tiki-Wiki plugin SNARF
 * 
 * This plugin replaces itself with the body (HTML) text at the URL given in the url argument.
 *
 */


function wikiplugin_snarf_help() {
    return tra("The SNARF plugin replaces itself with the HTML body of a URL.  Arbitrary regex replacement can be done on this content using regex and regexres, the latter being used as the second argument to preg_replace.").":<br />~np~{SNARF(url=>http://www.lojban.org,regex=>;.*<!-- Content -->(.*)<!-- /Content -->.*;, regexres=>$1)}".tra("This data is put in a CODE caption.")."{SNARF}~/np~";
}

function wikiplugin_snarf_info() {
	return array(
		'name' => tra('Snarf'),
		'documentation' => 'PluginSnarf',
		'description' => tra('Include the content of a remote HTTP page. Regular expression selecting the content portion to include must be specified.'),
		'prefs' => array( 'wikiplugin_snarf' ),
		'validate' => 'all',
		'params' => array(
			'url' => array(
				'required' => true,
				'name' => tra('URL'),
				'description' => tra('Full URL to the page to include.'),
			),
			'regex' => array(
				'required' => false,
				'name' => tra('Regular Expression'),
				'description' => tra('PCRE compliant regular expression'),
			),
			'regexres' => array(
				'required' => false,
				'name' => tra('Regular Expression Part'),
				'description' => tra('ex: $1'),
			),
			'wrap' => array(
				'required' => false,
				'name' => tra('Word Wrap'),
				'description' => tra('0|1, Enable word wrapping on the code to avoid breaking the layout.'),
			),
			'colors' => array(
				'required' => false,
				'name' => tra('Colors'),
				'description' => tra('Syntax highlighting to use. May not be used with line numbers. Available: php, html, sql, javascript, css, java, c, doxygen, delphi, ...'),
			),
			'ln' => array(
				'required' => false,
				'name' => tra('Line numbers'),
				'description' => tra('0|1, may not be used with colors.'),
			),
			'wiki' => array(
				'required' => false,
				'name' => tra('Wiki syntax'),
				'description' => tra('0|1, parse wiki syntax within the code snippet.'),
			),
			'rtl' => array(
				'required' => false,
				'name' => tra('Right to left'),
				'description' => tra('0|1, switch the text display from left to right to right to left'),
			),
			'ishtml' => array(
				'required' => false,
				'name' => tra('Content is HTML'),
				'description' => tra('0|1, display the content as is instead of escaping HTML special chars'),
			),
		),
	);
}

function wikiplugin_snarf($data, $params)
{
    global $tikilib;

    if( ! isset( $params['url'] ) )
    {
	return ("<b>". tra( "Missing url parameter for SNARF plugin." ) . "</b><br />");
    }

    if( function_exists("curl_init") )
    {
	$curl = curl_init(); 
	curl_setopt($curl, CURLOPT_URL, $params['url']);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 2);
	curl_setopt($curl, CURLOPT_TIMEOUT, 2);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($curl, CURLOPT_HEADER, false);
	curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true );
	curl_setopt($curl, CURLOPT_USERAGENT, "TikiWiki Snarf" );
	$snarf = curl_exec($curl);
	curl_close($curl); 

	// If content is HTML, keep only the content of the body
	if ( isset($params['ishtml']) && $params['ishtml'] == 1 ) {
		// Not using preg_replace due to its limitations to 100.000 characters
		$snarf = eregi_replace('^.*<\s*body[^>]*>', '', $snarf);
		$snarf = eregi_replace('<\s*\/body[^>]*>.*$', '', $snarf);
	}

	// If the user specified a more specialized regex
	if ( isset($params['regex']) && isset($params['regexres']) && preg_match('/^(.)(.)+\1[^e]*$/', $params['regex']) ) {
		$snarf = preg_replace( $params['regex'], $params['regexres'], $snarf );
	}

	if ( $data == '' ) $data = NULL;
	$code_defaults = array('caption' => $data, 'wrap' => '1', 'colors' => NULL, 'wiki' => '0', 'ln' => NULL, 'rtl' => NULL, 'ishtml' => NULL);

	foreach ( $code_defaults as $k => $v ) {
		if ( isset($params[$k]) ) $code_defaults[$k] = $params[$k];
		if ( $code_defaults[$k] === NULL ) unset($code_defaults[$k]);
	}

	include_once('lib/wiki-plugins/wikiplugin_code.php');
	$ret = wikiplugin_code($snarf, $code_defaults);

    } else {
	$ret = "<p>You need php-curl for the SNARF plugin!</p>\n";
    }
    return $ret;
}

?>
