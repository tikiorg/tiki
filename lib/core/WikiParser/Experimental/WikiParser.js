/* Jison generated parser */
var WikiParser = (function(){
var parser = {trace: function trace() { },
yy: {},
symbols_: {"error":2,"wiki":3,"wiki_contents":4,"EOF":5,"contents":6,"plugin":7,"INLINE_PLUGIN":8,"PLUGIN_START":9,"PLUGIN_END":10,"content":11,"np_content":12,"CONTENT":13,"HTML":14,"LINK":15,"HORIZONTAL_BAR":16,"SMILE":17,"BOLD_START":18,"BOLD_END":19,"BOX_START":20,"BOX_END":21,"CENTER_START":22,"CENTER_END":23,"COLORTEXT_START":24,"COLORTEXT_END":25,"ITALIC_START":26,"ITALIC_END":27,"LINK_START":28,"LINK_END":29,"STRIKETHROUGH_START":30,"STRIKETHROUGH_END":31,"TABLE_START":32,"TABLE_END":33,"TITLEBAR_START":34,"TITLEBAR_END":35,"UNDERSCORE_START":36,"UNDERSCORE_END":37,"WIKILINK_START":38,"WIKILINK_END":39,"NP_CONTENT":40,"$accept":0,"$end":1},
terminals_: {2:"error",5:"EOF",8:"INLINE_PLUGIN",9:"PLUGIN_START",10:"PLUGIN_END",13:"CONTENT",14:"HTML",15:"LINK",16:"HORIZONTAL_BAR",17:"SMILE",18:"BOLD_START",19:"BOLD_END",20:"BOX_START",21:"BOX_END",22:"CENTER_START",23:"CENTER_END",24:"COLORTEXT_START",25:"COLORTEXT_END",26:"ITALIC_START",27:"ITALIC_END",28:"LINK_START",29:"LINK_END",30:"STRIKETHROUGH_START",31:"STRIKETHROUGH_END",32:"TABLE_START",33:"TABLE_END",34:"TITLEBAR_START",35:"TITLEBAR_END",36:"UNDERSCORE_START",37:"UNDERSCORE_END",38:"WIKILINK_START",39:"WIKILINK_END",40:"NP_CONTENT"},
productions_: [0,[3,2],[4,0],[4,1],[4,2],[4,3],[7,1],[7,3],[6,1],[6,1],[6,2],[6,2],[11,1],[11,1],[11,1],[11,1],[11,1],[11,3],[11,3],[11,3],[11,3],[11,3],[11,3],[11,3],[11,3],[11,3],[11,3],[11,3],[12,1]],
performAction: function anonymous(yytext,yyleng,yylineno,yy,yystate,$$,_$) {

var $0 = $$.length - 1;
switch (yystate) {
case 1:return $$[$0-1];
break;
case 3:this.$ = $$[$0];
break;
case 4:this.$ = ($$[$0-1] ? $$[$0-1] : '') + ($$[$0] ? $$[$0] : '');
break;
case 5:this.$ = ($$[$0-2] ? $$[$0-2] : '') + ($$[$0-1] ? $$[$0-1] : '') + ($$[$0] ? $$[$0] : '');
break;
case 6:this.$ = plugin($$[$0]);
break;
case 7:
		$$[$0].body = $$[$0-1];
		this.$ = plugin($$[$0]);
	
break;
case 8:this.$ = $$[$0];
break;
case 9:this.$ = $$[$0];
break;
case 10:this.$ = $$[$0-1] + $$[$0];
break;
case 11:this.$ = $$[$0-1] + $$[$0];
break;
case 12:this.$ = $$[$0];
break;
case 13:this.$ = isHtmlPermissible($$[$0]);
break;
case 14:this.$ = $$[$0];
break;
case 15:this.$ = $$[$0];
break;
case 16:this.$ = $$[$0];
break;
case 17:this.$ = "<b>" + $$[$0-1] + "</b>";
break;
case 18:this.$ = "<div style='border: solid 1px black;'>" + $$[$0-1] + "</div>";
break;
case 19:this.$ = "<center>" + $$[$0-1] + "</center>";
break;
case 20:
		var text = $$[$0-1].split(':');
		this.$ = "<span style='color: #" + text[0] + ";'>" +text[1] + "</span>";
	
break;
case 21:this.$ = "<i>" + $$[$0-1] + "</i>";
break;
case 22:
		var link = $$[$0-1].split('|');
		var href = $$[$0-1];
		var text = $$[$0-1];
		
		if ($$[$0-1].match(/\|/)) {
			href = link[0];
			text = link[1];
		}
		
		this.$ = "<a href='" + href + "'>" + text  + "</a>";
	
break;
case 23:this.$ = "<span style='text-decoration: line-through;'>" + $$[$0-1] + "</span>";
break;
case 24:
		var tableContents = '';
		var rows = $$[$0-1].split('<br />');
		for(var i = 0; i < rows.length; i++) {
			var cells = rows[i].split('|');
			tableContents += "<tr>";
			for(var j = 0; j < cells.length; j++) {
				tableContents += "<td>" + cells[j] + "</td>";
			}
			tableContents += "</tr>";
		}
		this.$ = "<table style='width: 100%;'>" + tableContents + "</table>";
	
break;
case 25:this.$ = "<div class='titlebar'>" + $$[$0-1] + "</div>";
break;
case 26:this.$ = "<u>" + $$[$0-1] + "</u>";
break;
case 27:
		var wikilink = $$[$0-1].split('|');
		var href = $$[$0-1];
		var text = $$[$0-1];
		
		if ($$[$0-1].match(/\|/)) {
			href = wikilink[0];
			text = wikilink[1];
		}
		
		this.$ = "<a href='" + href + "'>" + text  + "</a>";
	
break;
case 28:this.$ = $$[$0];
break;
}
},
table: [{3:1,4:2,5:[2,2],6:3,8:[2,2],9:[2,2],11:4,12:5,13:[1,6],14:[1,7],15:[1,8],16:[1,9],17:[1,10],18:[1,11],20:[1,12],22:[1,13],24:[1,14],26:[1,15],28:[1,16],30:[1,17],32:[1,18],34:[1,19],36:[1,20],38:[1,21],40:[1,22]},{1:[3]},{5:[1,23],7:24,8:[1,25],9:[1,26]},{5:[2,3],8:[2,3],9:[2,3],10:[2,3],11:27,12:28,13:[1,6],14:[1,7],15:[1,8],16:[1,9],17:[1,10],18:[1,11],19:[2,3],20:[1,12],21:[2,3],22:[1,13],23:[2,3],24:[1,14],25:[2,3],26:[1,15],27:[2,3],28:[1,16],29:[2,3],30:[1,17],31:[2,3],32:[1,18],33:[2,3],34:[1,19],35:[2,3],36:[1,20],37:[2,3],38:[1,21],39:[2,3],40:[1,22]},{5:[2,8],8:[2,8],9:[2,8],10:[2,8],13:[2,8],14:[2,8],15:[2,8],16:[2,8],17:[2,8],18:[2,8],19:[2,8],20:[2,8],21:[2,8],22:[2,8],23:[2,8],24:[2,8],25:[2,8],26:[2,8],27:[2,8],28:[2,8],29:[2,8],30:[2,8],31:[2,8],32:[2,8],33:[2,8],34:[2,8],35:[2,8],36:[2,8],37:[2,8],38:[2,8],39:[2,8],40:[2,8]},{5:[2,9],8:[2,9],9:[2,9],10:[2,9],13:[2,9],14:[2,9],15:[2,9],16:[2,9],17:[2,9],18:[2,9],19:[2,9],20:[2,9],21:[2,9],22:[2,9],23:[2,9],24:[2,9],25:[2,9],26:[2,9],27:[2,9],28:[2,9],29:[2,9],30:[2,9],31:[2,9],32:[2,9],33:[2,9],34:[2,9],35:[2,9],36:[2,9],37:[2,9],38:[2,9],39:[2,9],40:[2,9]},{5:[2,12],8:[2,12],9:[2,12],10:[2,12],13:[2,12],14:[2,12],15:[2,12],16:[2,12],17:[2,12],18:[2,12],19:[2,12],20:[2,12],21:[2,12],22:[2,12],23:[2,12],24:[2,12],25:[2,12],26:[2,12],27:[2,12],28:[2,12],29:[2,12],30:[2,12],31:[2,12],32:[2,12],33:[2,12],34:[2,12],35:[2,12],36:[2,12],37:[2,12],38:[2,12],39:[2,12],40:[2,12]},{5:[2,13],8:[2,13],9:[2,13],10:[2,13],13:[2,13],14:[2,13],15:[2,13],16:[2,13],17:[2,13],18:[2,13],19:[2,13],20:[2,13],21:[2,13],22:[2,13],23:[2,13],24:[2,13],25:[2,13],26:[2,13],27:[2,13],28:[2,13],29:[2,13],30:[2,13],31:[2,13],32:[2,13],33:[2,13],34:[2,13],35:[2,13],36:[2,13],37:[2,13],38:[2,13],39:[2,13],40:[2,13]},{5:[2,14],8:[2,14],9:[2,14],10:[2,14],13:[2,14],14:[2,14],15:[2,14],16:[2,14],17:[2,14],18:[2,14],19:[2,14],20:[2,14],21:[2,14],22:[2,14],23:[2,14],24:[2,14],25:[2,14],26:[2,14],27:[2,14],28:[2,14],29:[2,14],30:[2,14],31:[2,14],32:[2,14],33:[2,14],34:[2,14],35:[2,14],36:[2,14],37:[2,14],38:[2,14],39:[2,14],40:[2,14]},{5:[2,15],8:[2,15],9:[2,15],10:[2,15],13:[2,15],14:[2,15],15:[2,15],16:[2,15],17:[2,15],18:[2,15],19:[2,15],20:[2,15],21:[2,15],22:[2,15],23:[2,15],24:[2,15],25:[2,15],26:[2,15],27:[2,15],28:[2,15],29:[2,15],30:[2,15],31:[2,15],32:[2,15],33:[2,15],34:[2,15],35:[2,15],36:[2,15],37:[2,15],38:[2,15],39:[2,15],40:[2,15]},{5:[2,16],8:[2,16],9:[2,16],10:[2,16],13:[2,16],14:[2,16],15:[2,16],16:[2,16],17:[2,16],18:[2,16],19:[2,16],20:[2,16],21:[2,16],22:[2,16],23:[2,16],24:[2,16],25:[2,16],26:[2,16],27:[2,16],28:[2,16],29:[2,16],30:[2,16],31:[2,16],32:[2,16],33:[2,16],34:[2,16],35:[2,16],36:[2,16],37:[2,16],38:[2,16],39:[2,16],40:[2,16]},{4:29,6:3,8:[2,2],9:[2,2],11:4,12:5,13:[1,6],14:[1,7],15:[1,8],16:[1,9],17:[1,10],18:[1,11],19:[2,2],20:[1,12],22:[1,13],24:[1,14],26:[1,15],28:[1,16],30:[1,17],32:[1,18],34:[1,19],36:[1,20],38:[1,21],40:[1,22]},{4:30,6:3,8:[2,2],9:[2,2],11:4,12:5,13:[1,6],14:[1,7],15:[1,8],16:[1,9],17:[1,10],18:[1,11],20:[1,12],21:[2,2],22:[1,13],24:[1,14],26:[1,15],28:[1,16],30:[1,17],32:[1,18],34:[1,19],36:[1,20],38:[1,21],40:[1,22]},{4:31,6:3,8:[2,2],9:[2,2],11:4,12:5,13:[1,6],14:[1,7],15:[1,8],16:[1,9],17:[1,10],18:[1,11],20:[1,12],22:[1,13],23:[2,2],24:[1,14],26:[1,15],28:[1,16],30:[1,17],32:[1,18],34:[1,19],36:[1,20],38:[1,21],40:[1,22]},{4:32,6:3,8:[2,2],9:[2,2],11:4,12:5,13:[1,6],14:[1,7],15:[1,8],16:[1,9],17:[1,10],18:[1,11],20:[1,12],22:[1,13],24:[1,14],25:[2,2],26:[1,15],28:[1,16],30:[1,17],32:[1,18],34:[1,19],36:[1,20],38:[1,21],40:[1,22]},{4:33,6:3,8:[2,2],9:[2,2],11:4,12:5,13:[1,6],14:[1,7],15:[1,8],16:[1,9],17:[1,10],18:[1,11],20:[1,12],22:[1,13],24:[1,14],26:[1,15],27:[2,2],28:[1,16],30:[1,17],32:[1,18],34:[1,19],36:[1,20],38:[1,21],40:[1,22]},{4:34,6:3,8:[2,2],9:[2,2],11:4,12:5,13:[1,6],14:[1,7],15:[1,8],16:[1,9],17:[1,10],18:[1,11],20:[1,12],22:[1,13],24:[1,14],26:[1,15],28:[1,16],29:[2,2],30:[1,17],32:[1,18],34:[1,19],36:[1,20],38:[1,21],40:[1,22]},{4:35,6:3,8:[2,2],9:[2,2],11:4,12:5,13:[1,6],14:[1,7],15:[1,8],16:[1,9],17:[1,10],18:[1,11],20:[1,12],22:[1,13],24:[1,14],26:[1,15],28:[1,16],30:[1,17],31:[2,2],32:[1,18],34:[1,19],36:[1,20],38:[1,21],40:[1,22]},{4:36,6:3,8:[2,2],9:[2,2],11:4,12:5,13:[1,6],14:[1,7],15:[1,8],16:[1,9],17:[1,10],18:[1,11],20:[1,12],22:[1,13],24:[1,14],26:[1,15],28:[1,16],30:[1,17],32:[1,18],33:[2,2],34:[1,19],36:[1,20],38:[1,21],40:[1,22]},{4:37,6:3,8:[2,2],9:[2,2],11:4,12:5,13:[1,6],14:[1,7],15:[1,8],16:[1,9],17:[1,10],18:[1,11],20:[1,12],22:[1,13],24:[1,14],26:[1,15],28:[1,16],30:[1,17],32:[1,18],34:[1,19],35:[2,2],36:[1,20],38:[1,21],40:[1,22]},{4:38,6:3,8:[2,2],9:[2,2],11:4,12:5,13:[1,6],14:[1,7],15:[1,8],16:[1,9],17:[1,10],18:[1,11],20:[1,12],22:[1,13],24:[1,14],26:[1,15],28:[1,16],30:[1,17],32:[1,18],34:[1,19],36:[1,20],37:[2,2],38:[1,21],40:[1,22]},{4:39,6:3,8:[2,2],9:[2,2],11:4,12:5,13:[1,6],14:[1,7],15:[1,8],16:[1,9],17:[1,10],18:[1,11],20:[1,12],22:[1,13],24:[1,14],26:[1,15],28:[1,16],30:[1,17],32:[1,18],34:[1,19],36:[1,20],38:[1,21],39:[2,2],40:[1,22]},{5:[2,28],8:[2,28],9:[2,28],10:[2,28],13:[2,28],14:[2,28],15:[2,28],16:[2,28],17:[2,28],18:[2,28],19:[2,28],20:[2,28],21:[2,28],22:[2,28],23:[2,28],24:[2,28],25:[2,28],26:[2,28],27:[2,28],28:[2,28],29:[2,28],30:[2,28],31:[2,28],32:[2,28],33:[2,28],34:[2,28],35:[2,28],36:[2,28],37:[2,28],38:[2,28],39:[2,28],40:[2,28]},{1:[2,1]},{5:[2,4],6:40,8:[2,4],9:[2,4],10:[2,4],11:4,12:5,13:[1,6],14:[1,7],15:[1,8],16:[1,9],17:[1,10],18:[1,11],19:[2,4],20:[1,12],21:[2,4],22:[1,13],23:[2,4],24:[1,14],25:[2,4],26:[1,15],27:[2,4],28:[1,16],29:[2,4],30:[1,17],31:[2,4],32:[1,18],33:[2,4],34:[1,19],35:[2,4],36:[1,20],37:[2,4],38:[1,21],39:[2,4],40:[1,22]},{5:[2,6],8:[2,6],9:[2,6],10:[2,6],13:[2,6],14:[2,6],15:[2,6],16:[2,6],17:[2,6],18:[2,6],19:[2,6],20:[2,6],21:[2,6],22:[2,6],23:[2,6],24:[2,6],25:[2,6],26:[2,6],27:[2,6],28:[2,6],29:[2,6],30:[2,6],31:[2,6],32:[2,6],33:[2,6],34:[2,6],35:[2,6],36:[2,6],37:[2,6],38:[2,6],39:[2,6],40:[2,6]},{4:41,6:3,8:[2,2],9:[2,2],10:[2,2],11:4,12:5,13:[1,6],14:[1,7],15:[1,8],16:[1,9],17:[1,10],18:[1,11],20:[1,12],22:[1,13],24:[1,14],26:[1,15],28:[1,16],30:[1,17],32:[1,18],34:[1,19],36:[1,20],38:[1,21],40:[1,22]},{5:[2,10],8:[2,10],9:[2,10],10:[2,10],13:[2,10],14:[2,10],15:[2,10],16:[2,10],17:[2,10],18:[2,10],19:[2,10],20:[2,10],21:[2,10],22:[2,10],23:[2,10],24:[2,10],25:[2,10],26:[2,10],27:[2,10],28:[2,10],29:[2,10],30:[2,10],31:[2,10],32:[2,10],33:[2,10],34:[2,10],35:[2,10],36:[2,10],37:[2,10],38:[2,10],39:[2,10],40:[2,10]},{5:[2,11],8:[2,11],9:[2,11],10:[2,11],13:[2,11],14:[2,11],15:[2,11],16:[2,11],17:[2,11],18:[2,11],19:[2,11],20:[2,11],21:[2,11],22:[2,11],23:[2,11],24:[2,11],25:[2,11],26:[2,11],27:[2,11],28:[2,11],29:[2,11],30:[2,11],31:[2,11],32:[2,11],33:[2,11],34:[2,11],35:[2,11],36:[2,11],37:[2,11],38:[2,11],39:[2,11],40:[2,11]},{7:24,8:[1,25],9:[1,26],19:[1,42]},{7:24,8:[1,25],9:[1,26],21:[1,43]},{7:24,8:[1,25],9:[1,26],23:[1,44]},{7:24,8:[1,25],9:[1,26],25:[1,45]},{7:24,8:[1,25],9:[1,26],27:[1,46]},{7:24,8:[1,25],9:[1,26],29:[1,47]},{7:24,8:[1,25],9:[1,26],31:[1,48]},{7:24,8:[1,25],9:[1,26],33:[1,49]},{7:24,8:[1,25],9:[1,26],35:[1,50]},{7:24,8:[1,25],9:[1,26],37:[1,51]},{7:24,8:[1,25],9:[1,26],39:[1,52]},{5:[2,5],8:[2,5],9:[2,5],10:[2,5],11:27,12:28,13:[1,6],14:[1,7],15:[1,8],16:[1,9],17:[1,10],18:[1,11],19:[2,5],20:[1,12],21:[2,5],22:[1,13],23:[2,5],24:[1,14],25:[2,5],26:[1,15],27:[2,5],28:[1,16],29:[2,5],30:[1,17],31:[2,5],32:[1,18],33:[2,5],34:[1,19],35:[2,5],36:[1,20],37:[2,5],38:[1,21],39:[2,5],40:[1,22]},{7:24,8:[1,25],9:[1,26],10:[1,53]},{5:[2,17],8:[2,17],9:[2,17],10:[2,17],13:[2,17],14:[2,17],15:[2,17],16:[2,17],17:[2,17],18:[2,17],19:[2,17],20:[2,17],21:[2,17],22:[2,17],23:[2,17],24:[2,17],25:[2,17],26:[2,17],27:[2,17],28:[2,17],29:[2,17],30:[2,17],31:[2,17],32:[2,17],33:[2,17],34:[2,17],35:[2,17],36:[2,17],37:[2,17],38:[2,17],39:[2,17],40:[2,17]},{5:[2,18],8:[2,18],9:[2,18],10:[2,18],13:[2,18],14:[2,18],15:[2,18],16:[2,18],17:[2,18],18:[2,18],19:[2,18],20:[2,18],21:[2,18],22:[2,18],23:[2,18],24:[2,18],25:[2,18],26:[2,18],27:[2,18],28:[2,18],29:[2,18],30:[2,18],31:[2,18],32:[2,18],33:[2,18],34:[2,18],35:[2,18],36:[2,18],37:[2,18],38:[2,18],39:[2,18],40:[2,18]},{5:[2,19],8:[2,19],9:[2,19],10:[2,19],13:[2,19],14:[2,19],15:[2,19],16:[2,19],17:[2,19],18:[2,19],19:[2,19],20:[2,19],21:[2,19],22:[2,19],23:[2,19],24:[2,19],25:[2,19],26:[2,19],27:[2,19],28:[2,19],29:[2,19],30:[2,19],31:[2,19],32:[2,19],33:[2,19],34:[2,19],35:[2,19],36:[2,19],37:[2,19],38:[2,19],39:[2,19],40:[2,19]},{5:[2,20],8:[2,20],9:[2,20],10:[2,20],13:[2,20],14:[2,20],15:[2,20],16:[2,20],17:[2,20],18:[2,20],19:[2,20],20:[2,20],21:[2,20],22:[2,20],23:[2,20],24:[2,20],25:[2,20],26:[2,20],27:[2,20],28:[2,20],29:[2,20],30:[2,20],31:[2,20],32:[2,20],33:[2,20],34:[2,20],35:[2,20],36:[2,20],37:[2,20],38:[2,20],39:[2,20],40:[2,20]},{5:[2,21],8:[2,21],9:[2,21],10:[2,21],13:[2,21],14:[2,21],15:[2,21],16:[2,21],17:[2,21],18:[2,21],19:[2,21],20:[2,21],21:[2,21],22:[2,21],23:[2,21],24:[2,21],25:[2,21],26:[2,21],27:[2,21],28:[2,21],29:[2,21],30:[2,21],31:[2,21],32:[2,21],33:[2,21],34:[2,21],35:[2,21],36:[2,21],37:[2,21],38:[2,21],39:[2,21],40:[2,21]},{5:[2,22],8:[2,22],9:[2,22],10:[2,22],13:[2,22],14:[2,22],15:[2,22],16:[2,22],17:[2,22],18:[2,22],19:[2,22],20:[2,22],21:[2,22],22:[2,22],23:[2,22],24:[2,22],25:[2,22],26:[2,22],27:[2,22],28:[2,22],29:[2,22],30:[2,22],31:[2,22],32:[2,22],33:[2,22],34:[2,22],35:[2,22],36:[2,22],37:[2,22],38:[2,22],39:[2,22],40:[2,22]},{5:[2,23],8:[2,23],9:[2,23],10:[2,23],13:[2,23],14:[2,23],15:[2,23],16:[2,23],17:[2,23],18:[2,23],19:[2,23],20:[2,23],21:[2,23],22:[2,23],23:[2,23],24:[2,23],25:[2,23],26:[2,23],27:[2,23],28:[2,23],29:[2,23],30:[2,23],31:[2,23],32:[2,23],33:[2,23],34:[2,23],35:[2,23],36:[2,23],37:[2,23],38:[2,23],39:[2,23],40:[2,23]},{5:[2,24],8:[2,24],9:[2,24],10:[2,24],13:[2,24],14:[2,24],15:[2,24],16:[2,24],17:[2,24],18:[2,24],19:[2,24],20:[2,24],21:[2,24],22:[2,24],23:[2,24],24:[2,24],25:[2,24],26:[2,24],27:[2,24],28:[2,24],29:[2,24],30:[2,24],31:[2,24],32:[2,24],33:[2,24],34:[2,24],35:[2,24],36:[2,24],37:[2,24],38:[2,24],39:[2,24],40:[2,24]},{5:[2,25],8:[2,25],9:[2,25],10:[2,25],13:[2,25],14:[2,25],15:[2,25],16:[2,25],17:[2,25],18:[2,25],19:[2,25],20:[2,25],21:[2,25],22:[2,25],23:[2,25],24:[2,25],25:[2,25],26:[2,25],27:[2,25],28:[2,25],29:[2,25],30:[2,25],31:[2,25],32:[2,25],33:[2,25],34:[2,25],35:[2,25],36:[2,25],37:[2,25],38:[2,25],39:[2,25],40:[2,25]},{5:[2,26],8:[2,26],9:[2,26],10:[2,26],13:[2,26],14:[2,26],15:[2,26],16:[2,26],17:[2,26],18:[2,26],19:[2,26],20:[2,26],21:[2,26],22:[2,26],23:[2,26],24:[2,26],25:[2,26],26:[2,26],27:[2,26],28:[2,26],29:[2,26],30:[2,26],31:[2,26],32:[2,26],33:[2,26],34:[2,26],35:[2,26],36:[2,26],37:[2,26],38:[2,26],39:[2,26],40:[2,26]},{5:[2,27],8:[2,27],9:[2,27],10:[2,27],13:[2,27],14:[2,27],15:[2,27],16:[2,27],17:[2,27],18:[2,27],19:[2,27],20:[2,27],21:[2,27],22:[2,27],23:[2,27],24:[2,27],25:[2,27],26:[2,27],27:[2,27],28:[2,27],29:[2,27],30:[2,27],31:[2,27],32:[2,27],33:[2,27],34:[2,27],35:[2,27],36:[2,27],37:[2,27],38:[2,27],39:[2,27],40:[2,27]},{5:[2,7],8:[2,7],9:[2,7],10:[2,7],13:[2,7],14:[2,7],15:[2,7],16:[2,7],17:[2,7],18:[2,7],19:[2,7],20:[2,7],21:[2,7],22:[2,7],23:[2,7],24:[2,7],25:[2,7],26:[2,7],27:[2,7],28:[2,7],29:[2,7],30:[2,7],31:[2,7],32:[2,7],33:[2,7],34:[2,7],35:[2,7],36:[2,7],37:[2,7],38:[2,7],39:[2,7],40:[2,7]}],
defaultActions: {23:[2,1]},
parseError: function parseError(str, hash) {
    throw new Error(str);
},
parse: function parse(input) {
    var self = this,
        stack = [0],
        vstack = [null], // semantic value stack
        lstack = [], // location stack
        table = this.table,
        yytext = '',
        yylineno = 0,
        yyleng = 0,
        recovering = 0,
        TERROR = 2,
        EOF = 1;

    //this.reductionCount = this.shiftCount = 0;

    this.lexer.setInput(input);
    this.lexer.yy = this.yy;
    this.yy.lexer = this.lexer;
    if (typeof this.lexer.yylloc == 'undefined')
        this.lexer.yylloc = {};
    var yyloc = this.lexer.yylloc;
    lstack.push(yyloc);

    if (typeof this.yy.parseError === 'function')
        this.parseError = this.yy.parseError;

    function popStack (n) {
        stack.length = stack.length - 2*n;
        vstack.length = vstack.length - n;
        lstack.length = lstack.length - n;
    }

    function lex() {
        var token;
        token = self.lexer.lex() || 1; // $end = 1
        // if token isn't its numeric value, convert
        if (typeof token !== 'number') {
            token = self.symbols_[token] || token;
        }
        return token;
    };

    var symbol, preErrorSymbol, state, action, a, r, yyval={},p,len,newState, expected;
    while (true) {
        // retreive state number from top of stack
        state = stack[stack.length-1];

        // use default actions if available
        if (this.defaultActions[state]) {
            action = this.defaultActions[state];
        } else {
            if (symbol == null)
                symbol = lex();
            // read action for current state and first input
            action = table[state] && table[state][symbol];
        }

        // handle parse error
        if (typeof action === 'undefined' || !action.length || !action[0]) {

            if (!recovering) {
                // Report error
                expected = [];
                for (p in table[state]) if (this.terminals_[p] && p > 2) {
                    expected.push("'"+this.terminals_[p]+"'");
                }
                var errStr = '';
                if (this.lexer.showPosition) {
                    errStr = 'Parse error on line '+(yylineno+1)+":\n"+this.lexer.showPosition()+'\nExpecting '+expected.join(', ');
                } else {
                    errStr = 'Parse error on line '+(yylineno+1)+": Unexpected " +
                                  (symbol == 1 /*EOF*/ ? "end of input" :
                                              ("'"+(this.terminals_[symbol] || symbol)+"'"));
                }
                this.parseError(errStr,
                    {text: this.lexer.match, token: this.terminals_[symbol] || symbol, line: this.lexer.yylineno, loc: yyloc, expected: expected});
            }

            // just recovered from another error
            if (recovering == 3) {
                if (symbol == EOF) {
                    throw new Error(errStr || 'Parsing halted.');
                }

                // discard current lookahead and grab another
                yyleng = this.lexer.yyleng;
                yytext = this.lexer.yytext;
                yylineno = this.lexer.yylineno;
                yyloc = this.lexer.yylloc;
                symbol = lex();
            }

            // try to recover from error
            while (1) {
                // check for error recovery rule in this state
                if ((TERROR.toString()) in table[state]) {
                    break;
                }
                if (state == 0) {
                    throw new Error(errStr || 'Parsing halted.');
                }
                popStack(1);
                state = stack[stack.length-1];
            }

            preErrorSymbol = symbol; // save the lookahead token
            symbol = TERROR;         // insert generic error symbol as new lookahead
            state = stack[stack.length-1];
            action = table[state] && table[state][TERROR];
            recovering = 3; // allow 3 real symbols to be shifted before reporting a new error
        }

        // this shouldn't happen, unless resolve defaults are off
        if (action[0] instanceof Array && action.length > 1) {
            throw new Error('Parse Error: multiple actions possible at state: '+state+', token: '+symbol);
        }

        switch (action[0]) {

            case 1: // shift
                //this.shiftCount++;

                stack.push(symbol);
                vstack.push(this.lexer.yytext);
                lstack.push(this.lexer.yylloc);
                stack.push(action[1]); // push state
                symbol = null;
                if (!preErrorSymbol) { // normal execution/no error
                    yyleng = this.lexer.yyleng;
                    yytext = this.lexer.yytext;
                    yylineno = this.lexer.yylineno;
                    yyloc = this.lexer.yylloc;
                    if (recovering > 0)
                        recovering--;
                } else { // error just occurred, resume old lookahead f/ before error
                    symbol = preErrorSymbol;
                    preErrorSymbol = null;
                }
                break;

            case 2: // reduce
                //this.reductionCount++;

                len = this.productions_[action[1]][1];

                // perform semantic action
                yyval.$ = vstack[vstack.length-len]; // default to $$ = $1
                // default location, uses first token for firsts, last for lasts
                yyval._$ = {
                    first_line: lstack[lstack.length-(len||1)].first_line,
                    last_line: lstack[lstack.length-1].last_line,
                    first_column: lstack[lstack.length-(len||1)].first_column,
                    last_column: lstack[lstack.length-1].last_column
                };
                r = this.performAction.call(yyval, yytext, yyleng, yylineno, this.yy, action[1], vstack, lstack);

                if (typeof r !== 'undefined') {
                    return r;
                }

                // pop off stack
                if (len) {
                    stack = stack.slice(0,-1*len*2);
                    vstack = vstack.slice(0, -1*len);
                    lstack = lstack.slice(0, -1*len);
                }

                stack.push(this.productions_[action[1]][0]);    // push nonterminal (reduce)
                vstack.push(yyval.$);
                lstack.push(yyval._$);
                // goto new state = table[STATE][NONTERMINAL]
                newState = table[stack[stack.length-2]][stack[stack.length-1]];
                stack.push(newState);
                break;

            case 3: // accept
                return true;
        }

    }

    return true;
}};/* Jison generated lexer */
var lexer = (function(){var lexer = ({EOF:1,
parseError:function parseError(str, hash) {
        if (this.yy.parseError) {
            this.yy.parseError(str, hash);
        } else {
            throw new Error(str);
        }
    },
setInput:function (input) {
        this._input = input;
        this._more = this._less = this.done = false;
        this.yylineno = this.yyleng = 0;
        this.yytext = this.matched = this.match = '';
        this.conditionStack = ['INITIAL'];
        this.yylloc = {first_line:1,first_column:0,last_line:1,last_column:0};
        return this;
    },
input:function () {
        var ch = this._input[0];
        this.yytext+=ch;
        this.yyleng++;
        this.match+=ch;
        this.matched+=ch;
        var lines = ch.match(/\n/);
        if (lines) this.yylineno++;
        this._input = this._input.slice(1);
        return ch;
    },
unput:function (ch) {
        this._input = ch + this._input;
        return this;
    },
more:function () {
        this._more = true;
        return this;
    },
pastInput:function () {
        var past = this.matched.substr(0, this.matched.length - this.match.length);
        return (past.length > 20 ? '...':'') + past.substr(-20).replace(/\n/g, "");
    },
upcomingInput:function () {
        var next = this.match;
        if (next.length < 20) {
            next += this._input.substr(0, 20-next.length);
        }
        return (next.substr(0,20)+(next.length > 20 ? '...':'')).replace(/\n/g, "");
    },
showPosition:function () {
        var pre = this.pastInput();
        var c = new Array(pre.length + 1).join("-");
        return pre + this.upcomingInput() + "\n" + c+"^";
    },
next:function () {
        if (this.done) {
            return this.EOF;
        }
        if (!this._input) this.done = true;

        var token,
            match,
            col,
            lines;
        if (!this._more) {
            this.yytext = '';
            this.match = '';
        }
        var rules = this._currentRules();
        for (var i=0;i < rules.length; i++) {
            match = this._input.match(this.rules[rules[i]]);
            if (match) {
                lines = match[0].match(/\n.*/g);
                if (lines) this.yylineno += lines.length;
                this.yylloc = {first_line: this.yylloc.last_line,
                               last_line: this.yylineno+1,
                               first_column: this.yylloc.last_column,
                               last_column: lines ? lines[lines.length-1].length-1 : this.yylloc.last_column + match[0].length}
                this.yytext += match[0];
                this.match += match[0];
                this.matches = match;
                this.yyleng = this.yytext.length;
                this._more = false;
                this._input = this._input.slice(match[0].length);
                this.matched += match[0];
                token = this.performAction.call(this, this.yy, this, rules[i],this.conditionStack[this.conditionStack.length-1]);
                if (token) return token;
                else return;
            }
        }
        if (this._input === "") {
            return this.EOF;
        } else {
            this.parseError('Lexical error on line '+(this.yylineno+1)+'. Unrecognized text.\n'+this.showPosition(), 
                    {text: "", token: null, line: this.yylineno});
        }
    },
lex:function lex() {
        var r = this.next();
        if (typeof r !== 'undefined') {
            return r;
        } else {
            return this.lex();
        }
    },
begin:function begin(condition) {
        this.conditionStack.push(condition);
    },
popState:function popState() {
        return this.conditionStack.pop();
    },
_currentRules:function _currentRules() {
        return this.conditions[this.conditionStack[this.conditionStack.length-1]].rules;
    }});
lexer.performAction = function anonymous(yy,yy_,$avoiding_name_collisions,YY_START) {

var YYSTATE=YY_START
switch($avoiding_name_collisions) {
case 0:
		yy_.yytext = yy_.yytext.substring(4, yy_.yytext.length - 5);
		return 40;
	
break;
case 1:
		var pluginName = yy_.yytext.match(/^\{([a-z]+)/)[1];
		var pluginParams =  yy_.yytext.match(/[ ].*?[}]|[/}]/);
		yy_.yytext = {
			name: pluginName,
			params: pluginParams,
			body: ''
		};
		return 8;
	
break;
case 2:
		var pluginName = yy_.yytext.match(/^\{([A-Z]+)/)[1];
		var pluginParams =  yy_.yytext.match(/[(].*?[)]/);
		
		if (!yy.pluginStack) yy.pluginStack = [];
		yy.pluginStack.push({
			name: pluginName,
			params: pluginParams
		});
		
		return 9;
	
break;
case 3:
		if (yy.pluginStack) {
			if (
				yy.pluginStack.length &&
				yy_.yytext.match(yy.pluginStack[yy.pluginStack.length - 1].name)
			) {
				var readyPlugin = yy.pluginStack.pop();
				yy_.yytext = readyPlugin;
				return 10;
			}
		}
		return 'CONTENT';
	
break;
case 4:
		yy_.yytext = "<hr />";
		return 16;
	
break;
case 5:
		var smile = yy_.yytext.substring(2, yy_.yytext.length - 2);
		yy_.yytext = "<img src='img/smiles/icon_" + smile + ".gif' alt='" + smile + "' />";
		return 17;
	
break;
case 6:
		var smile = yy_.yytext.substring(2, yy_.yytext.length - 2);
		yy_.yytext = "<img src='img/smiles/icon_" + smile + ".gif' alt='" + smile + "' />";
		return 13;
	
break;
case 7:this.popState();				return 19
break;
case 8:this.begin('bold');				return 18
break;
case 9:this.popState();				return 21
break;
case 10:this.begin('box');				return 20
break;
case 11:this.popState();				return 23
break;
case 12:this.begin('center');			return 22
break;
case 13:this.popState();				return 25
break;
case 14:this.begin('colortext');		return 24
break;
case 15:this.popState();				return 27
break;
case 16:this.begin('italic');			return 26
break;
case 17:this.popState();				return 29
break;
case 18:this.begin('link');				return 28
break;
case 19:this.popState();				return 31
break;
case 20:this.begin('strikethrough');	return 30
break;
case 21:this.popState();				return 33
break;
case 22:this.begin('table');			return 32
break;
case 23:this.popState();				return 35
break;
case 24:this.begin('titlebar');			return 34
break;
case 25:this.popState();				return 37
break;
case 26:this.begin('underscore');		return 36
break;
case 27:this.popState();				return 39
break;
case 28:this.begin('wikilink');			return 38
break;
case 29:return 14
break;
case 30:return 13
break;
case 31:
		yy_.yytext = yy_.yytext.replace(/\n/g, '<br />');
		return 13;
	
break;
case 32:return 5
break;
}
};
lexer.rules = [/^~np~(.|\n)*?~\/np~/,/^\{[a-z]+.*?\}/,/^\{[A-Z]+\(.*?\)\}/,/^\{[A-Z]+\}/,/^---/,/^\(:[a-z]+:\)/,/^\[\[.*?/,/^[_][_]/,/^[_][_]/,/^[\^]/,/^[\^]/,/^[:][:]/,/^[:][:]/,/^[\~][\~]/,/^[\~][\~][#]/,/^['][']/,/^['][']/,/^(\])/,/^(\[)/,/^[-][-]/,/^[-][-]/,/^[|][|]/,/^[|][|]/,/^[=][-]/,/^[-][=]/,/^[=][=][=]/,/^[=][=][=]/,/^[)][)]/,/^[(][(]/,/^<(.|\n)*?>/,/^(.)/,/^(\n)/,/^$/];
lexer.conditions = {"bold":{"rules":[0,1,2,3,4,5,6,7,8,10,12,14,16,18,20,22,24,26,28,29,30,31,32],"inclusive":true},"box":{"rules":[0,1,2,3,4,5,6,8,9,10,12,14,16,18,20,22,24,26,28,29,30,31,32],"inclusive":true},"center":{"rules":[0,1,2,3,4,5,6,8,10,11,12,14,16,18,20,22,24,26,28,29,30,31,32],"inclusive":true},"colortext":{"rules":[0,1,2,3,4,5,6,8,10,12,13,14,16,18,20,22,24,26,28,29,30,31,32],"inclusive":true},"italic":{"rules":[0,1,2,3,4,5,6,8,10,12,14,15,16,18,20,22,24,26,28,29,30,31,32],"inclusive":true},"link":{"rules":[0,1,2,3,4,5,6,8,10,12,14,16,17,18,20,22,24,26,28,29,30,31,32],"inclusive":true},"strikethrough":{"rules":[0,1,2,3,4,5,6,8,10,12,14,16,18,19,20,22,24,26,28,29,30,31,32],"inclusive":true},"table":{"rules":[0,1,2,3,4,5,6,8,10,12,14,16,18,20,21,22,24,26,28,29,30,31,32],"inclusive":true},"titlebar":{"rules":[0,1,2,3,4,5,6,8,10,12,14,16,18,20,22,23,24,26,28,29,30,31,32],"inclusive":true},"underscore":{"rules":[0,1,2,3,4,5,6,8,10,12,14,16,18,20,22,24,25,26,28,29,30,31,32],"inclusive":true},"wikilink":{"rules":[0,1,2,3,4,5,6,8,10,12,14,16,18,20,22,24,26,27,28,29,30,31,32],"inclusive":true},"INITIAL":{"rules":[0,1,2,3,4,5,6,8,10,12,14,16,18,20,22,24,26,28,29,30,31,32],"inclusive":true}};return lexer;})()
parser.lexer = lexer;
return parser;
})();
if (typeof require !== 'undefined' && typeof exports !== 'undefined') {
exports.parser = WikiParser;
exports.parse = function () { return WikiParser.parse.apply(WikiParser, arguments); }
exports.main = function commonjsMain(args) {
    if (!args[1])
        throw new Error('Usage: '+args[0]+' FILE');
    if (typeof process !== 'undefined') {
        var source = require('fs').readFileSync(require('path').join(process.cwd(), args[1]), "utf8");
    } else {
        var cwd = require("file").path(require("file").cwd());
        var source = cwd.join(args[1]).read({charset: "utf-8"});
    }
    return exports.parser.parse(source);
}
if (typeof module !== 'undefined' && require.main === module) {
  exports.main(typeof process !== 'undefined' ? process.argv.slice(1) : require("system").args);
}
}