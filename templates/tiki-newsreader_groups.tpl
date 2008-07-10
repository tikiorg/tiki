{*Smarty template*}
<h1><a class="pagetitle" href="tiki-newsreader_groups.php?serverId={$serverId}">{tr}Select news group{/tr}</a></h1>
{include file=tiki-mytiki_bar.tpl}
<br /><br />
[<a class="link" href="tiki-newsreader_servers.php">{tr}Back to servers{/tr}</a>]
<br /><br />
<table class="normal">
<tr>
  <td class="heading">{tr}Group{/tr}</td>
  <td style="text-align:right;" class="heading">{tr}Msgs{/tr}</td>
  <td class="heading">{tr}Desc{/tr}</td>
</tr>
{cycle values="odd,even" print=false}
{foreach from=$groups key=group item=item}
<tr>
  <td class="{cycle advance=false}"><a class="link" href="tiki-newsreader_news.php?server={$info.server}&amp;port={$info.port}{if !empty($info.username)}&amp;username={$info.username}{/if}{if !empty($info.password)}&amp;password={$info.password}{/if}&amp;group={$group}&amp;offset=0&amp;serverId={$serverId}&amp;serverId={$serverId}">{$group}</a></td>
  <td style="text-align:right;" class="{cycle advance=false}">{if isset($item.last) and isset($item.first)}{math equation="1+x-y" x=$item.last y=$item.first}{/if}</td>
  <td class="{cycle}">{$item.desc}</td>
</tr>
{/foreach}
</table>
<br /><br />
