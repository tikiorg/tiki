{* $Id$ *}
<div class="media">
    <span class="pull-left fa-stack fa-lg margin-right-18em" alt="{tr}Configuration Wizard{/tr}" title="Configuration Wizard">
	    <i class="fa fa-gear fa-stack-2x"></i>
	    <i class="fa fa-rotate-270 fa-magic fa-stack-2x margin-left-9em"></i>
    </span>
    {icon name="wrench" size=3 iclass="pull-right"}
    <div class="row">
        <div class="col-lg-9">
            {tr}The namespace separator should not{/tr}
            <ul>
                <li>{tr}contain any of the characters not allowed in wiki page names, typically{/tr} /?#[]@$&amp;+;=&lt;&gt;</li>
                <li>{tr}conflict with wiki syntax tagging{/tr}</li>
            </ul>
        </div>
    </div>
    <div class="media-body">
		<fieldset>
			<legend>{tr}Namespace settings{/tr}{help url="Namespaces"}</legend>
			{preference name=namespace_separator}
			{if isset($isStructures) and $isStructures eq true}
				{preference name=namespace_indicator_in_structure}
			{/if}
			<br/>
			<b>{tr}Settings that may be affected by the namespace separator{/tr}:</b><br/>
            {icon name="file-text-o" size=2 iclass="pull-right"}

			{tr}To use :: as a separator, you should also use ::: as the wiki center tag syntax{/tr}.<br/>
			{tr}Note: a conversion of :: to ::: for existing pages must be done manually{/tr}
			{preference name=feature_use_three_colon_centertag}

			{preference name=wiki_pagename_strip}
			<br>
			<em>{tr}See also{/tr} <a href="http://doc.tiki.org/Namespaces" target="_blank">{tr}Namespaces{/tr} @ doc.tiki.org</a></em>
		</fieldset>
	</div>
</div>
