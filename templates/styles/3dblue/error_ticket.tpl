{include file="header.tpl"}
{* Index we display a wiki page here *}

{if $feature_bidi eq 'y'}<table dir="rtl" ><tr><td>{/if}
<div id="tiki-main">

{if $feature_top_bar eq 'y'}<div id="tiki-top">{include file="tiki-top_bar.tpl"}</div>{/if}

<div id="tiki-mid"><table border="0" cellpadding="0" cellspacing="0" ><tr>
{if $feature_left_column eq 'y'}
<td id="leftcolumn">{section name=homeix loop=$left_modules}{$left_modules[homeix].data}{if $left_modules[homeix].data ne ''}<table cellspacing="0" width="100%" cellpadding="0" ><tr><td width="100%"><img hspace="4" alt="left shadow" src="styles/3dblue/modl.gif" /></td></tr></table>{/if}{/section}</td>
{/if}

<td id="centercolumn" valign="middle">
<div id="tiki-center">
<div class="cbox">
<div class="cbox-title">
{tr}Error{/tr}
</div>
<div class="cbox-data">
{$msg}<br /><br />
<form action="{$self}{if $query}?{$query|escape}{/if}" method="post">
{foreach key=k item=i from=$post}
<input type="hidden" name="{$k}" value="{$i|escape}" />
{/foreach}
<input type="submit" name="ticket_action_button" value="{tr}Click here to confirm your action{/tr}" />
</form><br /><br />
<a href="javascript:history.back()" class="linkmenu">{tr}Go back{/tr}</a><br /><br />
<a href="{$tikiIndex}" class="linkmenu">{tr}Return to home page{/tr}</a>
</div>
</div>
</div>
</td>

{if $feature_right_column eq 'y'}
<td id="rightcolumn">{section name=homeix loop=$right_modules}{$right_modules[homeix].data}{if $right_modules[homeix].data ne ''}<table cellspacing="0" cellpadding="0" width="100%" ><tr><td width="100%"><div align="right"><img hspace="4" alt="right shadow" src="styles/3dblue/modr.gif" /></div></td></tr></table>{/if}{/section}</td>
{/if}

</tr></table></div>

{if $feature_bot_bar eq 'y'}<div id="tiki-bot">{include file="tiki-bot_bar.tpl"}</div>{/if}

</div>
{if $feature_bidi eq 'y'}</td></tr></table>{/if}

{include file="footer.tpl"}
