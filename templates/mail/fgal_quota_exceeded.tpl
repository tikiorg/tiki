{* $Id$ *}{tr}{$prefs.mail_template_custom_text}File Gallery quota exceeded{/tr}
{if !empty($mail_fgal)}{tr}File gallery:{/tr} <a href="{$mail_machine}/{$mail_fgal.galleryId|sefurl:'file gallery'}">{$mail_fgal.name|escape}</a>
{tr}Quota:{/tr} {$mail_fgal.quota} {tr}Mb{/tr}
{/if}
{tr}User:{/tr} {$user|escape}
{tr}Size:{/tr} {$mail_diff|kbsize|replace:'&nbsp;':' '}
