{title help="tests"}{tr}TikiTests List{/tr}{/title}

{include file='tiki-tests_menubar.tpl'}

{pagination_links cant=$files_number offset=$offset step=$files_per_page}{/pagination_links}

<div class="table-responsive">
<table class="table">
<tr>
	<th>{tr}File Name{/tr}</th>
	<th>{tr}Actions{/tr}</th>
</tr>

{foreach from=$files item=file}
<tr>
	<td class="text">{$file}</td>
	<td class="action">
		{self_link action={tr}Remove{/tr} filename="$file" _ajax='n' _icon_name='delete'}{tr}Remove{/tr}{/self_link}
		{self_link _script='tiki_tests/tiki-tests_replay.php' action="{tr}Config{/tr}" filename="$file" _ajax='n' _icon_name='next'}{tr}Replay{/tr}{/self_link}
		{self_link _script='tiki_tests/tiki-tests_edit.php' action="{tr}Show{/tr}" filename="$file" _ajax='n' _icon_next='edit'}{tr}Edit{/tr}{/self_link}
	</td>
</tr>
{/foreach}
</table>
</div>

{pagination_links cant=$files_number offset=$offset step=$files_per_page}{/pagination_links}
