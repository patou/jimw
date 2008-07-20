// set blank image to local file
Ext.BLANK_IMAGE_URL = path + '/javascripts/extjs/resources/images/default/s.gif';

Ext.override(Ext.grid.GridPanel, {
        getDragDropText: function(){
            var sel = this.selModel.getSelected();
            if (sel)
                return sel.get('name');
            else
                return 'No selected row';
        }
    });

Ext.override(Ext.ux.FileTreePanel, {
	onNodeDragOver: function(e) {
             if(e.target.disabled || e.dropNode && e.dropNode.parentNode === e.target.parentNode && e.target.isLeaf()) {
		 e.cancel = true;
             }
        },
	    onBeforeNodeDrop: function (e) {
            var s = e.dropNode;
            var d = e.target.leaf ? e.target.parentNode : e.target;
            var oldName = '';
            var newName = '';
            if (!s) {
                var elt = e.data.selections[0];
                s = this.getNodeById(elt.id);
                e.dropNode = s;
            }
            if (s) {
                if (s.parentNode === d) {
                    return false;
                }
                if (this.hasChild(d, s.text) && !e.confirmed) {
                    this.confirmOverwrite(s.text, function () {e.confirmed = true;this.onBeforeNodeDrop(e);});
                    return false;
                }
                e.confirmed = false;
                e.oldParent = s.parentNode;
                oldName = this.getPath(s);
                newName = this.getPath(d) + "/" + s.text;
            }
            else {
                return false;
                var elt = e.data.selections[0];
            }

            if (false === this.fireEvent("beforerename", this, s, oldName, newName)) {
                return false;
            }
            var options = {url:this.renameUrl || this.dataUrl, method:this.method, scope:this, callback:this.cmdCallback, node:s, oldParent:s.parentNode, e:e, params:{cmd:"rename", oldname:oldName, newname:newName}};
            var conn = (new Ext.data.Connection).request(options);
            return true;
        }
});

var Rged = function() {
    // All Panel
    //this.northPanel = null;
    //this.westPanel = null;
    //this.centerPanel = null;
    this.menu = null;
    this.tree = null;
    this.grid = null;
    this.textBox = null;
    this.ds = null;

    }

