<h1><a href="tiki-create_webhelp.php" class="pagetitle">{tr}Create WebHelp{/tr}</a>

</h1>

{if  $generated eq 'y'}
<a class="link" href="whelp/{$dir}/index.html">{tr}You can browse the generated WebHelp here{/tr}</a><br /><br />
{/if}
<form method="post" action="tiki-create_webhelp.php">
<table class="normal">
  <tr>
  	<td class="formcolor">{tr}Structure{/tr}</td>
  	<td class="formcolor">{$struct_info.pageName}</td>
  </tr>
  <input type="hidden" name="name" value="{$struct_info.pageName}" />
  <input type="hidden" name="struct" value="{$struct_info.page_ref_id}" />
  <tr>
  	<td class="formcolor">{tr}Directory{/tr}</td>
  	<td class="formcolor"><input type="text" name="dir" value="{$struct_info.pageName}" /></td>
  </tr>
  <tr>
  	<td class="formcolor">{tr}Top page{/tr}</td>
  	<td class="formcolor"><input type="text" name="top" value="{$struct_info.pageName}" /></td>
  </tr>
  <tr>
  	<td class="formcolor">&nbsp;</td>
  	<td class="formcolor"><input type="submit" name="create" value="{tr}Create{/tr}" /></td>
  </tr>
</table>  
</form>
