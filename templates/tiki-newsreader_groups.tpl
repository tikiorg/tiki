{*Smarty template*}
<a class="pagetitle" href="tiki-newsreader_groups.php?serverId={$serverId}">{tr}Select news group{/tr}</a><br/><br/>
<table class="normal">
<tr>
  <td class="heading">{tr}Group{/tr}</td>
  <td class="heading">{tr}Msgs{/tr}</td>
  <td class="heading">{tr}Desc{/tr}</td>
</tr>
{cycle values="odd,even" print=false}
{foreach from=$groups key=group item=item}
<tr>
  <td class="{cycle advance=false}"><a class="link" href="tiki-newsreader_news.php?server={$info.server}&amp;port={$info.port}&amp;username={$info.username}&amp;password={$info.password}&amp;group={$group}&amp;offset=0">{$group}</a></td>
  <td class="{cycle advance=false}">{math equation="x-y" x=$item.last y=$item.first}</td>
  <td class="{cycle}">{$item.desc}</td>
</tr>
{/foreach}
</table>
