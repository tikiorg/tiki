{if $new_page}{tr}The page {$mail_page} was created by {$mail_user} at {$mail_date|tiki_short_datetime}{/tr}
{else}{tr}The page {$mail_page} was changed by {$mail_user} at {$mail_date|tiki_short_datetime}{/tr}{/if}

{if $mail_comment}{tr}Comment:{/tr} {$mail_comment}
{/if}
{if $mail_contributions}{tr}Contribution{/tr}: {$mail_contributions}{/if}

{tr}You can view the page by following this link:{/tr}
{$mail_machine_raw}/tiki-index.php?page={$mail_page|escape:"url"}

{if !$new_page}{tr}You can view a diff back to the previous version by following this link:{/tr} {* Using the full diff syntax so the links are still valid, even after a new version has been made.  -rlpowell *}
{$mail_machine_raw}/tiki-pagehistory.php?page={$mail_page|escape:"url"}&compare=1&oldver={$mail_oldver}&newver={$mail_newver}&diff_style=minsidediff
{/if}

{if $mail_hash}{tr}If you don't want to receive these notifications follow this link:{/tr}
{$mail_machine_raw}/tiki-user_watches.php?hash={$mail_hash}
{/if}

***********************************************************
{if $mail_diffdata}{tr}The changes in this version follow below, followed after by the current full page text.{/tr}
***********************************************************

{section name=ix loop=$mail_diffdata}
{if $mail_diffdata[ix].type == "diffheader"}
{assign var="oldd" value=$mail_diffdata[ix].old}
{assign var="newd" value=$mail_diffdata[ix].new}

+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+
@@ {tr}-Lines: {$oldd} changed to +Lines: {$newd}{/tr} @@
+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+
{elseif $mail_diffdata[ix].type == "diffdeleted"}
{section name=iy loop=$mail_diffdata[ix].data}
- {$mail_diffdata[ix].data[iy]|strip_tags:false|htmldecode}
{/section}
{elseif $mail_diffdata[ix].type == "diffadded"}
{section name=iy loop=$mail_diffdata[ix].data}
+ {$mail_diffdata[ix].data[iy]|strip_tags:false|htmldecode}
{/section}
{elseif $mail_diffdata[ix].type == "diffbody"}
{section name=iy loop=$mail_diffdata[ix].data}
{$mail_diffdata[ix].data[iy]|strip_tags:false|htmldecode}
{/section}
{/if}
{/section}

{* if $mail_diffdata *}
{/if}


***********************************************************
{tr}The new page content follows below.{/tr}
***********************************************************

{$mail_pagedata}
