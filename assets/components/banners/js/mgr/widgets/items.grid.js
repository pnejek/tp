function editbanner (bnid) {
	var win = new Ext.Window({
		id: 'windowgrid1'    
		,preventHeader: true
        ,iconCls:'icon-grid'
        ,width:800
        ,height:600
		,x: 300
		,y: 100
		,plain:true
        ,layout:'fit'
		,resizeHandles: 's e se'
        ,closable:true
        ,border:false
        ,maximizable:false
}).show();
win.update('<iframe src="/service/banners.html?bnid='+bnid+'" name="Баннеры" width="100%" height="100%">');
win.on('close', function (p) {Ext.getCmp('banners-grid-items').refresh();});
}

banners.grid.Items = function(config) {
    config = config || {};
	Ext.applyIf(config,{
        id: 'banners-grid-items',
		url: banners.config.connector_url
		,baseParams: {
			action: 'mgr/item/getlist',
			sort: 'ID',
			dir: 'DESC'
		}
		,fields: ['ID','PATH', 'HREF', 'SORTORDER', 'DISABLED']
		,remoteSort: true
		,autoHeight: true
        ,paging: true
        ,columns: [
		
		{
            header: 'ID'
            ,dataIndex: 'ID'
            ,width: 50
        },{
            header: 'Изображение'
            ,dataIndex: 'PATH'
            ,width: 200
			,renderer: function(value, metaData, record, rowIndex, colIndex, store) { 
				
				if (value){
					newvalueban = '<img src="'+value+'" width="180"/><br />';
				}
				else {
				newvalueban = '<font color="red">Нет изображения</font><br />';
				}
				newvalueban = newvalueban + '<a href="javascript:editbanner('+record.get("ID")+')">Изменить баннер</a>';
				return newvalueban;
			}
        },{
            header: 'Ссылка'
            ,dataIndex: 'HREF'
            ,width: 100
			,renderer: function(value, metaData, record, rowIndex, colIndex, store) { 
				
				if (value){
					newvalue = '<a href="'+value+'">'+value+'</a>';
				}
				else {
					newvalue = '<font color="red">Не задано</font>';
				}
				
				return newvalue;
			}
        },{
            header: 'Порядок'
            ,dataIndex: 'SORTORDER'
            ,width: 70
        },{
			header: 'Отключено'
            ,dataIndex: 'DISABLED'
            ,width: 50
			,renderer: function(value, metaData, record, rowIndex, colIndex, store) { 
			if (value)
			{
			newvalue = '<font color="green">Да</font>' 
			}
			else
			{
			newvalue = '<font color="red">Нет</font>' 
			}
			return newvalue;
			}
		}]
		,tbar: [{
            text: 'Добавить баннер'
            ,handler: this.addbanner
            ,scope: this
        },{
            text: 'Редактировать баннер'
            ,handler: this.updateItem
            ,scope: this
        },{
            text: 'Удалить баннер'
            ,handler: this.removeItem
            ,scope: this
        }]
    });
    banners.grid.Items.superclass.constructor.call(this,config);
};
Ext.extend(banners.grid.Items,MODx.grid.Grid,{
    windows: {}

    ,getMenu: function() {
        var m = [];
        m.push({
            text: 'Изменить'
            ,handler: this.updateItem
        });
        m.push('-');
        m.push({
            text: 'Удалить'
            ,handler: this.removeItem
        });
        this.addContextMenuItem(m);
    }
    
    
    ,updateItem: function(btn,e) {
		var rec = this.getSelectionModel().getSelected();
        if (!rec || !rec.id) return false;   
		var r = rec.data;
        if (!this.windows.updateItem) {
            this.windows.updateItem = MODx.load({
                xtype: 'banners-window-item-update'
                ,record: r
                ,listeners: {
                    'success': {fn:function() { 
					this.refresh(); 
						
					},scope:this}
                }
            });
        }
        this.windows.updateItem.fp.getForm().reset();
        this.windows.updateItem.fp.getForm().setValues(r);
        this.windows.updateItem.show(e.target);
    }
    
    ,removeItem: function(btn,e) {
        var rec = this.getSelectionModel().getSelected();
        if (!rec || !rec.id) return false;
        var r = rec.data;
        MODx.msg.confirm({
            title: 'Удаление баннера'
            ,text: 'Вы уверены, что хотите удалить эту позицию?'
            ,url: this.config.url
            ,params: {
                action: 'mgr/item/remove'
                ,id: r.ID
            }
            ,listeners: {
                'success': {fn:function(r) { 
				this.refresh(); 
				      
				},scope:this}
            }
        });
    }
	
	,addbanner: function(btn,e) {
        var win = new Ext.Window({
			id: 'windowgrid1'    
			,preventHeader: true
			,iconCls:'icon-grid'
			,width:800
			,height:600
			,x: 300
			,y: 100
			,plain:true
			,layout:'fit'
			,resizeHandles: 's e se'
			,closable:true
			,border:false
			,maximizable:false
		}).show();
		win.update('<iframe src="/service/banners.html?bnid=0" name="Баннеры" width="100%" height="100%">');
    }
});
Ext.reg('banners-grid-items',banners.grid.Items);

banners.window.UpdateItem = function(config) {
    config = config || {};
	var r = config.record;
    this.ident = config.ident || 'menuitem'+Ext.id();
    Ext.applyIf(config,{
        title: 'Редактирование баннера'
        ,id: this.ident
        ,height: 2000
        ,width: 475
        ,url: banners.config.connector_url
        ,action: 'mgr/item/update'
        ,fields: [{ 
			xtype: 'statictextfield'
			,name: 'ID'
			,id: 'banners-'+this.ident+'-id'
			,fieldLabel: 'ID'
			,width: 300
			,value: r.ID
			,submitValue: r.ID
		},{
            xtype: 'textfield'
            ,fieldLabel: 'Ссылка'
            ,name: 'HREF'
            ,id: 'banners-'+this.ident+'-href'
            ,width: 300
		},{
            xtype: 'textfield'
            ,fieldLabel: 'Порядок сортировки'
            ,name: 'SORTORDER'
            ,id: 'banners-'+this.ident+'-sort'
            ,width: 100
        },{
                name:'DISABLED'
                ,fieldLabel:'Выключен'
                ,xtype:'xcheckbox'
                ,checked:false
				,id: 'banners-'+this.ident+'-dis'
            }]
    });
    banners.window.UpdateItem.superclass.constructor.call(this,config);
};
Ext.extend(banners.window.UpdateItem,MODx.Window);
Ext.reg('banners-window-item-update',banners.window.UpdateItem);