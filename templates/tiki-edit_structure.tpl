<a class="pagetitle" href="tiki-edit_structure.php?structure={$structure|escape:"url"}&amp;page={$page|escape:"url"}">
  {tr}Structure{/tr}: {$structID}
</a><br/><br/>

<a class="link" href="tiki-admin_structures.php">{tr}Admin structures{/tr}</a><br/><br/>
<form action="tiki-edit_structure.php" method="post">
<input type="hidden" name="page" value="{$page|escape}" />
<input type="hidden" name="structID" value="{$structID|escape}" />

<h2>{tr}Modify Structure{/tr}</h2>
<br/>
<table class="normal">
  <tr>
  <td class="formcolor">{tr}In parent page{/tr}</td>
  <td class="formcolor">
	{$page}
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
  <select name="after">
  {section name=ix loop=$subpages}
  <option value="{$subpages[ix]|escape}" {if $max eq $subpages[ix]}selected="selected"{/if}>{$subpages[ix]}</option>
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
{if $remove eq 'y'}
<br/>
{tr}You will remove{/tr} {$removepage} {tr}and its subpages from the structure, now you have two options:{/tr}
<ul>
<li><a class="link" href="tiki-edit_structure.php?structure={$structure|escape:"url"}&amp;rremove={$removepage|escape:"url"}">{tr}Remove only from structure{/tr}</a></li>
<li><a class="link" href="tiki-edit_structure.php?structure={$structure|escape:"url"}&amp;sremove={$removepage|escape:"url"}">{tr}Remove from structure and remove page too{/tr}</a></li>
</ul>
{/if}

<br/>
<h2>{tr}Modify Structure Layout{/tr}</h2>
<table class="normal">
  <tr><td class="formcolor">Coming Soon</td></tr>
</table>
<br/>
<h2>{tr}Structure Layout{/tr}</h2>

{$html}
