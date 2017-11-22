<?php
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}

/**
 * Create object relations of type 'tiki.wiki.include', by parsing all
 * wiki pages and comments and check for Plugin Include calls.
 *
 * These are used to keep consistency when renaming an included page and to
 * warn user when an edition can affect other pages.
 *
 * @param Installer $installer
 */
function upgrade_20171121_create_plugin_include_relations_tiki($installer)
{

	global $prefs;
	$prefs['wikiplugin_maximum_passes'] = 500;

    $create_relations = function ($installer, $type, $objectId, $data) {
        $matches = WikiParser_PluginMatcher::match($data);
        $argParser = new WikiParser_PluginArgumentParser();
        foreach ($matches as $match) {
            if ($match->getName() == 'include') {
                $params = $argParser->parse($match->getArguments());
                $existing = $installer->table('tiki_object_relations')->fetchCount([
                    'relation' => 'tiki.wiki.include',
                    'source_type' => $type,
                    'source_itemId' => $objectId,
                    'target_type' => 'wiki page',
                    'target_itemId' => $params['page'],
                ]);
                if (!$existing) {
                    $installer->query(
                        'INSERT INTO `tiki_object_relations` (`relation`, `source_type`, `source_itemId`, `target_type`, `target_itemId`) VALUES(?, ?, ?, ?, ?)',
                        [
                            'tiki.wiki.include',
                            $type,
                            $objectId,
                            'wiki page',
                            $params['page'],
                        ]
                    );
                }
            }
        }
    };

    /**
     * Wiki pages
     */
	$tiki_pages = $installer->table('tiki_pages');
	$pages = $tiki_pages->fetchAll();

	foreach ($pages as $page) {
        $create_relations($installer, 'wiki page', $page['pageName'], $page['data']);
	}

    /**
     * Comments and forum posts
     */
	$table = $installer->table('tiki_comments');
	foreach ($table->fetchAll() as $comment) {
		if ($comment['objectType'] == 'forum') {
			$type = 'forum post';
		} else {
			$type = $comment['objectType'] . ' comment';
		}
        $create_relations($installer, $type, $comment['threadId'], $comment['data']);
	}

    /**
     * Blog posts
     */
	$table = $installer->table('tiki_blog_posts');

	foreach ($table->fetchAll() as $item) {
        $create_relations($installer, 'post', $item['postId'], $item['data']);
	}

    /**
     * Articles
     */
	$table = $installer->table('tiki_articles');

	foreach ($table->fetchAll() as $item) {
        $data = $item['heading'] . "\n" . $item['body'];
        $create_relations($installer, 'article', $item['articleId'], $data);
	}

    /**
     * Calendar events
     */
	$table = $installer->table('tiki_calendar_items');

	foreach ($table->fetchAll() as $item) {
        $create_relations($installer, 'calendar event', $item['calitemId'], $item['description']);
	}

    /**
     * Trackers
     */
	$table = $installer->table('tiki_trackers');

	foreach ($table->fetchAll() as $item) {
        if ($item['descriptionIsParsed'] == 'y') {
            $create_relations($installer, 'tracker', $item['trackerId'], $item['description']);
        }
	}

    /**
     * Tracker fields
     */
	$table = $installer->table('tiki_tracker_fields');

	foreach ($table->fetchAll() as $item) {
        if ($item['descriptionIsParsed'] == 'y') {
            $create_relations($installer, 'trackerfield', $item['fieldId'], $item['description']);
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
            $create_relations($installer, 'trackeritemfield', $objectId, $itemField['value']);
        }
	}
}


