{* $Id$ *}

<h1>{tr}Namespace setup{/tr}</h1>

<div style="float:left; width:60px"><img src="img/icons/large/icon-configuration48x48.png" alt="{tr}Namespace setup{/tr}"></div>
<div align="left" style="margin-top:1em;">
{tr}The namespace separator should not{/tr}
<ul>
<li>{tr}contain any of the characters not allowed in wiki page names, typically{/tr} /?#[]@$&+;=&lt;&gt;</li>
<li>{tr}conflict with wiki syntax tagging{/tr}</li>
</ul>
{preference name=namespace_separator}
{preference name=namespace_indicator_in_structure}
<fieldset>
	<legend>{tr}Settings that may be affected by the namespace separator{/tr}{help url="Watch"}</legend>

	{tr}To use :: as a separator, you should also use ::: as the wiki center tag syntax{/tr}.<br/>
	{tr}Note: a conversion of :: to ::: for existing pages must be done manually{/tr}
	{preference name=feature_use_three_colon_centertag}

	{tr}If the page name display stripper conflicts with the namespace separator, the namespace is used and the page name display is not stripped.{/tr}
	{preference name=wiki_pagename_strip}
</fieldset>
</div>
