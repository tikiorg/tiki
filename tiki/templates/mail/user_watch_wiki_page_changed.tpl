{if $new_page}{tr}The page {$mail_page} was created by {$mail_user} at {$mail_date|tiki_short_datetime}{/tr}
{else}{tr}The page {$mail_page} was changed by {$mail_user} at {$mail_date|tiki_short_datetime}{/tr}{/if}

{if $mail_comment}{tr}Comment:{/tr} {$mail_comment}

{/if}
{tr}You can view the page by following this link:{/tr}
{$mail_machine}/tiki-index.php?page={$mail_page|escape:"url"}

{if $mail_hash}{tr}If you don't want to receive these notifications follow this link:{/tr}
{$mail_machine_raw}/tiki-user_watches.php?hash={$mail_hash}

{/if}
{if $feature_wiki_email_diff_style == "unidiff" && !$new_page}
{tr}Differences between the 2 last versions:{/tr}
{section name=ix loop=$mail_pagedata}
{if $mail_pagedata[ix].type == "diffheader"}
{assign var="old" value=$mail_pagedata[ix].old}
{assign var="new" value=$mail_pagedata[ix].new}

@@ {tr}-Lines: {$old} changed to +Lines: {$new}{/tr} @@
{elseif $mail_pagedata[ix].type == "diffdeleted"}
{section name=iy loop=$mail_pagedata[ix].data}
- {$mail_pagedata[ix].data[iy]}
{/section}
{elseif $mail_pagedata[ix].type == "diffadded"}
{section name=iy loop=$mail_pagedata[ix].data}
+ {$mail_pagedata[ix].data[iy]}
{/section}
{elseif $mail_pagedata[ix].type == "diffbody"}
{section name=iy loop=$mail_pagedata[ix].data}
{$mail_pagedata[ix].data[iy]}
{/section}
{/if}
{/section}

{else}
{if !$new_page}{tr}You can view a diff back to the previous version by following this link:{/tr}
{$mail_machine}/tiki-pagehistory.php?page={$mail_page|escape:"url"}&diff2={$mail_last_version}

{/if}
{tr}The new page content follows below.{/tr}
***********************************************************
{$mail_pagedata}
{/if}
