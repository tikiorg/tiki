<div class="rbox" name="tip">
<div class="rbox-title" name="tip">{tr}Tip{/tr}</div>  
<div class="rbox-data" name="tip">{tr}To learn more about <a class="rbox-link" target="tikihelp" href="http://mods.tikiwiki.org">mods</a>.{/tr}
</div>
</div>
<br />

<style>
{literal}
.focus { background-color : #eeee77; }
{/literal}
</style>
<h1><a href="tiki-mods.php" class="pagetitle">{tr}TikiWiki Mods{/tr}</a></h1>
<span class="button2"><a href="tiki-mods_admin.php" class="linkbut">{tr}Mods Configuration{/tr}</a></span>
<span class="button2"><a href="tiki-mods.php?reload=1{$findarg}{$typearg}" class="linkbut">{tr}Update remote index{/tr}</a></span>
<span class="button2"><a href="tiki-mods.php?rebuild=1{$findarg}{$typearg}" class="linkbut">{tr}Rebuild local list{/tr}</a></span>
{if $feature_mods_provider eq 'y'}
<span class="button3">
<span class="button2"><a href="tiki-mods.php?republishall=1{$findarg}{$typearg}" class="linkbut">{tr}Republish all{/tr}</a></span>
<span class="button2"><a href="tiki-mods.php?publishall=1{$findarg}{$typearg}" class="linkbut">{tr}Publish all{/tr}</a></span>
<span class="button2"><a href="tiki-mods.php?unpublishall=1{$findarg}{$typearg}" class="linkbut">{tr}Unpublish all{/tr}</a></span>
</span>
{/if}
<br /><br />
{if $iswritable}
<div class="simplebox" style="color:#009900;"><b>{tr}Attention{/tr}</b><br />{tr}Apache has the right to write in your file tree, which enables the installation, removal or 
upgrade of packages. When you are done with those operations, think to fix those permissions back to a safe state (by using 
"./fixperms fix" for example).{/tr}</div>
{else}
<div class="simplebox" style="color:#990000;"><b>{tr}Attention{/tr}</b><br />{tr}To install, remove or upgrade packages you need to give the apache user the right
to write files in your web tree (you can use "./fixperms.sh open" to set it up). After installation you need to remove that
permission (using "./fixperms fix").{/tr}</div>
{/if}
<br />

{if $tikifeedback}
<br />
{section name=n loop=$tikifeedback}
<div class="simplebox {if $tikifeedback[n].num > 0} highlight{/if}">{$tikifeedback[n].mes}</div>
{/section}{/if}

<form method="get" action="tiki-mods.php">
<select name="type" onchange="this.form.submit();">
<option value="">{tr}all types{/tr}</option>
{foreach key=it item=i from=$types}
<option value="{$it|escape}"{if $it eq $type} selected="selected"{/if}>{$it}</option>
{/foreach}
</select>
<input type="text" name="find" value="{$find|escape}" />
<input type="submit" name="f" value="{tr}find{/tr}" />
</form>

<table cellspacing="0" cellpadding="2" border="0" class="normal">
{foreach key=type item=i from=$display}
<tr><td colspan="{if $feature_mods_provider eq 'y'}3{else}2{/if}">
<span class="button2"><a href="tiki-mods.php?type={$type|escape:"url"}{$findarg}" class="linkbut" title="{tr}Display only this type{/tr}">{$type}</a></span>
</td><td colspan="7">&nbsp;</td>
</tr>
{cycle values="odd,even" print=false}
{foreach key=item item=it from=$display.$type}
<tr class="{if $focus and $focus eq $display.$type.$item.name}focus{else}{cycle}{/if}">

{if $feature_mods_provider eq 'y'}
{assign var=mod value=$public.$type.$item.modname}
{if $public.$type.$item}
{if $dist.$mod.rev_major lt $local.$type.$item.rev_major or 
   ($dist.$mod.rev_major eq $local.$type.$item.rev_major and 
    $dist.$mod.rev_minor lt $local.$type.$item.rev_minor) or 
   ($dist.$mod.rev_major eq $local.$type.$item.rev_major and 
    $dist.$mod.rev_minor eq $local.$type.$item.rev_minor and
    $dist.$mod.rev_subminor lt $local.$type.$item.rev_subminor)}							
<td style="background:#fcfeac;"><a href="tiki-mods.php?unpublish={$public.$type.$item.modname|escape:"url"}{$findarg}{$typearg}" title="{tr}Unpublish{/tr}">[x]</a>{if $dist.$mod}<a 
href="tiki-mods.php?republish={$public.$type.$item.modname|escape:"url"}{$findarg}{$typearg}" title="{tr}Republish{/tr}">{$dist.$mod.revision}&gt;{$local.$type.$item.revision}</a>{/if}
{else}
<td style="background:#fcfeac;"><a href="tiki-mods.php?unpublish={$public.$type.$item.modname|escape:"url"}{$findarg}{$typearg}" title="{tr}Unpublish{/tr}">[x]</a>{if $dist.$mod}{$dist.$mod.revision}{/if}
{/if}
</td>
{elseif $local.$type.$item}
<td style="background:#ededed;"><a href="tiki-mods.php?publish={$local.$type.$item.modname|escape:"url"}{$findarg}{$typearg}" title="{tr}Publish{/tr}">[+]</a></td>
{else}
<td style="background:#ededed;"></td>
{/if}
{/if}

{if $remote.$type.$item}
{if $remote.$type.$item.rev_major gt $local.$type.$item.rev_major or
   ($remote.$type.$item.rev_major eq $local.$type.$item.rev_major and
    $remote.$type.$item.rev_minor gt $local.$type.$item.rev_minor) or
   ($remote.$type.$item.rev_major eq $local.$type.$item.rev_major and
   	$remote.$type.$item.rev_minor eq $local.$type.$item.rev_minor and
    $remote.$type.$item.rev_subminor gt $local.$type.$item.rev_subminor)}
<td style="background:#fcfeac;"><a href="tiki-mods.php?dl={$remote.$type.$item.modname|escape:"url"}-{$remote.$type.$item.revision}{$findarg}{$typearg}" title="{tr}Download{/tr}">{$remote.$type.$item.revision}</a></td>
{else}
<td style="background:#acfeac;"><a href="tiki-mods.php?dl={$remote.$type.$item.modname|escape:"url"}-{$remote.$type.$item.revision}{$findarg}{$typearg}" title="{tr}Download{/tr}">{$remote.$type.$item.revision}</a></td>
{/if}
{else}
<td style="background:#dcdcdc;"></td>
{/if}

{if $local.$type.$item.name}
<td><b><a href="tiki-mods.php?focus={$local.$type.$item.modname|escape:"url"}{$findarg}{$typearg}">{$local.$type.$item.name}</a></b></td>
<td>{$local.$type.$item.revision}</td>
<td>{$local.$type.$item.licence}</td>
<td>{$local.$type.$item.description}</td>
{if $installed.$type.$item} 
{if $local.$type.$item.rev_major gt $installed.$type.$item.rev_major or
   ($local.$type.$item.rev_major eq $installed.$type.$item.rev_major and
    $local.$type.$item.rev_minor gt $installed.$type.$item.rev_minor) or
   ($local.$type.$item.rev_major eq $installed.$type.$item.rev_major and
   	$local.$type.$item.rev_minor eq $installed.$type.$item.rev_minor and
    $local.$type.$item.rev_subminor gt $installed.$type.$item.rev_subminor)}
<td style="background:#dcdeac;">{$installed.$type.$item.revision}{if $iswritable}<a href="tiki-mods.php?action=upgrade&amp;package={$local.$type.$item.modname|escape:"url"}{$findarg}{$typearg}">&gt;{$local.$type.$item.revision}</a>{/if}</td>
{else}
<td style="background:#acfeac;">{$installed.$type.$item.revision}</td>
{/if}
<td style="background:#fcaeac;">{if $iswritable}<a href="tiki-mods.php?action=remove&amp;package={$local.$type.$item.modname|escape:"url"}{$findarg}{$typearg}">{tr}remove{/tr}</a>{/if}</td>
{else}
<td colspan="3">{if $iswritable}<a href="tiki-mods.php?action=install&amp;package={$local.$type.$item.modname|escape:"url"}{$findarg}{$typearg}">{tr}install{/tr}</a>{else}<b><s>{tr}Install{/tr}</s></b>{/if}</td>
{/if}
{else}
<td>{$remote.$type.$item.name}</td>
<td>{$remote.$type.$item.revision}</td>
<td>{$remote.$type.$item.licence}</td>
<td>{$remote.$type.$item.description}</td>
{/if}
</tr>

{if $focus and $focus eq $local.$type.$item.modname}
<tr class="{cycle}"><td colspan="{if $feature_mods_provider eq 'y'}9{else}8{/if}">
<table><tr><td>
<div class="simplebox">
{if $more.docurl}Documentation :<br />{foreach key=ku item=iu from=$more.docurl}<a href="{$iu}">{$iu}</a><br />{/foreach}{/if}
{if $more.devurl}Development : <br />{foreach key=ku item=iu from=$more.devurl}<a href="{$iu}">{$iu}</a><br />{/foreach}{/if}
{if $more.help}{$more.help}<br />{/if}
{if $more.help}{$more.help}<br />{/if}
{if $more.author}{tr}author{/tr}: {$more.author[0]}<br />{/if}

{tr}last modification{/tr}: {$more.lastmodif[0]}<br />
{tr}by{/tr}: {$more.contributor[0]}<br />
</div>
</td><td>
{foreach key=kk item=ii from=$more.files}
{$ii[0]} -&gt; <b>{$ii[1]}</b><br />
{/foreach}
</td></tr></table>

</td></tr>
{/if}

{/foreach}
{/foreach}
</table>

