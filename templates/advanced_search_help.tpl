<p>
<div><strong>{tr}Default search behavior{/tr}</strong></div>
<div>{tr}By default, all search terms are <em>optional</em>.{/tr} {tr}Objects that contain the terms are rated higher in the results.{/tr} {tr}For example, <strong>wiki forum</strong> will find:{/tr}
	<ul>
		<li>{tr}objects that include the term <strong>wiki</strong>{/tr}</li>
		<li>{tr}objects that include the term <strong>forum</strong>{/tr}</li>
		<li>{tr}objects that include both terms{/tr}</li>
	</ul>
</div>
</p>
<p>
<div><strong>{tr}Requiring terms{/tr}</strong></div>
<div>{tr}Add a plus sign ( + ) before a term to indicate that the term <em>must</em> appear in results.{/tr}
</div>
<p>
<div id="excluding"><strong>{tr}Excluding terms{/tr}</strong></div>
<div>{tr}Add a minus sign ( - ) before a term to indicate that the term <em>must not</em> appear results.{/tr} {tr}To reduce a term's value without completely excluding it, <a href="#reducing" title="{tr}Reducing a term's value{/tr}">use a tilde</a>.{/tr}</div>
</p>
<p>
<div><strong>{tr}Grouping terms{/tr}</strong></div>
<div>{tr}Use parenthesis ( ) to group terms into subexpressions.{/tr}</div>
</p>
<p>
<div><strong>{tr}Finding phrases{/tr}</strong></div>
<div>{tr}Use double quotes ( " ) around a phrase to find terms in the exact order, exactly as typed.{/tr}</div>
</p>
<p>
<div><strong>{tr}Using wildcards{/tr}</strong></div>
<div>{tr}Add an asterisk ( * ) after a term to find objects that include the root word.{/tr} {tr}For example, <strong>run*</strong> will find:{/tr}
	<ul>
		<li>{tr}objects that include the term <strong>run</strong>{/tr}</li>
		<li>{tr}objects that include the term <strong>runner</strong>{/tr}</li>
		<li>{tr}objects that include the term <strong>running</strong>{/tr}</li>		
	</ul>
</div>
</p>
<p>
<div id="reducing"><strong>{tr}Reducing a term's value{/tr}</strong></div>
<div>{tr}Add a tilde ( ~ ) before a term to reduce its value indicate to the ranking of the results.{/tr} {tr}Objects that contain the term will appear lower than other objects (unlike the <a href="#excluding" title={tr}Excluding terms{/tr}">minus sign</a> which will completely exclude a term).{/tr}</div>
</p>
<p>
<div><strong>{tr}Changing relevance value{/tr}</strong></div>
<div>{tr}Add a less than ( &lt; ) or greater than ( &gt; ) sign before a term to change the term's contribution to the overall relevance value assigned to a row.{/tr}</div>
</p>
