{title}{tr _0=$page _1=$version}Roll back page %0 to version %1{/tr}{/title}

<form action="tiki-rollback.php?page={$page|escape:url}&amp;version={$version|escape}&amp;rollback=y" method="post">
	<input type="submit" class="btn btn-default btn-sm" name="rollback" value="{tr}Roll back{/tr}">
	<div class="wikitext">{$preview.data}</div>
	<div>
		<input type="hidden" name="page" value="{$page|escape}">
		<input type="hidden" name="version" value="{$version|escape}">
		<label for="comment">{tr}Describe the reason for roll back{/tr} {help url='Using+Wiki+Pages' desc="{tr}Enter some text to describe the reason for rolling back{/tr}"}</label>
		<input class="form-control wikiedit" type="text" id="comment" name="comment" value="" maxlength="255">
	</div>
	<div align="center">
		<input type="submit" class="btn btn-default btn-sm" name="rollback" value="{tr}Roll back{/tr}">
	</div>
</form>

{include file='tiki-page_bar.tpl'}

