<?php
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}

/**
 * In some upgrade cases pages without a name can cause a "No wiki page specified" error on tiki-index.php
 * Also the pref wikiHomePage can be set to NULL causing the same error
 *
 * @param Installer $installer
 */
function upgrade_20171016_no_homepage_specificed_tiki($installer)
{
	$tiki_pages = $installer->table('tiki_pages');

	$namelessPages = $tiki_pages->fetchAll(['page_id'], ['pageName' => '']);

	foreach ($namelessPages as $page) {
		$tiki_pages->update(
			[
				'pageName' => "Page id #{$page['page_id']}",
				'comment' => 'Renamed by installer 20171016_no_homepage_specificed_tiki (pageName was empty)',
			],
			[
				'page_id' => $page['page_id'],
			]
		);
	}

	// null value prefs (e.g. wikiHomePage) count as being set but have no value, so cause errors.
	$installer->query('DELETE FROM `tiki_preferences` WHERE ISNULL(`value`);');
}
