{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-simple_plugin.tpl,v 1.6 2003-11-08 18:49:33 zaufi Exp $ *}

<div class="catlists">
    <div class="cbox-title">{$title}</div>
    <div class="cbox-data">
        {foreach key=t item=i from=$listcat}
            <b>{$t}:</b>
            {section name=o loop=$i}
                <a href="{$i[o].href}" class="link" title="{tr}Created{/tr} {$i[o].created|tiki_long_date}">
                    {$i[o].name}
                </a>
                {if $smarty.section.o.index ne $smarty.section.o.total - 1} &middot; {/if}
            {/section}<br />
        {/foreach}
    </div>
</div>
