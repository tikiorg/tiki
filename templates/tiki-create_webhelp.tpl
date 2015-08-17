{title help='WebHelp' url='tiki-create_webhelp.php'}{tr}Create WebHelp{/tr}{/title}
{if $generated eq 'y'}
	<div class="t_navbar">
		<span class="button btn btn-default">
			<a class="link" href="whelp/{$dir}/index.html">{tr}View generated WebHelp.{/tr}</a>
		</span>
	</div>
{/if}
{if $output ne ''}
	<div class="panel panel-default"><div class="panel-body">
		{$output}
	</div></div>
{/if}
<form method="post" action="tiki-create_webhelp.php" class="form-horizontal">
    <div class="form-group">
		<label class="col-sm-3 control-label">{tr}Structure{/tr}</label>
		<div class="col-sm-7">
	    	{$struct_info.pageName|default:"{tr}No structure{/tr}."}
	    </div>
    </div>
    <div class="form-group">
		<label class="col-sm-3 control-label">{tr}Directory{/tr}</label>
		<div class="col-sm-7">
		    <input type="text" id="dir" name="dir" value="{$struct_info.pageName}" class="form-control">
	    </div>
    </div>
    <div class="form-group">
		<label class="col-sm-3 control-label">{tr}Top page{/tr}</label>
		<div class="col-sm-7">
		    <input type="text" id="top" name="top" value="{$struct_info.pageName}" class="form-control">
	    </div>
    </div>

    <div class="form-group">
		<label class="col-sm-3 control-label"></label>
		<div class="col-sm-7">
		    <input type="submit" class="btn btn-default btn-sm" {if !$struct_info.pageName}disabled='disabled'{/if} name="create" value="{tr}Create{/tr}">
	    </div>
    </div>
	<input type="hidden" name="name" value="{$struct_info.pageName}">
	<input type="hidden" name="struct" value="{$struct_info.page_ref_id}">
</form>
