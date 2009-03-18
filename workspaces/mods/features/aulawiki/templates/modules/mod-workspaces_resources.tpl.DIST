{*
@author: Javier Reyes Gomez (jreyes@escire.com)
@date: 27/01/2006
@copyright (C) 2006 Javier Reyes Gomez (eScire.com)
@license http://www.gnu.org/copyleft/lgpl.html GNU/LGPL
*}
{tiki_workspaces_module title={tr}Resources{/tr} name="workspaces_resources" flip=$module_params.flip decorations=$module_params.decorations style_title=$style_title style_data=$style_data}
{include file="tiki-workspaces_module_error.tpl" error=$error_msg}
  
<div id="createForm" style="display:none;">
<form name="formCreateObject" method="post" action="{$ownurl}">
<input name="createObjectCategoryId" type="hidden" id="createObjectCategoryId" value="{$selectedCategory.categId}">
  <table class="normal">
  	<tr> 
{*     <td class="formcolor">{tr}Category:{/tr}<br/><input name="createObjectCategoryName" type="text" id="createObjectCategoryName" value="" size="30" maxlength="100"></td>*}
     <td class="formcolor"><label>{tr}Category:{/tr}</label></td>
     <td class="formcolor">{$selectedCategory.name}</td>
    </tr>
     <tr> 
      <td class="formcolor"><label>{tr}Name:{/tr}</label></td>
      <td class="formcolor"><input name="createObjectName" type="text" id="createObjectName" value="" size="41" maxlength="100"></td>
    </tr>
    <tr> 
      <td class="formcolor"><label>{tr}Description:{/tr}</label></td>
	  <td class="formcolor"><textarea name="createObjectDesc" size="20" cols="40" rows="2"> </textarea></td>
    </tr>
     <tr> 
      <td class="formcolor"><label>{tr}Object type:{/tr}</label></td>
      <td class="formcolor">{include file="tiki-workspaces_resource_types.tpl" value=$resource.type listName="createObjectType" multiple="false" showlabel="false" listsize="1"} 
      </td>
    </tr>
 
     <tr> 
      <td class="formcolor" colspan="2">
      <center>
      <input class="edubutton" type="submit" name="createObject" value="Create object"/>
      <input class="edubutton" type="button" onclick="document.getElementById('createForm').style.display='none';" name="cancel" value="Cancel"/>
      </center></td>
    </tr>
  </table>
</form>
</div>
<div id="formRemoveObject" style="display:none;">
<form name="formRemoveObject" method="post" action="{$ownurl}">
<input name="selectedRemoveObject" type="hidden" id="selectedRemoveObject" value="">
<input name="selectedRemoveObjectCat" type="hidden" id="selectedRemoveObjectCat" value="">
<input name="isRemoveCategory" type="hidden" id="isRemoveCategory" value="N">
  <table class="normal">
     <tr> 
      <td class="formcolor">
      {tr}are you sure to remove object {/tr}?
      </td>
    </tr>
      <tr> 
      <td class="formcolor" colspan="2">
      <center><input type="submit" name="removeObject" value="Yes"><input type="button" name="nobutton" value="No" onclick="document.getElementById('formRemoveGroup').style.display = 'display:none;';"></center></td>
    </tr>
  </table>
</form>
</div>
<div>
<label>Selected category:</label> {$selectedCategory.name} {$selectedCategory.description}
<div class="edubuttons">
<a class="edubutton" href="#" onclick="document.getElementById('createForm').style.display = 'block';"><img border=0 src="images/workspaces/pageNueva.gif"/> New</a>
<a class="edubutton" href="#" onclick="document.getElementById('pasteIdCateg').value='{$selectedCategory.categId}';document.getElementById('pasteForm').submit();"><img border=0 src="images/workspaces/edu_paste.gif"/> Paste</a>
<a class="edubutton" href="./tiki-admin_categories.php?removeCat={$selectedCategory.categId}">
<img src='img/icons2/delete.gif' height=10 border='0' alt='{tr}Remove{/tr}' title='{tr}Remove{/tr}' /> Remove Category</a>
</div>
</div>
<table class="normal">
<tr>
<td class="even">
{$tree}
</td>
<td>
{include file="tiki-workspaces_list_resources.tpl" resources=$categObjects viewType="y" viewDesc="n"} 
</td>
</tr>
</table>
{/tiki_workspaces_module}

