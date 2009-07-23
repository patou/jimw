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
						if (this.namefield) {
							this.namefield.setValue(node.text);
						}
						if (this.newdirfield) {
							this.newdirfield.setValue(node.id + "/");
						}
						this.loadPath(node.id);
					}, this);
			this.tree.on('dblclick', this.doCallback, this);
		    
			var cfg = {
				renderTo: document.body,
		    	title: lang.title,
		    	id: 'img-chooser-dlg',
		    	xtype: 'panel',
				border: true,
				closable: true,
				draggable: false,
				expandOnShow: true,
				resizable: false,
				autoScroll: true,
				width: '100%',
				heigth: '100%',
				
				items:[{
					id: 'img-tree-panel',
					split: true,
					//autoScroll: true,
	                cmargins: {top:2,bottom:2,right:2,left:2},
	                //fitToFrame: true,
	                overflow:'auto',
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
			
			if (this.config.namefield) {
				this.namefield = new Ext.form.TextField();
				this.namefield.on('keyup', function (field, e) {
					if (e.getKey() == Ext.EventObject.ENTER) {
						this.doCallback();
					}
				}, this);
					cfg.bbar = new Ext.Toolbar({
				    width: '100%',
				    height: 'auto',
				    items: [
						{
							id:'img-label-name',
							xtype: 'label',
							text: lang.labelName
						},this.namefield
				    ]
				});
			}
			if (this.config.createdir) {
				this.newdirfield = new Ext.form.TextField();
				this.newdirfield.on('keyup', function (field, e) {
					if (e.getKey() == Ext.EventObject.ENTER) {
						this.createDir();
					}
				}, this);
					cfg.bbar = new Ext.Toolbar({
				    width: '100%',
				    height: 'auto',
				    items: [
						{
							id:'img-label-name',
							xtype: 'label',
							text: lang.labelNewdir
						}
						,this.newdirfield
						,{
							xtype: 'button'
							,text: lang.create
							,iconCls: 'icon-newdir'
							,handler: this.createDir
							,scope: this
						}
				    ]
				});
			}
			Ext.apply(cfg, this.config);
		    this.win = new Ext.Window(cfg);
			this.win.on('close', this.cancel, this);
		}
		
		this.loadPath(default_path);
		this.win.toggleMaximize();
		this.win.show(el);
		this.win.setPosition(0, 0);
		//this.win.fitContainer();
		this.callback = callback;
		this.animateTarget = el;
	},
	
	reset : function(){
		
	},
	
	loadPath : function(path){
		this.path = path;
		this.reset();
		this.tree.selectPath( default_path+'root' + path, 'text', function (success, node) { if (success) node.expand() });
	},
	
	createDir: function() {
		this.dir = this.newdirfield.getValue();
		var options = {
			 url: newdirUrl
			,method: 'POST'
			,scope: this
			,callback: this.cmdCallbackNewdir
			,params:{
				 cmd:'newdir'
				,dir:this.dir
			}
		};
		Ext.Ajax.request(options);	
	},
	
	cmdCallbackNewdir: function (options, bSuccess, response) {
        var i, o, node;
        var showMsg = true;
        if (true === bSuccess) {
            o = Ext.decode(response.responseText);
            if (true === o.success) {
                Ext.Msg.alert('OK', lang.successNewdirText);
                var selNode = this.tree.getSelectionModel().getSelectedNode();
                selNode.reload();
                this.loadPath(this.dir);
            } else {
                    Ext.Msg.alert(lang.errorNewdirText, o.error);
            }
        }
    },
	
	doCallback : function(node, e){
        var selNode = this.tree.getSelectionModel().getSelectedNode();
        var callback = this.callback;
        var name = this.namefield ? this.namefield.getValue() : '';
		this.win.hide(this.animateTarget, function(){
            if (selNode.attributes && callback){
            	var node = selNode.attributes;
            	node.name = name;
				callback(node);
			}
		});
    },
 	
	onLoadException : function(v,o){
	    this.view.getEl().update('<div style="padding:10px;">'+lang.errorLoading+'</div>'); 
	}
};