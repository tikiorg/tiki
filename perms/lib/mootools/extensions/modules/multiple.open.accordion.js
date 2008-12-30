/*	Script: multiple.open.accordion.js
		Creates a Mootools <Fx.Accordion> that allows the user to open more than one element.
		
		Dependancies:
			 mootools - 	<Moo.js>, <Function.js>, <Array.js>, <String.js>, <Element.js>, <Fx.js>
			
		Author:
			Aaron Newton, <aaron [dot] newton [at] cnet [dot] com>

		
		Class: MultipleOpenAccordion
		Extends the <Fx.Elements> class from Mootools for an accordion element that allows
		the user to open more than one element.
		
		Arguments:
		togglers - elements that activate each section
		elements - the elements to resize
		options - the options object of key/value settings
		
		Options:
		openAll - (boolean) open all elements on startup; defaults to true.
		allowMultipleOpen - (boolean) allows users to open more than one element at a time; defaults to true.
		firstElementsOpen - (array) an array of elements to open on startup;
				only used if openAll = false and allowMultipleOpen = true;
				defaults to [0];
		start - (string) 'first-open' slides open each element in firstElementsOpen;
										 'open-first' opens each element in firstElementsOpen immediately using no effects (default)
		fixedHeight - integer, if you want your accordion to have a fixed height. defaults to false.
		fixedWidth - integer, if you want your accordion to have a fixed width. defaults to false.
		alwaysHide - boolean, if you want the ability to close your only-open item. defaults to true.
		wait - boolean. means that open and close transitions can cancel current ones (so if you click
		 on items before the previous finishes transitioning, the clicked transition will fire canceling the previous). 
		 true means that if one element is sliding open or closed, clicking on another will have no effect. 
		 for Accordion defaults to false.
		onActive - function to execute when an element starts to show
		onBackground - function to execute when an element starts to hide
		height - boolean, will add a height transition to the accordion if true. defaults to true.
		opacity - boolean, will add an opacity transition to the accordion if true. defaults to true.
		width - boolean, will add a width transition to the accordion if true. defaults to false, 
						css mastery is required to make this work!
	*/
