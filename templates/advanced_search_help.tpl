<ul>
	<li>+ : {tr}A leading plus sign indicates that this word must be present in every object returned.{/tr}</li>
	<li>- : {tr}A leading minus sign indicates that this word must not be present in any row returned.{/tr}</li>
    <li>{tr}By default (when neither plus nor minus is specified) the word is optional, but the object that contain it will be rated higher.{/tr}</li>
	<li>&lt; &gt; : {tr}These two operators are used to change a word's contribution to the relevance value that is assigned to a row.{/tr}</li>
	<li>( ) : {tr}Parentheses are used to group words into subexpressions.{/tr}</li>
	<li>~ : {tr}A leading tilde acts as a negation operator, causing the word's contribution to the object relevance to be negative. It's useful for marking noise words. An object that contains such a word will be rated lower than others, but will not be excluded altogether, as it would be with the - operator.{/tr}</li>
	<li>* : {tr}An asterisk is the truncation operator. Unlike the other operators, it should be appended to the word, not prepended.{/tr}</li>
	<li>&quot; : {tr}The phrase, that is enclosed in double quotes &quot;, matches only objects that contain this phrase literally, as it was typed.{/tr}</li>
</ul>
