<h1>{tr}Approve page changes in staging{/tr}</a>: <a class="pagetitle" href="tiki-index.php?page={$page|escape:"url"}">{$page}</a></h1>
{tr}Changes from the following versions under staging have been merged into the approved version{/tr} 

{cycle values="odd,even" print=false}
<table>
<tr>
<td class="{cycle advance=false}">{$staging_info.lastModif|tiki_short_datetime}</td>
{if $tiki_p_wiki_view_author ne 'n'}<td class="{cycle advance=false}">{$staging_info.user}</td>{/if}
{if $prefs.feature_wiki_history_ip ne 'n'}<td class="{cycle advance=false}">{$staging_info.ip}</td>{/if}
<td class="{cycle advance=false}">{if $staging_info.comment}{$staging_info.comment}{else}&nbsp;{/if}</td>
<td class="{cycle advance=false} button">{$staging_info.version}<br />{tr}Current{/tr}</td>
<td class="{cycle} button">&nbsp;<a class="link" href="tiki-pagehistory.php?page={$staging_page|escape:"url"}&amp;preview={$staging_info.version}" title="{tr}View{/tr}" target="_blank">v</a>
{if $tiki_p_wiki_view_source eq "y" and $prefs.feature_source eq "y"}
&nbsp;<a class="link" href="tiki-pagehistory.php?page={$staging_page|escape:"url"}&amp;source={$staging_info.version}" title="{tr}Source{/tr}" target="_blank">s</a>
{/if}
</td>
{section name=hist loop=$history}
<tr>
<td class="{cycle advance=false}">{$history[hist].lastModif|tiki_short_datetime}</td>
{if $tiki_p_wiki_view_author ne 'n'}<td class="{cycle advance=false}">{$history[hist].user}</td>{/if}
{if $prefs.feature_wiki_history_ip ne 'n'}<td class="{cycle advance=false}">{$history[hist].ip}</td>{/if}
<td class="{cycle advance=false}">{if $history[hist].comment}{$history[hist].comment}{else}&nbsp;{/if}</td>
<td class="{cycle advance=false} button">{$history[hist].version}</td>
<td class="{cycle} button">
&nbsp;<a class="link" href="tiki-pagehistory.php?page={$staging_page|escape:"url"}&amp;preview={$history[hist].version}" title="{tr}View{/tr}" target="_blank">v</a>
{if $tiki_p_wiki_view_source eq "y" and $prefs.feature_source eq "y"}
&nbsp;<a class="link" href="tiki-pagehistory.php?page={$staging_page|escape:"url"}&amp;source={$history[hist].version}" title="{tr}Source{/tr}" target="_blank">s</a>
{/if}
</td>
</tr>
{/section}
</table>
