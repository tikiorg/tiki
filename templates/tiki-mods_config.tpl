{title help="mods"}{tr}Package Config{/tr}{/title}

<div class="wikitext">
	<h2>
		Configure
		<br>
		{$type} <i>{$package}</i>
	</h2>
	<div class="wikitext">
		{$help}
	</div>
	<form action="tiki-mods.php" method="post" class="form-horizontal">
		<input type="hidden" name="action" value="configuration">
		<input type="hidden" name="package" value="{$type}-{$package}">
		<input type="hidden" name="type" value="{$type}">

		{foreach key=k item=i from=$info->configuration}
			<div class="form-group">
				<label class="col-sm-3 control-label">{$i[0]}</label>
				<div class="col-sm-7">
			      	<input type="text" name="conf[{$i[1]}]" value="{$i[2]}" class="form-control">
			    </div>
		    </div>
		{/foreach}
		<div class="form-group">
			<label class="col-sm-3 control-label"></label>
			<div class="col-sm-7">
		      	<input type="submit" class="btn btn-default btn-sm" name="go" value="configure">
		    </div>
	    </div>
	</form>
</div>

