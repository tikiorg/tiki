<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Tiki_Formula_Function_ArticleInfo extends Math_Formula_Function
{
	function evaluate( $element )
	{
		global $prefs;

		if ( count($element) != 3 ) {
			$this->error(tra('Expecting three arguments for article-info.'));
		}

		$supported = array( 'rating', 'age-second', 'age-hour', 'age-day', 'age-week', 'age-month', 'view-count' );
		if ( ! in_array($element[2], $supported) ) {
			$this->error(tra('Unsupported property. Supported properties are: ') . implode(', ', $supported));
		}

		if ( $prefs['feature_articles'] != 'y' ) {
			$this->error(tra('The Articles feature is not activated.'));
		}

		$type = $this->evaluateChild($element[0]);
		$object = $this->evaluateChild($element[1]);
		$property = $element[2];

		if ( $type == 'article' ) {
			$artlib = TikiLib::lib('art');
			$article = $artlib->get_article($object, false);

			if ( $property == 'rating' ) {
				return $article['rating'];
			} elseif ( $property == 'view-count' ) {
				return $article[ 'nbreads' ];
			} elseif ( substr($property, 0, 4) == 'age-' ) {
				$age = time() - $article['publishDate'];

				switch( $property ) {
				case 'age-hour':
					return max(0, floor($age / 3600));
				case 'age-day':
					return max(0, floor($age / (3600*24)));
				case 'age-week':
					return max(0, floor($age / (3600*24*7)));
				case 'age-month':
					return max(0, floor($age / (3600*24*30)));
				default:
					return max(0, $age);
				}
			}
		} elseif ( $type !== 0 ) {
			$this->error('Only available for articles.');
		}
	}
}

