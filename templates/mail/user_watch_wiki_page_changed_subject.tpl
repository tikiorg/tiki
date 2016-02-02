{* $Id$ *}
{if $mail_action eq 'new'}{tr}{$prefs.mail_template_custom_text}wiki page "%s" created by {$mail_user|username}{/tr}
{elseif $mail_action eq 'delete'}{tr}{$prefs.mail_template_custom_text}wiki page "%s" deleted by {$mail_user|username}{/tr}
{elseif $mail_action eq 'attach'}{tr}A file was attached to {$prefs.mail_template_custom_text}"%s"{/tr}
{else}{tr}{$prefs.mail_template_custom_text}wiki page "%s" changed by {$mail_user|username}{/tr}{/if}