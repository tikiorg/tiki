<a class="pagetitle" href="tiki-edit_structure.php?structure={$structure|escape:"url"}&amp;page={$page|escape:"url"}">
  {tr}Structure{/tr}: {$structure}
</a><br/><br/>
<a class="link" href="tiki-admin_structures.php">{tr}Admin structures{/tr}</a><br/><br/>
<form action="tiki-edit_structure.php" method="post">
<input type="hidden" name="page" value="{$page|escape}" />
<input type="hidden" name="structure" value="{$structure|escape}" />
<table class="normal">
<tr>
  <td class="formcolor">{tr}In parent page{/tr}</td>
  <td class="formcolor">
  <!--
  <select id='page' name="page" onChange="alert('tiki-edit_structure.php?structure={$structure|escape:"url"}&amp;page=' + document.getElementById('page').selectedIndex);">
  {section name=ix loop=$pages}
  <option value="{$pages[ix]|escape}" {if $page eq $pages[ix]}selected="selected"{/if}>{$pages[ix]}</option>
  {/section}
  </select>
  -->
  {$page}
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
  <input type="submit" name="create" value="{tr}create{/tr}" />
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
<h2>{tr}Structure{/tr}</h2>
<a class="link" href="tiki-edit_structure.php?structure={$structure|escape:"url"}&amp;page={$structure|escape:"url"}">
  {$structure}
</a>
[
 <a class="link" href="tiki-index.php?page={$structure|escape:"url"}">
  {tr}view{/tr}
 </a>
|
 <a class="link" href="tiki-editpage.php?page={$structure|escape:"url"}">
  {tr}edit{/tr}
 </a>
]<br/>
{$html}
