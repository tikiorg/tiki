{if $mail_action eq 'new'}{tr}The page {$mail_page} was created by {$mail_user|username} at {$mail_date|tiki_short_datetime}{/tr}
{elseif $mail_action eq 'delete'}{tr}The page {$mail_page} was deleted by {$mail_user|username} at {$mail_date|tiki_short_datetime}{/tr}
{elseif $mail_action eq 'attach'}{tr}A file was attached to {$mail_page}{/tr}
{else}{tr}The page {$mail_page} was changed by {$mail_user|username} at {$mail_date|tiki_short_datetime}{/tr}
{/if}

{if $mail_comment}{tr}Comment:{/tr} {$mail_comment}
{/if}
{if $mail_contributions}{tr}Contribution:{/tr} {$mail_contributions}{/if}

{if $mail_action eq 'delete'}{tr}The page {$mail_page} was deleted but used to be here:{/tr}
{else}{tr}You can view the page by following this link:{/tr}
{/if} 
{$mail_machine_raw}/tiki-index.php?page={$mail_page|escape:"url"}

{if $mail_action eq 'edit'}{tr}You can view a diff back to the previous version by following this link:{/tr} {* Using the full diff syntax so the links are still valid, even after a new version has been made.  -rlpowell *}
{$mail_machine_raw}/tiki-pagehistory.php?page={$mail_page|escape:"url"}&compare=1&oldver={$mail_oldver}&newver={$mail_newver}
{elseif $mail_action eq 'attach'}{$mail_data} : {$mail_machine_raw}/tiki-download_wiki_attachment.php?attId={$mail_attId}
{/if}

{if $watchId}
	{tr}If you don't want to receive these notifications follow this link:{/tr}
	{$mail_machine_raw}/tiki-user_watches.php?id={$watchId}
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
{if $mail_action eq 'delete'}{tr}The old page content follows below.{/tr}
{else}{tr}The new page content follows below.{/tr}
{/if}
***********************************************************

{$mail_pagedata}
