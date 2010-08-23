{* $Id$ *}
{* ==> put in this file what is not displayed in the layout (javascript, debug..)*}
{if (! isset($display) or $display eq '')}
	{if count($phpErrors)}
		{button _ajax="n" _id="show-errors-button" _onclick="flip('errors');return false;" _text="{tr}Show php error messages{/tr}"}
		<div id="errors" class="rbox warning" style="display:{if (isset($smarty.session.tiki_cookie_jar.show_errors) and $smarty.session.tiki_cookie_jar.show_errors eq 'y') or $prefs.javascript_enabled ne 'y'}block{else}none{/if};">
			&nbsp;{listfilter selectors='#errors>div'}
			{foreach item=err from=$phpErrors}
				{$err}
			{/foreach}
		</div>
	{/if}

	{if $tiki_p_admin eq 'y' and $prefs.feature_debug_console eq 'y'}
		{* Include debugging console.*}
		{debugger}
	{/if}

	{if $prefs.feature_phplayers eq 'y' and isset($phplayers_LayersMenu)}
		{$phplayers_LayersMenu->printHeader()}
		{$phplayers_LayersMenu->printFooter()}
	{/if}
{/if}
