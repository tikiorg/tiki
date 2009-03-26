{* $Id$ *}
<script type="text/javascript">
<!--//--><![CDATA[//><!--
        var articleTypes = new Array();
{foreach from=$types key=type item=properties}


        typeProp = new Array();

    {foreach from=$properties key=prop item=value}
        typeProp['{$prop|escape}'] = '{$value|escape}';
    {/foreach}

        articleTypes['{$type|escape}'] = typeProp;
{/foreach}
//--><!]]>
</script>
        
