{* $Id: user_watch_article_post.tpl 37282 2011-09-15 14:05:13Z sampaioprimo $ *}
{tr}{$mail_action} article post:{/tr} {tr}{$mail_title} by {$mail_user|username} at{/tr} {$mail_date|tiki_short_datetime}

{$mail_data}

--

{if $mail_action neq 'Delete'}{tr}View the article at:{/tr}
{$mail_machine_raw}/{$mail_postid|sefurl:article}
{/if}

{tr}If you don't want to receive these notifications follow this link:{/tr}
{$mail_machine_raw}/tiki-user_watches.php?id={$watchId}


