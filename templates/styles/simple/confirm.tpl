{* $Header: /cvsroot/tikiwiki/tiki/templates/styles/simple/confirm.tpl,v 1.4 2006-08-29 20:19:15 sylvieg Exp $ *}

				<div class="cbox">
					<div class="cbox-data">
						<form action="{$confirmaction}" method="post">
		{if $ticket}<input value="{$ticket}" name="ticket" type="hidden" />{/if}
							<button type="submit" name="daconfirm">{$confirmation_text}</button>
							<span class="button2"><a href="javascript:history.back()" class="linkbut">{tr}Go back{/tr}</a></span>
							<span class="button2"><a href="{$tikiIndex}" class="linkbut">{tr}Return to home page{/tr}</a></span>
						</form>
					</div>
				</div>
