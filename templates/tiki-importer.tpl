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
        <input type="checkbox" name="attachments"/><label for="attachments">{tr}Import images and attachments{/tr}</label><br />
        {tr}Number of page revisions to import (0 for all revisions){/tr}: <input type="input" name="wikiRevisions" default="-1"/><br />
        {tr}What to do with page names that already exists in TikiWiki?{/tr}<br />
        <select name="alreadyExistentPageName">
            <option value="doNotImport">Do not import</option>
            <option value="override">Override</option>
            <option value="appendPrefix">Append software name as prefix to the page name</option>
        </select>
        <br /><br />
        <input type="file" name="importFile"/><br />
        <input type="submit" value="{tr}Import!{/tr}"/>
    </form>
{/if}