<?php


// Wiki plugin to display a SWF myspace playlist in a wiki page  

function wikiplugin_myspace_help() {
        return tra("Displays a SWF myspace mp3 playlist on the wiki page").":<br />~np~{MYSPACE(page=>myspace_page)}{MYSPACE}~/np~";
}

function wikiplugin_myspace($data, $params) {
	
	extract ($params,EXTR_SKIP);

	if (!isset($page)) {
		return "error page parameter requested";
	}
	$ch = curl_init("http://www.myspace.com/$page");
	//$ch = curl_init("http://www.google.com/");
	//curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
 	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$data = curl_exec($ch);
	curl_close($ch);
	if (!$data) return "pas d'chance";

	$a=stripos($data, '<OBJECT id="mp3player" ');
	$data=substr($data, $a);
	$a=stripos($data, '</OBJECT>');
	$data=substr($data, 0, $a + strlen('</OBJECT>'));

	$data=str_replace("\n", " ", $data);
	$data=str_replace("\r", " ", $data);

	return $data;

}

?>
