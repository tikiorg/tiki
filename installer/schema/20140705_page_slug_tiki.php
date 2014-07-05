<?php

function post_20140705_page_slug_tiki($installer)
{
	$pages = $installer->table('tiki_pages');
	$names = $pages->fetchColumn('pageName', []);
	foreach ($names as $name) {
		$pages->update(['pageSlug' => urlencode($name)], [
			'pageName' => $name,
		]);
	}
}
