<a class="pagetitle" href="tiki-edit_structure.php?structure_id={$structure_id}&amp;page_ref_id={$page_ref_id}">
  {tr}Structure{/tr}: {$structID}
</a><br/><br/>

<a class="link" href="tiki-admin_structures.php">{tr}Admin structures{/tr}</a><br/><br/>
<form action="tiki-edit_structure.php" method="post">
<input type="hidden" name="page_ref_id" value="{$page_ref_id}" />
<input type="hidden" name="structure_id" value="{$structure_id}" />

<h2>{tr}Modify Structure{/tr}</h2>
<table class="normal">
  <tr>
  <td class="formcolor">{tr}In parent page{/tr}</td>
  <td class="formcolor">
	{$pageName}
  </td>
  </tr>
  <tr>
  <td class="formcolor">{tr}Page alias{/tr}</td>
  <td class="formcolor">
  <input type="text" name="pageAlias" value="{$pageAlias}" />
  </td>
  </tr>
  <tr>
  <td class="formcolor">
  {tr}After page{/tr}
  </td>
  <td class="formcolor">
  <select name="after_ref_id">
  {section name=ix loop=$subpages}
  <option value="{$subpages[ix].page_ref_id}" {if $max eq $subpages[ix].page_ref_id}selected="selected"{/if}>{$subpages[ix].pageName}</option>
  {/section}
  </select>
  </td>
  </tr>
  <tr>
  <td class="formcolor">
  {tr}create page{/tr}
  </td>
  <td class="formcolor">
  <input type="text" name="name" />
  </td>
  </tr>
  <tr>
  <td class="formcolor">
  {tr}Use pre-existing page{/tr}<br />
        <input type="text" name="find_objects" />
        <input type="submit" value="{tr}filter{/tr}" name="search_objects" />
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
  <input type="submit" name="create" value="{tr}update{/tr}" />
  </td>
  </tr>
</tr>
</table>
</form>

<br/>
<h2>{tr}Structure Layout{/tr}</h2>

{section name=ix loop=$subtree}
 {if $subtree[ix].pos eq ''}
     <a class='link' href='tiki-edit_structure.php?structure_id={$structure_id}&amp;page_ref_id={$subtree[ix].page_ref_id}'>{$subtree[ix].pageName}{if $subtree[ix].page_alias}({$subtree[ix].page_alias}){/if}</a>
	 &nbsp;[<a class='link' href='tiki-index.php?page_ref_id={$subtree[ix].page_ref_id}'>view</a>
	 |<a  class='link' href='tiki-editpage.php?page={$subtree[ix].pageName}'>edit</a>]
 {else}
   {if $subtree[ix].first}<ul>{/if}
   {* Handle dummy last entry *}
   {if $subtree[ix].last}
     </ul>
   {else}
     <li style='list-style:disc outside;'>{$subtree[ix].pos}
	     &nbsp;<a class='link' href='tiki-edit_structure.php?structure_id={$structure_id}&amp;page_ref_id={$subtree[ix].page_ref_id}'>{$subtree[ix].pageName}{if $subtree[ix].page_alias}({$subtree[ix].page_alias}){/if}</a>
		 &nbsp;[<a class='link' href='tiki-edit_structure.php?structure_id={$structure_id}&amp;remove={$subtree[ix].page_ref_id}'>x</a>]
		 &nbsp;[<a class='link' href='tiki-index.php?page_ref_id={$subtree[ix].page_ref_id}'>view</a>
		 |<a  class='link' href='tiki-editpage.php?page={$subtree[ix].pageName}'>edit</a>]
     </li>
   {/if}
 {/if}
{/section}

{if $remove eq 'y'}
<br/>
{tr}You will remove{/tr} '{$removePageName}' {tr}and its subpages from the structure, now you have two options:{/tr}
<ul>
<li><a class="link" href="tiki-edit_structure.php?structure_id={$structure_id}&amp;rremove={$removepage}">{tr}Remove only from structure{/tr}</a></li>
<li><a class="link" href="tiki-edit_structure.php?structure_id={$structure_id}&amp;sremove={$removepage}">{tr}Remove from structure and remove page too{/tr}</a></li>
</ul>
{/if}

