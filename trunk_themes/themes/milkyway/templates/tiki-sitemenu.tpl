{* $Id$ *}
{* site header horizontal menu *}
{if $prefs.feature_sitemenu eq 'y'}
<table class="pill" cellspacing="0" cellpadding="0"><tr><td class="pill_l">&nbsp;</td><td class="pill_m">
	{if $prefs.feature_cssmenus eq 'y'}
		{menu id=$prefs.feature_topbar_id_menu type=horiz css=y}
	{elseif $prefs.feature_phplayers eq 'y'}
		{phplayers id=$prefs.feature_topbar_id_menu type=horiz}
	{/if}
	</td><td class="pill_r">&nbsp;</td></tr></table>
{/if}