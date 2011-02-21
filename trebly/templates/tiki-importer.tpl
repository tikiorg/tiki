{title help="Tiki+Importer"}Tiki Importer{/title}

<br />

{if isset($chooseSoftware)}
    {remarksbox type="warning" title="{tr}Warning:{/tr}"}
        {tr}If you are NOT running a new Tiki installation, make a backup of your database before using this importer!{/tr}
    {/remarksbox}
    {if $safe_mode ne ''}
        {remarksbox type="warning" title="{tr}Warning:{/tr}"}
            {tr}Your PHP is running with safe mode enabled. This might cause problems to the import process as safe mode limits the possibility to change in run time some PHP settings (like max_execution_time). It is recommended to run this script without safe mode.{/tr}
        {/remarksbox}
    {/if}
    {remarksbox type="note" title="{tr}Note:{/tr}"}
	{tr}Depending on the size of the file from the source software, the import process may take a while to complete. This might be a problem according to your PHP and web server settings. This script tries to change the relevant settings but there are some settings that the script cannot change. So, if you are having problems with the script, please try to increase the value of the following PHP settings: max_input_time, max_execution_time (this setting is limited by the web server setting, if you are running Apache also change its Timeout setting), post_max_size, upload_max_filesize, memory_limit. It is recommended that you run this script on a server where you can change the values of those settings (if needed).{/tr}
    {/remarksbox}
       
    <br />
    <label for="importerClassName">{tr}Select the software to import from:{/tr}</label>
    <form method="post" name="chooseSoftware" action="tiki-importer.php">
        <select name="importerClassName" id="importerClassName">
            <option value=""></option>
            {foreach from=$availableSoftwares key=softwareClassName item=softwareName}
                <option value="{$softwareClassName}">{$softwareName}</option>
            {/foreach}
        </select>
        <input type="submit" value="{tr}OK{/tr}"/>
    </form>
{elseif isset($softwareSpecificOptions)}
    <h2>Options:</h2>
    <form method="post" enctype="multipart/form-data" action="tiki-importer.php" onsubmit="return confirm('{tr}WARNING: make sure to have a backup before running the script. If you do not have a backup this is the last chance to cancel the importer by clicking on the cancel button.{/tr}');";>
        <input type="hidden" name="importerClassName" value="{$importerClassName}"/>
        {foreach from=$importerOptions item=option}
            {if $option.type eq 'checkbox'}
                <input type="checkbox" name="{$option.name}" id="{$option.name}"/><label for="{$option.name}">{tr}{$option.label}{/tr}</label><br />
            {elseif $option.type eq 'text'}
                <label>{tr}{$option.label}:{/tr} <input type="text" name="{$option.name}" {if isset($option.value)}value="{$option.value}"{/if}/></label><br />
            {elseif $option.type eq 'select'}
		        <label for="{$option.name}">{tr}{$option.label}{/tr}</label><br />
		        <select id="{$option.name}" name="{$option.name}">
		        {foreach from=$option.options item=selectOption}
                    <option value="{$selectOption.name}">{$selectOption.label}</option>
		        {/foreach}
		        </select>
            {/if}
        {/foreach}
        <br /><br />
        <input type="file" name="importFile"/><br />
        <input type="submit" value="{tr}Import{/tr}"/>
    </form>
{elseif !empty($importFeedback)}
    <h4>{tr}Congratulations! You have successful imported your data to Tiki.{/tr}</h4>
    
    {if isset($importFeedback.importedPages)}
    	<p>
	    	{if isset($importFeedback.totalPages)}
		    	{tr 0=$importFeedback.importedPages 1=$importFeedback.totalPages}%0 pages imported from a total of %1{/tr}
		    {else}
		    	{tr 0=$importFeedback.importedPages}%0 pages imported{/tr}
		    {/if}
		    &nbsp;{tr}(you can see the list of wiki pages in your site <a href="tiki-listpages.php">here</a>).{/tr}
		</p>
	{/if}

	{if isset($importFeedback.importedPosts)}
       	<p>{tr 0=$importFeedback.importedPosts}%0 posts imported.{/tr}</p>
	{/if}
	
	{if isset($importFeedback.importedTags)}
       	<p>{tr 0=$importFeedback.importedTags}%0 tags imported.{/tr}</p>
	{/if}
	
	{if isset($importFeedback.importedCategories)}
       	<p>{tr 0=$importFeedback.importedCategories}%0 categories imported.{/tr}</p>
	{/if}

    {if !empty($importErrors)}
        <br />
        <p><b>{tr}Errors:{/tr}</b></p>
        <textarea rows="15" cols="100">{$importErrors}</textarea> 
    {/if}
    <br /><br />
    <p><b>{tr}Importer log:{/tr}</b></p>
    <textarea rows="15" cols="100">{$importLog}</textarea>
{/if}
