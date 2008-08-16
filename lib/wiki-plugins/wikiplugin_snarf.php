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
		'params' => array(
			'url' => array(
				'required' => true,
				'name' => tra('URL'),
				'description' => tra('Full URL to the page to include.'),
			),
			'regex' => array(
				'required' => true,
				'name' => tra('Regular Expression'),
				'description' => tra('PCRE compliant regular expression'),
			),
			'regexres' => array(
				'required' => false,
				'name' => tra('Regular Expression Part'),
				'description' => tra('ex: $1'),
			),
		),
	);
}

function wikiplugin_snarf($data, $params)
{

    global $tikilib;

    extract ($params,EXTR_SKIP);

    if( ! isset( $url ) )
    {
	return ("<b>". tra( "Missing url parameter for SNARF plugin." ) . "</b><br />");
    }

    if( function_exists("curl_init") )
    {
	//print("<pre>url: $url</pre>"); 

	$ch = curl_init( $url ); 
	// use output buffering instead of returntransfer -itmaybebuggy 
	ob_start(); 
	curl_exec($ch); 
	curl_close($ch); 
	$html = ob_get_contents(); 
	ob_end_clean(); 

	$snarf = preg_replace( "/.*<\s*body[^>]*>(.*)<[^>]*\/\s*body[^>]*>.*/si", "$1", $html );

	// If the user specified a more specialized regex
	if( isset( $regex ) && isset( $regexres ) 
		and preg_match('/^(.)(.)+\1[^e]*$/', $regex))
	{
	    //print("<pre>regex: ".htmlspecialchars($regex)."</pre>"); 
	    //print("<pre>regexres: ".htmlspecialchars($regexres)."</pre>"); 
	    $snarf = preg_replace( $regex, $regexres, $snarf );
	}

	//print("<pre>BODY: " . htmlspecialchars( $snarf ) . "</pre>"); 

	$ret = "{CODE(wrap=>1,wiki=>1,caption=>" . $data . ")}" . $snarf . "{CODE}";
    } else {
	$ret = "<p>You need php-curl for the SNARF plugin!</p>\n";
    }


    return $ret;
}

?>
