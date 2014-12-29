{* $Id$ *}

{tikimodule error=$module_params.error title=$tpl_module_title name="google" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}

	<form method="get" action="http://www.google.com/search" target="Google" class="form-horizontal" role="form">
		<div class="form-group">
			<div class="col-sm-2">
				<input type="hidden" name="hl" value="en"/>
				<input type="hidden" name="oe" value="UTF-8"/>
				<input type="hidden" name="ie" value="UTF-8"/>
				<input type="hidden" name="btnG" value="Google Search"/>
				<input name="googles" class="form-control-static" type="image" src="img/googleg.gif" alt="Google" />
			</div>
			<div class="col-sm-10">
				<input type="text" name="q" class="form-control" maxlength="100" />
			</div>
		</div>
		{if $url_host ne ''}
			<div class="col-sm-11 col-sm-offset-1 radio">
				<input type="hidden" name="domains" value="{$url_host}" />
				<label><input type="radio" name="sitesearch" value="{$url_host}" checked="checked" />{$url_host}</label>
			</div>
			<div class="col-sm-11 col-sm-offset-1 radio">
				<label><input type="radio" name="sitesearch" value="" />WWW</label>
			</div>
		{/if}
	</form>

{/tikimodule}
