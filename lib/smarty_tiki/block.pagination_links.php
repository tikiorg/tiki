<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

/**
 * smarty_block_pagination_links: Generate pagination links
 *
 * url: base url to use for links (specified through the content between the pagination_links starting and ending tags).
 *    Defaults to the current URL.
 *
 * params:
 *  - cant: Total number of items. [required]
 *  - offset: Current offset. Defaults to 0.
 *  - reloff: Current relative offset (to keep the original offset unchanged). If not specified, reloff is not used and offset is changed.
 *  - itemname: Untranslated string to use as the item name. Defaults to 'Page'.
 *  - noimg: If set to 'y', will use text instead of images for next/prev links. Only images mode supports first/last links. Defaults to 'n'.
 *  - class: CSS class to use for the whole div. Defaults to 'mini'.
 *  - template: template (.tpl file) used for AJAX_href links. Special value 'noauto',
 *       to get the template that has the same name of the current script (by changing the file extension from .php to .tpl)
 *       and set htmlelement to 'tiki-center' as a default.
 *  - htmlelement: htmlelement used for AJAX_href links.
 *  - step: number to add to an offset to go to the next item. Defaults to 1.
 *  - clean: remove empty arguments from generated URLs (to reduce URL lenght). Defaults to 'y'.
 *  - next: show (or not) next (and last) links. Defaults to 'y' if next elements exists.
 *  - prev: show (or not) previous (and first) links. Defaults to 'y' if previous elements exists.
 *  - usedots: Defaults to 'y'. Only used when 'direct_pagination' pref is set to 'y'.
 *      Instead of displaying one link for each item, only display some items like this
 *      (dots are not replaced by links, it's just a separation text):
 *      1 2 3 ... k-2 k-1 k k+1 k+2 ...  n-2 n-1 n
 *  - offset_arg: Name of the URL argument that contains the offset. Defaults to 'offset'.
 *	- zero_based_offset: Items addressed as zero-based (defaults to 'y'). If 'n' then "one based" offset used (1 to cant + 1)
 *		(jb tiki5: only fully tested without reloffset and step=1) 
 *	- show_numbers: Show/hide direct_pagination links, current and total numbers (Defaults to 'y')
 *  - _ajax : if set to 'n', will force disabling AJAX even if the ajax xajax feature is enabled (defaults to 'y')	AJAX_TODO
 */
