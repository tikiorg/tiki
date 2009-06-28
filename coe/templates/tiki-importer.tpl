{title help="TikiWikiGenericImporter"}TikiWiki generic importer{/title}

<br />

{if $chooseSoftware}
    {remarksbox type="warning" title="{tr}Warning:{/tr}"}
        {tr}If you are NOT running a new Tiki installation, make a backup of your database before using this importer!{/tr}
    {/remarksbox}
        
    <br />
    
    Please choose the software to import from:
    <form method="post" name="chooseSoftware" action="tiki-importer.php">
        <select name="importerClassName">
            <option value=""></option>
            {foreach from=$availableSoftwares key=softwareClassName item=softwareName}
                <option value="{$softwareClassName}">{$softwareName}</option>
            {/foreach}
        </select>
        <input type="submit" value="{tr}Ok{/tr}"/>
    </form>
{else if $softwareSpecificOptions}
    <h4>Import options:</h4>
    <form method="post" enctype="multipart/form-data" action="tiki-importer.php">
        <input type="hidden" name="importerClassName" value="{$importerClassName}"/>
        {foreach from=$importerOptions item=option}
            {if $option.type eq 'checkbox'}
                <input type="checkbox" name="{$option.name}"/><label for="{$options.name}">{tr}{$option.label}{/tr}</label><br />
            {elseif $option.type eq 'text'}
                {tr}{$option.label}{/tr}: <input type="text" name="{$option.name}"/><br />
            {elseif $option.type eq 'select'}
		        {tr}{$option.label}{/tr}<br />
		        <select name="{$option.name}">
		        {foreach from=$option.options item=selectOption}
                    <option value="{$selectOption.name}">{$selectOption.label}</option>
		        {/foreach}
		        </select>
            {/if}
        {/foreach}
        <br /><br />
        <input type="file" name="importFile"/><br />
        <input type="submit" value="{tr}Import!{/tr}"/>
    </form>
{/if}