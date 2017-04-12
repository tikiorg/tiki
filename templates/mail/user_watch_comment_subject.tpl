{* $Id$ *}{if $objecttype eq 'wiki'}
{tr}The {$prefs.mail_template_custom_text}Wiki page "{$mail_objectname}" was commented on by{/tr} {if $mail_user}{$mail_user|username}{else}{tr}an anonymous user{/tr}{/if}.
{* Blog comment mail *}
{elseif $objecttype eq 'blog'}
{tr}The {$prefs.mail_template_custom_text}Blog post "{$mail_objectname}" was commented on by{/tr} {if $mail_user}{$mail_user|username}{else}{tr}an anonymous user{/tr}{/if}.

{elseif $objecttype eq 'article'}
{tr}The {$prefs.mail_template_custom_text}article "{$mail_objectname}" was commented on by{/tr} {if $mail_user}{$mail_user|username}{else}{tr}an anonymous user{/tr}{/if}.
{elseif $objecttype eq 'trackeritem'}
{tr}The {$prefs.mail_template_custom_text}tracker item "{$mail_item_title}" of tracker "{$mail_objectname}" was commented on by{/tr} {if $mail_user}{$mail_user|username}{else}{tr}an anonymous user{/tr}{/if}.
{/if}
