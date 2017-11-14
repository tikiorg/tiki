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
function upgrade_20171114_create_plugin_include_relations($installer)
{
    global $prefs;
    $prefs['wikiplugin_maximum_passes'] = 500;
    
    $argParser = new WikiParser_PluginArgumentParser();

    $tiki_pages = $installer->table('tiki_pages');
    $pages = $tiki_pages->fetchAll();

    foreach ( $pages as $page ) {
        $matches = WikiParser_PluginMatcher::match($page['data']);
        foreach ( $matches as $match ) {
            if ( $match->getName() == 'include' ) {        
                $params = $argParser->parse($match->getArguments());
                $installer->query(
                    'INSERT INTO tiki_object_relations (relation, source_type, source_itemId, target_type, target_itemId) VALUES(?, ?, ?, ?, ?)',
                    array(
                        'tiki.wiki.include',
                        'wiki page',
                        $page['pageName'],
                        'wiki page',
                        $params['page'],
                    )
                );
            }
        }
    }

    $tiki_comments = $installer->table('tiki_comments');
    $comments = $tiki_comments->fetchAll();

    foreach ( $comments as $comment ) {
        if ( $comment['objectType'] == 'forum' ) {
            $type = 'forum post';
        } else {
            $type = $comment['objectType'] . ' comment';
        }
        $matches = WikiParser_PluginMatcher::match($comment['data']);
        foreach ( $matches as $match ) {
            if ( $match->getName() == 'include' ) {        
                $params = $argParser->parse($match->getArguments());
                $installer->query(
                    'INSERT INTO tiki_object_relations (relation, source_type, source_itemId, target_type, target_itemId) VALUES(?, ?, ?, ?, ?)',
                    array(
                        'tiki.wiki.include',
                        $type,
                        $comment['threadId'],
                        'wiki page',
                        $params['page'],
                    )
                );
            }
        }
    }
}
