{* $Id$ *}
{strip}
{tikimodule error=$module_params.error title=$tpl_module_title name=$tpl_module_name flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
	<div id="site_report">
		{if $module_params.report neq 'n' and $tiki_p_site_report eq 'y'}
			<a href="tiki-tell_a_friend.php?report=y&amp;url={$smarty.server.REQUEST_URI|escape:'url'}">{tr}Report to Webmaster{/tr}</a>
		{/if}
		{if $module_params.share neq 'n' and $tiki_p_tell_a_friend eq 'y'}
			<a href="tiki-share.php?url={$smarty.server.REQUEST_URI|escape:'url'}">{tr}Share this page{/tr}</a>
		{/if}
		{if $module_params.email neq 'n' and $tiki_p_tell_a_friend eq 'y'}
			<a href="tiki-tell_a_friend.php?url={$smarty.server.REQUEST_URI|escape:'url'}">{tr}Email this page{/tr}</a>
		{/if}
	</div>
{/tikimodule}
{/strip}
