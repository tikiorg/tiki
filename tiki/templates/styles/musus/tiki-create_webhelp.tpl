<a href="tiki-create_webhelp.php" class="pagetitle">{tr}Create WebHelp{/tr}</a>
<!-- the help link info -->
<br /><br />

{if  $generated eq 'y'}
<a href="whelp/{$dir}/index.html">{tr}You can browse the generated WebHelp here{/tr}</a><br /><br />
{/if}
<form method="post" action="tiki-create_webhelp.php">
<table>
  <tr>
  	<td>{tr}Structure{/tr}</td>
  	<td>{$struct_info.pageName}</td>
  </tr>
  <input type="hidden" name="name" value="{$struct_info.pageName}" />
  <input type="hidden" name="struct" value="{$struct_info.page_ref_id}" />
  <tr>
  	<td>{tr}Directory{/tr}</td>
  	<td><input type="text" name="dir" value="{$struct_info.pageName}" /></td>
  </tr>
  <tr>
  	<td>{tr}Top page{/tr}</td>
  	<td><input type="text" name="top" value="{$struct_info.pageName}" /></td>
  </tr>
  <tr>
  	<td>&nbsp;</td>
  	<td><input type="submit" name="create" value="{tr}Create{/tr}" /></td>
  </tr>
</table>  
</form>
