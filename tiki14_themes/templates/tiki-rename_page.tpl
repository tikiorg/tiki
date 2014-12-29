{title}{tr}Rename page:{/tr} {$page}{/title}

<div class="navbar" role="navigation">
	{assign var=thispage value=$page|escape:url}
	{button href="tiki-index.php?page=$thispage" class="btn btn-default btn-sm navbar-btn" _text="{tr}View page{/tr}"}
</div>

<form action="tiki-rename_page.php" method="post" class="form-horizontal" role="form">
	<input type="hidden" name="page" value="{$page|escape}">
	{if isset($page_badchars_display)}
		{if $prefs.wiki_badchar_prevent eq 'y'}
			{remarksbox type=errors title="{tr}Error{/tr}"}
				{tr _0=$page_badchars_display|escape}The page name specified contains unallowed characters. It will not be possible to save the page until those are removed: <strong>%0</strong>{/tr}
			{/remarksbox}
		{else}
			{remarksbox type=tip title="{tr}Tip{/tr}"}
				{tr _0=$page_badchars_display|escape}The page name specified contains characters that may render the page hard to access. You may want to consider removing those: <strong>%0</strong>{/tr}
			{/remarksbox}
			<input type="hidden" name="badname" value="{$newname|escape}">
			<input type="submit" class="btn btn-default btn-sm" name="confirm" value="{tr}Use this name anyway{/tr}">
		{/if}
	{elseif isset($msg)}
		{remarksbox type=errors}
			{$msg}
		{/remarksbox}
	{/if}
	<div class="form-group">
		<div class="col-sm-10">
			<label for="newpage" class="col-sm-2 control-label">{tr}New name{/tr}</label>
			<div class="col-sm-10">
				<input type='text' id='newpage' name='newpage' class="form-control" value='{$newname|escape}'>
			</div>
		</div>
		<div class="col-sm-2">
			<input type="submit" class="btn btn-primary btn-sm" name="rename" value="{tr}Rename{/tr}">
		</div>
	</div>
</form>
