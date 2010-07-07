{if $new_topic}
{tr}A new message was posted to forum{/tr}: {$mail_forum}

{tr}New topic:{/tr} {$mail_topic}
{tr}Author{/tr}: {if $mail_author}"{$mail_author|username}"
{else}{tr}An anonymous user{/tr}{/if}
{tr}Title{/tr}: {$mail_title}
{tr}Date{/tr}: {$mail_date|tiki_short_datetime}
{$mail_machine}/tiki-view_forum_thread.php?forumId={$forumId}&comments_parentId={$topicId}{if $threadId}#threadId{$threadId}{/if}

{if $mail_contributions}{tr}Contribution{/tr}: {$mail_contributions}{/if}
{else}
{if $mail_author}"{$mail_author|username}"{else}{tr}An anonymous user{/tr}{/if} {tr}has posted a reply to a thread you're watching. 
You can view the thread and reply at the following URL:{/tr} 

{$mail_machine}/tiki-view_forum_thread.php?forumId={$forumId}&comments_parentId={$topicId}{if $threadId}#threadId{$threadId}{/if}
{/if}


{tr}Message{/tr}:
----------------------------------------------------------------------
{$mail_message}
