{strip}

<div class="help_section">
{tr}The documents are returned sorted on relevance depending on order, proximity, frequency of terms. {/tr}
</div>
<div class="help_section">
	<h4>{tr}Default search behavior{/tr}</h4>
	<p>
		{tr}By default, all search terms are <em>optional</em>.{/tr}&nbsp;{tr}It behaves like an OR logic.{/tr}&nbsp;
		{tr}Objects that contain the more terms are rated higher in the results and will appear first.{/tr} {tr}For example, <strong>wiki forum</strong> will find:{/tr}
	</p>
	<ul>
		<li>{tr}objects that include both tokenized terms{/tr}</li>
		<li>{tr}objects that include the term <strong>wiki</strong>{/tr}</li>
		<li>{tr}objects that include the term <strong>forum</strong>{/tr} or <strong>forums</strong></li>
	</ul>
	
	<h4>{tr}Requiring terms{/tr}</h4>
	<p>
		{tr}Add a plus sign ( + ) before a term to indicate that the term <em>must</em> appear in results.{/tr}&nbsp;
		{tr}Example: <strong>+ wiki forum</strong> will find objects containing at least <strong>wiki</strong> or <strong>wikis</strong>. Objects with both terms and many occurences of the terms will appear first.{/tr}
	</p>
	
	<h4 id="excluding">{tr}Excluding terms{/tr}</h4>
	<p>{tr}Add a minus sign ( - ) before a term to indicate that the term <em>must not</em> appear in the results.{/tr}&nbsp;
	{tr}Example: <strong>-wiki forum</strong> will find objects that do not contain <strong>wiki</strong> but contain <strong>forum</strong>{/tr}
	</p>
	
	<h4>{tr}Grouping terms{/tr}</h4>
	<p>{tr}Use parenthesis ( ) to group terms into subexpressions.{/tr}&nbsp;
	{tr}Example: <strong>+wiki +(forum blog)</strong> will find objects that contain <strong>wiki</strong> and <strong>forum</strong> or that contain <strong>wiki</strong> and <strong>blog</strong> in any order.{/tr}</p>
	
	<h4>{tr}Boolean operators{/tr}</h4>
	<p>{tr}You can use AND or OR or NOT also to dp a bollena search.{/tr}&nbsp;
	{tr}Example: <strong>wiki and forum</strong> will find objects with both terms.{/tr}</p>
	
	<h4>{tr}Finding phrases{/tr}</h4>
	<p>{tr}Use double quotes ( " " ) around a phrase to find terms in the exact order, next to each other.{/tr}&nbsp;
	{tr}Example: <strong>"Alex Bell"</strong> will not find <strong>Bell Alex</strong> or <strong>Alex G. Bell</strong> but <strong>Alex Bells</strong>. {/tr}</p>

	<h4>{tr}Using wildcards{/tr}</h4>
	<p>
		{tr}Add an asterisk ( * ) after a term to find objects that include the root word.{/tr} {tr}For example, <strong>run*</strong> will find:{/tr}
	</p>
	<ul>
		<li>{tr}objects that include the term <strong>run</strong>{/tr}</li>
		<li>{tr}objects that include the term <strong>runner</strong>{/tr}</li>
		<li>{tr}objects that include the term <strong>running</strong>{/tr}</li>		
	</ul>
	
</div>
{/strip}
