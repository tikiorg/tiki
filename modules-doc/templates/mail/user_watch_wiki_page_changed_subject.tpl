{if $mail_action eq 'new'}{tr}Wiki page %s created by {$mail_user}{/tr}
{elseif $mail_action eq 'delete'}{tr}Wiki page %s deleted by {$mail_user}{/tr}
{else}{tr}Wiki page %s changed by {$mail_user}{/tr}{/if}