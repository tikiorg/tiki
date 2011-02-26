{* $Id$ *}
{tikimodule error=$module_params.error title=$tpl_module_title name="trackerhelp" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
{if $prefs.javascript_enabled eq 'y' and $prefs.feature_jquery_autocomplete eq 'y'}
{jq}$(".trackername").tiki("autocomplete", "trackername");{/jq}
{/if}
<form action="" method="post">
	<label>{tr}Tracker name:{/tr}<input type="text" name="trackerhelp_name" class="trackername"{if isset($smarty.session.trackerhelp_name)} value="{$smarty.session.trackerhelp_name|escape}"{/if} /></label>
	<label><input type="submit" name="trackerhelp" value="{tr}Go{/tr}" /></label>
</form>

{if !empty($smarty.session.trackerhelp_text)}
	{tr}ID:{/tr} {$smarty.session.trackerhelp_id}<div style="float:right"><a onclick="insertAt('editwiki', '{foreach from=$smarty.session.trackerhelp_pretty item=line}{$line|escape} {/foreach}')">{icon _id='add' alt="{tr}Insert fields in wiki textarea{/tr}"}</a></div><br />
	{textarea _wysiwyg=n  _toolbars='n' cols=$module_params.cols rows=$module_params.rows}{foreach from=$smarty.session.trackerhelp_text item=line}{$line|escape}
{/foreach}
	{/textarea}
{/if}
{/tikimodule}
