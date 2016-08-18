{* $Id$ *}

<div class="media">
<span class="pull-left fa-stack fa-lg margin-right-18em" alt="{tr}Configuration Wizard{/tr}" title="Configuration Wizard">
	<i class="fa fa-gear fa-stack-2x"></i>
	<i class="fa fa-rotate-270 fa-magic fa-stack-2x margin-left-9em"></i>
</span>
	{tr}Structures organize a group of wiki pages into a tree or book that can be easily navigated by users. Creating a structure is the fastest method for creating multiple pages at once. It's a great way to make a book, as well as to allow users to create new pages that will be automatically found in a common table of contents. New pages can inherit permissions from the structure homepage{/tr}.
    </br></br>
	<div class="media-body">
		{icon name="structure" size=3 iclass="pull-right"}
		<fieldset>
			<legend>{tr}Structures options{/tr}</legend>
			<div class="admin clearfix featurelist">
				{preference name=feature_wiki_open_as_structure}
				{preference name=feature_wiki_make_structure}
				{if $isCategories eq true}
					{preference name=feature_wiki_categorize_structure}
				{/if}
				{preference name=feature_wiki_multiprint}
				{preference name=feature_listorphanStructure}
				{preference name=feature_wiki_no_inherit_perms_structure}
				{preference name=feature_wiki_structure_drilldownmenu}
				{preference name=wiki_structure_bar_position}
			</div>
			<br>
			<em>{tr}See also{/tr} <a href="https://doc.tiki.org/Structures" target="_blank">{tr}Structures{/tr} @ doc.tiki.org</a></em>
		</fieldset>
	</div>
</div>
