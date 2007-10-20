{if $type eq 'd'}
  <iframe width='0' height='0' frameborder="0" src="tiki-page_loader.php?refresh={$refresh}&amp;pageName={$pageName|escape:"url"}">{tr}Browser not supported{/tr}</iframe>
{/if}
{$parsed}
{if $tiki_p_edit_html_pages eq 'y'}
<span class="button2"><a class="linkbut" href="tiki-admin_html_pages.php?pageName={$pageName|escape:"url"}">{tr}Edit{/tr}</a></span>
{if $type eq 'd'}
<span class="button2"><a class="linkbut" href="tiki-admin_html_page_content.php?pageName={$pageName|escape:"url"}">{tr}content{/tr}</a></span>
{/if}
{/if}
