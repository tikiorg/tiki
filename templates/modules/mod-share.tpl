{* $Id$ *}
{strip}
{tikimodule error=$module_params.error title=$tpl_module_title name=$tpl_module_name flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
	<div class="{if !$share_icons}site_report {/if}mod-share-item" id="site_report_{$share_mod_usage_counter}">
		{if (!isset($module_params.report) or $module_params.report neq 'n') and $tiki_p_site_report eq 'y'}
			{if $share_icons}
				{icon name='comment' title="{tr}Report to Webmaster{/tr}" href="tiki-tell_a_friend.php?report=y&amp;url={$smarty.server.REQUEST_URI|escape:'url'}" class='btn-link'}
			{else}
				<a href="tiki-tell_a_friend.php?report=y&amp;url={$smarty.server.REQUEST_URI|escape:'url'}">
					{tr}Report to Webmaster{/tr}
				</a>
			{/if}
		{/if}
		{if (!isset($module_params.share) or $module_params.share neq 'n') and $tiki_p_share eq 'y'}
			{if $share_icons}
				{icon name='share-alt' title="{tr}Share this page{/tr}" href="tiki-share.php?url={$smarty.server.REQUEST_URI|escape:'url'}" class='btn-link'}
			{else}
				<a href="tiki-share.php?url={$smarty.server.REQUEST_URI|escape:'url'}">
					{tr}Share this page{/tr}
				</a>
			{/if}
		{/if}
		{if (!isset($module_params.email) or $module_params.email neq 'n') and $tiki_p_tell_a_friend eq 'y'}
			{if $share_icons}
				{icon name='share' title="{tr}Send a link{/tr}" href="tiki-tell_a_friend.php?url={$smarty.server.REQUEST_URI|escape:'url'}" class='btn-link'}
			{else}
				<a href="tiki-tell_a_friend.php?url={$smarty.server.REQUEST_URI|escape:'url'}">
					{tr}Email this page{/tr}
				</a>
			{/if}
		{/if}
	</div>
	{if (isset($module_params.facebook) and $module_params.facebook neq 'n')}
		<div{$fb_div_attributes} class="mod-share-item">
			<div id="fb-root"></div>
			{jq notonready=true}
				(function(d, s, id) {
					var js, fjs = d.getElementsByTagName(s)[0];
					if (d.getElementById(id)) { return; }
					js = d.createElement(s); js.id = id;
					js.src = "//connect.facebook.net/{{$fb_locale}}/all.js#xfbml=1{{$fb_app_id_param}}";
					fjs.parentNode.insertBefore(js, fjs);
				}(document, 'script', 'facebook-jssdk'));
			{/jq}
			<div class="fb-like" {$fb_data_attributes}></div>
		</div>
	{/if}

	{if (isset($module_params.twitter) and $module_params.twitter neq 'n')}
		<div class="twitter-root mod-share-item"{$tw_div_attributes}>
			<a href="https://twitter.com/share" class="twitter-share-button" {$tw_data_attributes}>
				{$module_params.twitter_label|escape}
			</a>
			<script src="//platform.twitter.com/widgets.js" type="text/javascript"></script>
		</div>
	{/if}

	{if (isset($module_params.linkedin) and $module_params.linkedin neq 'n')}
		<div class="linkedin-root mod-share-item">
			<script src="https://platform.linkedin.com/in.js" type="text/javascript"></script>
			<script type="IN/Share"{$li_data_attributes}></script>
		</div>
	{/if}

	{if (isset($module_params.google) and $module_params.google neq 'n')}
		<div class="mod-share-item google-root"><div class="g-plusone"{$gl_data_attributes}></div></div>
		{jq notonready=true}
			{{$gl_script_addition}}
				(function() {
					var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
					po.src = 'https://apis.google.com/js/plusone.js';
					var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
				})();
		{/jq}
	{/if}

{/tikimodule}
{/strip}
