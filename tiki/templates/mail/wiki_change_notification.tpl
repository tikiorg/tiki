{tr}The page {$mail_page} was changed by {$mail_user} at
{$mail_date|tiki_short_datetime}{/tr}


{tr}You can view the page by following this link:
    {$mail_machine}/tiki-index.php?page={$mail_page}{/tr}


{tr}You can edit the page by following this link:
    {$mail_machine}/tiki-editpage.php?page={$mail_page}{/tr}


{tr}You can view a diff back to the previous version by following
this link:
    {$mail_machine}/tiki-pagehistory.php?page={$mail_page}&diff2={$mail_last_version}{/tr}


{tr}Comment:{/tr} {$mail_comment}



{tr}The new page content is:{/tr}
{$mail_pagedata}
