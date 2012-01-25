{* $Id$ *}
{strip}
{tikimodule error=$module_params.error title=$tpl_module_title name=$tpl_module_name flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
	<div class="site_report mod-share-item" id="site_report_{$share_mod_usage_counter}">
		{if (!isset($module_params.report) or $module_params.report neq 'n') and $tiki_p_site_report eq 'y'}
			<a href="tiki-tell_a_friend.php?report=y&amp;url={$smarty.server.REQUEST_URI|escape:'url'}">{tr}Report to Webmaster{/tr}</a>
		{/if}
		{if (!isset($module_params.share) or $module_params.share neq 'n') and $tiki_p_tell_a_friend eq 'y'}
			<a href="tiki-share.php?url={$smarty.server.REQUEST_URI|escape:'url'}">{tr}Share this page{/tr}</a>
		{/if}
		{if (!isset($module_params.email) or $module_params.email neq 'n') and $tiki_p_tell_a_friend eq 'y'}
			<a href="tiki-tell_a_friend.php?url={$smarty.server.REQUEST_URI|escape:'url'}">{tr}Email this page{/tr}</a>
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
				}(document, 'script', 'facebook-jssdk_{{$share_mod_usage_counter}}'));
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
			<script src="http://platform.linkedin.com/in.js" type="text/javascript"></script>
			<script type="IN/Share"{if !empty($module_params.linkedin_mode)} data-counter="{$module_params.linkedin_mode}"{/if}></script>
		</div>
	{/if}
{/tikimodule}
{/strip}
