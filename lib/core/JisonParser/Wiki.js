/* Jison generated parser */
var Wiki = (function(){
var parser = {trace: function trace() { },
yy: {},
symbols_: {"error":2,"wiki":3,"contents":4,"EOF":5,"content":6,"CONTENT":7,"HORIZONTAL_BAR":8,"SMILE":9,"HEADER_START":10,"HEADER_END":11,"BOLD_START":12,"BOLD_END":13,"BOX_START":14,"BOX_END":15,"CENTER_START":16,"CENTER_END":17,"COLORTEXT_START":18,"COLORTEXT_END":19,"ITALIC_START":20,"ITALIC_END":21,"LINK_START":22,"LINK_END":23,"STRIKETHROUGH_START":24,"STRIKETHROUGH_END":25,"TABLE_START":26,"TABLE_END":27,"TITLEBAR_START":28,"TITLEBAR_END":29,"UNDERSCORE_START":30,"UNDERSCORE_END":31,"WIKILINK_START":32,"WIKILINK_END":33,"INLINE_PLUGIN":34,"PLUGIN_START":35,"PLUGIN_END":36,"$accept":0,"$end":1},
terminals_: {2:"error",5:"EOF",7:"CONTENT",8:"HORIZONTAL_BAR",9:"SMILE",10:"HEADER_START",11:"HEADER_END",12:"BOLD_START",13:"BOLD_END",14:"BOX_START",15:"BOX_END",16:"CENTER_START",17:"CENTER_END",18:"COLORTEXT_START",19:"COLORTEXT_END",20:"ITALIC_START",21:"ITALIC_END",22:"LINK_START",23:"LINK_END",24:"STRIKETHROUGH_START",25:"STRIKETHROUGH_END",26:"TABLE_START",27:"TABLE_END",28:"TITLEBAR_START",29:"TITLEBAR_END",30:"UNDERSCORE_START",31:"UNDERSCORE_END",32:"WIKILINK_START",33:"WIKILINK_END",34:"INLINE_PLUGIN",35:"PLUGIN_START",36:"PLUGIN_END"},
productions_: [0,[3,1],[3,2],[3,1],[4,1],[4,2],[6,1],[6,1],[6,1],[6,2],[6,3],[6,2],[6,3],[6,2],[6,3],[6,2],[6,3],[6,2],[6,3],[6,2],[6,3],[6,2],[6,3],[6,3],[6,2],[6,3],[6,3],[6,2],[6,3],[6,2],[6,3],[6,2],[6,3],[6,2],[6,3],[6,1],[6,2],[6,3]],
performAction: function anonymous(yytext,yyleng,yylineno,yy,yystate,$$,_$) {

var $0 = $$.length - 1;
switch (yystate) {
case 1:return $$[$0];
break;
case 2:return $$[$0-1];
break;
case 3:return " ";
break;
case 4:this.$ = $$[$0];
break;
case 5:
		this.$ = $$[$0-1] + $$[$0]; //js

		//php this.$ = $$[$0-1] . $$[$0];
	
break;
case 6:this.$ = $$[$0];
break;
case 7:this.$ = $$[$0];
break;
case 8:this.$ = $$[$0];
break;
case 10:
		this.$ = parser.header($$[$0-1]); //js
		//php this.$ = this->header($$[$0-1]);
	
break;
case 12:
		this.$ = parser.bold($$[$0-1]); //js
		//php this.$ = this->bold($$[$0-1]);
	
break;
case 14:
		this.$ = parser.box($$[$0-1]); //js
		//php this.$ = this->box($$[$0-1]);
	
break;
case 16:
		this.$ = parser.center($$[$0-1]); //js
		//php this.$ = this->center($$[$0-1]);
	
break;
case 18:
		this.$ = parser.colortext($$[$0-1]); //js
		//php this.$ = this->colortext($$[$0-1]);
	
break;
case 20:
		this.$ = parser.italics($$[$0-1]); //js
		//php this.$ = this->italics($$[$0-1]);
	
break;
case 22:
		this.$ = parser.link($$[$0-1]); //js
		//php this.$ = this->link($$[$0-1]);
	
break;
case 23:
        this.$ = $$[$0-2] + $$[$0-1]; //js
        //php this.$ = $$[$0-2] . $$[$0-1];
    
break;
case 25:
		this.$ = parser.strikethrough($$[$0-1]); //js
		//php this.$ = this->strikethrough($$[$0-1]);
	
break;
case 26:
        this.$ = $$[$0-2] + $$[$0-1]; //js
        //php this.$ = $$[$0-2] . $$[$0-1];
    
break;
case 28:
		this.$ = parser.tableParser($$[$0-1]); //js
		//php this.$ = this->tableParser($$[$0-1]);
	
break;
case 30:
		this.$ = parser.titlebar($$[$0-1]); //js
		//php this.$ = this->titlebar($$[$0-1]);
	
break;
case 32:
		this.$ = parser.underscore($$[$0-1]); //js
		//php this.$ = this->underscore($$[$0-1]);
	
break;
case 34:
		this.$ = parser.wikilink($$[$0-1]); //js
		//php this.$ = this->wikilink($$[$0-1]);
	
break;
case 35:
 		this.$ = parser.plugin($$[$0]); //js

 		//php this.$ = this->plugin($$[$0]);
 	
break;
case 36:
  		$$[$0].body = ''; //js
        this.$ = parser.plugin($$[$0]); //js

        //php $$[$0]['body'] = '';
        //php this.$ = this->plugin($$[$0]);
  	
break;
case 37:
 		$$[$0].body = $$[$0-1]; //js
 		this.$ = parser.plugin($$[$0]); //js

 		//php $$[$0]['body'] = $$[$0-1];
 		//php this.$ = this->plugin($$[$0]);
 	
break;
}
},
table: [{3:1,4:2,5:[1,3],6:4,7:[1,5],8:[1,6],9:[1,7],10:[1,8],12:[1,9],14:[1,10],16:[1,11],18:[1,12],20:[1,13],22:[1,14],24:[1,15],26:[1,16],28:[1,17],30:[1,18],32:[1,19],34:[1,20],35:[1,21]},{1:[3]},{1:[2,1],5:[1,22],6:23,7:[1,5],8:[1,6],9:[1,7],10:[1,8],12:[1,9],14:[1,10],16:[1,11],18:[1,12],20:[1,13],22:[1,14],24:[1,15],26:[1,16],28:[1,17],30:[1,18],32:[1,19],34:[1,20],35:[1,21]},{1:[2,3]},{1:[2,4],5:[2,4],7:[2,4],8:[2,4],9:[2,4],10:[2,4],11:[2,4],12:[2,4],13:[2,4],14:[2,4],15:[2,4],16:[2,4],17:[2,4],18:[2,4],19:[2,4],20:[2,4],21:[2,4],22:[2,4],23:[2,4],24:[2,4],25:[2,4],26:[2,4],27:[2,4],28:[2,4],29:[2,4],30:[2,4],31:[2,4],32:[2,4],33:[2,4],34:[2,4],35:[2,4],36:[2,4]},{1:[2,6],5:[2,6],7:[2,6],8:[2,6],9:[2,6],10:[2,6],11:[2,6],12:[2,6],13:[2,6],14:[2,6],15:[2,6],16:[2,6],17:[2,6],18:[2,6],19:[2,6],20:[2,6],21:[2,6],22:[2,6],23:[2,6],24:[2,6],25:[2,6],26:[2,6],27:[2,6],28:[2,6],29:[2,6],30:[2,6],31:[2,6],32:[2,6],33:[2,6],34:[2,6],35:[2,6],36:[2,6]},{1:[2,7],5:[2,7],7:[2,7],8:[2,7],9:[2,7],10:[2,7],11:[2,7],12:[2,7],13:[2,7],14:[2,7],15:[2,7],16:[2,7],17:[2,7],18:[2,7],19:[2,7],20:[2,7],21:[2,7],22:[2,7],23:[2,7],24:[2,7],25:[2,7],26:[2,7],27:[2,7],28:[2,7],29:[2,7],30:[2,7],31:[2,7],32:[2,7],33:[2,7],34:[2,7],35:[2,7],36:[2,7]},{1:[2,8],5:[2,8],7:[2,8],8:[2,8],9:[2,8],10:[2,8],11:[2,8],12:[2,8],13:[2,8],14:[2,8],15:[2,8],16:[2,8],17:[2,8],18:[2,8],19:[2,8],20:[2,8],21:[2,8],22:[2,8],23:[2,8],24:[2,8],25:[2,8],26:[2,8],27:[2,8],28:[2,8],29:[2,8],30:[2,8],31:[2,8],32:[2,8],33:[2,8],34:[2,8],35:[2,8],36:[2,8]},{4:25,6:4,7:[1,5],8:[1,6],9:[1,7],10:[1,8],11:[1,24],12:[1,9],14:[1,10],16:[1,11],18:[1,12],20:[1,13],22:[1,14],24:[1,15],26:[1,16],28:[1,17],30:[1,18],32:[1,19],34:[1,20],35:[1,21]},{4:27,6:4,7:[1,5],8:[1,6],9:[1,7],10:[1,8],12:[1,9],13:[1,26],14:[1,10],16:[1,11],18:[1,12],20:[1,13],22:[1,14],24:[1,15],26:[1,16],28:[1,17],30:[1,18],32:[1,19],34:[1,20],35:[1,21]},{4:29,6:4,7:[1,5],8:[1,6],9:[1,7],10:[1,8],12:[1,9],14:[1,10],15:[1,28],16:[1,11],18:[1,12],20:[1,13],22:[1,14],24:[1,15],26:[1,16],28:[1,17],30:[1,18],32:[1,19],34:[1,20],35:[1,21]},{4:31,6:4,7:[1,5],8:[1,6],9:[1,7],10:[1,8],12:[1,9],14:[1,10],16:[1,11],17:[1,30],18:[1,12],20:[1,13],22:[1,14],24:[1,15],26:[1,16],28:[1,17],30:[1,18],32:[1,19],34:[1,20],35:[1,21]},{4:33,6:4,7:[1,5],8:[1,6],9:[1,7],10:[1,8],12:[1,9],14:[1,10],16:[1,11],18:[1,12],19:[1,32],20:[1,13],22:[1,14],24:[1,15],26:[1,16],28:[1,17],30:[1,18],32:[1,19],34:[1,20],35:[1,21]},{4:35,6:4,7:[1,5],8:[1,6],9:[1,7],10:[1,8],12:[1,9],14:[1,10],16:[1,11],18:[1,12],20:[1,13],21:[1,34],22:[1,14],24:[1,15],26:[1,16],28:[1,17],30:[1,18],32:[1,19],34:[1,20],35:[1,21]},{4:37,6:4,7:[1,5],8:[1,6],9:[1,7],10:[1,8],12:[1,9],14:[1,10],16:[1,11],18:[1,12],20:[1,13],22:[1,14],23:[1,36],24:[1,15],26:[1,16],28:[1,17],30:[1,18],32:[1,19],34:[1,20],35:[1,21]},{4:39,6:4,7:[1,5],8:[1,6],9:[1,7],10:[1,8],12:[1,9],14:[1,10],16:[1,11],18:[1,12],20:[1,13],22:[1,14],24:[1,15],25:[1,38],26:[1,16],28:[1,17],30:[1,18],32:[1,19],34:[1,20],35:[1,21]},{4:41,6:4,7:[1,5],8:[1,6],9:[1,7],10:[1,8],12:[1,9],14:[1,10],16:[1,11],18:[1,12],20:[1,13],22:[1,14],24:[1,15],26:[1,16],27:[1,40],28:[1,17],30:[1,18],32:[1,19],34:[1,20],35:[1,21]},{4:43,6:4,7:[1,5],8:[1,6],9:[1,7],10:[1,8],12:[1,9],14:[1,10],16:[1,11],18:[1,12],20:[1,13],22:[1,14],24:[1,15],26:[1,16],28:[1,17],29:[1,42],30:[1,18],32:[1,19],34:[1,20],35:[1,21]},{4:45,6:4,7:[1,5],8:[1,6],9:[1,7],10:[1,8],12:[1,9],14:[1,10],16:[1,11],18:[1,12],20:[1,13],22:[1,14],24:[1,15],26:[1,16],28:[1,17],30:[1,18],31:[1,44],32:[1,19],34:[1,20],35:[1,21]},{4:47,6:4,7:[1,5],8:[1,6],9:[1,7],10:[1,8],12:[1,9],14:[1,10],16:[1,11],18:[1,12],20:[1,13],22:[1,14],24:[1,15],26:[1,16],28:[1,17],30:[1,18],32:[1,19],33:[1,46],34:[1,20],35:[1,21]},{1:[2,35],5:[2,35],7:[2,35],8:[2,35],9:[2,35],10:[2,35],11:[2,35],12:[2,35],13:[2,35],14:[2,35],15:[2,35],16:[2,35],17:[2,35],18:[2,35],19:[2,35],20:[2,35],21:[2,35],22:[2,35],23:[2,35],24:[2,35],25:[2,35],26:[2,35],27:[2,35],28:[2,35],29:[2,35],30:[2,35],31:[2,35],32:[2,35],33:[2,35],34:[2,35],35:[2,35],36:[2,35]},{4:49,6:4,7:[1,5],8:[1,6],9:[1,7],10:[1,8],12:[1,9],14:[1,10],16:[1,11],18:[1,12],20:[1,13],22:[1,14],24:[1,15],26:[1,16],28:[1,17],30:[1,18],32:[1,19],34:[1,20],35:[1,21],36:[1,48]},{1:[2,2]},{1:[2,5],5:[2,5],7:[2,5],8:[2,5],9:[2,5],10:[2,5],11:[2,5],12:[2,5],13:[2,5],14:[2,5],15:[2,5],16:[2,5],17:[2,5],18:[2,5],19:[2,5],20:[2,5],21:[2,5],22:[2,5],23:[2,5],24:[2,5],25:[2,5],26:[2,5],27:[2,5],28:[2,5],29:[2,5],30:[2,5],31:[2,5],32:[2,5],33:[2,5],34:[2,5],35:[2,5],36:[2,5]},{1:[2,9],5:[2,9],7:[2,9],8:[2,9],9:[2,9],10:[2,9],11:[2,9],12:[2,9],13:[2,9],14:[2,9],15:[2,9],16:[2,9],17:[2,9],18:[2,9],19:[2,9],20:[2,9],21:[2,9],22:[2,9],23:[2,9],24:[2,9],25:[2,9],26:[2,9],27:[2,9],28:[2,9],29:[2,9],30:[2,9],31:[2,9],32:[2,9],33:[2,9],34:[2,9],35:[2,9],36:[2,9]},{6:23,7:[1,5],8:[1,6],9:[1,7],10:[1,8],11:[1,50],12:[1,9],14:[1,10],16:[1,11],18:[1,12],20:[1,13],22:[1,14],24:[1,15],26:[1,16],28:[1,17],30:[1,18],32:[1,19],34:[1,20],35:[1,21]},{1:[2,11],5:[2,11],7:[2,11],8:[2,11],9:[2,11],10:[2,11],11:[2,11],12:[2,11],13:[2,11],14:[2,11],15:[2,11],16:[2,11],17:[2,11],18:[2,11],19:[2,11],20:[2,11],21:[2,11],22:[2,11],23:[2,11],24:[2,11],25:[2,11],26:[2,11],27:[2,11],28:[2,11],29:[2,11],30:[2,11],31:[2,11],32:[2,11],33:[2,11],34:[2,11],35:[2,11],36:[2,11]},{6:23,7:[1,5],8:[1,6],9:[1,7],10:[1,8],12:[1,9],13:[1,51],14:[1,10],16:[1,11],18:[1,12],20:[1,13],22:[1,14],24:[1,15],26:[1,16],28:[1,17],30:[1,18],32:[1,19],34:[1,20],35:[1,21]},{1:[2,13],5:[2,13],7:[2,13],8:[2,13],9:[2,13],10:[2,13],11:[2,13],12:[2,13],13:[2,13],14:[2,13],15:[2,13],16:[2,13],17:[2,13],18:[2,13],19:[2,13],20:[2,13],21:[2,13],22:[2,13],23:[2,13],24:[2,13],25:[2,13],26:[2,13],27:[2,13],28:[2,13],29:[2,13],30:[2,13],31:[2,13],32:[2,13],33:[2,13],34:[2,13],35:[2,13],36:[2,13]},{6:23,7:[1,5],8:[1,6],9:[1,7],10:[1,8],12:[1,9],14:[1,10],15:[1,52],16:[1,11],18:[1,12],20:[1,13],22:[1,14],24:[1,15],26:[1,16],28:[1,17],30:[1,18],32:[1,19],34:[1,20],35:[1,21]},{1:[2,15],5:[2,15],7:[2,15],8:[2,15],9:[2,15],10:[2,15],11:[2,15],12:[2,15],13:[2,15],14:[2,15],15:[2,15],16:[2,15],17:[2,15],18:[2,15],19:[2,15],20:[2,15],21:[2,15],22:[2,15],23:[2,15],24:[2,15],25:[2,15],26:[2,15],27:[2,15],28:[2,15],29:[2,15],30:[2,15],31:[2,15],32:[2,15],33:[2,15],34:[2,15],35:[2,15],36:[2,15]},{6:23,7:[1,5],8:[1,6],9:[1,7],10:[1,8],12:[1,9],14:[1,10],16:[1,11],17:[1,53],18:[1,12],20:[1,13],22:[1,14],24:[1,15],26:[1,16],28:[1,17],30:[1,18],32:[1,19],34:[1,20],35:[1,21]},{1:[2,17],5:[2,17],7:[2,17],8:[2,17],9:[2,17],10:[2,17],11:[2,17],12:[2,17],13:[2,17],14:[2,17],15:[2,17],16:[2,17],17:[2,17],18:[2,17],19:[2,17],20:[2,17],21:[2,17],22:[2,17],23:[2,17],24:[2,17],25:[2,17],26:[2,17],27:[2,17],28:[2,17],29:[2,17],30:[2,17],31:[2,17],32:[2,17],33:[2,17],34:[2,17],35:[2,17],36:[2,17]},{6:23,7:[1,5],8:[1,6],9:[1,7],10:[1,8],12:[1,9],14:[1,10],16:[1,11],18:[1,12],19:[1,54],20:[1,13],22:[1,14],24:[1,15],26:[1,16],28:[1,17],30:[1,18],32:[1,19],34:[1,20],35:[1,21]},{1:[2,19],5:[2,19],7:[2,19],8:[2,19],9:[2,19],10:[2,19],11:[2,19],12:[2,19],13:[2,19],14:[2,19],15:[2,19],16:[2,19],17:[2,19],18:[2,19],19:[2,19],20:[2,19],21:[2,19],22:[2,19],23:[2,19],24:[2,19],25:[2,19],26:[2,19],27:[2,19],28:[2,19],29:[2,19],30:[2,19],31:[2,19],32:[2,19],33:[2,19],34:[2,19],35:[2,19],36:[2,19]},{6:23,7:[1,5],8:[1,6],9:[1,7],10:[1,8],12:[1,9],14:[1,10],16:[1,11],18:[1,12],20:[1,13],21:[1,55],22:[1,14],24:[1,15],26:[1,16],28:[1,17],30:[1,18],32:[1,19],34:[1,20],35:[1,21]},{1:[2,21],5:[2,21],7:[2,21],8:[2,21],9:[2,21],10:[2,21],11:[2,21],12:[2,21],13:[2,21],14:[2,21],15:[2,21],16:[2,21],17:[2,21],18:[2,21],19:[2,21],20:[2,21],21:[2,21],22:[2,21],23:[2,21],24:[2,21],25:[2,21],26:[2,21],27:[2,21],28:[2,21],29:[2,21],30:[2,21],31:[2,21],32:[2,21],33:[2,21],34:[2,21],35:[2,21],36:[2,21]},{5:[1,57],6:23,7:[1,5],8:[1,6],9:[1,7],10:[1,8],12:[1,9],14:[1,10],16:[1,11],18:[1,12],20:[1,13],22:[1,14],23:[1,56],24:[1,15],26:[1,16],28:[1,17],30:[1,18],32:[1,19],34:[1,20],35:[1,21]},{1:[2,24],5:[2,24],7:[2,24],8:[2,24],9:[2,24],10:[2,24],11:[2,24],12:[2,24],13:[2,24],14:[2,24],15:[2,24],16:[2,24],17:[2,24],18:[2,24],19:[2,24],20:[2,24],21:[2,24],22:[2,24],23:[2,24],24:[2,24],25:[2,24],26:[2,24],27:[2,24],28:[2,24],29:[2,24],30:[2,24],31:[2,24],32:[2,24],33:[2,24],34:[2,24],35:[2,24],36:[2,24]},{5:[1,59],6:23,7:[1,5],8:[1,6],9:[1,7],10:[1,8],12:[1,9],14:[1,10],16:[1,11],18:[1,12],20:[1,13],22:[1,14],24:[1,15],25:[1,58],26:[1,16],28:[1,17],30:[1,18],32:[1,19],34:[1,20],35:[1,21]},{1:[2,27],5:[2,27],7:[2,27],8:[2,27],9:[2,27],10:[2,27],11:[2,27],12:[2,27],13:[2,27],14:[2,27],15:[2,27],16:[2,27],17:[2,27],18:[2,27],19:[2,27],20:[2,27],21:[2,27],22:[2,27],23:[2,27],24:[2,27],25:[2,27],26:[2,27],27:[2,27],28:[2,27],29:[2,27],30:[2,27],31:[2,27],32:[2,27],33:[2,27],34:[2,27],35:[2,27],36:[2,27]},{6:23,7:[1,5],8:[1,6],9:[1,7],10:[1,8],12:[1,9],14:[1,10],16:[1,11],18:[1,12],20:[1,13],22:[1,14],24:[1,15],26:[1,16],27:[1,60],28:[1,17],30:[1,18],32:[1,19],34:[1,20],35:[1,21]},{1:[2,29],5:[2,29],7:[2,29],8:[2,29],9:[2,29],10:[2,29],11:[2,29],12:[2,29],13:[2,29],14:[2,29],15:[2,29],16:[2,29],17:[2,29],18:[2,29],19:[2,29],20:[2,29],21:[2,29],22:[2,29],23:[2,29],24:[2,29],25:[2,29],26:[2,29],27:[2,29],28:[2,29],29:[2,29],30:[2,29],31:[2,29],32:[2,29],33:[2,29],34:[2,29],35:[2,29],36:[2,29]},{6:23,7:[1,5],8:[1,6],9:[1,7],10:[1,8],12:[1,9],14:[1,10],16:[1,11],18:[1,12],20:[1,13],22:[1,14],24:[1,15],26:[1,16],28:[1,17],29:[1,61],30:[1,18],32:[1,19],34:[1,20],35:[1,21]},{1:[2,31],5:[2,31],7:[2,31],8:[2,31],9:[2,31],10:[2,31],11:[2,31],12:[2,31],13:[2,31],14:[2,31],15:[2,31],16:[2,31],17:[2,31],18:[2,31],19:[2,31],20:[2,31],21:[2,31],22:[2,31],23:[2,31],24:[2,31],25:[2,31],26:[2,31],27:[2,31],28:[2,31],29:[2,31],30:[2,31],31:[2,31],32:[2,31],33:[2,31],34:[2,31],35:[2,31],36:[2,31]},{6:23,7:[1,5],8:[1,6],9:[1,7],10:[1,8],12:[1,9],14:[1,10],16:[1,11],18:[1,12],20:[1,13],22:[1,14],24:[1,15],26:[1,16],28:[1,17],30:[1,18],31:[1,62],32:[1,19],34:[1,20],35:[1,21]},{1:[2,33],5:[2,33],7:[2,33],8:[2,33],9:[2,33],10:[2,33],11:[2,33],12:[2,33],13:[2,33],14:[2,33],15:[2,33],16:[2,33],17:[2,33],18:[2,33],19:[2,33],20:[2,33],21:[2,33],22:[2,33],23:[2,33],24:[2,33],25:[2,33],26:[2,33],27:[2,33],28:[2,33],29:[2,33],30:[2,33],31:[2,33],32:[2,33],33:[2,33],34:[2,33],35:[2,33],36:[2,33]},{6:23,7:[1,5],8:[1,6],9:[1,7],10:[1,8],12:[1,9],14:[1,10],16:[1,11],18:[1,12],20:[1,13],22:[1,14],24:[1,15],26:[1,16],28:[1,17],30:[1,18],32:[1,19],33:[1,63],34:[1,20],35:[1,21]},{1:[2,36],5:[2,36],7:[2,36],8:[2,36],9:[2,36],10:[2,36],11:[2,36],12:[2,36],13:[2,36],14:[2,36],15:[2,36],16:[2,36],17:[2,36],18:[2,36],19:[2,36],20:[2,36],21:[2,36],22:[2,36],23:[2,36],24:[2,36],25:[2,36],26:[2,36],27:[2,36],28:[2,36],29:[2,36],30:[2,36],31:[2,36],32:[2,36],33:[2,36],34:[2,36],35:[2,36],36:[2,36]},{6:23,7:[1,5],8:[1,6],9:[1,7],10:[1,8],12:[1,9],14:[1,10],16:[1,11],18:[1,12],20:[1,13],22:[1,14],24:[1,15],26:[1,16],28:[1,17],30:[1,18],32:[1,19],34:[1,20],35:[1,21],36:[1,64]},{1:[2,10],5:[2,10],7:[2,10],8:[2,10],9:[2,10],10:[2,10],11:[2,10],12:[2,10],13:[2,10],14:[2,10],15:[2,10],16:[2,10],17:[2,10],18:[2,10],19:[2,10],20:[2,10],21:[2,10],22:[2,10],23:[2,10],24:[2,10],25:[2,10],26:[2,10],27:[2,10],28:[2,10],29:[2,10],30:[2,10],31:[2,10],32:[2,10],33:[2,10],34:[2,10],35:[2,10],36:[2,10]},{1:[2,12],5:[2,12],7:[2,12],8:[2,12],9:[2,12],10:[2,12],11:[2,12],12:[2,12],13:[2,12],14:[2,12],15:[2,12],16:[2,12],17:[2,12],18:[2,12],19:[2,12],20:[2,12],21:[2,12],22:[2,12],23:[2,12],24:[2,12],25:[2,12],26:[2,12],27:[2,12],28:[2,12],29:[2,12],30:[2,12],31:[2,12],32:[2,12],33:[2,12],34:[2,12],35:[2,12],36:[2,12]},{1:[2,14],5:[2,14],7:[2,14],8:[2,14],9:[2,14],10:[2,14],11:[2,14],12:[2,14],13:[2,14],14:[2,14],15:[2,14],16:[2,14],17:[2,14],18:[2,14],19:[2,14],20:[2,14],21:[2,14],22:[2,14],23:[2,14],24:[2,14],25:[2,14],26:[2,14],27:[2,14],28:[2,14],29:[2,14],30:[2,14],31:[2,14],32:[2,14],33:[2,14],34:[2,14],35:[2,14],36:[2,14]},{1:[2,16],5:[2,16],7:[2,16],8:[2,16],9:[2,16],10:[2,16],11:[2,16],12:[2,16],13:[2,16],14:[2,16],15:[2,16],16:[2,16],17:[2,16],18:[2,16],19:[2,16],20:[2,16],21:[2,16],22:[2,16],23:[2,16],24:[2,16],25:[2,16],26:[2,16],27:[2,16],28:[2,16],29:[2,16],30:[2,16],31:[2,16],32:[2,16],33:[2,16],34:[2,16],35:[2,16],36:[2,16]},{1:[2,18],5:[2,18],7:[2,18],8:[2,18],9:[2,18],10:[2,18],11:[2,18],12:[2,18],13:[2,18],14:[2,18],15:[2,18],16:[2,18],17:[2,18],18:[2,18],19:[2,18],20:[2,18],21:[2,18],22:[2,18],23:[2,18],24:[2,18],25:[2,18],26:[2,18],27:[2,18],28:[2,18],29:[2,18],30:[2,18],31:[2,18],32:[2,18],33:[2,18],34:[2,18],35:[2,18],36:[2,18]},{1:[2,20],5:[2,20],7:[2,20],8:[2,20],9:[2,20],10:[2,20],11:[2,20],12:[2,20],13:[2,20],14:[2,20],15:[2,20],16:[2,20],17:[2,20],18:[2,20],19:[2,20],20:[2,20],21:[2,20],22:[2,20],23:[2,20],24:[2,20],25:[2,20],26:[2,20],27:[2,20],28:[2,20],29:[2,20],30:[2,20],31:[2,20],32:[2,20],33:[2,20],34:[2,20],35:[2,20],36:[2,20]},{1:[2,22],5:[2,22],7:[2,22],8:[2,22],9:[2,22],10:[2,22],11:[2,22],12:[2,22],13:[2,22],14:[2,22],15:[2,22],16:[2,22],17:[2,22],18:[2,22],19:[2,22],20:[2,22],21:[2,22],22:[2,22],23:[2,22],24:[2,22],25:[2,22],26:[2,22],27:[2,22],28:[2,22],29:[2,22],30:[2,22],31:[2,22],32:[2,22],33:[2,22],34:[2,22],35:[2,22],36:[2,22]},{1:[2,23],5:[2,23],7:[2,23],8:[2,23],9:[2,23],10:[2,23],11:[2,23],12:[2,23],13:[2,23],14:[2,23],15:[2,23],16:[2,23],17:[2,23],18:[2,23],19:[2,23],20:[2,23],21:[2,23],22:[2,23],23:[2,23],24:[2,23],25:[2,23],26:[2,23],27:[2,23],28:[2,23],29:[2,23],30:[2,23],31:[2,23],32:[2,23],33:[2,23],34:[2,23],35:[2,23],36:[2,23]},{1:[2,25],5:[2,25],7:[2,25],8:[2,25],9:[2,25],10:[2,25],11:[2,25],12:[2,25],13:[2,25],14:[2,25],15:[2,25],16:[2,25],17:[2,25],18:[2,25],19:[2,25],20:[2,25],21:[2,25],22:[2,25],23:[2,25],24:[2,25],25:[2,25],26:[2,25],27:[2,25],28:[2,25],29:[2,25],30:[2,25],31:[2,25],32:[2,25],33:[2,25],34:[2,25],35:[2,25],36:[2,25]},{1:[2,26],5:[2,26],7:[2,26],8:[2,26],9:[2,26],10:[2,26],11:[2,26],12:[2,26],13:[2,26],14:[2,26],15:[2,26],16:[2,26],17:[2,26],18:[2,26],19:[2,26],20:[2,26],21:[2,26],22:[2,26],23:[2,26],24:[2,26],25:[2,26],26:[2,26],27:[2,26],28:[2,26],29:[2,26],30:[2,26],31:[2,26],32:[2,26],33:[2,26],34:[2,26],35:[2,26],36:[2,26]},{1:[2,28],5:[2,28],7:[2,28],8:[2,28],9:[2,28],10:[2,28],11:[2,28],12:[2,28],13:[2,28],14:[2,28],15:[2,28],16:[2,28],17:[2,28],18:[2,28],19:[2,28],20:[2,28],21:[2,28],22:[2,28],23:[2,28],24:[2,28],25:[2,28],26:[2,28],27:[2,28],28:[2,28],29:[2,28],30:[2,28],31:[2,28],32:[2,28],33:[2,28],34:[2,28],35:[2,28],36:[2,28]},{1:[2,30],5:[2,30],7:[2,30],8:[2,30],9:[2,30],10:[2,30],11:[2,30],12:[2,30],13:[2,30],14:[2,30],15:[2,30],16:[2,30],17:[2,30],18:[2,30],19:[2,30],20:[2,30],21:[2,30],22:[2,30],23:[2,30],24:[2,30],25:[2,30],26:[2,30],27:[2,30],28:[2,30],29:[2,30],30:[2,30],31:[2,30],32:[2,30],33:[2,30],34:[2,30],35:[2,30],36:[2,30]},{1:[2,32],5:[2,32],7:[2,32],8:[2,32],9:[2,32],10:[2,32],11:[2,32],12:[2,32],13:[2,32],14:[2,32],15:[2,32],16:[2,32],17:[2,32],18:[2,32],19:[2,32],20:[2,32],21:[2,32],22:[2,32],23:[2,32],24:[2,32],25:[2,32],26:[2,32],27:[2,32],28:[2,32],29:[2,32],30:[2,32],31:[2,32],32:[2,32],33:[2,32],34:[2,32],35:[2,32],36:[2,32]},{1:[2,34],5:[2,34],7:[2,34],8:[2,34],9:[2,34],10:[2,34],11:[2,34],12:[2,34],13:[2,34],14:[2,34],15:[2,34],16:[2,34],17:[2,34],18:[2,34],19:[2,34],20:[2,34],21:[2,34],22:[2,34],23:[2,34],24:[2,34],25:[2,34],26:[2,34],27:[2,34],28:[2,34],29:[2,34],30:[2,34],31:[2,34],32:[2,34],33:[2,34],34:[2,34],35:[2,34],36:[2,34]},{1:[2,37],5:[2,37],7:[2,37],8:[2,37],9:[2,37],10:[2,37],11:[2,37],12:[2,37],13:[2,37],14:[2,37],15:[2,37],16:[2,37],17:[2,37],18:[2,37],19:[2,37],20:[2,37],21:[2,37],22:[2,37],23:[2,37],24:[2,37],25:[2,37],26:[2,37],27:[2,37],28:[2,37],29:[2,37],30:[2,37],31:[2,37],32:[2,37],33:[2,37],34:[2,37],35:[2,37],36:[2,37]}],
defaultActions: {3:[2,3],22:[2,2]},
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
    }

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
        _handle_error:
        if (typeof action === 'undefined' || !action.length || !action[0]) {

            if (!recovering) {
                // Report error
                expected = [];
                for (p in table[state]) if (this.terminals_[p] && p > 2) {
                    expected.push("'"+this.terminals_[p]+"'");
                }
                var errStr = '';
                if (this.lexer.showPosition) {
                    errStr = 'Parse error on line '+(yylineno+1)+":\n"+this.lexer.showPosition()+"\nExpecting "+expected.join(', ') + ", got '" + this.terminals_[symbol]+ "'";
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
}};
 /* parser extensions */

// additional module code //js
parser.extend = { //js
	parser: function(extension) { //js
        if (extension) { //js
            for (var attr in extension) { //js
                parser[attr] = extension[attr]; //js
            } //js
        } //js
    }, //js
    lexer: function() { //js
		if (extension) { //js
			for (var attr in extension) { //js
				parser[attr] = extension[attr]; //js
			} //js
       	} //js
	} //js
}; //js/* Jison generated lexer */
var lexer = (function(){
var lexer = ({EOF:1,
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
less:function (n) {
        this._input = this.match.slice(n) + this._input;
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
            tempMatch,
            index,
            col,
            lines;
        if (!this._more) {
            this.yytext = '';
            this.match = '';
        }
        var rules = this._currentRules();
        for (var i=0;i < rules.length; i++) {
            tempMatch = this._input.match(this.rules[rules[i]]);
            if (tempMatch && (!match || tempMatch[0].length > match[0].length)) {
                match = tempMatch;
                index = i;
                if (!this.options.flex) break;
            }
        }
        if (match) {
            lines = match[0].match(/\n.*/g);
            if (lines) this.yylineno += lines.length;
            this.yylloc = {first_line: this.yylloc.last_line,
                           last_line: this.yylineno+1,
                           first_column: this.yylloc.last_column,
                           last_column: lines ? lines[lines.length-1].length-1 : this.yylloc.last_column + match[0].length}
            this.yytext += match[0];
            this.match += match[0];
            this.yyleng = this.yytext.length;
            this._more = false;
            this._input = this._input.slice(match[0].length);
            this.matched += match[0];
            token = this.performAction.call(this, this.yy, this, rules[index],this.conditionStack[this.conditionStack.length-1]);
            if (this.done && this._input) this.done = false;
            if (token) return token;
            else return;
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
    },
topState:function () {
        return this.conditionStack[this.conditionStack.length-2];
    },
pushState:function begin(condition) {
        this.begin(condition);
    }});
lexer.options = {"flex":true};
lexer.performAction = function anonymous(yy,yy_,$avoiding_name_collisions,YY_START) {

var YYSTATE=YY_START
switch($avoiding_name_collisions) {
case 0:return 7;//For now let individual plugins handle else
break;
case 1:
		yy_.yytext = parser.inlinePlugin(yy_.yytext); //js
		return 34; //js

		//php $yy_.yytext = $this->inlinePlugin($yy_.yytext);
		//php return 'INLINE_PLUGIN';
	
break;
case 2:
		lexer.begin('plugin'); //js
		yy.pluginStack = parser.stackPlugin(yy_.yytext, yy.pluginStack); //js

		if (parser.size(yy.pluginStack) == 1) {//js
			return 35; //js
		} else {//js
			return 'CONTENT'; //js
		}//js

		//php $this->begin('plugin');
		//php $this->stackPlugin($yy_.yytext);

		//php if (count($this->pluginStack) == 1) {
		//php 	return 'PLUGIN_START';
		//php } else {
		//php 	return 'CONTENT';
		//php }
	
break;
case 3:
		lexer.unput("{" + yy.pluginStack[parser.size(yy.pluginStack) - 1].name + "}"); //js

		//php $this->unput("{" . $this->pluginStack[count($this->pluginStack) - 1]['name'] . "}");
	
break;
case 4:
		var plugin = yy.pluginStack[yy.pluginStack.length - 1]; //js
		if (('{' + plugin.name + '}') == yy_.yytext) { //js
			lexer.popState(); //js
			if (yy.pluginStack) { //js
				if ( //js
					parser.size(yy.pluginStack) > 0 && //js
					parser.substring(yy_.yytext, 1, -1) == yy.pluginStack[parser.size(yy.pluginStack) - 1].name //js
				) { //js
					if (parser.size(yy.pluginStack) == 1) { //js
						yy_.yytext = yy.pluginStack[parser.size(yy.pluginStack) - 1]; //js
						yy.pluginStack = parser.pop(yy.pluginStack); //js
						return 36; //js
					} else { //js
						yy.pluginStack = parser.pop(yy.pluginStack); //js
						return 'CONTENT'; //js
					} //js
				} //js
			} //js
		} //js
		return 'CONTENT'; //js

		//php $plugin = end($this->pluginStack);
		//php if (('{' . $plugin['name'] . '}') == $yy_.yytext) {
		//php   $this->popState();
		//php   if (!empty($this->pluginStack)) {
		//php 	    if (
		//php 		    count($this->pluginStack) > 0 &&
		//php 		    $this->substring($yy_.yytext, 1, -1) == $this->pluginStack[count($this->pluginStack) - 1]['name']
		//php 	    ) {
		//php 		    if (count($this->pluginStack) == 1) {
		//php 			    $yy_.yytext = $this->pluginStack[count($this->pluginStack) - 1];
		//php 			    array_pop($this->pluginStack);
		//php 			    return 'PLUGIN_END';
		//php 		    } else {
		//php 			    array_pop($this->pluginStack);
		//php 			    return 'CONTENT';
		//php 		    }
		//php 	    }
		//php   }
		//php }
		//php return 'CONTENT';
	
break;
case 5:
		yy_.yytext = parser.hr(); //js
		//php $yy_.yytext = $this->hr();

		return 8;
	
break;
case 6:
		yy_.yytext = parser.substring(yy_.yytext, 2, -2); //js
		yy_.yytext = parser.smile(yy_.yytext); //js

		//php $yy_.yytext = $this->substring($yy_.yytext, 2, -2);
		//php $yy_.yytext = $this->smile($yy_.yytext);

		return 9;
	
break;
case 7:
		yy_.yytext = parser.substring(yy_.yytext, 2, -1); //js

		//php $yy_.yytext = $this->substring($yy_.yytext, 2, -1);

		return 7;
	
break;
case 8:
		if (parser.isPlugin()) return 5; //js
		lexer.unput('__'); //js

		//php if ($this->isPlugin()) return 'EOF';
        //php $this->unput('__');
	
break;
case 9:
		if (parser.isPlugin()) return 7; //js
		lexer.popState(); //js
		return 'BOLD_END'; //js

		//php if ($this->isPlugin()) return 'CONTENT';
		//php $this->popState();
		//php return 'BOLD_END';
	
break;
case 10:
		if (parser.isPlugin()) return 7; //js
		lexer.begin('bold'); //js
		return 'BOLD_START'; //js

		//php if ($this->isPlugin()) return 'CONTENT';
		//php $this->begin('bold');
		//php return 'BOLD_START';
	
break;
case 11:
		if (parser.isPlugin()) return 7; //js
		lexer.unput('^'); //js

		//php if ($this->isPlugin()) return 'CONTENT';
		//php $this->unput('^');
	
break;
case 12:
		if (parser.isPlugin()) return 7; //js
		lexer.popState(); //js
		return 'BOX_END'; //js

		//php if ($this->isPlugin()) return 'CONTENT';
		//php $this->popState();
		//php return 'BOX_END';
	
break;
case 13:
		if (parser.isPlugin()) return 7; //js
		lexer.begin('box'); //js
		return 'BOX_START'; //js

		//php if ($this->isPlugin()) return 'CONTENT';
		//php $this->begin('box');
		//php return 'BOX_START';
	
break;
case 14:
		if (parser.isPlugin()) return 7; //js
		lexer.unput('::'); //js

		//php if ($this->isPlugin()) return 'CONTENT';
        //php $this->unput('::');
	
break;
case 15:
		if (parser.isPlugin()) return 7; //js
		lexer.popState(); //js
		return 'CENTER_END'; //js

		//php if ($this->isPlugin()) return 'CONTENT';
		//php $this->popState();
		//php return 'CENTER_END';
	
break;
case 16:
		if (parser.isPlugin()) return 7; //js
		lexer.begin('center'); //js
		return 'CENTER_START'; //js

		//php if ($this->isPlugin()) return 'CONTENT';
		//php $this->begin('center');
		//php return 'CENTER_START';
	
break;
case 17:
		if (parser.isPlugin()) return 7; //js\
		lexer.unput('~~'); //js

		//php if ($this->isPlugin()) return 'CONTENT';
        //php $this->unput('~~');
	
break;
case 18:
		if (parser.isPlugin()) return 7; //js
		lexer.popState(); //js
		return 'COLORTEXT_END'; //js

		//php if ($this->isPlugin()) return 'CONTENT';
		//php $this->popState();
		//php return 'COLORTEXT_END';
	
break;
case 19:
		if (parser.isPlugin()) return 7; //js
		lexer.begin('colortext'); //js
		return 'COLORTEXT_START'; //js

		//php if ($this->isPlugin()) return 'CONTENT';
		//php $this->begin('colortext');
		//php return 'COLORTEXT_START';
	
break;
case 20:
		if (parser.isPlugin()) return 7; //js
		lexer.unput("\n"); //js

		//php if ($this->isPlugin()) return 'CONTENT';
        //php $this->unput("\n");
	
break;
case 21:
		if (parser.isPlugin()) return 7; //js
		lexer.popState(); //js
		return 'HEADER_END'; //js

		//php if ($this->isPlugin()) return 'CONTENT';
		//php $this->popState();
		//php return 'HEADER_END';
	
break;
case 22:
		if (parser.isPlugin()) return 7; //js
		parser.begin('header'); //js
		return 'HEADER_START'; //js

		//php if ($this->isPlugin()) return 'CONTENT';
		//php $this->begin('header');
		//php return 'HEADER_START';
	
break;
case 23:
		if (parser.isPlugin()) return 7; //js
		lexer.unput("''"); //js

		//php if ($this->isPlugin()) return 'CONTENT';
		//php $this->unput("''");
	
break;
case 24:
		if (parser.isPlugin()) return 7; //js
		lexer.popState(); //js
		return 'ITALIC_END'; //js

		//php if ($this->isPlugin()) return 'CONTENT';
		//php $this->popState();
		//php return 'ITALIC_END';
	
break;
case 25:
		if (parser.isPlugin()) return 7; //js
		lexer.begin('italic'); //js
		return 'ITALIC_START'; //js

		//php if ($this->isPlugin()) return 'CONTENT';
		//php $this->begin('italic');
		//php return 'ITALIC_START';
	
break;
case 26:
		lexer.popState(); //js
		return 5; //js

        //php $this->popState();
        //php return 'EOF';
	
break;
case 27:
		if (parser.isPlugin()) return 7; //js
		lexer.popState(); //js
		return 'LINK_END'; //js

		//php if ($this->isPlugin()) return 'CONTENT';
		//php $this->popState();
		//php return 'LINK_END';
	
break;
case 28:
		if (parser.isPlugin()) return 7; //js
		lexer.begin('link'); //js
		return 'LINK_START'; //js

		//php if ($this->isPlugin()) return 'CONTENT';
		//php $this->begin('link');
		//php return 'LINK_START';
	
break;
case 29:return 7;
break;
case 30:
		lexer.popState(); //js
		return 5; //js

		//php $this->popState();
		//php return 'EOF';
	
break;
case 31:
		if (parser.isPlugin()) return 7; //js
		lexer.popState(); //js
		return 'STRIKETHROUGH_END'; //js

		//php if ($this->isPlugin()) return 'CONTENT';
		//php $this->popState();
		//php return 'STRIKETHROUGH_END';
	
break;
case 32:
		if (parser.isPlugin()) return 7; //js
		lexer.begin('strikethrough'); //js
		return 'STRIKETHROUGH_START'; //js

		//php if ($this->isPlugin()) return 'CONTENT';
		//php $this->begin('strikethrough');
		//php return 'STRIKETHROUGH_START';
	
break;
case 33:
		if (parser.isPlugin()) return 7; //js
		lexer.unput('||'); //js

		//php if ($this->isPlugin()) return 'CONTENT';
        //php $this->unput('||');
	
break;
case 34:
		if (parser.isPlugin()) return 7; //js
		lexer.popState(); //js
		return 'TABLE_END'; //js

		//php if ($this->isPlugin()) return 'CONTENT';
		//php $this->popState();
		//php return 'TABLE_END';
	
break;
case 35:
		if (parser.isPlugin()) return 7; //js
		lexer.begin('table'); //js
		return 'TABLE_START'; //js

		//php if ($this->isPlugin()) return 'CONTENT';
		//php $this->begin('table');
		//php return 'TABLE_START';
	
break;
case 36:
		if (parser.isPlugin()) return 7; //js
		lexer.unput('=-'); //js

		//php if ($this->isPlugin()) return 'CONTENT';
		//php $this->unput('=-');
	
break;
case 37:
		if (parser.isPlugin()) return 7; //js
		lexer.popState(); //js
		return 'TITLEBAR_END'; //js

		//php if ($this->isPlugin()) return 'CONTENT';
		//php $this->popState();
		//php return 'TITLEBAR_END';
	
break;
case 38:
		if (parser.isPlugin()) return 7; //js
		lexer.begin('titlebar'); //js
		return 'TITLEBAR_START'; //js

		//php if ($this->isPlugin()) return 'CONTENT';
		//php $this->begin('titlebar');
		//php return 'TITLEBAR_START';
	
break;
case 39:
		if (parser.isPlugin()) return 7; //js
		lexer.unput('==='); //js

		//php if ($this->isPlugin()) return 'CONTENT';
		//php $this->unput('===');
	
break;
case 40:
		if (parser.isPlugin()) return 7; //js
		lexer.popState(); //js
		return 'UNDERSCORE_END'; //js

		//php if ($this->isPlugin()) return 'CONTENT';
		//php $this->popState();
		//php return 'UNDERSCORE_END';
	
break;
case 41:
		if (parser.isPlugin()) return 7; //js
		lexer.begin('underscore'); //js
		return 'UNDERSCORE_START'; //js

		//php if ($this->isPlugin()) return 'CONTENT';
		//php $this->begin('underscore');
		//php return 'UNDERSCORE_START';
	
break;
case 42:
		if (parser.isPlugin()) return 7; //js
		lexer.unput('))'); //js

		//php if ($this->isPlugin()) return 'CONTENT';
		//php $this->unput('))');
	
break;
case 43:
		if (parser.isPlugin()) return 7; //js
		lexer.popState(); //js
		return 'WIKILINK_END'; //js

		//php if ($this->isPlugin()) return 'CONTENT';
		//php $this->popState();
		//php return 'WIKILINK_END';
	
break;
case 44:
		if (parser.isPlugin()) return 7; //js
		lexer.begin('wikilink'); //js
		return 'WIKILINK_START'; //js

		//php if ($this->isPlugin()) return 'CONTENT';
		//php $this->begin('wikilink');
		//php return 'WIKILINK_START';
	
break;
case 45:return 7;
break;
case 46:return 7;
break;
case 47:return 7;
break;
case 48:return 7;
break;
case 49:return 7;
break;
case 50:return 5;
break;
case 51:console.log(yy_.yytext);
break;
}
};
lexer.rules = [/^(?:\{ELSE\})/,/^(?:\{([a-z]+).*?\})/,/^(?:\{([A-Z]+)\(.*?\)\})/,/^(?:$)/,/^(?:\{([A-Z]+)\})/,/^(?:---)/,/^(?:\(:([a-z]+):\))/,/^(?:\[\[.*?)/,/^(?:$)/,/^(?:[_][_])/,/^(?:[_][_])/,/^(?:$)/,/^(?:[\^])/,/^(?:[\^])/,/^(?:$)/,/^(?:[:][:])/,/^(?:[:][:])/,/^(?:$)/,/^(?:[\~][\~])/,/^(?:[\~][\~][#])/,/^(?:$)/,/^(?:[\n\r])/,/^(?:[\n\r][!])/,/^(?:$)/,/^(?:[']['])/,/^(?:[']['])/,/^(?:$)/,/^(?:(\]))/,/^(?:(\[))/,/^(?:-- )/,/^(?:$)/,/^(?:[-][-])/,/^(?:[-][-])/,/^(?:$)/,/^(?:[|][|])/,/^(?:[|][|])/,/^(?:$)/,/^(?:[=][-])/,/^(?:[-][=])/,/^(?:$)/,/^(?:[=][=][=])/,/^(?:[=][=][=])/,/^(?:$)/,/^(?:[)][)])/,/^(?:[(][(])/,/^(?:<(.|\n)*?>)/,/^(?:[A-Za-z0-9]+)/,/^(?:(.))/,/^(?:(\n))/,/^(?:(\s))/,/^(?:$)/,/^(?:.)/];
lexer.conditions = {"plugin":{"rules":[0,1,2,3,4,5,6,7,10,13,16,19,22,25,28,29,32,35,38,41,44,45,46,47,48,49,50,51],"inclusive":true},"bold":{"rules":[0,1,2,5,6,7,8,9,10,13,16,19,22,25,28,29,32,35,38,41,44,45,46,47,48,49,50,51],"inclusive":true},"box":{"rules":[0,1,2,5,6,7,10,11,12,13,16,19,22,25,28,29,32,35,38,41,44,45,46,47,48,49,50,51],"inclusive":true},"center":{"rules":[0,1,2,5,6,7,10,13,14,15,16,19,22,25,28,29,32,35,38,41,44,45,46,47,48,49,50,51],"inclusive":true},"colortext":{"rules":[0,1,2,5,6,7,10,13,16,17,18,19,22,25,28,29,32,35,38,41,44,45,46,47,48,49,50,51],"inclusive":true},"italic":{"rules":[0,1,2,5,6,7,10,13,16,19,22,23,24,25,28,29,32,35,38,41,44,45,46,47,48,49,50,51],"inclusive":true},"header":{"rules":[0,1,2,5,6,7,10,13,16,19,20,21,22,25,28,29,32,35,38,41,44,45,46,47,48,49,50,51],"inclusive":true},"link":{"rules":[0,1,2,5,6,7,10,13,16,19,22,25,26,27,28,29,32,35,38,41,44,45,46,47,48,49,50,51],"inclusive":true},"strikethrough":{"rules":[0,1,2,5,6,7,10,13,16,19,22,25,28,29,30,31,32,35,38,41,44,45,46,47,48,49,50,51],"inclusive":true},"table":{"rules":[0,1,2,5,6,7,10,13,16,19,22,25,28,29,32,33,34,35,38,41,44,45,46,47,48,49,50,51],"inclusive":true},"titlebar":{"rules":[0,1,2,5,6,7,10,13,16,19,22,25,28,29,32,35,36,37,38,41,44,45,46,47,48,49,50,51],"inclusive":true},"underscore":{"rules":[0,1,2,5,6,7,10,13,16,19,22,25,28,29,32,35,38,39,40,41,44,45,46,47,48,49,50,51],"inclusive":true},"wikilink":{"rules":[0,1,2,5,6,7,10,13,16,19,22,25,28,29,32,35,38,41,42,43,44,45,46,47,48,49,50,51],"inclusive":true},"INITIAL":{"rules":[0,1,2,5,6,7,10,13,16,19,22,25,28,29,32,35,38,41,44,45,46,47,48,49,50,51],"inclusive":true}};
return lexer;})()
parser.lexer = lexer;function Parser () { this.yy = {}; }Parser.prototype = parser;parser.Parser = Parser;
return new Parser;
})();
if (typeof require !== 'undefined' && typeof exports !== 'undefined') {
exports.parser = Wiki;
exports.Parser = Wiki.Parser;
exports.parse = function () { return Wiki.parse.apply(Wiki, arguments); }
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