Rged.prototype =  {
    path: '',
	logoutText: 'Logout',
	foldersText: 'Folders',
	filesText: 'Fichiers',
	nameText: 'Name',
    sizeText: 'Size',
    lastUpdatedText: 'Last Update',
    editText: 'Edit',
    successText: 'Success',
    // Initialize the Tree
    init_tree: function() {

           // tree in the panel

	this.tree = new Ext.ux.FileTreePanel({
        el: 'tree'
		, animate: true
		, url: basename + '/default/file/get.ajax'
        , renameUrl: basename + '/default/file/rename.ajax'
        , deleteUrl: basename + '/default/file/delete.ajax'
        , downloadUrl: basename + '/default/file/download.ajax'
        , downloadText: 'Download'
        , newdirUrl: basename + '/default/file/newdir.ajax'
        , uploadUrl: basename + '/default/file/upload.ajax'
        , iconPath: path + '/images/icons/'
		, readOnly: false
		, containerScroll: true
		, enableSort: false
		, enableDD: true
        , ddGroup: 'TreeDD'
		, enableUpload: true
		, enableRename: true
		, enableDelete: true
        , enableDownload: true
		, enableNewDir: true
        , enableEdit: true
		, edit: true
        , height:'auto'
        , width:'auto'
		, maxFileSize: 1048575
		, hrefPrefix: '/public/'
		, rootPath:'/'
        , rootText:'root'
	});

	this.tree.render();
        this.tree.getRootNode().expand();
        this.tree.on('click', this.tree_onClick, this);
        this.tree.on('renamesuccess', function(tree, node, newname, oldname) { this.change_path(this.path)}, this);
        this.tree.on('beforeopen', this.tree_onDownload, this);
        this.tree.on('edit', this.tree_onEdit, this);
    },

    tree_onDownload: function(tree, node, mode) {
        window.location = basename + '?controller=file&action=download&file=' + node.id;
        return false;
    },
    
    tree_onEdit: function(tree, node) {
        this.editFile(node.id, node.text);
    },

    tree_onClick: function (node, elt) {
        var path = '/';
        if (node.isLeaf())
            path += this.tree.getPath(node.parentNode, 'path');
        else
            path += this.tree.getPath(node, 'path');
        this.load_path(path);
        node.select ();
    },

    // When the user press the key enter
    menu_onSpecialKey: function(field, e) {
       if (e.getKey() == e.ENTER) {
           var path = field.getValue ()
           this.load_path(path);
       }
    },

    // When the TextBox in the menu change
    menu_onChange: function(field, newval, oldval) {
       this.load_path(newval);
    },

    menu_onRename: function(o, e) {
        var sel = this.grid.selModel.getSelected();
        this.renameFile(sel);
    },
    menu_onDelete: function(o, e) {
        var sel = this.grid.selModel.getSelected();
        this.deleteFile(sel);
    },
    menu_onDownload: function(o, e) {
        var sel = this.grid.selModel.getSelected();
        this.downloadFile(sel);
    },
    menu_onEdit: function(o, e) {
        var sel = this.grid.selModel.getSelected();
        if (sel.get('edit') != 'none')
            this.editFile(sel.get('path'), sel.get('name'), sel.get('cls'));
    },
    menu_onNewdir: function(o, e) {
        this.newdirFile();
    },
    menu_onUpload: function(o, e) {
        this.uploadFile();
    },
    menu_onRefresh: function(o, e) {
        this.change_path(this.path);
    },

    menu_DropDelete: function(n, dd, e, data){
       if (e.selections)
           this.deleteFile(e.selections[0]);
       else
           this.tree.deleteNode(e.node);
   },

   menu_DropRename: function(n, dd, e, data){
       if (e.selections)
           this.renameFile(e.selections[0]);
   },
   
   menu_DropEdit: function(n, dd, e, data){
   	   var edit = e.selections ? e.selections[0].get('edit') : undefined;
       if (edit != undefined && edit != 'none')
           this.editFile(e.selections[0].get('path'), e.selections[0].get('name'), e.selections[0].get('cls'));
   },

   menu_DropDownload: function(n, dd, e, data){
       if (e.selections)
           this.downloadFile(e.selections[0]);
       else
           this.tree.openNode(e.node);
   },
    // Initialize the menu
    init_menu: function () {
       this.menu = new Ext.Toolbar()
       this.menu.render('menu')
       this.menu.addButton({
           id: 'rename',
           text: Ext.ux.FileTreeMenu.prototype.renameText,
           cls: 'x-btn-text-icon menu-rename',
           handler: this.menu_onRename,
           scope: this
           });
       this.menu.addButton({
           id: 'delete',
           text: Ext.ux.FileTreeMenu.prototype.deleteText,
            cls: 'x-btn-text-icon menu-delete',
            handler: this.menu_onDelete,
            scope: this});
       this.menu.addButton({
           id: 'download',
           text: Ext.ux.FileTreeMenu.prototype.openDwnldText,
           cls: 'x-btn-text-icon menu-download',
           handler: this.menu_onDownload,
           scope: this});
       this.menu.addButton({
           id: 'refresh',
           text: Ext.ux.FileTreeMenu.prototype.reloadText,
           cls: 'x-btn-text-icon menu-refresh',
           handler: this.menu_onRefresh,
           scope: this});
       this.menu.addButton({
           id: 'edit',
           text: this.editText,
           cls: 'x-btn-text-icon menu-edit',
           handler: this.menu_onEdit,
           scope: this});
       this.menu.addButton({
           id: 'newdir',
           text: Ext.ux.FileTreeMenu.prototype.newdirText,
           iconCls: Ext.ux.FileTreeMenu.prototype.newdirIconCls,
           handler: this.menu_onNewdir,
           scope: this});
       this.menu.addButton({
           id: 'upload',
           text: Ext.ux.FileTreeMenu.prototype.uploadFileText,
           iconCls: Ext.ux.FileTreeMenu.prototype.uploadIconCls,
           handler: this.menu_onUpload,
           scope: this});
       this.textBox = new Ext.form.TextField ({
           cls: 'rged-adress',
           width: 500});
       this.textBox.on('change', this.menu_onChange, this);
       this.textBox.on('specialkey', this.menu_onSpecialKey, this);
       this.menu.addField(this.textBox);
       this.menu.addFill ();
       this.menu.addButton({
           text: this.logoutText,
           cls: 'x-btn-text-icon menu-logout',
           handler: function(o, e) {
               window.location = basename + '/index/index';
           }
       });
    },

    // Initilize the global layout
    init_layout: function () {
            this.mainLayout = new Ext.Viewport({
                layout:'border',
                items: [
                {
                region: 'north',
                contentEl: 'menu',
                split:false,
                initialSize: 32,
                titlebar: false,
                fitToFrame: true,
                closable: false
                },
                {
                region: 'west',
                contentEl:'tree',
                split:true,
                width: 250,
                minWidth: 175,
                maxWidth: 400,
                titlebar: true,
                collapsible: true,
                animate: true,
                useShim: true,
                autoScroll: true,
                cmargins: {top:2,bottom:2,right:2,left:2},
                fitToFrame: true,
                closable: false,
                title: this.foldersText
            },
            {
                xtype:'tabpanel',
                region: 'center',
                border:false,
                titlebar: false,
                autoScroll:false,
                activeTab:0,
                tabPosition: 'top',
                closeOnTab: true,
                resizeTabs: true,
                fitToFrame: true,
                autoScroll: true,
                items: [this.grid]
            }
            ]
            });

    },

    // Initilize the Grid view
    init_grid: function () {
            // Grid Data Store
        this.ds = new Ext.data.Store({
            proxy: new Ext.data.HttpProxy({url: basename + '/default/file/list.ajax'}),
            reader: new Ext.data.JsonReader({
                root: 'Files',
                totalProperty: 'FilesCount',
                id: 'path'
            }, [
                {name: 'name', mapping: 'name'},
                {name: 'path', mapping: 'path'},
                {name: 'iconCls', mapping: 'iconCls'},
                {name: 'edit', mapping: 'edit'},
                {name: 'size', mapping: 'size', type: 'int'},
                {name: 'lastChange', mapping: 'lastChange', type: 'date', dateFormat: 'D M  j h:i:s Y'}
            ])
        });

        // Convert a file size in Kilo/Mega/Giga Octet
        function size(val){
            if(val < 1024){
                return Math.round( val) + ' b';
            } else  {
                val = Math.round( val / 1024);
                if(val < 1024){
                return val + ' Kb';
                }
                else {
                    val = Math.round( val / 1024);
                    if(val < 1024){
                        return val + ' Mb';
                    }
                    else {
                        return Math.round( val / 1024) + ' Gb';
                    }
                }
            }
            return val;
        }

        // Display the icon for file type
        function icon(val){
        	return '<img width="16" height="18" class="x-tree-node-icon '+val+'" unselectable="on" src="'+Ext.BLANK_IMAGE_URL+'"/>';
             //return '<div class="x-tree-node-el x-tree-node-leaf x-unselectable '+val+'" unselectable="on"><img width="16" height="18" class="x-tree-node-icon" src="'+Ext.BLANK_IMAGE_URL+'"/></div>';
        }
	// Column Model of the grid
//        var colModel = new Ext.grid.ColumnModel([
//                        {id: 'icon', header: '<img src="/images/icons/arrow_up.png" width="16" height="18"/>', width: 25, sortable: false, renderer: icon, dataIndex: 'cls', fixed : true},
//			{id:'name',header: "Name", width: 160, sortable: true, locked:false, dataIndex: 'name'
//                            , editor: new Ext.grid.GridEditor(new Ext.form.TextField({
//                                allowBlank: false}))},
//			{header: "Size", width: 75, sortable: true, renderer: size, dataIndex: 'size'},
//                        {header: "Last Updated", width: 85, sortable: true, renderer: Ext.util.Format.dateRenderer('m/d/Y'), dataIndex: 'lastChange'}
//		]);


        // create the Grid
        this.grid = new Ext.grid.GridPanel({
            title: this.filesText,
            closable:false,
            store: this.ds,
            columns: [
               {
                   id: 'icon',
                   header: '<img src="'+path+'/images/icons/arrow_up.png" width="16" height="18"/>',
                   width: 25, 
                   sortable: false, 
                   renderer: icon, 
                   dataIndex: 'iconCls',
                   locked:true,
                   resizable: false,
                   menuDisabled: true,
                   hideable:false,
                   fixed : true
               },
               {
                   id:'name',
                   header: this.nameText,
                   width: 160,
                   sortable: true,
                   locked:false,
                   dataIndex: 'name',
                   editor: new Ext.grid.GridEditor(new Ext.form.TextField({
                                allowBlank: false}))},
                {
                    header: this.sizeText,
                    width: 75,
                    sortable: true,
                    renderer: size,
                    dataIndex: 'size'},
                {
                    header: this.lastUpdatedText,
                    width: 85,
                    sortable: true,
                    renderer: Ext.util.Format.dateRenderer('m/d/Y'),
                    dataIndex: 'lastChange'

                }
            ],
            autoHeight:true,
            sm:  new Ext.grid.RowSelectionModel({singleSelect: true}),
            enableDragDrop : true,
            ddGroup: 'TreeDD',
            autoExpandColumn: 'name',
            loadMask: true,
            renderTo: 'grid'
        });

        //this.grid.render('grid');
        //this.grid.getSelectionModel().selectFirstRow();
        this.grid.on('celldblclick', this.grid_onCellDblClick, this);
        this.grid.on('cellclick', this.grid_onCellClick, this);
        this.grid.on('cellcontextmenu', this.grid_onCellContextMenu, this);
        this.grid.on('headerdblclick', this.grid_onHeaderClick, this);
        this.grid_installKeyMap();
    },

    grid_onCellDblClick: function( grid, rowIndex, columnIndex, e )
    {
        var rec = grid.getStore().getAt(rowIndex);
        var path = rec.get('path');
        var folder = rec.get('iconCls');
        if (folder == 'folder') {
            this.load_path(path);
        }
        //this.tree.expandPath(path.substrsub, 'path', function (success, node) { if (success) node.select()});
    },


    grid_onCellClick: function( grid, rowIndex, columnIndex, e )
    {
        var rec = grid.getStore().getAt(rowIndex);
        var p = "/root" + rec.get('path');
        if (p.substr(p.length - 1, 1) == '/')
            p = p.substr(0, p.length - 1)
        this.tree.selectPath(p, 'text', function (success, node) { if (success) node.expand() });
    },

    //When the user click on the arrox up in the first column
    grid_onHeaderClick: function( grid, columnIndex, e )
    {
        if (grid.getColumnModel().getColumnId(columnIndex) == 'icon') {
            var pos = this.path.lastIndexOf('/');
            if (pos >= 0) {
                var path = this.path.substr(0, pos);
                this.load_path(path);
            }
        }
    },
    // On right click
    grid_onCellContextMenu: function( grid, rowIndex, columnIndex, e )
    {
        e.stopEvent();
        e.preventDefault();

        if(!this.contextMenu) {
                this.contextMenu = new Ext.menu.Menu({
                        items: [
                                        // node name we're working with placeholder
                                  { id:'nodename', disabled:true, disabledClass:'', cls:'ux-ftm-nodename'}
                                , new Ext.menu.Separator({id:'sep-open'})
                                , {	id:'rename'
                                        , text:Ext.ux.FileTreeMenu.prototype.renameText + ' (F2)'
                                        , iconCls:Ext.ux.FileTreeMenu.prototype.renameIconCls
                                        , scope:this
                                        , handler:this.onContextMenuItem
                                }
                                , {	id:'edit'
                                        , text:this.editText
                                        , iconCls: 'icon-edit'
                                        , scope:this
                                        , handler:this.onContextMenuItem
                                }
                                , {	id:'delete'
                                        , text:Ext.ux.FileTreeMenu.prototype.deleteText + ' (' + this.tree.deleteKeyName + ')'
                                        , iconCls:Ext.ux.FileTreeMenu.prototype.deleteIconCls
                                        , scope:this
                                        , handler:this.onContextMenuItem
                                }
                                , {	id:'download'
                                        , text:Ext.ux.FileTreeMenu.prototype.openDwnldText + ' (Enter)'
                                        , iconCls:Ext.ux.FileTreeMenu.prototype.openDwnldIconCls
                                        , scope:this
                                        , handler:this.onContextMenuItem
                                }
                        ]
                });
        }
        this.grid.selModel.selectRow(rowIndex);
        var sel = this.grid.selModel.getSelected();
        // save current node to context menu and open submenu
        var menu = this.contextMenu;
        menu.item = sel;
        var itemEdit = menu.items.get('edit');
        var itemNodename = menu.items.get('nodename');
        if (sel) {
            
            var edit = sel.get('edit');
            itemEdit.setDisabled(!(edit && edit != 'none'));

            // set menu item text to node text
            
            itemNodename.setText(Ext.util.Format.ellipsis(sel.get('name'), 25));
            menu.items.get('delete').setDisabled(false);
            menu.items.get('rename').setDisabled(false);
            menu.items.get('download').setDisabled(false);
        }
        else {
            itemNodename.setText('');
            itemEdit.setDisabled(true);
            menu.items.get('delete').setDisabled(true);
            menu.items.get('rename').setDisabled(true);
            menu.items.get('download').setDisabled(true);
        }
        menu.showAt(menu.getEl().getAlignToXY(e.target, 'tl-tl?', [0, 18]));
        itemNodename.container.setStyle('opacity', 1);

    },
    // }}}
    // {{{
    /**
		* context menu item click handler
		* @param {MenuItem} item
		* @param {Event} e event
		*/
    onContextMenuItem: function(item, e) {

        // get node for before event
        var sel = item.parentMenu.item;
        var node = this.tree.getNodeById(sel.id);

        // menu item switch
        switch(item.id) {

                // {{{
                case 'rename':
                        this.renameFile(sel);
                break;
                // }}}
                // {{{
                case 'delete':
                        this.deleteFile(sel);
                break;
                // }}}
                // {{{
                case 'edit':
                        this.editFile(sel.get('path'), sel.get('name'), sel.get('cls'));
                break;
                // }}}
                // {{{
                case 'download':
                        this.downloadFile(sel);
                break;
                // }}}

         }; // end of switch(item.id)
    },

    renameFile: function(sel) {
        // set focus to no button to avoid accidental deletions
        var msgdlg = Ext.Msg.getDialog();
        msgdlg.defaultButton = msgdlg.buttons[2];//.focus();
        Ext.Msg.prompt(this.tree.renameText
                , Ext.ux.FileTreeMenu.prototype.renameText + ' <b>' + sel.get('name') + '</b> to ?'
                , function(response, newname) {
                        var conn;
                        // do nothing if answer is not yes
                        if ('ok' !== response) {
                                return;
                        }
                        // answer is yes
                        else {
                               this.rename(this.path + newname, sel.get('path'));
                        }
                }
                , this
        );
    },

    rename: function (newname, oldname) {
     // setup request options
        options = {
                url: this.tree.renameUrl || this.tree.url
                , method: this.tree.method
                , scope: this
                , callback: this.cmdCallback
                , params: {
                        cmd: 'rename'
                        , oldname: oldname
                        , newname: newname
                }
        };
        // send request
        conn = new Ext.data.Connection().request(options);
    },
    deleteFile: function(sel) {
        // display confirmation message
        // set focus to no button to avoid accidental deletions
        var msgdlg = Ext.Msg.getDialog();
        msgdlg.defaultButton = msgdlg.buttons[2];//.focus();
        Ext.Msg.confirm(this.tree.deleteText
                , this.tree.reallyWantText + ' ' + Ext.ux.FileTreeMenu.prototype.deleteText.toLowerCase() + ' <b>' + sel.get('name') + '</b>?'
                , function(response) {
                        var conn;
                        // do nothing if answer is not yes
                        if('yes' !== response) {
                                return;
                        }
                        // answer is yes
                        else {
                                // setup request options
                                options = {
                                        url: this.tree.deleteUrl || this.tree.dataUrl
                                        , method: this.tree.method
                                        , scope: this
                                        , callback: this.cmdCallback
                                        , params: {
                                                cmd: 'delete'
                                                , file: sel.get('path')
                                        }
                                };
                                // send request
                                conn = new Ext.data.Connection().request(options);
                        }
                }
                , this
        );
    },

    editFile: function(path, name, cls) {
        var tab = this.mainLayout.layout.center.panel;
        var href = basename + '?controller=file&action=edit&ext=ajax&file=' + path;
        if (!name)
            name = '';
        var TabId = cls;
        var tabEl = tab.getComponent(TabId);
        if(tabEl){
            tab.setActiveTab(tabEl);
        }else{
            var autoLoad = {url: href};
            var p = tab.add(new Ext.Panel({
                closable: true,
                autoScroll:true,
                id: TabId,
                title: name,
                //autoLoad: autoLoad,
                html: '<iframe width="100%" height="100%" src="'+href+'" style="border: 0;"></iframe>',
                iconCls: 'x-tree-node-icon '+ cls
            }));
            tab.setActiveTab(p);
        }
    },

	newdirFile: function() {
		// set focus to no button to avoid accidental deletions
        var msgdlg = Ext.Msg.getDialog();
        msgdlg.defaultButton = msgdlg.buttons[2];//.focus();
        Ext.Msg.prompt(this.tree.renameText
                , Ext.ux.FileTreeMenu.prototype.newdirText
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
			 url:this.tree.newdirUrl || this.tree.url
			,method:this.tree.method
			,scope:this
			,callback:this.cmdCallback
			,params:{
				 cmd:'newdir'
				,dir:dir
			}
		};
		Ext.Ajax.request(options);	
	},
	uploadFile: function() {
		var winUpload = new Ext.Window({
	         width:180
			,minWidth:165
	        ,id:'winUpload'
	        ,height:220
			,minHeight:200
	//		,stateful:false
	        ,layout:'fit'
	        ,border:false
	        ,closable:true
	        ,title:'UploadPanel'
			,iconCls:'icon-upload'
			,items:[{
				  xtype:'uploadpanel'
				 ,buttonsAt:'tbar'
				 ,id:'uppanel'
				 ,url:this.tree.uploadUrl
				 ,path:this.path
				 ,maxFileSize:1048576
	//			 ,enableProgress:false
	//			 ,singleUpload:true
			}]
	    });
	    winUpload.show.defer(500, winUpload);
	},
    downloadFile: function(sel) {
        //window.location = basename + '?controller=file&action=download&file=' + sel.get('path');
        
		// create hidden target iframe
		var id = Ext.id();
		var frame = document.createElement('iframe');
		frame.id = id;
		frame.name = id;
		frame.className = 'x-hidden';
		if(Ext.isIE) {
			frame.src = Ext.SSL_SECURE_URL;
		}

		document.body.appendChild(frame);

		if(Ext.isIE) {
			document.frames[id].name = id;
		}

		var form = Ext.DomHelper.append(document.body, {
			 tag:'form'
			,method:'post'
			,action:this.tree.downloadUrl || this.url
			,target:id
		});

		document.body.appendChild(form);

		var hidden;

		// append cmd to form
		hidden = document.createElement('input');
		hidden.type = 'hidden';
		hidden.name = 'cmd';
		hidden.value = 'download';
		form.appendChild(hidden);

		// append path to form
		hidden = document.createElement('input');
		hidden.type = 'hidden';
		hidden.name = 'path';
		hidden.value = path;
		form.appendChild(hidden);

		var callback = function() {
			Ext.EventManager.removeListener(frame, 'load', callback, this);
			setTimeout(function() {document.body.removeChild(form);}, 100);
			setTimeout(function() {document.body.removeChild(frame);}, 110);
		};
		
		Ext.EventManager.on(frame, 'load', callback, this);

		form.submit();
    },

    cmdCallback: function (options, bSuccess, response) {
        var i, o, node;
        var showMsg = true;
        if (true === bSuccess) {
            o = Ext.decode(response.responseText);
            if (true === o.success) {
                Ext.Msg.alert('OK', this.successText);
                this.change_path(this.path);
            } else {
                    Ext.Msg.alert(this.errorText, o.error);
            }
        }
    },

    grid_installKeyMap: function() {

            // install keymap
            var keymap = new Ext.KeyMap(this.grid.getGridEl(), [


                    // open
                    {
                            key: Ext.EventObject.ENTER // F2 key = edit
                            , scope: this
                            , fn: function(key, e) {
                                    var sel = this.grid.selModel.getSelected();
                                    this.downloadFile(sel);
                    }}

                    // rename
                   , {
                            key: 113 // F2 key = rename
                            , scope: this
                            , fn: function(key, e) {
                                    var sel = this.grid.selModel.getSelected();
                                    this.renameFile(sel);
                    }}

                    // delete
                    , {
                            key: 46 // Delete key
                            , stopEvent: true
                            , scope: this
                            , fn: function(key, e) {
                                    var sel = this.grid.selModel.getSelected();
                                    this.deleteFile(sel);
                    }}

                    // reload
                    , {
                            key: 69 // Ctrl + E = reload
                            , ctrl: true
                            , stopEvent: true
                            , scope: this
                            , fn: function(key, e) {
                                    this.change_path(this.path);
                    }}
            ]);
    },

    // Manage browser history
    init_history: function () {
        var bookmarkedSection = Ext.ux.History.getBookmarkedState( "dir" );
        var init = bookmarkedSection || '';

        Ext.ux.History.register( "dir", init, function( state ) {
        // This is called after calling YAHOO.util.History.navigate, or after the user
        // has trigerred the back/forward button. We cannot discrminate between
        // these two situations.
            var cur = Ext.ux.History.getCurrentState ("dir");
            //if (cur != state)
                //this.change_path(path);
        }, this, true );

        Ext.ux.History.initialize();
        //this.load_path (init);
    },

    init_drop: function() {
        ddel = new Ext.dd.DropZone('delete', {ddGroup: 'TreeDD', notifyOver: function(dd, e, data)
           {
              return 'x-dd-drop-ok';
           }});
       var rged = this;
       ddel.notifyDrop = function(n, dd, e, data){
          rged.menu_DropDelete.apply(rged, arguments);
       };
       var ddren = new Ext.dd.DropZone('rename', {ddGroup: 'TreeDD', notifyOver: function(dd, e, data)
           {
              return 'x-dd-drop-ok';
           }});
       ddren.notifyDrop = function(n, dd, e, data){
          rged.menu_DropRename.apply(rged, arguments);
       };
       var ddown = new Ext.dd.DropZone('download', {ddGroup: 'TreeDD', notifyOver: function(dd, e, data)
           {
              return 'x-dd-drop-ok';
           }});
       ddown.notifyDrop = function(n, dd, e, data){
          rged.menu_DropDownload.apply(rged, arguments);
       };
       var ddown = new Ext.dd.DropZone('edit', {ddGroup: 'TreeDD', notifyOver: function(dd, e, data)
           {
              var edit = (data.selections) ? data.selections[0].data.edit : data.node.attributes.edit;
              return (edit != 'none')?'x-dd-drop-ok':'x-dd-drop-nodrop';
           }});
       ddown.notifyDrop = function(n, dd, e, data){
          rged.menu_DropEdit.apply(rged, arguments);
       };
       var ddgrid = new Ext.dd.DropTarget(this.grid.getView().mainBody, {
           ddGroup: 'TreeDD',
           notifyDrop: function(dd, e, data)
           {
               rged.grid_notifyDrop.apply(rged, arguments);
           },

           notifyOver: function(dd, e, data)
           {
              var drop=dd.getDragData(e).selections[0];
              return (drop.data.cls == 'folder')?'x-dd-drop-ok-add':'x-dd-drop-nodrop';
           } });
    },

   grid_notifyDrop: function(dd, e, data)
   {
      var drop=dd.getDragData(e).selections[0];
      var drag=data.selections[0];
      if (drop.data.cls == 'folder')
        this.rename(drop.id + "/" + drag.data.name, drag.id);
   },

    init : function() {
       Ext.QuickTips.init();
       this.init_tree();
       this.init_menu ();
       this.init_grid ();
       this.init_layout ();
       this.init_drop ();
       var local = this;
       unFocus.History.addEventListener('historyChange', function(path) {local.change_path(path);});
       var path = unFocus.History.getCurrent() || '/';
       this.change_path (path);
    },

    load_path : function (path) {
        if (this.path != path) {
            if (path == '')
                path = '/';
            unFocus.History.addHistory(path);
            //this.change_path(path);
        }
    },

    change_path: function (path) {
        this.path = path;
        this.ds.load ({params: {path: path}});
        var p = "/root" + path;
        if (p.substr(p.length - 1, 1) == '/')
            p = p.substr(0, p.length - 1)
        this.textBox.setValue(p.substr(5, p.length - 5));
        this.tree.selectPath(p, 'text', function (success, node) { if (success) node.expand() });
    }
};

Ext.onReady(function() {
    var rged = new Rged ();
    rged.init ();
    Ext.get('loading').remove();
    Ext.get('loading-mask').fadeOut({remove:true});
});


