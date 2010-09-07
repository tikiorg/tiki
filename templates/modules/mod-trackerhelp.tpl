{tikimodule error=$module_params.error title=$tpl_module_title name="trackerhelp" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
<form action="" method="post">
	<label>{tr}Name:{/tr}<input type="text" name="trackerhelp_name" value="{$smarty.session.trackerhelp_name|escape}" /></label>
	<label><input type="submit" name="trackerhelp" value="{tr}Help{/tr}" /></label>
</form>

{if !empty($smarty.session.trackerhelp_text)}
	{tr}ID:{/tr} {$smarty.session.trackerhelp_id}<br />
	{* {textarea _toolbars='n' _simple='y' cols=$module_params.cols rows=$module_params.rows}*}
	<textarea cols={$module_params.cols} rows={$module_params.rows}>{foreach from=$smarty.session.trackerhelp_text item=line}{$line|escape}
{/foreach}
	</textarea>
	{*{/textarea}*}
{/if}
{/tikimodule}
