{tr}A new message was posted to forum{/tr}: {$mail_forum}

{if $new_topic}{tr}New topic:{/tr}{else}{tr}Topic:{/tr}{/if} {$mail_topic}
{tr}Author{/tr}: {$mail_author}
{tr}Title{/tr}: {$mail_title}
{tr}Date{/tr}: {$mail_date|tiki_short_datetime}

{tr}Message{/tr}:

{$mail_message}
