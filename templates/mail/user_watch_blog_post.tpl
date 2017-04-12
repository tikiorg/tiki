{* $Id$ *}{tr}New {$prefs.mail_template_custom_text}blog post: {$mail_title}, "{$mail_post_title}", by {$mail_user|username} at {$mail_date|tiki_short_datetime:"":"n"}{/tr}
{if $mail_contributions}

{tr}Contribution:{/tr} {$mail_contributions}{/if}

{tr}View the blog at:{/tr}
{$mail_machine_raw}/{$mail_postid|sefurl:blogpost}

{tr}If you don't want to receive these notifications follow this link:{/tr}
{$mail_machine_raw}/tiki-user_watches.php?id={$watchId}
