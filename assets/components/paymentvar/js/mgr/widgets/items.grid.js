paymentvar.grid.Items = function(config) {
    config = config || {};
	Ext.applyIf(config,{
        id: 'paymentvar-grid-items',
		url: paymentvar.config.connector_url
		,baseParams: {
			action: 'mgr/item/getlist',
			sort: 'ID',
			dir: 'ASC'
		}
		,fields: ['ID','NAME']
		,remoteSort: true
		,autoHeight: true
        ,paging: true
        ,columns: [
		
		{
            header: 'ID'
            ,dataIndex: 'ID'
            ,width: 30
        },{
            header: 'Название'
            ,dataIndex: 'NAME'
            ,width: 180
        }]
		,tbar: [{
            text: 'Добавить способ оплаты'
            ,handler: this.createItem
            ,scope: this
        }]
    });
    paymentvar.grid.Items.superclass.constructor.call(this,config);
};
Ext.extend(paymentvar.grid.Items,MODx.grid.Grid,{
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
    
    ,createItem: function(btn,e) {
        if (!this.windows.createItem) {
            this.windows.createItem = MODx.load({
                xtype: 'paymentvar-window-item-create'
                ,listeners: {
                    'success': {fn:function() { this.refresh(); },scope:this}
                }
            });
        }
        this.windows.createItem.fp.getForm().reset();
        this.windows.createItem.show(e.target);
    }
    ,updateItem: function(btn,e) {
        if (!this.menu.record || !this.menu.record.ID) return false;
        var r = this.menu.record;

        if (!this.windows.updateItem) {
            this.windows.updateItem = MODx.load({
                xtype: 'paymentvar-window-item-update'
                ,record: r
                ,listeners: {
                    'success': {fn:function() { this.refresh(); },scope:this}
                }
            });
        }
        this.windows.updateItem.fp.getForm().reset();
        this.windows.updateItem.fp.getForm().setValues(r);
        this.windows.updateItem.show(e.target);
    }
    
    ,removeItem: function(btn,e) {
        if (!this.menu.record) return false;
        
        MODx.msg.confirm({
            title: 'Удаление статуса'
            ,text: 'Вы уверены, что хотите удалить эту позицию?'
            ,url: this.config.url
            ,params: {
                action: 'mgr/item/remove'
                ,id: this.menu.record.ID
            }
            ,listeners: {
                'success': {fn:function(r) { this.refresh(); },scope:this}
            }
        });
    }
});
Ext.reg('paymentvar-grid-items',paymentvar.grid.Items);

paymentvar.window.CreateItem = function(config) {
    config = config || {};
    this.ident = config.ident || 'mecitem'+Ext.id();
    Ext.applyIf(config,{
        title: 'Добавление способа оплаты'
        ,id: this.ident
        ,height: 550
        ,width: 475
        ,url: paymentvar.config.connector_url
        ,action: 'mgr/item/create'
        ,fields: [{
            xtype: 'textfield'
            ,fieldLabel: 'Название'
            ,name: 'NAME'
            ,id: 'paymentvar-'+this.ident+'-name'
            ,width: 300
        }]
    });
    paymentvar.window.CreateItem.superclass.constructor.call(this,config);
};
Ext.extend(paymentvar.window.CreateItem,MODx.Window);
Ext.reg('paymentvar-window-item-create',paymentvar.window.CreateItem);


paymentvar.window.UpdateItem = function(config) {
    config = config || {};
	var r = config.record;
    this.ident = config.ident || 'meuitem'+Ext.id();
    Ext.applyIf(config,{
        title: _('paymentvar.item_update')
        ,id: this.ident
        ,height: 150
        ,width: 475
        ,url: paymentvar.config.connector_url
        ,action: 'mgr/item/update'
        ,fields: [{ 
			xtype: 'statictextfield'
			,name: 'ID'
			,id: 'paymentvar-'+this.ident+'-id'
			,fieldLabel: 'ID'
			,width: 300
			,value: r.ID
			,submitValue: r.ID
		},{
            xtype: 'textfield'
            ,fieldLabel: 'Название'
            ,name: 'NAME'
            ,id: 'paymentvar-'+this.ident+'-name'
            ,width: 300
        }]
    });
    paymentvar.window.UpdateItem.superclass.constructor.call(this,config);
};
Ext.extend(paymentvar.window.UpdateItem,MODx.Window);
Ext.reg('paymentvar-window-item-update',paymentvar.window.UpdateItem);