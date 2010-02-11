{title help=Structure+Admin url=tiki-create_webhelp.php}{tr}Create WebHelp{/tr}{/title}
{if  $generated eq 'y'}
<div class="navbar">
  <span class="button">
	<a class="link" href="whelp/{$dir}/index.html">{tr}View generated WebHelp.{/tr}</a>
  </span>
</div>
{/if}
{if $output ne ''}
<div class="simplebox">
{$output}
</div>
{/if}
<form method="post" action="tiki-create_webhelp.php">
<table class="normal">
  <tr>
  	<td class="formcolor">{tr}Structure{/tr}</td>
  	<td class="formcolor">{$struct_info.pageName|default:"{tr}No structure{/tr}."}</td>
  </tr>
  <input type="hidden" name="name" value="{$struct_info.pageName}" />
  <input type="hidden" name="struct" value="{$struct_info.page_ref_id}" />
  <tr>
  	<td class="formcolor"><label for="id">{tr}Directory{/tr}</label></td>
  	<td class="formcolor"><input type="text" id="dir" name="dir" value="{$struct_info.pageName}" /></td>
  </tr>
  <tr>
  	<td class="formcolor"><label for="top">{tr}Top page{/tr}</label></td>
  	<td class="formcolor"><input type="text" id="top" name="top" value="{$struct_info.pageName}" /></td>
  </tr>
  <tr>
  	<td class="formcolor">&nbsp;</td>
  	<td class="formcolor"><input type="submit" {if !$struct_info.pageName}disabled=disabled" {/if}name="create" value="{tr}Create{/tr}" /></td>
  </tr>
</table>  
</form>

