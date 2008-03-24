{include file="header.tpl"}
{if $prefs.feature_bidi eq 'y'}
<table dir="rtl" ><tr><td>
{/if}
<div id="tiki-main" class="simplebox">
<h3>{tr}Details{/tr}</h3>
<table class="normalnoborder">
{if $info.name}
<tr class="formcolor"><td>{tr}Name{/tr}</td><td><b>{$info.name}</b></td></tr>
{/if}
{if $info.version}
<tr class="formcolor"><td>{tr}Version{/tr}</td><td><b>{$info.version}</b></td></tr>
{/if}
{if $info.longdesc}
<tr class="formcolor"><td colspan="2">{$info.longdesc}</td></tr>
{/if}
{if $info.hits}
<tr class="formcolor"><td>{tr}Downloads{/tr}</td><td>{$info.hits}</td></tr>
{/if}
</table>
<div class="cbox">
<a href="#" onclick="javascript:window.close();" class="link">{tr}close{/tr}</a>
</div>
</div>
{if $prefs.feature_bidi eq 'y'}
</td></tr></table>
{/if}
{include file="footer.tpl"}