function smarty_block_pagination_links($params, $url, &$smarty, $repeat) {
	global $prefs;

	if ($repeat) return;

	$html = '';
	$default_type = 'absolute_path';

	// Check main params and return no pagination links if there is a mistake
	if ( ! isset($params['cant']) || $params['cant'] <= 0 ) return '';
	if ( isset($params['step']) && $params['step'] == -1 ) return '';// display all
	if ( ! isset($params['offset']) ) $params['offset'] = 0;
	if ( ! isset($params['offset_arg']) ) $params['offset_arg'] = 'offset';
	if ( ! isset($params['zero_based_offset']) ) $params['zero_based_offset'] = 'y';
	if ( ! isset($params['show_numbers']) ) $params['show_numbers'] = 'y';
	if ($params['zero_based_offset'] != 'y') {
		//$params['offset']--;
		$zero_based_min = 1;
		$zero_based_maxminus = 0;
	} else {
		$zero_based_min = 0;
		$zero_based_maxminus = 1;
	}
	$params['_ajax'] = isset($params['_ajax']) ? $params['_ajax'] : 'y';
	if ( isset($params['reloff']) && (
		$params['reloff'] + $params['offset'] >= $params['cant']
		|| $params['reloff'] + $params['offset'] < $zero_based_min
	) ) return '';
	if ( ! isset($params['reloff']) && ( $params['offset'] >= $params['cant'] + $zero_based_min || $params['offset'] < $zero_based_min ) ) return '';
	// Include smarty functions used below
	require_once $smarty->_get_plugin_filepath('block', 'ajax_href');
	require_once $smarty->_get_plugin_filepath('function', 'query');
	require_once $smarty->_get_plugin_filepath('function', 'icon');

	// Make sure every params are initialized
	if ( ! isset($params['itemname']) ) $params['itemname'] = 'Page';
	if ( ! isset($params['noimg']) ) $params['noimg'] = ( $prefs['pagination_icons'] == 'n' ? 'y' : 'n' );
	if ( ! isset($params['usedots']) ) $params['usedots'] = 'y';
	if ( ! isset($params['class']) ) $params['class'] = 'mini';
	if ( ! isset($params['htmlelement']) ) $params['htmlelement'] = 'tiki-center';
   if ( ! isset($params['template']) ) {
      $params['template'] = basename($_SERVER['PHP_SELF'], '.php').'.tpl';
      if ( $params['template'] == 'tiki-index.tpl' ) {
         $params['template'] = 'tiki-show_page.tpl';
      }
   }

	if ( ! file_exists('templates/'.$params['template']) || $params['template'] == 'noauto' ) {
		$params['htmlelement'] = '';
		$params['template'] = '';
	}

	if ( ! isset($params['step']) || $params['step'] <= 0 ) {
		$params['step'] = 1;
		$nb_pages = $params['cant'];
	} else {
		$nb_pages = ceil($params['cant'] / $params['step']);
	}

	if ( $nb_pages == 0 || ( $nb_pages == 1 && $prefs['pagination_hide_if_one_page'] == 'y' ) ) return '';

	if ( empty($url) || preg_match('/^\s*$/', $url) ) {
		$url = smarty_function_query(array('_type' => $default_type), $smarty);
	}

	// remove empty url arguments (done by default)
	if ( ! isset($params['clean']) || $params['clean'] == 'y' ) {
		$url = preg_replace('/(?<=\?|&amp;)[^=]+=(?=&amp;|$)/U', '', $url);
	}

	// remove old arguments that will be replaced and add new ones
	$url = preg_replace('/(?<=&amp;|&|\?)(move|reloff|'.$params['offset_arg'].')=[^&]*/', '', trim($url));

	// remove &amp; that are redundant or at the end of url
	$url = preg_replace('/(?:(\?|&amp;)(&amp;)+|(\?|&amp;))$/', '\\1', $url);

	$url_args_pos = strpos($url, '?');
	if ( $url_args_pos === false ) {
		$url .= '?';
	} elseif ( $url_args_pos < strlen($url) - 1 ) {
		$url .= '&amp;';
	}

	if ( isset($params['reloff']) ) {
		$prev_offset = 'reloff='.($params['reloff'] - $params['step']).'&amp;'.$params['offset_arg'].'='.$params['offset'];
		$next_offset = 'reloff='.($params['reloff'] + $params['step']).'&amp;'.$params['offset_arg'].'='.$params['offset'];
		$prev_fast_offset = 'reloff='.($params['reloff'] - $params['step'] * ceil( $nb_pages / 10 )).'&amp;'.$params['offset_arg'].'='.$params['offset'];
		$next_fast_offset = 'reloff='.($params['reloff'] + $params['step'] * ceil( $nb_pages / 10 )).'&amp;'.$params['offset_arg'].'='.$params['offset'];
		$real_offset = $params['offset'] + $params['reloff'];
	} else {
		$prev_offset = $params['offset_arg'].'='.max($zero_based_min, $params['offset'] - $params['step']);
		$next_offset = $params['offset_arg'].'='.min($params['cant'] - $zero_based_maxminus, $params['offset'] + $params['step']);
		$prev_fast_offset = $params['offset_arg'].'='.max($zero_based_min, $params['offset'] - $params['step'] * ceil( $nb_pages / 10 ));
		$next_fast_offset = $params['offset_arg'].'='.min($params['cant'] - $zero_based_maxminus, $params['offset'] + $params['step'] * ceil( $nb_pages / 10 ));
		$real_offset = $params['offset'];
	}

	if ( ! isset($params['next']) ) {
		$params['next'] = ( $real_offset < ($nb_pages - $zero_based_maxminus) * $params['step'] ) ? 'y' : 'n';
	}
	if ( ! isset($params['prev']) ) {
		$params['prev'] = ( $real_offset > $zero_based_min ) ? 'y' : 'n';
	}

	// Max. number of links when using direct pagination
	$max_middle_links = max(0, $prefs['direct_pagination_max_middle_links']);
	$max_ending_links = ( $prefs['pagination_firstlast'] != 'n' ) ? max(0, $prefs['direct_pagination_max_ending_links']) : 0;
	$max_links = ( 1 + $max_ending_links + $max_middle_links ) * 2 + 1;

	// Handle next/prev images
	if ( $params['noimg'] == 'n' ) {
		$tmp = array(
			'first' => tra("First {$params['itemname']}"),
			'last' => tra("Last {$params['itemname']}"),
			'next' => tra("Next {$params['itemname']}"),
			'previous' => tra("Prev {$params['itemname']}"),
			'next_fast' => tra('Fast Next'),
			'previous_fast' => tra('Fast Prev'),
		);
		$images = array();
		foreach ( $tmp as $ik => $iv ) {
			$images[$ik] = smarty_function_icon(
				array(
					'_id' => 'resultset_'.$ik,
					'border' => '0',
					'alt' => $iv,
					'style' => 'vertical-align:middle;'
				),
				$smarty
			);
		}
		unset($tmp);
	}

	if ( $params['cant'] > 0 ) {
		if ( ! function_exists('make_prevnext_link') ) {
			function make_prevnext_link($url, $content, $params, $class = 'prevnext') {
				global $smarty;
				
				$link = '<a class="'.$class.'" ';
				if ($params['_ajax'] == 'y') {
					$link .= smarty_block_ajax_href(
						array('template' => $params['template'], 'htmlelement' => $params['htmlelement'], '_ajax' => $params['_ajax'],),
						$url,
						$smarty,
						false
					);
				} else {
					$link .= " href=\"$url\" ";
				}
				$link .= '>'.$content.'</a>';
				
				return $link;
			}
		}

		$html .= '<div class="'.$params['class'].'" style="text-align: center">'; // inline style can be overriden in css using !important

		if ( $prefs['nextprev_pagination'] != 'n' ) {
			if ( $params['prev'] == 'y' ) {
				if ( isset($images) ) {
					if ( $prefs['pagination_firstlast'] != 'n' && $params['offset'] > $params['step'] ) {
						$html .= make_prevnext_link( $url.( isset($params['reloff']) ?
								$params['offset_arg'].'='.$params['offset'].'&amp;reloff=-'.$params['offset'] : $params['offset_arg']."=$zero_based_min"
							), $images['first'], $params, 'prevnext first'
						);
					}
					if ( $prefs['pagination_fastmove_links'] == 'y' && $next_fast_offset != $next_offset ) {
						$html .= make_prevnext_link( $url.$prev_fast_offset, $images['previous_fast'], $params, 'prevnext prev_fast' );
					}
				}
				$html .= ( isset($images) ? '' : '[' )
					.make_prevnext_link($url.$prev_offset, ( isset($images) ? $images['previous'] : tra('Prev') ), $params, 'prevnext prev' )
					.( isset($images) ? '' : '] ' );
	   		} elseif (isset($images)) {
				$html .= '<a class="prevnext"><span style="padding-left:16px"></span></a><a class="prevnext"><span style="padding-left:16px"> </span></a>';
			}
			$html .= '<span class="pagenums">' . tra($params['itemname']);
			if ($params['show_numbers'] == 'y') {
				$html .= ': '.($zero_based_maxminus + floor(($real_offset) / $params['step'])).'/'.$nb_pages;
			}
			$html .= "</span>";
			if ( $params['next'] == 'y' ) {
				$html .= ( isset($images) ? '' : ' [' )
					.make_prevnext_link($url.$next_offset, ( isset($images) ? $images['next'] : tra('Next') ), $params, 'prevnext next' )
					.( isset($images) ? '' : ']' );
				if ( isset($images) ) {
					if ( $prefs['pagination_fastmove_links'] == 'y' && $next_fast_offset != $next_offset ) {
						$html .= make_prevnext_link( $url.$next_fast_offset, $images['next_fast'], $params, 'prevnext next_fast' );
					}
					if ( $prefs['pagination_firstlast'] != 'n'  && $params['offset'] + 2*$params['step'] < $params['cant'] + $zero_based_min ) {
						$i = ( $nb_pages - $zero_based_maxminus ) * $params['step'] ;
						$html .= make_prevnext_link( $url.( isset($params['reloff']) ?
								$params['offset_arg'].'='.$params['offset'].'&amp;reloff='.($i - $params['offset']) : $params['offset_arg'].'='.$i
							), $images['last'], $params, 'prevnext last'
						);
					}
				}
   			} elseif (isset($images)) {
				$html .= '<a class="prevnext"><span style="padding-left:16px"></span></a><a class="prevnext"><span style="padding-left:16px"> </span></a>';
			}
		}

		if ( $prefs['direct_pagination'] == 'y' && $nb_pages > 1 && $params['show_numbers'] == 'y' ) {
			$html .= "<br />";
			$last_dots = false;
			$page_num = floor($real_offset / $params['step']);
			foreach ( range(0, $nb_pages - 1) as $k ) {
				if ( $k + $zero_based_min == $page_num ) {
					$html .= '<span class="prevnext" style="font-weight:bold">'.($k + 1).'</span>';
					$last_dots = false;
				} elseif ( $params['usedots'] != 'y' ||
					( $params['usedots'] == 'y' &&
						( $nb_pages <= $max_links
						|| ( $k <= $max_ending_links && $prefs['pagination_firstlast'] != 'n' )
						|| ( $k >= $nb_pages - $max_ending_links - 1 && $prefs['pagination_firstlast'] != 'n' )
						|| ( abs( $page_num - $k ) ) <= $max_middle_links
						|| ( $prefs['pagination_fastmove_links'] == 'y' && abs( $page_num - $k ) == ceil( $nb_pages / 10 ) )
						)
					)
				) {
					if ( isset($params['reloff']) ) {
						$url_k = $params['offset_arg'].'='.$params['offset']
							.'&amp;reloff='.($params['step'] * $k - $params['offset']);
					} else {
						$url_k = $params['offset_arg'].'='.($params['step'] * ($k + $zero_based_min));
					}
					$html .= make_prevnext_link($url.$url_k, $k+1, $params);
					$last_dots = false;
				} elseif ( ! $last_dots )  {
					$html .= '<span class="prevnext" style="font-weight:bold">&hellip;</span>';
					$last_dots = true;
				}
			}
		}
		$html .= "</div>";
	}
	return $html;
}
