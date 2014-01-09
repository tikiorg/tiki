{title}{tr}Remove page:{/tr} {$page} ({if $version == 'last'}{tr}Last Version{/tr}{else}{tr}Version:{/tr} {$version}{/if}){/title}

<div class="navbar" role="navigation">
	{assign var=thispage value=$page|escape:'url'}
	{button href="tiki-index.php?page=$thispage" class="btn btn-default btn-sm navbar-btn" _text="{tr}View page{/tr}"}
</div>

<form action="tiki-removepage.php" method="post" role="form">
	{remarksbox type="warning" title="{tr}Warning{/tr}"}{tr}You are about to remove this page{/tr}<strong>: {$page|escape}</strong>{/remarksbox}
	<div class="chekbox">
		<label>
			<input type="checkbox" id="all" name="all"> {tr}Remove all versions of this page{/tr}
		</label>
	</div>
	<input type="hidden" name="page" value="{$page|escape}">
	<input type="hidden" name="version" value="{$version|escape}">
	<input type="hidden" name="historyId" value="{$historyId|escape}">
	<div class="form-group">
		<label for="remove-page" class="sr-only">{tr}Remove{/tr}</label>
		<input type="submit" class="btn btn-warning btn-sm" id="remove-page" name="remove" value="{tr}Remove{/tr}">
	</div>
</form>
