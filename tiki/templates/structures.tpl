{if $feature_wiki_showstructs eq 'y'}
<tr>
	<td class="formcolor">{tr}Structures:{/tr}</td>
	<td class="formcolor">
  [ <a class="link" href="javascript:show('showstructs');">{tr}show structures{/tr}</a>
  | <a class="link" href="javascript:hide('showstructs');">{tr}hide structures{/tr}</a> ]
	<div id="showstructs" style="display:block;">
	This page is in the following Structures:
	<br/>
	<table border="1" cellpadding="0" cellspacing="0">
		<tr><td class="heading">Structure ID:</td><td class="heading">Page Alias</td><td class="heading">Tags</td></tr>
		{foreach from=$showstructs item=page_alias key=structID}
			<tr><td class="odd">{$structID}</td><td class="odd">{$page_alias}</td><td class="odd"> </td></tr>
		{/foreach}  
	</table>
  
  {if $tiki_p_edit_structures eq 'y'}
    <a href="tiki-admin_structures.php" class="link">{tr}Admin structures{/tr}</a>
  {/if}
  </div>
  </td>
</tr>
{/if}{* $feature_wiki_showstructs eq 'y' *}
