{* $Id$ *}

<h1>{tr}Set up Structures{/tr}</h1>
<div style="float:left; width:60px"><img src="img/icons/large/wikipages48x48.png" alt="{tr}Set up Structures{/tr}" /></div>
{tr}Structures organize a group of wiki pages into a tree or book that can be easily navigated by users. Creating a structure is the fastest method for creating multiple pages at once. It's a great way to make a book, as well as to allow users to create new pages that will be automatically found in a common table of contents. New pages can inherit permissions from the structure homepage{/tr}.
<div align="left" style="margin-top:1em;">
<fieldset>
	<legend>{tr}Structures options{/tr}</legend>
		<table>
		<tr>
		<td style="width:50%">
		{preference name=feature_wiki_open_as_structure}
		{tr}Opens the structure heading for structure pages, even if no "structure" parameter is given in the URL{/tr}.<br>
		<br>
		{preference name=feature_wiki_make_structure}
		<br>
		{if $isCategories eq true}
			{preference name=feature_wiki_categorize_structure}
			{tr}Categorize all new structure pages as the root page{/tr}.<br>
		{/if}
		</td>
		<td>
		{preference name=feature_wiki_multiprint}
		{tr}Print a structure as a book{/tr}.<br>
		<br>
		{preference name=feature_listorphanStructure}
		<br>
		{preference name=feature_wiki_no_inherit_perms_structure}
		{tr}Normally pages will inherit object permissions from their parent page. However, object permissions override category permissions{/tr}. 
		{tr}So, if you are relying on category permissions in structures, you may want to consider this setting{/tr}.<br>
		<br>
		</td>
		</tr>
		</table>
		{tr}See also{/tr} <a href="https://doc.tiki.org/Structures" target="_blank">{tr}Structures{/tr} @ doc.tiki.org</a>
</fieldset>
</div>
