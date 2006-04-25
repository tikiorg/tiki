<table width="100%">
<tr>
<td width="100%">
{section name=ix loop=$subtree}
 {if $subtree[ix].pos eq ''}
 	<ul class="estruct_index">
	<li class="estruct_index" >
	 <a class='link' href='./aulawiki-view_structure.php?print={$structureId}#{$subtree[ix].pageName}'>{$subtree[ix].pageName}{if $subtree[ix].page_alias}({$subtree[ix].page_alias}){/if}</a>
	 </li>
	 </ul>
 {else}
   {if $subtree[ix].first}<ul class="estruct_index">{/if}
   {* Handle dummy last entry *}
   {if $subtree[ix].last}
     </ul>
   {else}
     {if $page_ref_id eq $subtree[ix].page_ref_id}<b>{/if}
     <li class="estruct_index" >{$subtree[ix].pos}
	     &nbsp;<a class='link' href='./aulawiki-view_structure.php?print={$structureId}#{$subtree[ix].pageName}'>
         {$subtree[ix].pageName}{if $subtree[ix].page_alias}({$subtree[ix].page_alias}){/if}</a>
{*		 &nbsp;<a class='link' href='tiki-edit_structure.php?page_ref_id={$subtree[ix].page_ref_id}&amp;remove={$subtree[ix].page_ref_id}'><img src="img/icons2/delete2.gif" border="0" title="{tr}Delete from structure{/tr}" alt="{tr}Delete from structure{/tr}" /></a>
		 <a href='?page_ref_id={$subtree[ix].page_ref_id}&amp;move_node=1&amp;print={$structureId}'><img src="images/aulawiki/edu_nav_right.gif" border="0" title="{tr}Promote{/tr}" alt="{tr}Promote{/tr}" /></a>
  		 <a href='?page_ref_id={$subtree[ix].page_ref_id}&amp;move_node=2&amp;print={$structureId}'><img src="images/aulawiki/edu_nav_up.gif" vspace="2" border="0" title="{tr}Previous{/tr}" alt="{tr}Previous{/tr}" /></a>
  		 <a href='?page_ref_id={$subtree[ix].page_ref_id}&amp;move_node=3&amp;print={$structureId}'><img src="images/aulawiki/edu_nav_down.gif" vspace="2" border="0" title="{tr}Next{/tr}" alt="{tr}Next{/tr}" /></a>
  		 <a href='?page_ref_id={$subtree[ix].page_ref_id}&amp;move_node=4&amp;print={$structureId}'><img src="images/aulawiki/edu_nav_left.gif" border="0" title="{tr}Demote{/tr}" alt="{tr}Demote{/tr}" /></a>
*}		 
     </li>
     {if $page_ref_id eq $subtree[ix].page_ref_id}</b>{/if}
   {/if}
 {/if}
{/section}
</td>
<td align="right">
     <a class='link' href='tiki-edit_structure.php?page_ref_id={$structureId}'><img src="img/icons/expand.gif" border=0/></a>
</td>
</table>