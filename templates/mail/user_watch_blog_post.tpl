{tr}New blog post: {$mail_title} by {$mail_user} at {$mail_date|tiki_short_datetime}{/tr}

{tr}View the blog at:{/tr}
{$mail_machine_raw}/tiki-view_blog_post.php?blogId={$mail_blogid}&amp;postId={$mail_postid}

{tr}If you don't want to receive these notifications follow this link:{/tr}
{$mail_machine_raw}/tiki-user_watches.php?hash={$mail_hash}

