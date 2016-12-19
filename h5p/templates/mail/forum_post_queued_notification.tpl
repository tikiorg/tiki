{* $Id$ *}{if $threadId}{tr}Note that this is an edited post{/tr}

{/if}
{if $new_topic}
{tr}A new message was posted to {$prefs.mail_template_custom_text}forum:{/tr} {$mail_forum}

{tr}New topic:{/tr} {$mail_topic}
{tr}Author:{/tr} {if $mail_author}"{$mail_author|username}"
{else}{tr}An anonymous {$prefs.mail_template_custom_text}user{/tr}{/if}
{tr}Title:{/tr} {$mail_title}
{tr}Date:{/tr} {$mail_date|tiki_short_datetime:"":"n"}
{$mail_machine}/{$topicId|sefurl:"forum post"}{if $threadId}#threadId={$threadId}{/if}

{if $mail_contributions}{tr}Contribution:{/tr} {$mail_contributions}{/if}
{else}
{tr}Topic:{/tr} {$mail_topic|escape}

{if $mail_author}"{$mail_author|username}"{else}{tr}An anonymous user{/tr}{/if} {tr}has posted a reply to a thread that requires moderation.
You can approve or reject the post at the following URL:{/tr}

{$mail_machine}/tiki-forum_queue.php?forumId={$forumId}

{if !empty($approvalhash)}{tr}To instantly approve this post without logging in, use the following URL:{/tr}

{$mail_machine}/tiki-forum_queue.php?forumId={$forumId}&qId={$queueId}&ahash={$approvalhash}{/if}
{/if}


{tr}Message:{/tr}
----------------------------------------------------------------------
{$mail_message}