MultipleOpenAccordion = Fx.Elements.extend({
	extendOptions: function(options){
		Object.extend(this.options, Object.extend({
			openAll: true,
			allowMultipleOpen: true,
			firstElementsOpen: [0],
			start: 'open-first',
			fixedHeight: false,
			fixedWidth: false,
			alwaysHide: true,
			wait: false,
			onActive: Class.empty,
			onBackground: Class.empty,
			height: true,
			opacity: true,
			width: false
		}, options || {}));
	},
	initialize: function(togglers, elements, options){
		this.parent(elements, options);
		this.extendOptions(options);
		this.previousClick = 'nan';
		this.elementsVisible = [];
		togglers.each(function(tog, i){
			$(tog).addEvent('click', function(){this.toggleSection(i)}.bind(this));
		}, this);
		this.togglers = togglers;
		this.h = {}; 
		this.w = {};
		this.o = {};
		this.now = [];
		this.elements.each(function(el, i){
			this.now[i] = {};
			if(this.options.openAll && this.options.allowMultipleOpen) $(el).setStyles({'overflow': 'hidden'});
			else $(el).setStyles({'height': 0, 'overflow': 'hidden'});
		}, this);
		if(!this.options.openAll || !this.options.allowMultipleOpen) {
			switch(this.options.start){
				case 'first-open': this.showSection(this.options.firstElementsOpen[0]); break;
				case 'open-first': this.toggleSection(this.options.firstElementsOpen[0]); break;
			}
		}
		if (this.options.openAll && this.options.allowMultipleOpen) {
			this.showAll();
		} else if (this.options.allowMultipleOpen) {
			this.openSections(this.options.firstElementsOpen);
		}
	},
	hideThis: function(i){ //sets up the effects for hiding an element
		this.elementsVisible[i] = false;
		if (this.options.height) this.h = {'height': [this.elements[i].offsetHeight, 0]};
		if (this.options.width) this.w = {'width': [this.elements[i].offsetWidth, 0]};
		if (this.options.opacity) this.o = {'opacity': [this.now[i]['opacity'] || 1, 0]};
	},

	showThis: function(i){ //sets up the effects for showing an element
		this.elementsVisible[i] = true;
		if (this.options.height) this.h = {'height': [this.elements[i].offsetHeight, this.options.fixedHeight || this.elements[i].scrollHeight]};
		if (this.options.width) this.w = {'width': [this.elements[i].offsetWidth, this.options.fixedWidth || this.elements[i].scrollWidth]};
		if (this.options.opacity) this.o = {'opacity': [this.now[i]['opacity'] || 0, 1]};
	},
/*	Property: toggleSection
		Opens or closes a section depending on its state and the options of the Accordion.
		
		Argumetns:
		iToToggle - (integer) the index of the section to open or close
	*/
	toggleSection: function(iToToggle){
		//let's open an object, or close it, depending on it's state
		//now, if the index to toggle isn't the previous click
		//or we're going to allow items to be closed (so that all of them are closed
		//or we're allowing more than one item to be open at a time, continue
		//otherwise, we're looking at an item that was just clicked, and it should already be open
		if(iToToggle != this.previousClick || this.options.alwaysHide || this.options.allowMultipleOpen) {
			//save the previous click
			this.previousClick = iToToggle;
			var objObjs = {};
			var err = false;
			var madeInactive = false;
			//go through each element
			this.elements.each(function(el, i){
				var update = false;
				//set up it's now state
				this.now[i] = this.now[i] || {};
				//if the element is the one clicked
				if(i==iToToggle){
					//if the element is visible, hide it if we allow alwaysHide or multiple
					if (this.elementsVisible[i] && (this.options.allowMultipleOpen || this.options.alwaysHide)){
						//if ! wait and timer
						if(!(this.options.wait && this.timer)) {
							//hide it
							update = true;
							this.hideThis(i);
						} else {
							this.previousClick = 'nan';
							err = true;
						}
					} else if(!this.elementsVisible[i]){
					//else if hidden, show it
						//if ! wait and timer
						if(!(this.options.wait && this.timer)) {
							//show it
							update = true;
							this.showThis(i);
						} else {
							this.previousClick = 'nan';
							err = true;
						}
					}
				} else if(this.elementsVisible[i] && !this.options.allowMultipleOpen) {
				//else (not clicked) if it's visible, hide it, unless we allow multiple open
					//if ! wait and timer
					if(!(this.options.wait && this.timer)) {
						//hide it
						update = true;
						this.hideThis(i);
					} else {
						this.previousClick = 'nan';
						err = true;
					}
				} //else it's not clicked, it's not open, so leave it alone because we allow multiples
				//set up the effect instructions
				if(update) objObjs[i] = Object.extend(this.h, Object.extend(this.o, this.w));
			}, this);
			//if there's an error, just stop
			if (err) return;
			//if we didn't inactivate anything, call the activate function on the element we clicked
			if (!madeInactive) this.options.onActive.call(this, this.togglers[iToToggle], iToToggle);
			//execute the background call on all the others
			this.togglers.each(function(tog, i){
				if (!this.elementsVisible[i]) this.options.onBackground.call(this, tog, i);
			}, this);
			//execute the custom function, which resizes everything.
			return this.custom(objObjs);
		}
	},
/*	Property: showSection
		Opens a section of the accordion if it's not open already.
		
		Arguments:
		i - (integer) the index of the section to show
		useFx - (boolean) open it immediately (false) or slide it open using the effects (true);  defaults to false;
	*/
	showSection: function(i, useFx){
		if($pick(useFx, false)) {
			if (!this.elementsVisible[i]) this.toggleSection(i);
		} else {
			this.setSectionStyle(i,$(this.elements[i]).scrollWidth, $(this.elements[i]).scrollHeight, 1);
			this.elementsVisible[i] = true;
			return true;
		}
	},
/*	Property: hideSection
		Closes a section of the accordion if it's not closed already.
		
		Arguments:
		i - (integer) the index of the section to hide
		useFx - (boolean) close it immediately (false) or slide it closed using the effects (true);  defaults to false;
	*/
	hideSection: function(i, useFx){
		if($pick(useFx, false)) {
			if (this.elementsVisible[i]) this.toggleSection(i);
		} else {
			this.setSectionStyle(i,0,0,0);
			this.elementsVisible[i] = false;
			return true;
		}
	},
	//internal function; sets a section (i) to the width (w), height (h), and opacity (o) passed in
	setSectionStyle: function(i,w,h,o){ 
			if (this.options.opacity) $(this.elements[i]).setOpacity(o);
			if (this.options.height) $(this.elements[i]).setStyle('height',h+'px');
			if (this.options.width) $(this.elements[i]).setStyle('width',w+'px');
	},
/*	Property: showAll
		Opens all the elements in the accordion immediately; used on startup	*/
	showAll: function(){
		if(this.options.allowMultipleOpen){
			this.elements.each(function(el,idx){
					this.showSection(idx, false);
			}, this);
		}
	},
/*	Property: hideAll
		Closes all the elements in the accordion immediately; used on startup	*/
	hideAll: function(useFx){
		if(this.options.allowMultipleOpen){
			this.elements.each(function(el,idx){
				this.hideSection(idx, false);
			}, this);
		}
	},
/*	Property: openSection
		Opens specific sections of the accordion immediately; used on startup.
		
		Arguments:
		sections - array of indexes to open.
	*/
	openSections: function(sections) {
		if(this.options.allowMultipleOpen){
			this.elements.each(function(el,idx){
				if(sections.test(idx)) this.showSection(idx, false);
				else this.hideSection(idx, false);
			}, this);
		}
	}
});
/* do not edit below this line */   

/* Section: Change Log 

$Source: /cvsroot/tikiwiki/tiki/lib/mootools/extensions/modules/multiple.open.accordion.js,v $
$Log: not supported by cvs2svn $
Revision 1.2  2007/01/26 05:53:47  newtona
syntax update for mootools 1.0

Revision 1.1  2007/01/22 21:59:03  newtona
moved from fx.multiple.open.accordion.js

Revision 1.1  2007/01/09 02:39:35  newtona
renamed addons directory to "common" directory

Revision 1.5  2006/12/06 20:14:59  newtona
carousel - improved performance, changed some syntax, actually deployed into usage and tested
cnet.nav.accordion - improved css selectors for time
multiple accordion - fixed a typo
dbug.js - added load timers
element.cnet.js - changed syntax to utilize mootools more effectively
function.cnet.js - equated $set to $pick in preparation for mootools v1

Revision 1.4  2006/11/06 19:19:31  newtona
fixed a bug and removed some dbug.log statements

Revision 1.3  2006/11/04 01:35:27  newtona
removing a dbug line

Revision 1.2  2006/11/04 00:53:45  newtona
no change

Revision 1.1  2006/11/02 21:28:08  newtona
checking in for the first time.


*/
