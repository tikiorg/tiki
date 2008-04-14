{* $Id$ *}
<div class="cbox">
{if !empty($confirmation_text)}<div class="cbox-title">{icon _id=information style="vertical-align:middle"} {$confirmation_text}</div>{/if}
<br />
<div class="cbox-data">
<form action="{$confirmaction}" method="post">
{if $ticket}<input value="{$ticket}" name="ticket" type="hidden" />{/if}
{foreach key=k item=i from=$post}
	{if is_array($i)}
		{foreach from=$i item=i2}
<input type="hidden" name="{$k}[]" value="{$i2|escape}" />
		{/foreach}
	{else}
<input type="hidden" name="{$k}" value="{$i|escape}" />
	{/if}
{/foreach}
<input type="submit" name="daconfirm" value="{tr}Click here to confirm your action{/tr}" />
<span class="button2"><a href="javascript:history.back()" class="linkbut">{tr}Go back{/tr}</a></span>
<span class="button2"><a href="{$prefs.tikiIndex}" class="linkbut">{tr}Return to home page{/tr}</a></span>
</form>
</div>
</div><br />

</div>
