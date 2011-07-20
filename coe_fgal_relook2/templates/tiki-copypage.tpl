{* $Id$ *}
{title}{tr}Copy page:{/tr}&nbsp;{$page}{/title}

<div class="navbar">
	{assign var=thispage value=$page|escape:url}
	{button href="tiki-index.php?page=$thispage" _text="{tr}View page{/tr}"}
</div>

<form action="tiki-copypage.php" method="post">
	<input type="hidden" name="page" value="{$page|escape}" />
	{if isset($page_badchars_display)}
		{if $prefs.wiki_badchar_prevent eq 'y'}
			{remarksbox type=errors title="{tr}Invalid page name{/tr}"}
				{tr 0=$page_badchars_display|escape}The page name specified contains unallowed characters. It will not be possible to save the page until those are removed: <strong>%0</strong>{/tr}
			{/remarksbox}
		{else}
			{remarksbox type=tip title="{tr}Tip{/tr}"}
				{tr 0=$page_badchars_display|escape}The page name specified contains characters that may render the page hard to access. You may want to consider removing those: <strong>%0</strong>{/tr}
			{/remarksbox}
			<input type="hidden" name="badname" value="{$newname|escape}" />
			<input type="submit" name="confirm" value="{tr}Use this name anyway{/tr}" />
		{/if}
	{elseif isset($msg)}
		{remarksbox type=errors}
			{$msg}
		{/remarksbox}
	{/if}
	<table class="formcolor">
		<tr>
			<td><label for='newpage'>{tr}New name:{/tr}</label></td>
			<td>
				<input type='text' id='newpage' name='newpage' size='40' value='{$newname|escape}'/>
				<input type="submit" name="copy" value="{tr}Copy{/tr}" />
			</td>
		</tr>
	</table>
</form>
