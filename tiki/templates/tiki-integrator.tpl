{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-integrator.tpl,v 1.1 2003-10-13 17:17:22 zaufi Exp $ *}

{if strlen($css_file) > 0}
<head><link rel="StyleSheet"  href="{$css_file}" type="text/css" /></head>
{/if}
<div class="integrated-page">
  {$data}
</div>
