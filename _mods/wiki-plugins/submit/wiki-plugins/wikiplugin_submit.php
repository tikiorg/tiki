<?php

// Insert social networking site submissions links
// Usage:
// {SUBMIT()}
// 		image=y|n  #show the network's icon? default = y
//		text=y|n   #show the network's name? default = n
// {SUBMIT}
//
// By Rick Sapir (ricks99)
// www.keycontent.org

function wikiplugin_submit_help() {
	return tra("Insert social network submission links").":<br />~np~{SUBMIT(image=y|n,text=y|n)}{SUBMIT}~/np~";
}

function wikiplugin_submit($data, $params) {
	extract ($params,EXTR_SKIP);

//image, default = y (show icons)
	if (!(isset($image))) {
		$image = 'y';
	} elseif ($image != 'n' and $image != 'no') {
		$image = 'y';
	}

//text, default = n (no text)
	if (!(isset($text))) {
		$text = 'n';
	} elseif ($text != 'y' and $text != 'yes') {
		$text = 'n';
	}

//names of the sites. used as ALT and TITLE text. shown if text=y
//you can add or remove items, as desired
$title = array(
	'Digg',
	'del.icio.us',
	'Blinklist',
	'Furl',
	'Reddit',
	'Blogmarks',
	'Magnolia',
	'Sphere',
	'Yahoo!',
	'Technorati',
	'Blue Dot',
	'Simpy',
	'Newsvine',
	'Stumble Upon',
	'co.mments.com',
	'Blinkbits',
	'BlogMemes',
	'Connotea',
    'Mister Wong',
	'Facebook',
	'Google'
);

//icons of the sites. assumed to be 16x16 pixels. shown if image=y
//for each TITLE, there must be a matching ICON
//icons must be located in <TIKI_HOME>/img/icons2/ directory
$icon = array(
	'digg.png',
	'delicious.png',
	'blink.png',
	'furl.png',
	'reddit.png',
	'blogmarks.png',
	'magnolia.png',
	'sphere.png',
	'yahoo.png',
	'technorati.png',
	'bluedot.png',
	'simpy.png',
	'newsvine.png',
	'stumbleupon.png',
	'comments.png',
	'blinkbits.png',
	'blogmemes.png',
	'connotea.png',
    'mrwong.png',
	'facebook.png',
	'google.png'
);

//submission url of the site. refer to each site's faqs for exact url.
//for each TITLE, there must be a matching URL
$url = array(
	'http://digg.com/submit?phase=2&url=http://',
	'https://secure.del.icio.us/login?url=http://',
	'http://www.blinklist.com/index.php?Action=Blink/addblink.php&Url=http://',
	'http://www.furl.net/storeIt.jsp?u=http://',
	'http://reddit.com/submit?url=http://',
	'http://blogmarks.net/my/new.php?mini=1&simple=1&url=http://',
	'http://ma.gnolia.com/bookmarklet/add?url=http://',
	'http://www.sphere.com/search?q=sphereit:http://',
	'http://myweb2.search.yahoo.com/myresults/bookmarklet?u=http://',
	'http://technorati.com/faves/?add=http://',
	'http://bluedot.us/Authoring.aspx?u=http://',
	'http://www.simpy.com/simpy/LinkAdd.do?href=http://',
	'http://www.newsvine.com/_tools/seed&save?u=http://',
	'http://www.stumbleupon.com/submit?url=http://',
	'http://co.mments.com/track?url=http://',
	'http://www.blinkbits.com/bookmarklets/save.php?v=1&source_url=http://',
	'http://www.blogmemes.net/post.php?url=http://',
	'http://www.connotea.org/addpopup?continue=confirm&uri=http://',
    'http://www.mister-wong.com/addurl/?bm_url=http://',
	'http://www.facebook.com/share.php?u=http://',
	'http://www.google.com/bookmarks/mark?op=edit&amp;output=popup&amp;bkmk=http://'
);

//get the current tiki page and server
$my = $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];

//build the submission links
$result = "<p>".tra('Share this page:')."</p><p class='mini'>";

//loop for each $title
for ($i = 0; $i < count($title); $i++) {
	$result .= "&nbsp;<a href='".$url[$i].$my."' title='".tra('Submit to '). $title[$i] . "'>";

//show the image?
	if ($image == 'y'){
		$result .= "<img src='img/icons2/" . $icon[$i] . "' width='16' height='16' border='0' alt='" . $title[$i] . "' />";
	}

//show the text?
	if ($text == 'y'){
		$result .= "&nbsp;" . $title[$i];
	}

$result .= "</a>&nbsp;";

	if (($text == 'y') and (next($title))){
		$result .= "&nbsp;|&nbsp;";
	}

}

$result .= "</p>";

return $result;

}

?>
