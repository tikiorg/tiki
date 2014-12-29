{* $Id$ *}
{tr}A new source category was detected by the RSS article creator at{/tr} {$mail_date|tiki_short_datetime:"":"n"}

{tr}Feed:{/tr} {$title|escape}

{tr}New categories:{/tr}
{foreach $newcats as $cat}
{$cat|escape}
{/foreach}

{tr}Configure settings for the new category at:{/tr} {$mail_machine_raw}/tiki-admin_rssmodules.php?article={$rssId|escape}

