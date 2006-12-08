{* $Header: /cvsroot/tikiwiki/tiki/templates/confirm.tpl,v 1.8 2006-12-08 20:56:41 sylvieg Exp $ *}
        <div class="cbox">
		{if !empty($confirmation_text)}<div class="cbox-title">{$confirmation_text}</div>{/if}
        <br />
        <div class="cbox-data">
				<form action="{$confirmaction}" method="post">
{if $ticket}<input value="{$ticket}" name="ticket" type="hidden" />{/if}
				<input type="submit" name="daconfirm" value="{tr}Click here to confirm your action{/tr}" />
        <span class="button2"><a href="javascript:history.back()" class="linkbut">{tr}Go back{/tr}</a></span>
        <span class="button2"><a href="{$tikiIndex}" class="linkbut">{tr}Return to home page{/tr}</a></span>
				</form>
        </div>
        </div><br />

      </div>
