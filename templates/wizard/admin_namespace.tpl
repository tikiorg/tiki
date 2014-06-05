{* $Id$ *}

<img class="pull-right" src="img/icons/large/icon-configuration48x48.png" alt="{tr}Namespace setup{/tr}" />
<div class="media">
    <img class="pull-left" src="img/icons/large/wizard_admin48x48.png" alt="{tr}Admin Wizard{/tr}" title="{tr}Admin Wizard{/tr}" />
    <div class="media-body">
        {tr}The namespace separator should not{/tr}
        <ul>
            <li>{tr}contain any of the characters not allowed in wiki page names, typically{/tr} /?#[]@$&amp;+;=&lt;&gt;</li>
            <li>{tr}conflict with wiki syntax tagging{/tr}</li>
        </ul>
    </div>
    <div class="adminWizardContent">
        <fieldset>
	        <legend>{tr}Namespace settings{/tr}{help url="Namespaces"}</legend>
	        {preference name=namespace_separator}
	        {if isset($isStructures) and $isStructures eq true}
		        {preference name=namespace_indicator_in_structure}
	        {/if}
	        <br/>
	        <b>{tr}Settings that may be affected by the namespace separator{/tr}:</b><br/>
	        <img src="img/icons/large/wikipages.png" class="adminWizardIconright" />

	        {tr}To use :: as a separator, you should also use ::: as the wiki center tag syntax{/tr}.<br/>
	        {tr}Note: a conversion of :: to ::: for existing pages must be done manually{/tr}
	        {preference name=feature_use_three_colon_centertag}

	        {preference name=wiki_pagename_strip}
	        <br>
	        <em>{tr}See also{/tr} <a href="http://doc.tiki.org/Namespaces" target="_blank">{tr}Namespaces{/tr} @ doc.tiki.org</a></em>
        </fieldset>
    </div>
</div>