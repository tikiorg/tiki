{* $Header: /cvsroot/tikiwiki/_mods/templates/sdl_collection/templates/tiki-simple_plugin.tpl,v 1.1 2004-05-09 23:09:15 damosoft Exp $ *}

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
