{if $mail_action eq 'new'}{tr}Wiki page %s created by {$mail_user|username}{/tr}
{elseif $mail_action eq 'delete'}{tr}Wiki page %s deleted by {$mail_user|username}{/tr}
{elseif $mail_action eq 'attach'}{tr}A file was attached to %s{/tr}
{else}{tr}Wiki page %s changed by {$mail_user|username}{/tr}{/if}