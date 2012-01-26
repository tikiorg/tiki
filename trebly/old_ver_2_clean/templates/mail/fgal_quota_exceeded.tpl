{tr}File gallery quota exceeded{/tr}
{if !empty($mail_fgal)}{tr}File gallery:{/tr} <a href="{$mail_machine_raw}/tiki-list_file_gallery.php?galleryId={$mail_fgal.galleryId}">{$mail_fgal.name|escape}</a>
{tr}Quota:{/tr} ($mail_fgal.quota} {tr}Mb{/tr}{/if}
{tr}User:{/tr} {$user|escape}
{tr}Size:{/tr} {$mail_diff|kbsize|replace:'&nbsp;':' '}
