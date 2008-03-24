{if count($showstructs) ne 0}
<tr class="formcolor">
	<td>{tr}Structures:{/tr}</td>
	<td>
  [ <a class="link" href="javascript:show('showstructs');" onclick="needToConfirm = false;">{tr}show structures{/tr}</a>
  | <a class="link" href="javascript:hide('showstructs');" onclick="needToConfirm = false;">{tr}hide structures{/tr}</a> ]
	<div id="showstructs" style="display:none;">
	<table>
		{foreach from=$showstructs item=page_info }
			<tr><td>{$page_info.pageName}{if !empty($page_info.page_alias)}({$page_info.page_alias}){/if}</td></tr>
		{/foreach}  
	</table>
  
  {if $tiki_p_edit_structures eq 'y'}
    <a href="tiki-admin_structures.php" class="link">{tr}Manage structures{/tr}</a>
  {/if}
  </div>
  </td>
</tr>
{/if}
