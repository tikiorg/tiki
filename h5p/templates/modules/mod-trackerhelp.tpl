{* $Id$ *}
{tikimodule error=$module_params.error title=$tpl_module_title name="trackerhelp" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
	{if $prefs.javascript_enabled eq 'y' and $prefs.feature_jquery_autocomplete eq 'y'}
		{jq}$(".trackername").tiki("autocomplete", "trackername");{/jq}
	{/if}
	<form method="post">
		<div class="form-group">
			<label class="col-sm-3 control-label" for="trackerhelp_name">{tr}Tracker name:{/tr}</label>
			<div class="col-sm-9">
				<input type="text" name="trackerhelp_name" id="trackerhelp_name" class="form-control trackername"{if isset($smarty.session.trackerhelp_name)} value="{$smarty.session.trackerhelp_name|escape}"{/if} />
			</div>
		</div>
		<div class="form-group">
			<div class="col-sm-9 col-sm-offset-3">
				<input type="submit" class="btn btn-default btn-sm" name="trackerhelp" value="{tr}Go{/tr}" />
			</div>
		</div>
	</form>

	{if !empty($smarty.session.trackerhelp_text)}
		{tr}ID:{/tr} {$smarty.session.trackerhelp_id}<div style="float:right"><a onclick="insertAt('editwiki', '{foreach from=$smarty.session.trackerhelp_pretty item=line}{$line|escape} {/foreach}')" class="'tips" title=":{tr}Insert fields in wiki textarea{/tr}">{icon name='add'}</a></div><br>
		{textarea _simple='y' _toolbars='n' cols=$module_params.cols rows=$module_params.height}{foreach from=$smarty.session.trackerhelp_text item=line}{$line|escape}
		{/foreach}{/textarea}
	{/if}
{/tikimodule}
