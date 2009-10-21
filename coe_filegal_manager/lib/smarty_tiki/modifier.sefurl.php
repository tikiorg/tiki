<?php
// CVS: $Id: modifier.sefurl.php,v 1.1.2.2 2008-02-16 22:40:31 sylvieg Exp $

// Translate only if feature_multilingual is on

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}

function smarty_modifier_sefurl($source, $type='wiki', $with_next = '', $all_langs='' ) {
	global $prefs, $wikilib, $smarty;
	include_once('lib/wiki/wikilib.php');

	switch($type){
	case 'wiki page':
	case 'wiki':
		return $wikilib->sefurl($source, $with_next, $all_langs);
	case 'blog':
		$href = 'tiki-view_blog.php?blogId='.$source;
		break;
	case 'blogpost':
		$href = 'tiki-view_blog_post.php?postId='.$source;
		break;
	case 'gallery':
		$href = 'tiki-browse_gallery.php?galleryId='. $source;
		break;
	case 'videogallery':
		$href = 'tiki-browse_video_gallery.php?galleryId='. $source;
		break;
	case 'article':
		$href = 'tiki-read_article.php?articleId='. $source;
		break;
	case 'file':
		$href = 'tiki-download_file.php?fileId='. $source;
		break;
	case 'thumbnail':
		$href = 'tiki-download_file.php?fileId='. $source.'&amp;thumbnail';
		break;
	case 'display':
		$href = 'tiki-download_file.php?fileId='. $source.'&amp;display';
		break;
	case 'preview':
		$href = 'tiki-download_file.php?fileId='. $source.'&amp;preview';
		break;
	default:
		$href = $source;
		break;
	}
	if ($with_next) {
		$href .= '&amp;';
	}
	if ($prefs['feature_sefurl'] == 'y') {
		include_once('tiki-sefurl.php');
		return filter_out_sefurl($href, $smarty, $type, '', $with_next);
	} else {
		return $href;
	}
}
