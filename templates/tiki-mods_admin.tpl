{* $Id$ *}
{title help="mods"}{tr}Tiki Mods Configuration{/tr}{/title}

<div class="t_navbar">
	{button href="tiki-mods.php" class="btn btn-default" _text="{tr}Mods Install/uninstall{/tr}"}
</div>

{section name=n loop=$tikifeedback}<div class="alert{if $tikifeedback[n].num > 0} alert-warning{/if}">{$tikifeedback[n].mes}</div>{/section}
<br>
<form method="post" action="tiki-mods_admin.php" class="form-horizontal">
	<div class="form-group">
		<label class="col-sm-3 control-label">{tr}Enable Mods providing{/tr}</label>
		<div class="col-sm-7">
			<div class="checkbox">
	      		<label><input type="checkbox" name="feature_mods_provider" value="on"{if $prefs.feature_mods_provider eq 'y'} checked="checked"{/if}>{tr}Enable{/tr}</label>
	      	</div>
	    </div>
    </div>
    <div class="form-group">
		<label class="col-sm-3 control-label">{tr}Mods local directory{/tr}</label>
		<div class="col-sm-7">
	      	<input type="text" name="mods_dir" value="{$prefs.mods_dir}" size="42" class="form-control">
	    </div>
    </div>
    <div class="form-group">
		<label class="col-sm-3 control-label">{tr}Mods remote server{/tr}</label>
		<div class="col-sm-7">
	      	<input type="text" name="mods_server" value="{$prefs.mods_server}" size="42" class="form-control">
	    </div>
    </div>
    <div class="form-group">
		<label class="col-sm-3 control-label"></label>
		<div class="col-sm-7">
	      	<input type="submit" class="btn btn-primary btn-sm" name="save" value="{tr}Save{/tr}">
	    </div>
    </div>
</form>

