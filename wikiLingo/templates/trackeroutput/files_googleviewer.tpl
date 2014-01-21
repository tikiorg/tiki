{foreach from=$files item=file}
{object_link type="file" id="{$file.id|escape}"}
<iframe src="{$file.url|escape}" width="480" height="640" style="border: none;"></iframe>
{/foreach}
