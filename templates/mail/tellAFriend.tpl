{* $Id$ *}{if !empty($comment)}
{$prefs.mail_template_custom_text}{$comment}
{else}
{tr}Look at this {$prefs.mail_template_custom_text}link:{/tr}
{/if}
{$url_for_friend|replace:' ':'+'}

{$name|username}
