<?php

function upgrade_20141003_change_style_pref_to_theme_tiki($installer)
{
	$tiki_preferences = $installer->table('tiki_preferences');
	$style = $tiki_preferences->fetchOne('value', ['name' => 'style']);

	if ($tiki_preferences->fetchOne('value', ['name' => 'theme_active', 'value' => 'legacy'])) {
		$tiki_preferences->update(			// upgrade from 13 where theme_active may have already been set
			[
				'value' => str_replace('.css', '', $style)
			],
			[
				'name' => 'theme_active',
				'value' => 'legacy',
			]
		);
	} else {
		$tiki_preferences->insert(
			[
				'value' => str_replace('.css', '', $style),
				'name' => 'theme_active',
			]
		);
	}
}
