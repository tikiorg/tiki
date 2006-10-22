{*
@author: Javier Reyes Gomez (jreyes@escire.com)
@date: 27/01/2006
@copyright (C) 2006 Javier Reyes Gomez (eScire.com)
@license http://www.gnu.org/copyleft/lgpl.html GNU/LGPL
*}
<div id="newPage{$structureId}" style="display:none;">
<form name="formCreateObject" method="post" action="{$ownurl}">
<input name="structureId" type="hidden" value="{$structureId}"/>
<input name="parentPage{$structureId}" type="hidden" id="parentPage{$structureId}" value=""/>
  <table class="normal">
     <tr> 
      <td class="formcolor"><label>{tr}Name{/tr}:</label></td>
      <td class="formcolor"><input name="createObjectName" type="text" id="createObjectName" value="" size="41" maxlength="100"/></td>
    </tr>
    <tr> 
      <td class="formcolor"><label>{tr}Description{/tr}:</label></td>
	  <td class="formcolor"><textarea name="createObjectDesc" size="20" cols="40" rows="2"/> </textarea></td>
    </tr>
     <tr> 
      <td class="formcolor"><label>{tr}Object type{/tr}:</label></td>
      <td class="formcolor">{tr}wiki page{/tr}</td>
    </tr>
 
     <tr> 
      <td class="formcolor" colspan="2">
      <center>
      <input class="edubutton" type="submit" name="createObject" value="Create object"/>
      <input class="edubutton" type="button" onclick="document.getElementById('newPage{$structureId}').style.display = 'none';" name="cancel" value="Cancel"/>
      </center></td>
    </tr>
  </table>
</form>
</div>

<div id="formRemoveObject{$structureId}" style="display:none;">
<form name="formRemoveObject" method="post" action="{$ownurl}">
<input name="structureId" type="hidden" value="{$structureId}"/>
<input name="removePageId{$structureId}" type="hidden" id="removePageId{$structureId}" value="">
  <table class="normal">
     <tr> 
      <td class="formcolor">
      {tr}are you sure to remove object {/tr}?
      </td>
    </tr>
      <tr> 
      <td class="formcolor" colspan="2">
      <center><input class="edubutton" type="submit" name="removeObject" value="Yes"> <input class="edubutton"type="button" name="nobutton" value="No" onclick="document.getElementById('formRemoveObject{$structureId}').style.display = 'none';"></center></td>
    </tr>
  </table>
</form>
</div>

{section name=ix loop=$subtree}
 {if $subtree[ix].pos eq ''}
 	<ul class="estruct_index">
	<li class="estruct_index" >
	 <a class='link' href='./tiki-workspaces_view_structure.php?print={$structureId}#{$subtree[ix].pageName}'>{$subtree[ix].pageName}{if $subtree[ix].page_alias}({$subtree[ix].page_alias}){/if}</a>
	 {if $canadmin}
		 &nbsp;<a href="#" onclick="document.getElementById('parentPage{$structureId}').value='{$subtree[ix].page_ref_id}';document.getElementById('newPage{$structureId}').style.display = 'block';"><img border=0 src="images/workspaces/pageNueva.gif"/></a>
	 {/if}
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
	     &nbsp;<a class='link' href='./tiki-workspaces_view_structure.php?print={$structureId}#{$subtree[ix].pageName}'>
         {$subtree[ix].pageName}{if $subtree[ix].page_alias}({$subtree[ix].page_alias}){/if}</a>
         {if $canadmin}
		 &nbsp;<a href="#" onclick="document.getElementById('parentPage{$structureId}').value='{$subtree[ix].page_ref_id}';document.getElementById('newPage{$structureId}').style.display = 'block';"><img border=0 src="images/workspaces/pageNueva.gif"/></a>
		 &nbsp;<a href="#" onclick="document.getElementById('removePageId{$structureId}').value='{$subtree[ix].page_ref_id}';document.getElementById('formRemoveObject{$structureId}').style.display = 'block';"><img src="img/icons2/delete2.gif" border="0" title="{tr}Delete from structure{/tr}" alt="{tr}Delete from structure{/tr}" /></a>
		 <a href='?page_ref_id={$subtree[ix].page_ref_id}&amp;move_node=1&amp;print={$structureId}'><img src="images/workspaces/edu_nav_right.gif" border="0" title="{tr}Promote{/tr}" alt="{tr}Promote{/tr}" /></a>
  		 <a href='?page_ref_id={$subtree[ix].page_ref_id}&amp;move_node=2&amp;print={$structureId}'><img src="images/workspaces/edu_nav_up.gif" vspace="2" border="0" title="{tr}Previous{/tr}" alt="{tr}Previous{/tr}" /></a>
  		 <a href='?page_ref_id={$subtree[ix].page_ref_id}&amp;move_node=3&amp;print={$structureId}'><img src="images/workspaces/edu_nav_down.gif" vspace="2" border="0" title="{tr}Next{/tr}" alt="{tr}Next{/tr}" /></a>
  		 <a href='?page_ref_id={$subtree[ix].page_ref_id}&amp;move_node=4&amp;print={$structureId}'><img src="images/workspaces/edu_nav_left.gif" border="0" title="{tr}Demote{/tr}" alt="{tr}Demote{/tr}" /></a>
		 {/if}
     </li>
     {if $page_ref_id eq $subtree[ix].page_ref_id}</b>{/if}
   {/if}
 {/if}
{/section}
