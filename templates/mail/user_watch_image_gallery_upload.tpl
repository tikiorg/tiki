{* $Id$ *}
{tr}A new file was posted to image gallery:{/tr} {$galleryName}

{tr}Posted by:{/tr} {$author|username}
{tr}Date:{/tr} {$mail_date|tiki_short_datetime}
{tr}Name:{/tr} {$fname}
{tr}File Name:{/tr} {$filename}
{tr}File Description:{/tr} {$description}

You can see the new image at:
{$mail_machine_raw}/tiki-browse_image.php?galleryId={$galleryId}&imageId={$imageId}
