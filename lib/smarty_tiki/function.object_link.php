<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
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
	if ( ! isset( $params['type'], $params['id'] ) && ! isset( $params['identifier'] ) ) {
		return tra('No object information provided.');
	}

	if ( isset( $params['type'], $params['id'] ) ) {
		$type = $params['type'];
		$object = $params['id'];
	} else {
		list($type, $object) = explode(':', $params['identifier'], 2);
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
			global $structlib;
			include_once ('lib/structures/structlib.php');
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
	default:
		$function = 'smarty_function_object_link_default';
					break;
	}

	return $function($smarty, $object, $title, $type, $url);
}

function smarty_function_object_link_default( $smarty, $object, $title = null, $type = 'wiki page', $url = null )
{
	global $base_url;
	
	$smarty->loadPlugin('smarty_modifier_sefurl');
	$smarty->loadPlugin('smarty_modifier_escape');

	if (empty($title)) {
		$title = TikiLib::lib('object')->get_title($type, $object);
	}

	if (empty($title) && $type == 'freetag') {
		// Blank freetag should not be returned with "No title specified"
		return '';
	}

	$escapedPage = smarty_modifier_escape($title ? $title : tra('No title specified'));

	if ($url) {
		$escapedHref = smarty_modifier_escape($url);
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
	$class = ' class="' . implode(' ', $classList) . '"';

	$html = '<a href="' . $base_url . $escapedHref . '"' . $class . $metadata . '>' . $escapedPage . '</a>';

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

function smarty_function_object_link_user( $smarty, $user, $title = null )
{
	$smarty->loadPlugin('smarty_modifier_userlink');

	return smarty_modifier_userlink($user, 'link', 'not_set', $title ? $title : '');
}

function smarty_function_object_link_external( $smarty, $link_orig, $title = null, $type = null )
{
	global $cachelib; require_once 'lib/cache/cachelib.php';
	global $tikilib;

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
		$data = '<a href="' . $escapedHref . '">' . $escapedLink . '</a>'
					. "<div class='link_extend_title'><em>" . $escapedTitle . "</em></div>";
	} else {
		$data = '<a href="' . $escapedHref . '">' . $escapedTitle . '</a>';
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
	global $relationlib; require_once 'lib/attributes/relationlib.php';
	global $attributelib; require_once 'lib/attributes/attributelib.php';
	global $cachelib; require_once 'lib/cache/cachelib.php';

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
		return tr('freetags disabled');
	}

	if (is_numeric($tag)) {
		$tag = TikiLib::lib('freetag')->get_tag_from_id($tag);
	}

	return smarty_function_object_link_default($smarty, $tag, $tag, 'freetag');
}

