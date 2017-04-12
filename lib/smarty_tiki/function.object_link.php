<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function smarty_function_object_link( $params, $smarty )
{
	if ( ! isset( $params['type'], $params['id'] ) &&  ! isset( $params['type'], $params['objectId'] ) && ! isset( $params['identifier'] ) ) {
		return tra('No object information provided.');
	}

	if ( isset( $params['type'], $params['id'] ) ) {
		$type = $params['type'];
		$object = $params['id'];
	} else {
		list($type, $object) = explode(':', $params['identifier'], 2);
	}

    if ( isset( $params['objectId'] ) && ! isset( $params['id'] ) ) {
        $type = $params['type'];
        $object = $params['objectId'];
    }

	$title = isset( $params['title'] ) ? $params['title'] : null;
	$url = isset( $params['url'] ) ? $params['url'] : null;

	switch ( $type ) {
	case 'wiki page':
	case 'wikipage':
	case 'wiki':
		$type = 'wiki page';
		$function = 'smarty_function_object_link_default';
		if (! $title) {
			$title = $object;
		}
		global $prefs;
		if ($prefs['feature_wiki_structure'] === 'y') {
			$structlib = TikiLib::lib('struct');
			$page_id = $structlib->get_struct_ref_id($title);
			if ($page_id) {
				$alias = $structlib->get_page_alias($page_id);
				if ($alias) {
					$title = $alias;
				}
			}
		}
		break;
	case 'user':
		$function = 'smarty_function_object_link_user';
		break;
	case 'external':
	case 'external_extended':
		$function = 'smarty_function_object_link_external';
		break;
	case 'relation_source':
		$function = 'smarty_function_object_link_relation_source';
		break;
	case 'relation_target':
		$function = 'smarty_function_object_link_relation_target';
		break;
	case 'freetag':
		$function = 'smarty_function_object_link_freetag';
		break;
	case 'trackeritem':
		$function = 'smarty_function_object_link_trackeritem';
		break;
	case 'group':
		// Nowhere to link, at least, yet.
		return $object;
	case 'forumpost':
	case 'forum post':
		$function = 'smarty_function_object_link_forumpost';
		break;
	default:
		$function = 'smarty_function_object_link_default';
		break;
	}

	return $function($smarty, $object, $title, $type, $url, $params);
}

function smarty_function_object_link_default( $smarty, $object, $title = null, $type = 'wiki page', $url = null, $params = array() )
{
	global $base_url;

	$smarty->loadPlugin('smarty_modifier_sefurl');
	$smarty->loadPlugin('smarty_modifier_escape');
	$smarty->loadPlugin('smarty_modifier_addongroupname');

	if (empty($title)) {
		$title = TikiLib::lib('object')->get_title($type, $object);
	}

	if (empty($title) && ! empty($params['backuptitle'])) {
		$title = $params['backuptitle'];
	}

	if (empty($title) && $type == 'freetag') {
		// Blank freetag should not be returned with "No title specified"
		return '';
	}

	// get add on object title if needed
	$title = smarty_modifier_addongroupname($title);

	$text = $title;
	$titleAttribute = '';
	if ($type == 'wiki page') {
		$titleAttribute .= ' title="' . smarty_modifier_escape($title) . '"';
		$text = TikiLib::lib('wiki')->get_without_namespace($title);
	}

	$escapedText = smarty_modifier_escape($text ? $text : tra('No title specified'));

	if ($url) {
		$escapedHref = smarty_modifier_escape(TikiLib::tikiUrlOpt($url));
	} else {
		$escapedHref = smarty_modifier_escape(smarty_modifier_sefurl($object, $type));
	}

	$classList = array();

	if ( $type == "blog post" ) {
		$classList[] = "link";
	} elseif ( $type == "freetag" ) {
		$classList[] = 'freetag';
	}

	$metadata = TikiLib::lib('object')->get_metadata($type, $object, $classList);

	if (! empty($params['class'])) {
		$classList[] = $params['class'];
	}

	$class = ' class="' . implode(' ', $classList) . '"';

	if (strpos($escapedHref, '://') === false) {
		//$html = '<a href="' . $base_url . $escapedHref . '"' . $class . $titleAttribute . $metadata . '>' . $escapedText . '</a>';
		// When the link is created for a tiki page, then we do NOT want the baseurl included, 
		// because it might be we are using a reverse proxy or a an ssl offloader, or we access from a public fqdn that is not
		// configured for teh ip adress we run our webserver.
		// Eaxmple: Fqdn = tiki.mydomain.com -> port forwarding/nat to: 192.168.1.110. 
		// In this case links should NOT be generated as absolut urls pointing to  192.168.1.110 which would be the part of the baseUrl.
		$html = '<a href="' . $escapedHref . '"' . $class . $titleAttribute . $metadata . '>' . $escapedText . '</a>';
	} else {
		$html = '<a rel="external" href="' . $escapedHref . '"' . $class . $titleAttribute . $metadata . '>' . $escapedText . '</a>';
	}

	$attributelib = TikiLib::lib('attribute');
	$attributes = $attributelib->get_attributes($type, $object);

	global $prefs;
	if (isset($attributes['tiki.content.source']) && $prefs['fgal_source_show_refresh'] == 'y') {
		$smarty->loadPlugin('smarty_function_icon');
		$smarty->loadPlugin('smarty_function_service');
		$html .= '<a class="file-refresh" href="' .
			smarty_function_service(
				array(
					'controller' => 'file',
					'action' => 'refresh',
					'fileId' => intval($object),
				),
				$smarty
			) . '">' .
			smarty_function_icon(
				array('_id' => 'arrow_refresh',),
				$smarty
			) . '</a>';

		TikiLib::lib('header')->add_js(
			'
			$(".file-refresh").removeClass("file-refresh").click(function () {
			$.getJSON($(this).attr("href"));
			$(this).remove();
			return false;
		});'
		);
	}

	return $html;
}

