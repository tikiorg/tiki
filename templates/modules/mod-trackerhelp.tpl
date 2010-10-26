{tikimodule error=$module_params.error title=$tpl_module_title name="trackerhelp" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
<form action="" method="post">
	<label>{tr}Tracker name:{/tr}<input type="text" name="trackerhelp_name" value="{$smarty.session.trackerhelp_name|escape}" /></label>
	<label><input type="submit" name="trackerhelp" value="{tr}Go{/tr}" /></label>
</form>

{if !empty($smarty.session.trackerhelp_text)}
	{tr}ID:{/tr} {$smarty.session.trackerhelp_id}<br />
	{textarea _wysiwyg=n  _toolbars='n' cols=$module_params.cols rows=$module_params.rows}{foreach from=$smarty.session.trackerhelp_text item=line}{$line|escape}
{/foreach}
	{/textarea}
{/if}
{/tikimodule}
