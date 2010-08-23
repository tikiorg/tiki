{* $Id$ *}
{* site header horizontal menu *}
{if $prefs.feature_top_bar eq 'y' and $prefs.feature_sitemenu eq 'y'}
	{if $prefs.feature_sitemenu_custom_code}
		{eval var=$prefs.feature_sitemenu_custom_code}
	{elseif $prefs.feature_cssmenus eq 'y'}
		{menu id=$prefs.feature_topbar_id_menu type=horiz css=y}
	{elseif $prefs.feature_phplayers eq 'y'}
		{phplayers id=$prefs.feature_topbar_id_menu type=horiz}
	{/if}
{/if}