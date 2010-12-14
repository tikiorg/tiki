{* $Id *}
{* topbar custom code (displays below menu *}
{if $prefs.feature_topbar_custom_code}
	<div class="clearfix" id="topbar_custom_code">
		{eval var=$prefs.feature_topbar_custom_code}
	</div>
{/if}
