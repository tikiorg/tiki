<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

/*
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
 *  - template: template (.tpl file) used for AJAX_href links. Special value 'auto',
 *       to get the template that has the same name of the current script (by changing the file extension from .php to .tpl)
 *       and set htmlelement to 'tiki-center' as a default.
 *  - htmlelement: htmlelement used for AJAX_hred links.
 *  - step: number to add to an offset to go to the next item. Defaults to 1.
 *  - clean: remove empty arguments from generated URLs (to reduce URL lenght). Defaults to 'y'.
 *  - next: show (or not) next (and last) links. Defaults to 'y' if next elements exists.
 *  - prev: show (or not) previous (and first) links. Defaults to 'y' if previous elements exists.
 *  - usedots: Defaults to 'y'. Only used when 'direct_pagination' pref is set to 'y'.
 *      Instead of displaying one link for each item, only display some items like this
 *      (dots are not replaced by links, it's just a separation text):
 *      1 2 3 ... k-2 k-1 k k+1 k+2 ...  n-2 n-1 n
 *
 */
function smarty_block_pagination_links($params, $url, &$smarty) {
	global $prefs;
	$html = '';

	// Check main params and return no pagination links if there is a mistake
	if ( ! isset($params['cant']) || $params['cant'] <= 0 ) return '';
	if ( ! isset($params['offset']) ) $params['offset'] = 0;
	if ( isset($params['reloff']) && (
		$params['reloff'] + $params['offset'] >= $params['cant']
		|| $params['reloff'] + $params['offset'] < 0
	) ) return '';
	if ( ! isset($params['reloff']) && ( $params['offset'] >= $params['cant'] || $params['offset'] < 0 ) ) return '';

	// Include smarty functions used below
	require_once $smarty->_get_plugin_filepath('block', 'ajax_href');
	require_once $smarty->_get_plugin_filepath('function', 'query');
	require_once $smarty->_get_plugin_filepath('function', 'html_image');

	// Make sure every params are initialized
	if ( ! isset($params['itemname']) ) $params['itemname'] = 'Page';
	if ( ! isset($params['noimg']) ) $params['noimg'] = 'n';
	if ( ! isset($params['usedots']) ) $params['usedots'] = 'y';
	if ( ! isset($params['class']) ) $params['class'] = 'mini';
	if ( ! isset($params['template']) ) $params['template'] = '';
	if ( ! isset($params['htmlelement']) ) {
		$params['htmlelement'] = '';
		if ( $params['template'] == 'auto' ) {
			$params['template'] = str_replace('.php', '.tpl', $_SERVER['PHP_SELF']);
			if ( file_exists('templates/'.$params['template']) ) {
				$params['htmlelement'] = 'tiki-center';
			} else {
				$params['template'] = '';
			}
		}
	}

	if ( ! isset($params['step']) || $params['step'] <= 0 ) {
		$params['step'] = 1;
		$nb_pages = $params['cant'];
	} else {
		$nb_pages = ceil($params['cant'] / $params['step']);
	}
	if ( empty($url) || preg_match('/^\s*$/', $url) ) $url = $_SERVER['PHP_SELF'].'?'.smarty_function_query(null, $smarty);

	// remove empty url arguments (done by default)
	if ( ! isset($params['clean']) || $params['clean'] == 'y' ) {
		$url = preg_replace('/(?<=\?|&amp;)[^=]+=(?=&amp;|$)/U', '', $url);
	}

	// remove old arguments that will be replaced and add new ones
	$url = preg_replace('/(?<=&amp;|&|\?)(move|reloff|offset)=[^&]*/', '', trim($url));

	// remove &amp; that are redundant or at the end of url
	$url = preg_replace('/(?:(\?|&amp;)(&amp;)+|(\?|&amp;))$/', '\\1', $url);

	$url_args_pos = strpos($url, '?');
	if ( $url_args_pos === false ) {
		$url .= '?';
	} elseif ( $url_args_pos < strlen($url) - 1 ) {
		$url .= '&amp;';
	}

	if ( isset($params['reloff']) ) {
		$prev_offset = 'reloff='.($params['reloff'] - $params['step']).'&amp;offset='.$params['offset'];
		$next_offset = 'reloff='.($params['reloff'] + $params['step']).'&amp;offset='.$params['offset'];
		$real_offset = $params['offset'] + $params['reloff'];
	} else {
		$prev_offset = 'offset='.max(0, $params['offset'] - $params['step']);
		$next_offset = 'offset='.min($params['cant'], $params['offset'] + $params['step']);
		$real_offset = $params['offset'];
	}

	if ( ! isset($params['next']) ) {
		$params['next'] = ( $real_offset < ($nb_pages - 1) * $params['step'] ) ? 'y' : 'n';
	}
	if ( ! isset($params['prev']) ) {
		$params['prev'] = ( $real_offset > 0 ) ? 'y' : 'n';
	}

	// Handle next/prev images
	if ( $params['noimg'] == 'n' ) {
		$tmp = array(
			'first' => tra('First '.$params['itemname']),
			'last' => tra('Last '.$params['itemname']),
			'next' => tra('Next '.$params['itemname']),
			'previous' => tra('Prev '.$params['itemname']),
		);
		$images = array();
		foreach ( $tmp as $ik => $iv ) {
			$images[$ik] = smarty_function_html_image(
				array(
					'file' => "pics/icons/resultset_$ik.png",
					'border' => '0',
					'alt' => $iv,
					'title' => $iv,
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
				return "\n".'<a class="'.$class.'" '.smarty_block_ajax_href(
					array('template' => $params['template'], 'htmlelement' => $params['htmlelement']),
					$url,
					$smarty
				).'>'.$content.'</a>';
			}
		}

		$html .= '<div class="'.$params['class'].'">';
		if ( $params['prev'] == 'y' ) {
			if ( isset($images) ) {
				$html .= make_prevnext_link( $url.( isset($params['reloff']) ?
						'offset='.$params['offset'].'&amp;reloff=-'.$params['offset'] : 'offset=0'
					), $images['first'], $params
				);
			}
			$html .= ( isset($images) ? '' : '[' )
				.make_prevnext_link($url.$prev_offset, ( isset($images) ? $images['previous'] : tra('Prev') ), $params )
				.( isset($images) ? '' : ']' );
   		}
		$html .= ' '.tra($params['itemname']).': '.(1 + floor(($real_offset) / $params['step'])).'/'.$nb_pages;
		if ( $params['next'] == 'y' ) {
			$html .= ( isset($images) ? '' : '[' )
				.make_prevnext_link($url.$next_offset, ( isset($images) ? $images['next'] : tra('Next') ), $params )
				.( isset($images) ? '' : ']' );
			if ( isset($images) ) {
				$i = ( $nb_pages - 1 ) * $params['step'] ;
				$html .= make_prevnext_link( $url.( isset($params['reloff']) ?
						'offset='.$params['offset'].'&amp;reloff='.($i - $params['offset']) : 'offset='.$i
					), $images['last'], $params
				);
			}
   		}
		if ( $prefs['direct_pagination'] == 'y' ) {
			$html .= "\n<br />";
			$last_dots = false;
			$page_num = ceil($real_offset / $params['step']);
			foreach ( range(0, $nb_pages - 1) as $k ) {
				if ( $k * $params['step'] == $real_offset ) {
					$html .= "\n".'<span class="prevnext" style="font-weight:bold">'.($k + 1).'</span>';
					$last_dots = false;
				} elseif ( $params['usedots'] != 'y' ||
					( $params['usedots'] == 'y' &&
						( $nb_pages < 12 || $k < 3 || $k >= $nb_pages - 3 || ( abs( $page_num - $k ) ) < 3 )
					)
				) {
					if ( isset($params['reloff']) ) {
						$url_k = 'offset='.$params['offset']
							.'&amp;reloff='.($params['step'] * $k - $params['offset']);
					} else {
						$url_k = 'offset='.($params['step'] * $k);
					}
					$html .= make_prevnext_link($url.$url_k, $k+1, $params);
					$last_dots = false;
				} elseif ( ! $last_dots )  {
					$html .= "\n".'<span class="prevnext" style="font-weight:bold">...</span>';
					$last_dots = true;
				}
			}
		}
		$html .= "\n</div>";
	}
	return $html;
}

?>
