<h1><a href="tiki-mods.php" class="pagetitle">{tr}TikiWiki mods Management{/tr}</a></h1>
<a href="http://mods.tikiwiki.org">check http://mods.tikiwiki.org</a><br /><br />

<form>
<div class="pagebar">
<span class="button2"><a href="tiki-mods.php?rebuild=1" class="linkbut">{tr}rebuild local list{/tr}</a></span>
<input type="text" name="master" value="{$master}" size="42" />
<input type="hidden" name="refresh" value="1" />
<input type="submit" name="act" value="{tr}refrech servers list{/tr}" />
<br /><br />
{section name=u loop=$remote_masters}
<span class="button2"><a href="{$remote_masters[u]}/Packages/" class="linkbut">{$remote_masters[u]}</a> (<a href="tiki-mods.php?reload={$remote_masters[u]|escape:"url"}">reload</a>)</span><br />
{/section}
</form>
<br /><br />

{if count($list)}
<table cellspacing="0" cellpadding="0" border="0" class="normal">
<tr><td valign="top" class="third">
<div class="simplebox">
<span class="button2"><a class="linkbut" href="tiki-mods.php?type=all">{tr}all{/tr}</a></span><br /><br />
{foreach key=k item=i from=$list}
<span class="button2"><a class="linkbut" href="tiki-mods.php?type={$k}">{$k}</a></span><br /><br />
{/foreach}
</div>
</td>
{if $type and count($display)}
<td valign="top">
<table cellspacing="0" cellpadding="2" border="0" class="normal">
{foreach key=type item=i from=$display}
<tr><td colspan="10" class="third"><b class="pagetitle">{$type}</b></td></tr>
<tr>
<td colspan="3">&nbsp;</td>
<td class="third"><b>{tr}Name{/tr}</b></td>
<td class="third">{tr}Revision{/tr}</td>
<td class="third">{tr}Licence{/tr}</td>
<td class="third">{tr}Description{/tr}</td>
<td colspan="3" class="third">&nbsp;</td>
</tr>
{cycle values="odd,even" print=false}
{foreach key=l item=j from=$i}
<tr class="{if $focus and $focus eq $j.name}highlight{else}{cycle}{/if}">
{if $public.$type.$l}
<td style="background:#fcfeac;"><a href="tiki-mods.php?type={$type}&unpublish={$type}-{$public.$type.$l.name}">O</a></td>
{else}
<td style="background:#ededed;"><a href="tiki-mods.php?type={$type}&publish={$type}-{$list.$type.$l.name}">X</a></td>
{/if}
{if $remote.$type.$l}
{if $remote.$type.$l.revision > $j.revision}
<td style="background:#fcfeac;"><a href="tiki-mods.php?dl={$type}-{$remote.$type.$l.name}">{$remote.$type.$l.revision}</a></td>
{else}
<td style="background:#acfeac;">==</td>
{/if}
{else}
<td style="background:#dcdcdc;">&nbsp;</td>
{/if}
</td>
<td><img src="img/icn/gz.gif" height="16" width="16" border="0" alt="" /></td>
<td><b><a href="tiki-mods.php?type={$type}&amp;focus={$j.name|escape:"url"}">{$j.name}</a></b></td>
<td>{$j.revision}</td>
<td>{$j.licence}</td>
<td>{$j.description}</td>
{if $installed.$type.$l} 
{if $installed.$type.$l.revision < $j.revision}
<td style="background:#acfeac;">{tr}installed{/tr}</td>
<td style="background:#dcdeac;">r{$installed.$type.$l.revision} <a href="tiki-mods.php?type={$type}&amp;action=upgrade&amp;package={$j.name|escape:"url"}">{tr}upgrade to {/tr}</a> r{$j.revision}</td>
{else}
<td style="background:#acfeac;">{tr}up to date{/tr} r{$installed.$type.$l.revision}</td>
<td style="background:#acfeac;">{tr}installed{/tr}</td>
{/if}
<td style="background:#fcaeac;"><a href="tiki-mods.php?type={$type}&amp;action=remove&amp;package={$j.name|escape:"url"}">{tr}remove{/tr}</a></td>
{else}
<td colspan="3"><a href="tiki-mods.php?type={$type}&amp;action=install&amp;package={$j.name|escape:"url"}">{tr}install{/tr}</a></td>
{/if}
</tr>
{if $focus and $focus eq $j.name}
<tr class="{cycle}"><td colspan="4">
{if $public.$type.$l}
<div class="simplebox" style="background:#fcfeac;">Public <a href="tiki-mods.php?type={$type}&unpublish={$type}-{$public.$type.$l.name}">{tr}Unpublish{/tr}</a></div>
{else}
<div class="simplebox" style="background:#ededed;">Local <a href="tiki-mods.php?type={$type}&unpublish={$type}-{$public.$type.$l.name}">{tr}Publish{/tr}</a></div>
{/if}
{if $remote.$type.$l}
{if $remote.$type.$l.revision > $j.revision}
<div class="simplebox" style="background:#fcfeac;">New version <a href="tiki-mods.php?dl={$type}-{$remote.$type.$l.name}">{$remote.$type.$l.revision}</a></div>
{else}
<div class="simplebox" style="background:#acfeac;">Up to date</div>
{/if}
{else}
<div class="simplebox" style="background:#dcdcdc;">Not available</div>
{/if}
<div class="simplebox">
{if $more.help}{$more.help}<br />{/if}
{if $more.author}{tr}author{/tr}: {$more.author[0]}<br />{/if}
<br />
{tr}last modification{/tr}:<br />
{$more.lastmodif[0]}<br />
{tr}by{/tr}: {$more.contributor[0]}<br />
</div>
</td><td colspan="4">
{foreach key=kk item=ii from=$more.files}
{$ii[0]} -&gt; <b>{$ii[1]}</b><br />
{/foreach}
</td></tr>
{/if}
{/foreach}
{/foreach}
<tr><td colspan="10" class="third"><img src="img/icons/0.png" width="1" height="1" alt="" hspace="0" vspace="0" /></td></tr>
</table>
</td>
{/if}
</tr></table>
{/if}
