<br/>
<div align="center">
<table border="1" bgcolor="#EAEAEA" width="100%" border="0" cellpadding="0" cellspacing="0">
<tr><td class="heading">{tr}URL{tr}</td><td class="text">{$info.url}</td></tr>
<tr><td class="heading">{tr}Cached{/tr}</td><td class="text">{$info.refresh|date_format:"%A %d of %B, %Y [%H:%M:%S]"}</td>
<tr><td class="text" colspan="2"><br/>{tr}This is a cached version of the page.{/tr}</td></tr>
</table>
</div>
<br/>
<div class="cachedpage">
{$info.data}
</div>
