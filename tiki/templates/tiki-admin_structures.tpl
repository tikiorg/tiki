<a href="tiki-admin_structures.php" class="pagetitle">{tr}Structures{/tr}</a>
<br/><br/>
<h2>{tr}Create new structure{/tr}</h2>
<form action="tiki-admin_structures.php" method="post">
<table class="normal">
<tr>
   <td class="formcolor">{tr}name{/tr}:</td>
   <td class="formcolor"><input type="text" name="name" /></td>
</tr>    
<tr>
   <td class="formcolor">&nbsp;</td>
   <td class="formcolor"><input type="submit" name="create" /></td>
</tr>
</table>
</form>
<br/><br/>
<table class="normal">
<tr>
  <td class="heading">{tr}Structure{/tr}</td>
</tr>
{cycle values="odd,even" print=false}
{section loop=$channels name=ix}
<tr>
  <td class="{cycle}"><a class="tablename" href="tiki-edit_structure.php?structure={$channels[ix].page}">{$channels[ix].page}</a></td>
</tr>
{/section}
</table>