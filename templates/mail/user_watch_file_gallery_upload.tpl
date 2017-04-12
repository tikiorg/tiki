{* $Id$ *}{tr}A new file was posted to {$prefs.mail_template_custom_text}file gallery:{/tr} {$galleryName}

{tr}Posted by:{/tr} {$author|username}
{tr}Date:{/tr} {$mail_date|tiki_short_datetime:"":"n"}
{tr}Name:{/tr} {$fname}
{tr}File Name:{/tr} {$filename}
{tr}File Description:{/tr} {$fdescription}

{if $mail_machine}
{tr}You can download the new file at:{/tr}
{$mail_machine}/{$galleryId|sefurl:'file gallery'}
{else}
{tr _0=$fileId _1=$galleryId}The new file ID is %0 and it is in gallery ID %1{/tr}
{/if}
