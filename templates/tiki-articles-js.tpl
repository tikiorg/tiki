{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-articles-js.tpl,v 1.3 2006-08-29 20:19:12 sylvieg Exp $ *}
<script type="text/javascript">
        var articleTypes = new Array();
{foreach from=$types key=type item=properties}


        typeProp = new Array();

    {foreach from=$properties key=prop item=value}
        typeProp['{$prop|escape}'] = '{$value|escape}';
    {/foreach}

        articleTypes['{$type|escape}'] = typeProp;
{/foreach}
</script>
        