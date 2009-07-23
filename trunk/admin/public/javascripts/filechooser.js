/*
 * Ext JS Library 2.0
 * Copyright(c) 2006-2007, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://extjs.com/license
 */

Ext.BLANK_IMAGE_URL = path + '/javascripts/extjs/resources/images/default/s.gif';

var ImageChooser = function(config){
	this.config = config;
}

ImageChooser.prototype = {
    // cache data by image name for easy lookup
    lookup : {},
    path: default_path,
    cancel: function () {
		var callback = this.callback;
		this.win.hide(this.animateTarget, function(){
            if(callback){
				callback('');
			}
		});
	},
	show : function(el, callback){
		if(!this.win){
			this.initTemplates();
			
			this.store = new Ext.data.JsonStore({
			    url: basename + '/?controller=file&action=list&format=ajax',
			    root: 'Files',
				totalProperty: 'FilesCount',
                id: 'path',
			    fields: [
			        {name: 'name', mapping: 'name'},
                	{name: 'path', mapping: 'path'},
                	{name: 'iconCls', mapping: 'iconCls'},
                	{name: 'edit', mapping: 'edit'},
					{name: 'url', mapping: 'url'},
					{name: 'thumb', mapping: 'thumb'},
                	{name: 'size', mapping: 'size', type: 'int'},
                	{name: 'lastChange', mapping: 'lastChange'/*, type: 'date', dateFormat: 'D M  j h:i:s Y'*/}
			    ],
			    listeners: {
			    	'load': {fn:function(){ this.view.select(0); }, scope:this, single:true}
			    }
			});
			
			
			var formatSize = function(data){
		        if(data.size < 1024) {
		            return data.size + " bytes";
		        } else {
		            return (Math.round(((data.size*10) / 1024))/10) + " KB";
		        }
		    };
			
			var formatData = function(data){
		    	data.shortName = data.name.ellipse(15);
		    	data.sizeString = formatSize(data);
		    	data.dateString = data.lastChange;//new Date(data.lastChange).format(lang.dateFormat);
				if (data.thumb == '') {
					if (data.iconCls == 'file-jpg' || data.iconCls == 'file-jpeg' || data.iconCls == 'file-gif' || data.iconCls == 'file-png')
						data.thumbnail = data.url;
					else
						data.thumbnail = Ext.BLANK_IMAGE_URL;
				}
				else 
					data.thumbnail = data.thumb;
		    	this.lookup[data.name] = data;
		    	return data;
		    };
			
		    this.view = new Ext.DataView({
				tpl: this.thumbTemplate,
				singleSelect: true,
				overClass:'x-view-over',
				itemSelector: 'div.thumb-wrap',
				emptyText : '<div style="padding:10px;">'+lang.emptyText+'</div>',
				store: this.store,
				listeners: {
					'selectionchange': {fn:this.showDetails, scope:this, buffer:100},
					'dblclick'       : {fn:this.onDblClick, scope:this},
					'loadexception'  : {fn:this.onLoadException, scope:this},
					'beforeselect'   : {fn:function(view){
				        return view.store.getRange().length > 0;
				    }}
				},
				prepareData: formatData.createDelegate(this)
			});
			
			this.treeloader = new Ext.tree.TreeLoader({
				url: basename + '/default/file/get.ajax'//'?controller=file&action=get&cmd=get&format=ajax'
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
						this.loadPath(node.id);
					}, this);
		    
			var cfg = {
				renderTo: document.body,
		    	title: lang.title,
		    	id: 'img-chooser-dlg',
		    	layout: 'border',
				border: true,
				closable: true,
				draggable: false,
				expandOnShow: true,
				resizable: false, 
				items:[{
					id: 'img-tree-panel',
					region: 'west',
					split: true,
					width: 150,
					minWidth: 150,
					maxWidth: 250,
					xtype: 'panel',
					titlebar: true,
	                collapsible: true,
	                animate: true,
	                useShim: true,
	                autoScroll: true,
	                cmargins: {top:2,bottom:2,right:2,left:2},
	                fitToFrame: true,
	                closable: false,
	                title: lang.folderTitle,
					items: this.tree
					},
					{
					id: 'img-chooser-view',
					region: 'center',
					autoScroll: true,
					items: this.view
                    
				},{
					id: 'img-detail-panel',
					region: 'east',
					split: true,
					width: 150,
					minWidth: 150,
					maxWidth: 250
				}],
				tbar:[{
					xtype: 'button',
					id: 'return_root',
                	text: lang.root,
					listeners: {
						'click': {
							scope: this,
							fn: function() { this.loadPath(default_path);}
						}
					}
                },{
                	xtype: 'tbtext',
                	text: lang.filter
                },{
                	xtype: 'textfield',
                	id: 'filter',
                	selectOnFocus: true,
                	width: 100,
                	listeners: {
                		'render': {fn:function(){
					    	Ext.getCmp('filter').getEl().on('keyup', function(){
					    		this.filter();
					    	}, this, {buffer:500});
                		}, scope:this}
                	}
                }, ' ', '-', {
                	xtype: 'tbtext',
                	text: lang.sortBy
                }, {
                	id: 'sortSelect',
                	xtype: 'combo',
			        typeAhead: true,
			        triggerAction: 'all',
			        width: 100,
			        editable: false,
			        mode: 'local',
			        displayField: 'desc',
			        valueField: 'name',
			        lazyInit: false,
			        value: 'name',
			        store: new Ext.data.SimpleStore({
				        fields: ['name', 'desc'],
				        data : [['name', lang.name],['size', lang.fileSize],['lastChange', lang.lastModified]]
				    }),
				    listeners: {
						'select': {fn:this.sortImages, scope:this}
				    }
			    },' ','-',{
					xtype: 'button',
					id: 'upload',
       				text: lang.Upload,
       				iconCls: 'icon-upload',
       				listeners: {
						'click': {
							scope: this,
							fn: function() { this.uploadFile();}
						}
					}
                },{
					xtype: 'button',
					id: 'upload',
       				text: lang.Newdir,
       				iconCls: 'icon-newdir',
       				listeners: {
						'click': {
							scope: this,
							fn: function() { this.newdirFile();}
						}
					}
                }],
				buttons: [{
					id: 'ok-btn-thumbnail',
					text: lang.OKThumbnail,
					handler: this.doCallbackThumbnail,
					scope: this
				},{
					id: 'ok-btn',
					text: lang.OK,
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
				this.namefield.on('specialkey', this.doCallbackEnter, this);
				cfg.bbar = new Ext.Toolbar({
			    width: '100%',
			    height: 'auto',
			    items: [
					{
						id:'img-label-name',
						xtype: 'label',
						width: '100%',
						text: lang.labelName
					},this.namefield
			    ]
				});
			}
			Ext.apply(cfg, this.config);
		    this.win = new Ext.Window(cfg);
			this.win.on('close', this.cancel, this);
		}
		this.loadPath('/');
		this.win.toggleMaximize();
		this.win.show(el);
		this.win.setPosition(0, 0);
		//this.win.fitContainer();
		this.callback = callback;
		this.animateTarget = el;
	},
	
	initTemplates : function(){
		this.thumbTemplate = new Ext.XTemplate(
			'<tpl for=".">',
				'<div class="thumb-wrap" id="{name}">',
				'<div class="thumb {iconCls}"><img class="x-view-thumb-icon" src="{thumbnail}" title="{name}"></div>',
				'<span>{shortName}</span></div>',
			'</tpl>'
		);
		this.thumbTemplate.compile();
		
		this.detailsTemplate = new Ext.XTemplate(
			'<div class="details">',
				'<tpl for=".">',
					'',
					'<div class="thumb-wrap" id="{name}">',
					'<div class="thumb {iconCls}"><img class="x-view-thumb-icon" src="{thumbnail}" title="{name}" width="64"></div>',
					'<span>{shortName}</span></div>',
					'<div class="details-info">',
					'<b>', lang.name,'</b>',
					'<span>{name}</span>',
					'<b>', lang.size, '</b>',
					'<span>{sizeString}</span>',
					'<b>', lang.lastModified, '</b>',
					'<span>{dateString}</span></div>',
				'</tpl>',
			'</div>'
		);
		this.detailsTemplate.compile();
	},
	
	showDetails : function(){
	    var selNode = this.view.getSelectedNodes();
	    var detailEl = Ext.getCmp('img-detail-panel').body;
		if (selNode && selNode.length > 0) {
			selNode = selNode[0];
			Ext.getCmp('ok-btn').enable();
			var data = this.lookup[selNode.id];
			if (this.namefield) {
				this.namefield.setValue(data.name);
			}
			if (data.thumb.length > 0)
				Ext.getCmp('ok-btn-thumbnail').show();
			else
				Ext.getCmp('ok-btn-thumbnail').hide();
            detailEl.hide();
            this.detailsTemplate.overwrite(detailEl, data);
            detailEl.slideIn('l', {stopFx:true,duration:0.2});
		} else {
		    detailEl.update('');
		    Ext.getCmp('ok-btn-thumbnail').hide();
		}
	},
	
	filter : function(){
		var filter = Ext.getCmp('filter');
		this.view.store.filter('name', filter.getValue());
		this.view.select(0);
	},
	
	sortImages : function(){
		var v = Ext.getCmp('sortSelect').getValue();
    	this.view.store.sort(v, v == 'name' ? 'asc' : 'desc');
    	this.view.select(0);
    },
	
	reset : function(){
		if(this.win.rendered){
			Ext.getCmp('filter').reset();
			this.view.getEl().dom.scrollTop = 0;
		}
	    this.view.store.clearFilter();
		this.view.select(0);
	},

	loadPath : function(path){
		this.path = path;
		this.store.load({params : {path: path}});
		this.reset();
		this.tree.selectPath( default_path+'root' + path, 'text', function (success, node) { if (success) node.expand() });
	},

	onDblClick : function(){
		var selNode = this.view.getSelectedNodes()[0];
		var data = this.lookup[selNode.id];
		if (data.iconCls != 'folder')
			this.doCallback();
		else
			this.loadPath(data.path);
	},
	
	doCallbackEnter : function(field, e){
		var selNode = this.view.getSelectedNodes()[0];
		var data = this.lookup[selNode.id];
		if (data.iconCls != 'folder' && e.getKey() == Ext.EventObject.ENTER)
			this.doCallback();
    },
	
	doCallback : function(){
        var selNode = this.view.getSelectedNodes()[0];
		var callback = this.callback;
		var lookup = this.lookup;
		var name = this.namefield ? this.namefield.getValue() : '';
		this.win.hide(this.animateTarget, function(){
            if (selNode && callback){
				var data = lookup[selNode.id];
				name = name ? name : data.name;
				callback(data.url, name);
			}
		});
    },
    
    doCallbackThumbnail : function(){
        var selNode = this.view.getSelectedNodes()[0];
		var callback = this.callback;
		var lookup = this.lookup;
		var name = this.namefield ? this.namefield.getValue() : '';
		this.win.hide(this.animateTarget, function(){
            if (selNode && callback){
				var data = lookup[selNode.id];
				name = name ? name : data.name;
				if (data.thumb.length > 0)
					callback(data.thumb, name);
			}
		});
    },
	
	onLoadException : function(v,o){
	    this.view.getEl().update('<div style="padding:10px;">'+lang.errorLoading+'</div>'); 
	},
	
	uploadFile: function() {
		var winUpload = new Ext.Window({
	         width:180
			,minWidth:165
	        ,id:'winUpload'
	        ,height:220
			,minHeight:200
	//		,stateful:false
	        ,layout: 'fit'
	        ,border: false
	        ,closable: true
	        ,title:'UploadPanel'
			,iconCls:'icon-upload'
			,items:[{
				  xtype:'uploadpanel'
				 ,buttonsAt:'tbar'
				 ,id:'uppanel'
				 ,url: uploadUrl
				 ,path:this.path
				 ,maxFileSize:1048576
	//			 ,enableProgress:false
	//			 ,singleUpload:true
			}]
	    });
	    winUpload.show.defer(500, winUpload);
	    winUpload.on('close', this.refresh, this);
	},
	newdirFile: function() {
		// set focus to no button to avoid accidental deletions
        var msgdlg = Ext.Msg.getDialog();
        msgdlg.defaultButton = msgdlg.buttons[2];//.focus();
        Ext.Msg.prompt(lang.newdirText
                , lang.newdirText
                , function(response, newname) {
                        var conn;
                        // do nothing if answer is not yes
                        if ('ok' !== response) {
                                return;
                        }
                        // answer is yes
                        else {
                               this.newdir(this.path + '/' + newname);
                        }
                }
                , this
        );
	},
	newdir: function(dir) {
		var options = {
			 url: newdirUrl
			,method: this.tree.method
			,scope: this
			,callback: this.cmdCallbackNewdir
			,params:{
				 cmd:'newdir'
				,dir:dir
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
                this.loadPath(this.path);
            } else {
                    Ext.Msg.alert(lang.errorNewdirText, o.error);
            }
        }
    },
	refresh: function() {
		this.loadPath(this.path);
	}
};

String.prototype.ellipse = function(maxLength){
    if(this.length > maxLength){
        return this.substr(0, maxLength-3) + '...';
    }
    return this;
};