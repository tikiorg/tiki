<a class="pagetitle" href="tiki-edit_structure.php?page_ref_id={$page_ref_id}">
  {tr}Modify Structure{/tr}: {$structure_name}
</a><br/><br/>

<form action="tiki-edit_structure.php" method="post">
<input type="hidden" name="page_ref_id" value="{$page_ref_id}" />

<h2>{tr}Current Node{/tr}: {$pageName}</h2>
<table class="normal">
  <tr>
  <td class="formcolor">{tr}Page Alias{/tr}</td>
  <td class="formcolor">
  <input type="text" name="pageAlias" value="{$pageAlias}" />  <input type="submit" name="create" value="{tr}Update{/tr}" />
  </td>
  </tr>
  <tr>
  <td class="formcolor">{tr}Move{/tr}</td>
  <td class="formcolor">
  <a href='tiki-edit_structure.php?page_ref_id={$page_ref_id}&amp;move_node=1'><img src='img/icons2/nav_dot_right.gif' hspace="3" border='0' title="{tr}Promote{/tr}"/></a>
  <a href='tiki-edit_structure.php?page_ref_id={$page_ref_id}&amp;move_node=2'><img src='img/icons2/nav_home.gif' hspace="3" border='0' title="{tr}Previous{/tr}"/></a>
  <a href='tiki-edit_structure.php?page_ref_id={$page_ref_id}&amp;move_node=3'><img src='img/icons2/nav_down.gif' hspace="3" border='0' title="{tr}Next{/tr}"/></a>
  <a href='tiki-edit_structure.php?page_ref_id={$page_ref_id}&amp;move_node=4'><img src='img/icons2/nav_dot_left.gif' hspace="3" border='0' title="{tr}Demote{/tr}"/></a>
  </td>
  </tr>
</table>
<h3>{tr}Add pages to current node{/tr}:</h3>
<table class="normal">
  <tr>
  <td class="formcolor">
  {tr}After page{/tr}
  </td>
  <td class="formcolor">
  <select name="after_ref_id">
  {section name=ix loop=$subpages}
  <option value="{$subpages[ix].page_ref_id}" {if $insert_after eq $subpages[ix].page_ref_id}selected="selected"{/if}>{$subpages[ix].pageName}</option>
  {/section}
  </select>
  </td>
  </tr>
  <tr>
  <td class="formcolor">
  {tr}Create page{/tr}
  </td>
  <td class="formcolor">
  <input type="text" name="name" />
  </td>
  </tr>
  <tr>
  <td class="formcolor">
  {tr}Use pre-existing page{/tr}<br />
        <input type="text" name="find_objects" />
        <input type="submit" value="{tr}Go{/tr}" name="search_objects" />
  </td>
  <td class="formcolor">
  <select name="name2[]" multiple="multiple" size="5">
  {section name=list loop=$listpages}
  <option value="{$listpages[list].pageName|escape}">{$listpages[list].pageName|truncate:40:"(...)":true}</option>
  {/section}
  </select>
  </td>
  </tr>
  <tr>
  <td class="formcolor">&nbsp;</td>
  <td class="formcolor">
  <input type="submit" name="create" value="{tr}Update{/tr}" />
  </td>
  </tr>
</tr>
</table>
</form>

<br/>
<h2>{tr}Structure Layout{/tr}</h2>

{section name=ix loop=$subtree}
 {if $subtree[ix].pos eq ''}
     <a class='link' href='tiki-edit_structure.php?page_ref_id={$subtree[ix].page_ref_id}'>{$subtree[ix].pageName}{if $subtree[ix].page_alias}({$subtree[ix].page_alias}){/if}</a>
	 &nbsp;[<a class='link' href='tiki-index.php?page_ref_id={$subtree[ix].page_ref_id}'>{tr}View{/tr}</a>
	 |<a  class='link' href='tiki-editpage.php?page={$subtree[ix].pageName}'>{tr}Edit{/tr}</a>]
 {else}
   {if $subtree[ix].first}<ul>{/if}
   {* Handle dummy last entry *}
   {if $subtree[ix].last}
     </ul>
   {else}
     {if $page_ref_id eq $subtree[ix].page_ref_id}<b>{/if}
     <li style='list-style:disc outside;'>{$subtree[ix].pos}
	     &nbsp;<a class='link' href='tiki-edit_structure.php?page_ref_id={$subtree[ix].page_ref_id}'>
         {$subtree[ix].pageName}{if $subtree[ix].page_alias}({$subtree[ix].page_alias}){/if}</a>
		 &nbsp;[<a class='link' href='tiki-edit_structure.php?page_ref_id={$subtree[ix].page_ref_id}&amp;remove={$subtree[ix].page_ref_id}'>Delete</a>]
		 &nbsp;[<a class='link' href='tiki-index.php?page_ref_id={$subtree[ix].page_ref_id}'>{tr}View{/tr}</a>
		 |<a  class='link' href='tiki-editpage.php?page={$subtree[ix].pageName}'>{tr}Edit{/tr}</a>]
     </li>
     {if $page_ref_id eq $subtree[ix].page_ref_id}</b>{/if}
   {/if}
 {/if}
{/section}

{if $remove eq 'y'}
<br/>
{tr}You will remove{/tr} '{$removePageName}' {tr}and its subpages from the structure, now you have two options:{/tr}
<ul>
<li><a class="link" href="tiki-edit_structure.php?page_ref_id={$removepage}&amp;rremove={$removepage}">{tr}Remove only from structure{/tr}</a></li>
<li><a class="link" href="tiki-edit_structure.php?page_ref_id={$removepage}&amp;sremove={$removepage}">{tr}Remove from structure and remove page too{/tr}</a></li>
</ul>
{/if}

