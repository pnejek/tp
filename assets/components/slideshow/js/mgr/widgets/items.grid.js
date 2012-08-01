function editslide (slid) {
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
win.update('<iframe src="/service/slides.html?slid='+slid+'" name="Слайдшоу" width="100%" height="100%">');
win.on('close', function (p) {Ext.getCmp('slideshow-grid-items').refresh();});
}

slideshow.grid.Items = function(config) {
    config = config || {};
	Ext.applyIf(config,{
        id: 'slideshow-grid-items',
		url: slideshow.config.connector_url
		,baseParams: {
			action: 'mgr/item/getlist',
			sort: 'ID',
			dir: 'DESC'
		}
		,fields: ['ID','PATH', 'DESC', 'DISABLED', 'HREF']
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
					newvalueimage = '<img src="'+value+'" width="180"/><br />';
				}
				else
				{
					newvalueimage = '<font color="red">Нет изображения</font><br />'
				}
				newvalueimage = newvalueimage+ '<a href="javascript:editslide('+record.get("ID")+')">Cлайд</a>';
				return newvalueimage;
			}
        },{
			header: 'Описание'
            ,dataIndex: 'DESC'
            ,width: 200
		},{
			header: 'Скрыто'
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
		},{
            header: 'Ссылка'
            ,dataIndex: 'HREF'
            ,width: 100
			,renderer: function(value, metaData, record, rowIndex, colIndex, store) { 
				
				if (value){
					newvalue = '<a href="'+value+'">'+value+'</a>';
				}
				else
				{
				   newvalue='<font color="red">Не задано</font>';
				}
				return newvalue;
			}
        }]
		,tbar: [{
            text: 'Добавить слайд'
            ,handler: this.addslide
            ,scope: this
        },{
            text: 'Редактировать слайд'
            ,handler: this.updateItem
            ,scope: this
        },{
            text: 'Удалить слайд'
            ,handler: this.removeItem
            ,scope: this
        }]
    });
    slideshow.grid.Items.superclass.constructor.call(this,config);
};
Ext.extend(slideshow.grid.Items,MODx.grid.Grid,{
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
                xtype: 'slideshow-window-item-update'
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
            title: 'Удаление слайда'
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
	
	,addslide: function(btn,e) {
        var win = new Ext.Window({
			id: 'windowgrid1'    
			,preventHeader: true
			,iconCls:'icon-grid'
			,width:800
			,height:500
			,x: 300
			,y: 100
			,plain:true
			,layout:'fit'
			,resizeHandles: 's e se'
			,closable:true
			,border:false
			,maximizable:false
		}).show();
		win.update('<iframe src="/service/slides.html?slid=0" name="Слайдшоу" width="100%" height="100%">');
    }
});
Ext.reg('slideshow-grid-items',slideshow.grid.Items);

slideshow.window.UpdateItem = function(config) {
    config = config || {};
	var r = config.record;
    this.ident = config.ident || 'menuitem'+Ext.id();
    Ext.applyIf(config,{
        title: 'Редактирование слайда'
        ,id: this.ident
        ,height: 200
        ,width: 475
        ,url: slideshow.config.connector_url
        ,action: 'mgr/item/update'
        ,fields: [{ 
			xtype: 'statictextfield'
			,name: 'ID'
			,id: 'slideshow-'+this.ident+'-id'
			,fieldLabel: 'ID'
			,width: 300
			,value: r.ID
			,submitValue: r.ID
		},{
            xtype: 'textarea'
            ,fieldLabel: 'Описание'
            ,name: 'DESC'
            ,id: 'slideshow-'+this.ident+'-desc'
            ,width: 300
        },{
                name:'DISABLED'
                ,fieldLabel:'Выключен'
                ,xtype:'xcheckbox'
                ,checked:false
				,id: 'slideshow-'+this.ident+'-dis'
            },{
            xtype: 'textfield'
            ,fieldLabel: 'Ссылка'
            ,name: 'HREF'
            ,id: 'slideshow-'+this.ident+'-href'
            ,width: 300
			}]
    });
    slideshow.window.UpdateItem.superclass.constructor.call(this,config);
};
Ext.extend(slideshow.window.UpdateItem,MODx.Window);
Ext.reg('slideshow-window-item-update',slideshow.window.UpdateItem);