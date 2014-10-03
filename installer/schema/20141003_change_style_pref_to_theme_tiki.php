<?php

function upgrade_20141003_change_style_pref_to_theme_tiki($installer)
{
	$tiki_preferences = $installer->table('tiki_preferences');
	$style = $tiki_preferences->fetchOne('value', array('name' => 'style'));

	$tiki_preferences->update(
		array(
			'value' => str_replace('.css', '', $style)
		),
		array(
			'name' => 'theme_active',
			'value' => 'legacy',
		)
	);
}

