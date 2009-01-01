{tr}A new file have been attached to page{/tr} {$mail_page} {tr}by{/tr} {$mail_user}.
{tr}Date{/tr}: {$mail_date|tiki_short_datetime}

{tr}File name{/tr}: {$mail_att_name}
{tr}Type{/tr}: {$mail_att_type}
{tr}Size{/tr}: {$mail_att_size}
{tr}Comment{/tr}: {$mail_att_comment}

{$mail_machine_raw}/tiki-index.php?page={$mail_page|escape:'url'}

{tr}If you don't want to receive these notifications follow this link:{/tr}
{$mail_machine_raw}/tiki-user_watches.php?hash={$mail_hash}
