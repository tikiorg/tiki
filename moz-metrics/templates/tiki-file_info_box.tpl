<div class='opaque'>
<div class='box-title'>{tr}Information{/tr}</div>
<div class='box-data'>
{if $showname eq 'y' and $file_info.name neq ''}<i>{$file_info.name}</i><br /><br />{/if}
{if $showfilename eq 'y' and $file_info.filename neq ''}{tr}Filename{/tr}: {$file_info.filename}<br />{/if}
{if $showimageid eq 'y' and $file_info.imageId neq ''}{tr}ID{/tr}: {$file_info.imageId}<br />{/if}
{if $showdescription eq 'y' and $file_info.description neq ''}{$file_info.description}<br />{/if}
{if $showcreated eq 'y' and $file_info.created neq ''}{tr}Created{/tr}: {$file_info.created|tiki_short_date}<br />{/if}
{if $showuser eq 'y' and $file_info.user neq ''}{tr}User{/tr}: <a href="tiki-user_information.php?view_user={$file_info.user|escape}">{$file_info.user}</a><br />{/if}
{if $showxysize eq 'y' and $file_info.xsize neq ''}{tr}Size (width x height){/tr}: {$file_info.xsize}x{$file_info.ysize}<br />{/if}
{if $showfilesize eq 'y' and $file_info.filesize neq ''}{tr}Filesize{/tr}: {$file_info.filesize} {tr}bytes{/tr}<br />{/if}
{if $showhits eq 'y' and $file_info.hits neq ''}{tr}Hits{/tr}: {$file_info.hits}{/if}
</div>
</div>
