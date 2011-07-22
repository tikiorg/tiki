{if $objecttype eq 'wiki'}
{tr}The Wiki page {$mail_objectname} was commented on by{/tr} {if $mail_user}{$mail_user|username}{else}{tr}an anonymous user{/tr}{/if}.
{elseif $objecttype eq 'article'}
{tr}The article {$mail_objectname} was commented on by{/tr} {if $mail_user}{$mail_user|username}{else}{tr}an anonymous user{/tr}{/if}.
{elseif $objecttype eq 'trackeritem'}
{tr}The tracker item {$mail_objectid} of tracker {$mail_objectname} was commented on by{/tr} {if $mail_user}{$mail_user|username}{else}{tr}an anonymous user{/tr}{/if}.
{/if}

{tr}You can view the comment by following this link:{/tr}
{if $objecttype eq 'wiki'}
{$mail_machine_raw}/tiki-index.php?page={$mail_objectname|escape:"url"}#comments
{elseif $objecttype eq 'article'}
{$mail_machine_raw}/tiki-read_article.php?articleId={$mail_objectid}#comments
{elseif $objecttype eq 'trackeritem'}
{$mail_machine_raw}/tiki-view_tracker_item.php?itemId={$mail_objectid}
{/if}

{tr}Title:{/tr} {$mail_title}
{tr}Comment:{/tr} {$mail_comment}
{tr}Date:{/tr} {$mail_date|tiki_short_datetime}

{if $watchId}
{tr}If you don't want to receive these notifications follow this link:{/tr}
{$mail_machine_raw}/tiki-user_watches.php?id={$watchId}
{/if}

