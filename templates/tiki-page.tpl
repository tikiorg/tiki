{if $type eq 'd'}
  <iframe width='0' height='0' frameborder="0" src="tiki-page_loader.php?refresh={$refresh}&amp;pageName={$pageName}">Browser not supported</iframe>
{/if}
{$parsed}
{if $tiki_p_edit_html_pages eq 'y'}
<hr/><small><a class="link" href="tiki-admin_html_pages.php?pageName={$pageName}">edit</a>
{if $type eq 'd'}
<a class="link" href="tiki-admin_html_page_content.php?pageName={$pageName}">content</a>
{/if}
</small>
{/if}
