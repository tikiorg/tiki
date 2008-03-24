{* $Header: /cvsroot/tikiwiki/tiki/templates/mail/user_watch_article_post.tpl,v 1.3 2007-04-20 14:38:08 sylvieg Exp $ *}
{tr}New article post: {$mail_title} by {$mail_user} at {$mail_date|tiki_short_datetime}{/tr}

{tr}View the article at:{/tr}
{$mail_machine_raw}/tiki-read_article.php?articleId={$mail_postid}
{if $mail_user ne 'admin'}

{tr}If you don't want to receive these notifications follow this link:{/tr}
{$mail_machine_raw}/tiki-user_watches.php?hash={$mail_hash}
{/if}
{$mail_data}

