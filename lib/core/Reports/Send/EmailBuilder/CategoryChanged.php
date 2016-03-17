<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * Class for category_changed events
 */
class Reports_Send_EmailBuilder_CategoryChanged extends Reports_Send_EmailBuilder_Abstract
{
	public function getTitle()
	{
		return tr('Changes in categories:');
	}

	public function getOutput(array $change)
	{
		$base_url = $change['data']['base_url'];

		if ($change['data']['action'] == 'object entered category') {

			$output = '<u>' . $change['data']['user'] . '</u> '.
				tr(
					'added the %0 %1 to the category %2',
					$change['data']['objectType'],
					"<a href=\"{$base_url}{$change['data']['objectUrl']}\">{$change['data']['objectName']}</a>",
					"<a href=\"{$base_url}tiki-browse_categories.php?parentId={$change['data']['categoryId']}&deep=off\">{$change['data']['categoryName']}</a>"
				);

		} elseif ($change['data']['action']=="object leaved category") {

			$output = '<u>' . $change['data']['user'] . '</u>' .
				tr(
					"removed the %0 %1 from the category %2",
					$change['data']['objectType'],
					"<a href=\"{$base_url}{$change['data']['objectUrl']}\">{$change['data']['objectName']}</a>",
					"<a href=\"{$base_url}tiki-browse_categories.php?parentId={$change['data']['categoryId']}&deep=off\">{$change['data']['categoryName']}</a>."
				);

		} elseif ($change['data']['action'] == 'category created') {

			$output = '<u>' . $change['data']['user'] . '</u> ' . tra('created the subcategory') .
					" <a href=\"{$base_url}tiki-browse_categories.php?parentId=" . $change['data']['categoryId'] . "&deep=off\">" .
					$change['data']['categoryName'] . '</a> ' . tra('in') .
					" <a href=\"{$base_url}tiki-browse_categories.php?parentId=" . $change['data']['parentId'] . "&deep=off\">" .
					$change['data']['parentName'] . '</a>.';

		} elseif ($change['data']['action'] == 'category removed') {

			$output = '<u>' . $change['data']['user'] . '</u> ' . tra('removed the subcategory') .
					" <a href=\"{$base_url}tiki-browse_categories.php?parentId=" . $change['data']['categoryId'] . "&deep=off\">" .
					$change['data']['categoryName'] . '</a> ' . tra('from') . " <a href=\"{$base_url}tiki-browse_categories.php?parentId=" .
					$change['data']['parentId'] . "&deep=off\">" . $change['data']['parentName'] . '</a>.';

		} elseif ($change['data']['action'] == 'category updated') {
			$output = "<u>" . $change['data']['user'] . '</u> ' . tra('edited the category') .
					" <a href=\"{$base_url}tiki-browse_categories.php?parentId=" . $change['data']['categoryId'] .
					"&deep=off\">" . $change['data']['categoryName'] . '</a>';
		}

		return $output;
	}
}
