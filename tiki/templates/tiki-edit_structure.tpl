<a class="pagetitle" href="tiki-edit_structure.php?structure={$structure}&amp;page={$page}">{tr}Structure{/tr}: {$structure}</a><br/><br/>
<a class="link" href="tiki-admin_structures.php">Admin structures</a><br/><br/>
<form action="tiki-edit_structure.php" method="post">
<input type="hidden" name="page" value="{$page}" />
<input type="hidden" name="structure" value="{$structure}" />
<table class="normal">
<tr>
  <td class="formcolor">{tr}In parent page{/tr}</td>
  <td class="formcolor">
  <!--
  <select id='page' name="page" onChange="alert('tiki-edit_structure.php?structure={$structure}&amp;page=' + document.getElementById('page').selectedIndex);">
  {section name=ix loop=$pages}
  <option value="{$pages[ix]}" {if $page eq $pages[ix]}selected="selected"{/if}>{$pages[ix]}</option>
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
  <option value="{$subpages[ix]}" {if $max eq $subpages[ix]}selected="selected"{/if}>{$subpages[ix]}</option>
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
<li><a class="link" href="tiki-edit_structure.php?structure={$structure}&amp;rremove={$removepage}">{tr}Remove only from structure{/tr}</a></li>
<li><a class="link" href="tiki-edit_structure.php?structure={$structure}&amp;sremove={$removepage}">{tr}Remove from structure and remove page too{/tr}</a></li>
</ul>
{/if}
<h2>{tr}Structure{/tr}</h2>
<a class="link" href="tiki-edit_structure.php?structure={$structure}&amp;page={$structure}">{$structure}</a> [<a class="link" href="tiki-index.php?page={$structure}">{tr}view{/tr}</a>|<a class="link" href="tiki-editpage.php?page={$structure}">{tr}edit{/tr}</a>]<br/>
{$html}
