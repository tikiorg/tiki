{* $Id$ *}

				<div class="cbox">
					<div class="cbox-data">
						<form action="{$confirmaction}" method="post">
		{if $ticket}<input value="{$ticket}" name="ticket" type="hidden" />{/if}
							<button type="submit" name="daconfirm">{$confirmation_text}</button>
							{button href="#" _onclick="javascript:history.back(); return false;" _text="{tr}Go back{/tr}"}
							{button href=$prefs.tikiIndex _text="{tr}Return to home page{/tr}"}
						</form>
					</div>
				</div>
