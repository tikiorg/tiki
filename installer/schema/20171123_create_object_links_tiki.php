<?php
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}

/**
 * Create links in tiki_links table for objects other than wiki pages, 
 * comments and forum posts. 
 *
 * @param Installer $installer
 */
function upgrade_20171123_create_object_links_tiki($installer)
{

    $create_links = function ($installer, $type, $objectId, $data) {
        $parserlib = TikiLib::lib('parser');
        $pages = $parserlib->get_pages($data);
        
		$linkhandle = "objectlink:$type:$objectId";

        foreach ($pages as $page) {
            $installer->query('REPLACE INTO `tiki_links` (`fromPage`, `toPage`) values (?, ?)', [$linkhandle, $page]);
        }
    };

    /**
     * Blog posts
     */
	$table = $installer->table('tiki_blog_posts');

	foreach ($table->fetchAll() as $item) {
        $create_links($installer, 'post', $item['postId'], $item['data']);
	}

    /**
     * Articles
     */
	$table = $installer->table('tiki_articles');

	foreach ($table->fetchAll() as $item) {
        $data = $item['heading'] . "\n" . $item['body'];
        $create_links($installer, 'article', $item['articleId'], $data);
	}

    /**
     * Calendar events
     */
	$table = $installer->table('tiki_calendar_items');

	foreach ($table->fetchAll() as $item) {
        $create_links($installer, 'calendar event', $item['calitemId'], $item['description']);
	}

    /**
     * Trackers
     */
	$table = $installer->table('tiki_trackers');

	foreach ($table->fetchAll() as $item) {
        if ($item['descriptionIsParsed'] == 'y') {
            $create_links($installer, 'tracker', $item['trackerId'], $item['description']);
        }
	}

    /**
     * Tracker fields
     */
	$table = $installer->table('tiki_tracker_fields');

	foreach ($table->fetchAll() as $item) {
        if ($item['descriptionIsParsed'] == 'y') {
            $create_links($installer, 'trackerfield', $item['fieldId'], $item['description']);
        }
	}

    
    /**
     * Tracker item fields
     */
	$trackerFields = $installer->table('tiki_tracker_fields');
    $itemFields = $installer->table('tiki_tracker_item_fields');
	foreach ($trackerFields->fetchAll(['fieldId'], ['type' => 'a']) as $field) {
        $fieldId = $field['fieldId'];
        foreach($itemFields->fetchAll([], ['fieldId' => (int)$fieldId]) as $itemField) {
            $objectId = sprintf("%d:%d", (int)$itemField['itemId'], $fieldId);
            $create_links($installer, 'trackeritemfield', $objectId, $itemField['value']);
        }
	}
}
