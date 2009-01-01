{title help="tests"}{tr}TikiTests List{/tr}{/title}

{include file='tiki-tests_menubar.tpl'}

{pagination_links cant=$files_number offset=$offset step=$files_per_page}{/pagination_links}

<table class="normal" width="100%" style="clear: both;">
<tr>
	<th>{tr}File Name{/tr}</th>
	<th>{tr}Actions{/tr}</th>
</tr>
{cycle values="odd,even" print=false}
{foreach from=$files item=file}
<tr class="{cycle}">
	<td>{$file}</td>
	<td width="100px">
		{self_link action={tr}Remove{/tr} filename="$file" _ajax='n' _icon='cross'}{tr}Remove{/tr}{/self_link}
		{self_link _script='tiki_tests/tiki-tests_replay.php' action={tr}Config{/tr} filename="$file" _ajax='n' _icon='resultset_next'}{tr}Replay{/tr}{/self_link}
		{self_link _script='tiki_tests/tiki-tests_edit.php' action={tr}Show{/tr} filename="$file" _ajax='n' _icon='pencil'}{tr}Edit{/tr}{/self_link}
	</td>
</tr>
{/foreach}
</table>

{pagination_links cant=$files_number offset=$offset step=$files_per_page}{/pagination_links}
