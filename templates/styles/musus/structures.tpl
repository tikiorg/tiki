{if count($showstructs) ne 0}
<tr>
	<td class="formcolor">{tr}Structures:{/tr}</td>
	<td class="formcolor">
  [ <a class="link" href="javascript:show('showstructs');">{tr}show structures{/tr}</a>
  | <a class="link" href="javascript:hide('showstructs');">{tr}hide structures{/tr}</a> ]
	<div id="showstructs" style="display:none;">
	<table>
		{foreach from=$showstructs item=page_info }
			<tr><td>{$page_info.pageName}{if !empty($page_info.page_alias)}({$page_info.page_alias}){/if}</td></tr>
		{/foreach}  
	</table>
  {if $tiki_p_edit_structures eq 'y'}
    <a href="tiki-admin_structures.php">{tr}Admin structures{/tr}</a>
  {/if}
  </div>
  </td>
</tr>
{/if}