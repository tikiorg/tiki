<h2>{tr}User_versions_for:{/tr} {$ruser}</h2>
{if $preview}
<h2>{tr}Version:{/tr} {$version}</h2>
<div class="wikitext">{$preview.data}</div>
<br /> 
{/if}
<br />
<div align="center">
<table  border="1"  cellpadding="0" cellspacing="0">
<tr>
<td id="heading">{tr}Date{/tr}</td>
<td id="heading">{tr}Page{/tr}</td>
<td id="heading">{tr}Version{/tr}</td>
<td id="heading">{tr}Ip{/tr}</td>
<td id="heading">{tr}Comment{/tr}</td>
<td id="heading">{tr}Action{/tr}</td>
</tr>
{cycle values="even,odd" print=false}
{section name=hist loop=$history}
<tr>
<td id="{cycle advance=false}">&nbsp;{$history[hist].lastModif|tiki_long_datetime}&nbsp;</td>
<td id="{cycle advance=false}">&nbsp;<a href="tiki-index.php?page={$history[hist].pageName|escape:"url"}">{$history[hist].pageName}</a>&nbsp;</td>
<td id="{cycle advance=false}">&nbsp;{$history[hist].version}&nbsp;</td>
<td id="{cycle advance=false}">&nbsp;{$history[hist].ip}&nbsp;</td>
<td id="{cycle advance=false}">&nbsp;{$history[hist].comment}&nbsp;</td>
<td id="{cycle}">&nbsp;<a href="tiki-userversions.php?ruser={$ruser}&amp;page={$history[hist].pageName|escape:"url"}&amp;preview=1&amp;version={$history[hist].version}">{tr}View{/tr}</a>&nbsp;</td>
</tr>
{sectionelse}
	{norecords _colspan=6}
{/section}
</table>
</div>
