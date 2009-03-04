/*
 * Ext JS Library 2.0
 * Copyright(c) 2006-2007, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://extjs.com/license
 */

Ext.BLANK_IMAGE_URL = path + '/javascripts/extjs/resources/images/default/s.gif';

var Chooser = function(config){
	this.config = config;
}

Chooser.prototype = {
    // cache data by image name for easy lookup
    lookup : {},
    path: default_path,
    node: {},
    cancel: function () {
		var callback = this.callback;
		this.win.hide(this.animateTarget, function(){
            if (callback){
				callback({});
			}
		});
	},
	show : function(el, url, callback){
		if (!this.win){
					
			this.treeloader = new Ext.tree.TreeLoader({
				url: url//'?controller=file&action=get&cmd=get&format=ajax'
				, requestMethod: 'post'
			});
			this.treeloader.baseParams.cmd = 'get';
			this.treeloader.baseParams.folder = 1;
	
			// do not rely on node.id attribute send path instead
			this.treeloader.on({
				beforeload:{
					scope: this
					, fn: function(loader, node) {
						loader.baseParams.path = node.id;
					}
				}
			});
			
			this.tree = new Ext.tree.TreePanel({
				root: new Ext.tree.AsyncTreeNode({text:lang.root, path: default_path, id: default_path, allowDrag:false}),
				loader: this.treeloader
			});
			
			this.tree.on('click', function(node, e) {
						this.node = node;
						this.loadPath(node.id);
					}, this);
			this.tree.on('dblclick', this.doCallback, this);
		    
			var cfg = {
				//renderTo: document.body,
		    	title: lang.title,
		    	id: 'img-chooser-dlg',
		    	xtype: 'panel',
				border: true,
				closable: true,
				draggable: false,
				expandOnShow: true,
				resizable: false, 
				items:[{
					id: 'img-tree-panel',
					split: true,
					autoScroll: true,
	                cmargins: {top:2,bottom:2,right:2,left:2},
	                fitToFrame: true,
	                closable: false,
	                items: this.tree
					}],
				buttons: [{
					id: 'ok-btn',
					text: lang.SelectDirectory,
					handler: this.doCallback,
					scope: this
				},{
					text: lang.cancel,
					handler: this.cancel,
					scope: this
				}],
				keys: {
					key: 27, // Esc key
					handler: this.cancel,
					scope: this
				}
			};
			Ext.apply(cfg, this.config);
		    this.win = new Ext.Window(cfg);
			this.win.on('close', this.cancel, this);
		}
		
		this.loadPath('/');
	    this.win.show(el);
		this.win.setPosition(0, 0);
		this.win.fitContainer();
		this.callback = callback;
		this.animateTarget = el;
	},
	
	reset : function(){
		if (this.win.rendered){
		}
	},
	
	loadPath : function(path){
		this.path = path;
		this.reset();
		this.tree.selectPath( default_path+'root' + path, 'text', function (success, node) { if (success) node.expand() });
	},
	
	doCallback : function(node, e){
        var selNode = this.tree.getSelectionModel().getSelectedNode();
        var callback = this.callback;
		this.win.hide(this.animateTarget, function(){
            if (selNode.attributes && callback){
				callback(selNode.attributes);
			}
		});
    },
 	
	onLoadException : function(v,o){
	    this.view.getEl().update('<div style="padding:10px;">'+lang.errorLoading+'</div>'); 
	}
};