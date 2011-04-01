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
	
	<h4>{tr}Boolean operators{/tr}</h4>
	<p>{tr}You can use AND or OR or NOT also to do a boolean search.{/tr}&nbsp;
	{tr}Example: <strong>wiki and forum</strong> will find objects with both terms.{/tr}&nbsp;
	{tr}Example: <strong>wiki or forum</strong> will find objects with one of the term.{/tr}</p>

	<h4>{tr}Grouping terms{/tr}</h4>
	<p>{tr}Use parenthesis ( ) to group terms into subexpressions.{/tr}&nbsp;
	{tr}Example: <strong>+wiki +(forum blog)</strong> will find objects that contain <strong>wiki</strong> and <strong>forum</strong> or that contain <strong>wiki</strong> and <strong>blog</strong> in any order.{/tr}</p>
		
	<h4>{tr}Finding phrases{/tr}</h4>
	<p>{tr}Use double quotes ( " " ) around a phrase to find terms in the exact order, next to each other.{/tr}&nbsp;
	{tr}Example: <strong>"Alex Bell"</strong> will not find <strong>Bell Alex</strong> or <strong>Alex G. Bell</strong> but <strong>Alex Bells</strong>. {/tr}</p>

</div>
{/strip}
