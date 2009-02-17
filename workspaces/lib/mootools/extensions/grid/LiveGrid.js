/**
 * LiveGrid - LiveGrid for everything
 * 
 * Scrolling datagrid connected to an Ajax backend.
 * Scrollbars are navigation.
 * 
 * Inspired by ...
 *  ... Rico LiveGrid
 * 
 * @version		beta
 * 
 * @license		MIT-style license
 * @author		Harald Kirschner <mail [at] digitarald.de>
 * @copyright	Author
 */
var LiveGrid = new Class({

	options: {
		count: false,
		scroller: false,
		body: false,
		columns: false,
		url: null,
		rowClass: 'row',
		responseHandler: null,
		ajaxOptions: {},
		scrollFx: true,
		onRequest: Class.empty,
		onComplete: Class.empty,
		onPopulate: Class.empty,
		scrollOptions: {},
		requestVars: {
			offset: 'offset',
			length: 'length'},
		ajaxData: {},
		reqLength: 'length',
		emptyRow: new Element('div')
	},

	initialize: function(el, options) {
		this.element = $(el);
		this.setOptions(options);

		this.scroller = $(this.options.scroller) || this.element;
		this.body = $(this.options.body) || this.element;
		this.scrollFx = new Fx.Scroll(this.scroller, this.options.scrollOptions);
		this.scrollFx.addEvent('onStart', this.checkTimeout.pass([true], this));
		this.scrollFx.addEvent('onComplete', this.checkTimeout.pass([false], this));
		this.prepare();
		this.reset();
		this.scroller.addEvent('scroll', this.onScrollEvt.bind(this));
	},

	getRows: function() {
		return this.body.getChildren();
	},

	refresh: function() {
		if (this.rows){
			this.rows.length = 0;
			this.getRows().destroy();
		}
		this.prepare();
		this.reset();
	},

	prepare: function() {
		var rows = this.getRows();
		this.count = this.count || this.options.count || rows.length;
		for (var i = 0, j = this.count; i < j; i++) {
			(rows[i] || this.clearRow(this.options.emptyRow.clone().inject(this.body), true))
				.addClass(this.options.rowClass).addClass(this.options.rowClass + '-' + ((i % 2) ? 'odd' : 'even'))
				.populated = !!(rows[i]);
		}
		this.rows = this.getRows();
		this.resetCalcs();
	},

	reset: function() {
		this.first = 0;
		this.populated = 0;
		this.checkScroll();
	},

	resetCalcs: function() {
		this.rowHeight = this.rows[0].offsetHeight;
		this.columns = (this.count * this.rowHeight) / this.scroller.scrollHeight;
		this.perPage = Math.ceil(this.scroller.offsetHeight / this.rowHeight * this.columns);
		this.pageChecks = {up: - this.perPage, down: 2 * this.perPage};
	},

	checkTimeout: function(state) {
		if (state === false) {
			this.timeout = $clear(this.timeout);
			this.onScrollEvt();
		} else this.timeout = (state === true) ? true : this.checkTimeout.delay(state, this);
	},

	onScrollEvt: function() {
		if (this.timeout) return;
		$clear(this.scrollTimer);
		var diff = (this.scroller.scrollTop - this.lastCheck) / this.rowHeight;
		var time = 100;
		if (diff < this.pageChecks.up / 2 || diff > this.pageChecks.down / 2) {
			this.lastCheck = this.scroller.scrollTop;
			time = 5;
		};
		this.scrollTimer = this.checkScroll.delay(time, this);
	},

	checkScroll: function() {
		this.lastCheck = this.scroller.scrollTop;
		this.first = Math.ceil(Math.floor((((this.lastCheck + this.rowHeight / 2) / this.rowHeight) * this.columns) / this.columns) * this.columns);
		this.page = Math.ceil(this.first / this.perPage) + 1;
		var start = this.first + this.pageChecks.up;
		var length = this.pageChecks.down - this.pageChecks.up;
		if (start < 0) {
			length += start;
			start = 0;
		}
		if (start + length > this.rows.length) length = null;
		var rows = this.rows.copy(start, length).filter(function(row) {
			return (!row.populated && !row.requested);
		});
		this.fireEvent('onScroll', [this.first + 1, Math.min(this.first + this.perPage, this.count)]);
		if (rows.length) this.request(rows);
	},

	request: function(rows) {
		var offset = this.rows.indexOf(rows[0]);
		var length = rows.length;
		this.setRowState(offset, length, true);
		if (!this.xhr) {
			this.xhr = new AjaxQueue(this.options.url, {
				ajaxOptions: this.options.ajaxOptions
			}).addEvents({
				onSuccess: (this.options.responseHandler || this.responseHandler).bind(this),
				onFailure: this.onFailure.bind(this),
				onComplete: this.onComplete.bind(this)
			});
		};
		var data = $merge(this.options.ajaxData);
		data[this.options.requestVars.offset] = offset;
		data[this.options.requestVars.length] = length;
		this.xhr.request(data);
		this.fireEvent('onRequest', [this.xhr]);
	},

	responseHandler: function(text, xml, data) {
		data.offset = data[this.options.requestVars.offset];
		data.length = data[this.options.requestVars.length];
		this.populate(Json.decode(text), data.offset);
	},

	onFailure: function(data) {
		this.setRowState(data.offset, data.length, false);
	},

	onComplete: function() {
		this.fireEvent('onComplete', [this.xhr]);
	},

	setRowState: function(offset, length, state) {
		for (var i = offset, j = offset + length; i < j; i++) this.rows[i].requested = null;
	},

	populate: function(rows, offset) {
		for (var i = 0, j = rows.length; i < j; i++) {
			var index = offset + i;
			(this.options.populateRow || this.populateRow).call(this, this.rows[index], rows[i], index).populated = index;
		}
		this.fireEvent('onPopulate', [rows, offset]);
		this.populated += rows.length;
	},

	populateRow: function(row, html) {
		return row.setHTML(html);
	},

	scrollTo: function(row) {
		if (!(row = this.rows[row])) return;
		this.scrollFx.scrollTo(0, row.offsetTop - this.scroller.getTop());
	},

	scrollToPage: function(page) {
		this.scrollTo((page - 1) * this.perPage);
	},

	scrollBy: function(dir) {
		this.scrollTo(Math.round(this.first + dir * this.columns));
	},

	scrollByPage: function(dir) {
		this.scrollToPage(this.page + dir);
	},

	scrollComplete: function(dir) {
		this.scrollTo((dir == 1) ? (this.count - 1) : 0);
	},

	clearRow: function(el, init) {
		el.populated = el.requested = null;
		return (init) ? el : el.empty().setHTML('&nbsp;');
	}

});

LiveGrid.implement(new Events, new Options);