function smarty_function_object_link_trackeritem( $smarty, $object, $title = null, $type = 'wiki page', $url = null )
{
	global $prefs;
	$pre = null;

	$item = Tracker_Item::fromId($object);

	//Set show status to 'y' by default
	if (!empty($prefs['tracker_status_in_objectlink'])) {
		$show_status = $prefs['tracker_status_in_objectlink'];
	} else {
		$show_status = 'y';
	}

	if (($show_status == 'y') && $item && $status = $item->getDisplayedStatus()) {
		$alt = tr($status);
		$pre = "<img src=\"img/icons/status_$status.gif\" alt=\"$status\"/>&nbsp;";
	}

	return $pre . smarty_function_object_link_default($smarty, $object, $title, $type, $url);
}

function smarty_function_object_link_user( $smarty, $user, $title = null )
{
	$smarty->loadPlugin('smarty_modifier_userlink');

	return smarty_modifier_userlink($user, 'link', 'not_set', $title ? $title : '');
}

function smarty_function_object_link_external( $smarty, $link_orig, $title = null, $type = null )
{
	$cachelib = TikiLib::lib('cache');
	$tikilib = TikiLib::lib('tiki');

	if (substr($link_orig, 0, 4) === 'www.') {
		$link = 'http://' . $link_orig;
	} else {
		$link = $link_orig;
	}

	if ( ! $title ) {
		if ( ! $title = $cachelib->getCached($link, 'object_link_ext_title') ) {
			$body = $tikilib->httprequest($link);
			if ( preg_match('|<title>(.+)</title>|', $body, $parts) ) {
				$title = TikiFilter::get('text')->filter($parts[1]);
			} else {
				$title = $link_orig;
			}

			$cachelib->cacheItem($link, $title, 'object_link_ext_title');
		}
	}

	$smarty->loadPlugin('smarty_modifier_escape');
	$escapedHref = smarty_modifier_escape($link);
	$escapedLink = smarty_modifier_escape($link_orig);
	$escapedTitle = smarty_modifier_escape($title);

	if ( $type == 'external_extended' && "$link_orig" != "$title") {
		$data = '<a rel="external" href="' . $escapedHref . '">' . $escapedLink . '</a>'
					. "<div class='link_extend_title'><em>" . $escapedTitle . "</em></div>";
	} else {
		$data = '<a rel="external" href="' . $escapedHref . '">' . $escapedTitle . '</a>';
	}

	return $data;
}

function smarty_function_object_link_relation_source($smarty, $relationId, $title = null)
{
	return smarty_function_object_link_relation_end($smarty, 'source', $relationId, $title);
}

function smarty_function_object_link_relation_target($smarty, $relationId, $title = null)
{
	return smarty_function_object_link_relation_end($smarty, 'target', $relationId, $title);
}

function smarty_function_object_link_relation_end( $smarty, $end, $relationId, $title = null )
{
	$relationlib = TikiLib::lib('relation');
	$attributelib = TikiLib::lib('attribute');
	$cachelib = TikiLib::lib('cache');

	$cacheKey = "$relationId:$end:$title";

	if ( ! $out = $cachelib->getCached($cacheKey, 'relation_link') ) {
		$relation = $relationlib->get_relation($relationId);

		if ( $relation ) {
			if ( ! $title ) {
				$attributes = $attributelib->get_attributes('relation', $relationId);
				$key = 'tiki.relation.' . $end;

				if ( isset( $attributes[$key] ) && ! empty( $attributes[$key] ) ) {
					$title = $attributes[$key];
				}
			}

			$type = $relation[$end . '_type'];
			$object = $relation[$end . '_itemId'];

			$out = smarty_function_object_link(
				array(
					'type' => $type,
					'id' => $object,
					'title' => $title,
				),
				$smarty
			);

			$cachelib->cacheItem($cacheKey, $out, 'relation_link');
		} else {
			$out = tra('Relation not found.');
		}
	}

	return $out;
}

function smarty_function_object_link_freetag( $smarty, $tag, $title = null )
{
	global $prefs;
	if ($prefs['feature_freetags'] != 'y') {
		return tr('tags disabled');
	}

	if (is_numeric($tag)) {
		$tag = TikiLib::lib('freetag')->get_tag_from_id($tag);
	}

	return smarty_function_object_link_default($smarty, $tag, $tag, 'freetag');
}
function smarty_function_object_link_forumpost( $smarty, $object, $title = null, $type = 'forumpost', $url = null )
{
	$commentslib = TikiLib::lib('comments');
	$comment = $commentslib->get_comment($object);

	while (empty($comment['title'])) {
		$parent = $commentslib->get_comment($comment['parentId']);
		$comment['title'] = $parent['title'];
		if ($parent['parentId'] == 0) {
			break;
		}
	}

	return "<a href='tiki-view_forum_thread.php?threadId=" . $comment['threadId']. "'>" .$comment['title'] . "</a>";
}

