<div class="wiki-edithelp" width="100%">
<p>
<a class="wiki">{tr}TextFormattingRules{/tr}</a><br />
<strong>{tr}Emphasis{/tr}:</strong> '<strong></strong>' {tr}for{/tr} <em>{tr}italics{/tr}</em>, _<em></em>_ {tr}for{/tr} <strong>{tr}bold{/tr}</strong>, '<strong></strong>'_<em></em>_ {tr}for{/tr} <em><strong>{tr}both{/tr}</strong></em><br />
<strong>{tr}Lists{/tr}:</strong> * {tr}for bullet lists{/tr}, # {tr}for numbered lists{/tr}, ;{tr}term{/tr}:{tr}definition{/tr} {tr}for definiton lists{/tr}<br/> 
<strong>{tr}Wiki References{/tr}:</strong> {tr}JoinCapitalizedWords or use{/tr} (({tr}page{/tr})) {tr}or{/tr} (({tr}page|desc{/tr})) {tr}for wiki references{/tr} )){tr}SomeName{/tr}(( {tr}prevents referencing{/tr}<br/>
{if $feature_drawings eq 'y'}
<strong>{tr}Drawings{/tr}:</strong> "{literal}{{/literal}draw name=foo} {tr}creates the editable drawing foo{/tr}<br/>
{/if}
<strong>{tr}External links{/tr}:</strong> {tr}use square brackets for an{/tr} {tr}external link{/tr}: [URL] {tr}or{/tr} [URL|{tr}link_description{/tr}] {tr}or{/tr} [URL|{tr}description{/tr}|nocache].<br />
<strong>{tr}Misc{/tr}:</strong> "!", "!!", "!!!" {tr}make_headings{/tr}, "-<em></em>-<em></em>-<em></em>-" {tr}makes a horizontal rule{/tr} "==={tr}text{/tr}===" {tr}underlines text{/tr}<br />
<strong>{tr}Title bar{/tr}:</strong> "-={tr}title{/tr}=-" {tr}creates a title bar{/tr}.<br/>
<strong>{tr}Images{/tr}:</strong> "{literal}{{/literal}img src=http://example.com/foo.jpg width=200 height=100 align=center link=http://www.yahoo.com desc=foo}" {tr}displays an image{/tr} {tr}height width desc link and align are optional{/tr}<br/> 
<strong>{tr}Non cacheable images{/tr}:</strong> "{literal}{{/literal}img src=http://example.com/foo.jpg?nocache=1 width=200 height=100 align=center link=http://www.yahoo.com desc=foo}" {tr}displays an image{/tr} {tr}height width desc link and align are optional{/tr}<br/> 
<strong>{tr}Tables{/tr}:</strong> "||row1-col1|row1-col2|row1-col3||row2-col1|row2-col2col3||" {tr}creates a table{/tr}<br/>
<strong>{tr}RSS feeds{/tr}:</strong> "{literal}{{/literal}rss id=n max=m{literal}}{/literal}" {tr}displays rss feed with id=n maximum=m items{/tr}<br/>
<strong>{tr}Simple box{/tr}:</strong> "^{tr}Box content{/tr}^" {tr}Creates a box with the data{/tr}<br/>
<strong>{tr}Dynamic content{/tr}:</strong> "{literal}{{/literal}content id=n}" {tr}Will be replaced by the actual value of the dynamic content block with id=n{/tr}<br/>
<strong>{tr}Colored text{/tr}:</strong> "~~#FFEE33:{tr}some text{/tr}~~" {tr}Will display using the indicated HTML color{/tr}<br/>
<strong>{tr}Center{/tr}:</strong> "::{tr}some text{/tr}::" {tr}Will display the text centered{/tr}<br/>
</p>
</div>
