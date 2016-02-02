{* $Id$ *}
{* this tpl is included in the various layout tpl's to show the  *}
{* clickable fullscreen icon if the full screen feature has been set *}

{if $prefs.feature_fullscreen eq 'y' and $filegals_manager eq '' and $print_page ne 'y'}
	<div id="fullscreenbutton">
		{if $smarty.session.fullscreen eq 'n'}
			{self_link fullscreen="y" _ajax='n' _icon=application_get _title="{tr}Fullscreen{/tr}"}{/self_link}
		{else}
			{self_link fullscreen="n" _ajax='n' _icon=application_put _title="{tr}Cancel Fullscreen{/tr}"}{/self_link}
		{/if}
	</div>
{/if}
