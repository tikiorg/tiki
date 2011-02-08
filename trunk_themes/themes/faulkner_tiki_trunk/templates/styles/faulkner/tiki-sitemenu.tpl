{* $Id$ *}
{* site header horizontal menu *}
{if $prefs.feature_sitemenu eq 'y'}
	<div role="navigation">
		{if $prefs.feature_sitemenu_custom_code}
			<table id="sitemenu"><tr><td>		
				{eval var=$prefs.feature_sitemenu_custom_code}
			</td></tr></table>
		{elseif $prefs.feature_cssmenus eq 'y'}
			<table id="sitemenu"><tr><td>
				{menu id=$prefs.feature_topbar_id_menu type=horiz css=y}
			</td></tr></table>
		{elseif $prefs.feature_phplayers eq 'y'}
			{phplayers id=$prefs.feature_topbar_id_menu type=horiz}
		{/if}
	</div>
{/if}