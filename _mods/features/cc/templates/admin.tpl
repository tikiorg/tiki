<h1><a class="pagetitle" href="cc-admin.php">{tr}Currencies Admin{/tr}</a></h1>
<br /><br />

{if $msg}<div class="alert">{$msg}</div>{/if}

<form method="post" action="cc-admin.php">
<table class="normal">
<tr class="formcolor">
<td>{tr}Currency Provider Unique Name{/tr}</td>
<td><input type="text" name="cc_cpun" value="{$cc_cpun}" /></td>
</tr>
<tr class="formcolor">
<td>{tr}Contact email{/tr}</td>
<td><input type="text" name="cc_mail" value="{$cc_mail}" /></td>
</tr>
<tr class="formcolor">
<td>{tr}Domain for list of CC service providers{/tr}</td>
<td><input type="text" name="cc_ccsp_ref" value="{$cc_ccsp_ref}" /></td>
</tr>
<tr>
<td>&nbsp;</td>
<td><input type="submit" name="act" value="{tr}Save{/tr}" /></td>
</tr>
</table>
</form>

<div class="wikitext">
<h1>{tr}Currencies Providers{/tr}</h1>
<span class="button2"><a href="cc-admin.php?providers_refresh=1" class="linkbut">{tr}Refresh{/tr}</a></span>
<span class="button2"><a href="" class="linkbut">{tr}{/tr}</a></span>
<br /><br />

<table>
<tr>
<th>CPUN</th>
<th>url</th>
<th>Last update</th>
<th>Currencies</th>
</tr>
{foreach item=p from=$providers}
<tr>
<td>{$p.cpun}</td>
<td><a href="http://{$p.url}">http://{$p.url}</a></td>
<td>{$p.lastupdate|tiki_short_date}</td>
<td style="text-align:right">{$p.cc}</td>
</tr>
{/foreach}
</table>

