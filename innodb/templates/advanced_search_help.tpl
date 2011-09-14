{* $Id$ *}
{strip}

<h3>{tr}Non Advanced Search or Natural Search{/tr}</h3>
<div class="help_section">
{tr}The documents are returned sorted on relevance depending on order, proximity, frequency of terms. {/tr}
</div>
<h3>{tr}Advanced Search or Boolean Search{/tr}</h3>
<div class="help_section">
	<h4>{tr}Default search behavior{/tr}</h4>
	<p>
		{tr}By default, all search terms are <em>optional</em>.{/tr}&nbsp;{tr}It behaves like an OR logic.{/tr}&nbsp;
		{tr}Objects that contain the more terms are rated higher in the results and will appear first in their type.{/tr} {tr}For example, <strong>wiki forum</strong> will find:{/tr}
	</p>
	<ul>
		<li>{tr}objects that include both terms{/tr}</li>
		<li>{tr}objects that include the term <strong>wiki</strong>{/tr}</li>
		<li>{tr}objects that include the term <strong>forum</strong>{/tr}</li>
	</ul>
	
	<h4>{tr}Requiring terms{/tr}</h4>
	<p>
		{tr}Add a plus sign ( + ) before a term to indicate that the term <em>must</em> appear in results.{/tr}&nbsp;
		{tr}Example: <strong>+wiki forum</strong> will find objects containing at least <strong>wiki</strong>. Objects with both terms and many occurences of the terms will appear first.{/tr}
	</p>
	
	<h4 id="excluding">{tr}Excluding terms{/tr}</h4>
	<p>{tr}Add a minus sign ( - ) before a term to indicate that the term <em>must not</em> appear in the results.{/tr}&nbsp;
	{tr}To reduce a term's value without completely excluding it, <a href="#reducing" title="{tr}Reducing a term's value{/tr}">use a tilde</a>.{/tr}&nbsp;
	{tr}Example: <strong>-wiki forum</strong> will find objects that do not contain <strong>wiki</strong> but contain <strong>forum</strong>{/tr}
	</p>
	
	<h4>{tr}Grouping terms{/tr}</h4>
	<p>{tr}Use parenthesis ( ) to group terms into subexpressions.{/tr}&nbsp;
	{tr}Example: <strong>+wiki +(forum blog)</strong> will find objects that contain <strong>wiki</strong> and <strong>forum</strong> or that contain <strong>wiki</strong> and <strong>blog</strong> in any order.{/tr}</p>
	
	
	<h4>{tr}Finding phrases{/tr}</h4>
	<p>{tr}Use double quotes ( " " ) around a phrase to find terms in the exact order, exactly as typed.{/tr}&nbsp;
	{tr}Example: <strong>"Alex Bell"</strong> will not find <strong>Bell Alex</strong> or <strong>Alex G. Bell</strong>. {/tr}</p>

	<h4>{tr}Using wildcards{/tr}</h4>
	<p>
		{tr}Add an asterisk ( * ) after a term to find objects that include the root word.{/tr} {tr}For example, <strong>run*</strong> will find:{/tr}
	</p>
	<ul>
		<li>{tr}objects that include the term <strong>run</strong>{/tr}</li>
		<li>{tr}objects that include the term <strong>runner</strong>{/tr}</li>
		<li>{tr}objects that include the term <strong>running</strong>{/tr}</li>		
	</ul>
	
	<h4 id="reducing">{tr}Reducing a term's value{/tr}</h4>
	<p>
		{tr}Add a tilde ( ~ ) before a term to reduce its value indicate to the ranking of the results.{/tr}&nbsp;
		{tr}Objects that contain the term will appear lower than other objects (unlike the <a href="#excluding" title="{tr}Excluding terms{/tr}">minus sign</a>	which will completely exclude a term).{/tr}&nbsp;
		{tr}Example: <strong>+wiki ~forum</strong> will rate an object with only <strong>wiki</strong> higher that an object with <strong>wiki</strong> and  <strong>forum</strong>.{/tr}
	</p>

	<h4>{tr}Changing relevance value{/tr}</h4>
	<p>
		{tr}Add a less than ( &lt; ) or greater than ( &gt; ) sign before a term to change the term's contribution to the overall relevance value assigned to a object.{/tr}&nbsp;
			{tr}Example: <strong>+wiki +(&gt;forum &lt; blog)</strong> will find objects that contain <strong>wiki</strong> and <strong>forum</strong> or <strong>wiki</strong> and <strong>blog</strong> in any order. <strong>wiki forum</strong> will be rated higher.{/tr}
	</p>
</div>
{/strip}
