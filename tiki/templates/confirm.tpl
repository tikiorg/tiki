{* $Header: /cvsroot/tikiwiki/tiki/templates/confirm.tpl,v 1.9 2007-02-17 10:10:31 mose Exp $ *}
<div class="cbox">
{if !empty($confirmation_text)}<div class="cbox-title">{$confirmation_text}</div>{/if}
<br />
<div class="cbox-data">
<form action="{$confirmaction}" method="post">
{if $ticket}<input value="{$ticket}" name="ticket" type="hidden" />{/if}
{foreach key=k item=i from=$post}
<input type="hidden" name="{$k}" value="{$i|escape}" />
{/foreach}
<input type="submit" name="daconfirm" value="{tr}Click here to confirm your action{/tr}" />
<span class="button2"><a href="javascript:history.back()" class="linkbut">{tr}Go back{/tr}</a></span>
<span class="button2"><a href="{$tikiIndex}" class="linkbut">{tr}Return to home page{/tr}</a></span>
</form>
</div>
</div><br />

</div>
