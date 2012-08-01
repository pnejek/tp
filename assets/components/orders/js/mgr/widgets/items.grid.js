function orderconsist (orderid) {
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
win.update('<iframe src="/orderconsistmanager.html?orderid='+orderid+'" name="Состав заказа" width="100%" height="100%">');
}

var storedeliviry = new Ext.data.JsonStore({
		autoLoad: true,
		url: '/components.html?action=deliviry',
		fields: [{name:'DELIVIRY_ID', type: 'int'}, 'NAME'],
		storeId: 'storedeliviry'
	
    });
storedeliviry.load();
var storepayment = new Ext.data.JsonStore({
		autoLoad: true,
		url: '/components.html?action=payment',
		fields: [{name:'PAYMENT_ID', type: 'int'}, 'NAME'],
		storeId: 'storepayment'
	
    });
storepayment.load();
var orderstatus = new Ext.data.JsonStore({
		autoLoad: true,
		url: '/components.html?action=orderstatus',
		fields: [{name:'STATUS', type: 'int'}, 'NAME'],
		storeId: 'orderstatus'
	
    });
orderstatus.load();
var newvalueofuser = new Array();
var titleofuser = new Array();
orders.grid.Items = function(config) {
    config = config || {};
	Ext.applyIf(config,{
        id: 'orders-grid-items',
		url: orders.config.connector_url
		,baseParams: {
			action: 'mgr/item/getlist',
			sort: 'ID',
			dir: 'ASC',
			STATUS: -1
		}
		,fields: ['ID','USER_ID', 'DELIVIRY_ID', 'PAYMENT_ID', 'COMMENT', 'STATUS', 'ADDRESS', 'PAYANYWAY', 'DATE']
		,remoteSort: true
		,autoHeight: true
        ,paging: true
		,disableSelection: false
        ,columns: [
		
		{
            header: 'Заказ'
            ,dataIndex: 'ID'
            ,width: 70
			,renderer: function(value, metaData, record, rowIndex, colIndex, store) {
			  return value+'<br /><a href="javascript:orderconsist('+value+')">Состав</a>';
			}
        },{
            header: 'Покупатель'
            ,dataIndex: 'USER_ID'
            ,width: 100
			,renderer: function(value, metaData, record, rowIndex, colIndex, store) {
				
				Ext.Ajax.request({
					url: '/components.html',
					success: function(response, opts) {
					    var obj = Ext.decode(response.responseText);
						newvalueofuser[value] = obj['FULLNAME']+'<br />'+obj['EMAIL'];
						titleofuser[value] = obj['FULLNAME']+' '+obj['EMAIL'];
					},
				    params: { action: 'userinfo', userid: value }
				});
				return '<span title="'+titleofuser[value]+'">'+newvalueofuser[value]+'</span>';
			}
        },{
            header: 'Статус'
            ,dataIndex: 'STATUS'
            ,width: 100
			,renderer: function(value, metaData, record, rowIndex, colIndex, store) {
				var newvalueofstatus='';
				var status='';
				status =orderstatus.getAt(orderstatus.findExact('STATUS', value)).get('NAME');
				newvalueofstatus = '<span title="'+status+'"><font color="green">'+status+'</font></span>';
				return newvalueofstatus;
			}
        },{
            header: 'Способ доставки'
            ,dataIndex: 'DELIVIRY_ID'
            ,width: 100
			,renderer: function(value, metaData, record, rowIndex, colIndex, store) {
				var newvalueofdel='';
				var del='';
				if(value>0) {	
					del =storedeliviry.getAt(storedeliviry.findExact('DELIVIRY_ID', value)).get('NAME');
					newvalueofdel = '<span title="'+del+'"><font color="green">'+del+'</font></span>';
				}
				if (value==0)
				{   
					newvalueofdel = '<font color="red">Не задано</font>'; 
				}
				return newvalueofdel;
			}
        },{
            header: 'Способ оплаты'
            ,dataIndex: 'PAYMENT_ID'
            ,width: 100
			,renderer: function(value, metaData, record, rowIndex, colIndex, store) {
				var newvalueofpay='';
				var pay = '';
				if(value>0) {	
					pay =storepayment.getAt(storepayment.findExact('PAYMENT_ID', value)).get('NAME');
					newvalueofpay = '<span title="'+pay+'"><font color="green">'+pay+'</font></span>';
				}
				if (value==0)
				{   
					newvalueofpay = '<font color="red">Не задано</font>'; 
				}
				return newvalueofpay;
			}
        },{
            header: 'Комментарий'
            ,dataIndex: 'COMMENT'
            ,width: 100
			,renderer: function(value, metaData, record, rowIndex, colIndex, store) {
				var newvalueofcomment='';
				if(value) {	
				
					newvalueofcomment ='<span title="'+value+'">'+value+'</span>';
				}
				
				return newvalueofcomment;
			}
        },{
            header: 'Адрес'
            ,dataIndex: 'ADDRESS'
            ,width: 100
			,renderer: function(value, metaData, record, rowIndex, colIndex, store) {
				var newvalueaddress='';
				var telephone = '';
				if(value) {
					var text = Ext.util.JSON.decode(value);
					for (var j in text) {
						if(text[j] && j!='phone') {
							newvalueaddress+=', '+text[j];
						}
						if(text[j] && j=='phone') {
							telephone = text[j];
						}
					}					
						address=newvalueaddress.substr(2);
						newvalueaddress+=address+'<br />Телефон для связи: <br />'+telephone;
					newvalueaddress = '<span title="'+address+' Телефон для связи: '+telephone+'">'+address+'<br /> Телефон для связи: <br />'+telephone+'</span>';
				}	
				return newvalueaddress;
			}
        },{
            header: 'Оплата ПС'
            ,dataIndex: 'PAYANYWAY'
            ,width: 100
			,renderer: function(value, metaData, record, rowIndex, colIndex, store) {
				var newvaluepayanyway='';
				if(value) {
					var text = Ext.util.JSON.decode(value);	
					newvaluepayanyway='Транзакция: '+text['MNT_TRANSACTION_ID']+'<br />' +
					'Операция внутри ПС: '+text['MNT_OPERATION_ID']+'<br />' +
					'Сумма: '+text['MNT_AMOUNT']+' ' +text['MNT_CURRENCY_CODE'];
					var title = 'Транзакция: '+text['MNT_TRANSACTION_ID']+'  ' +
					'Операция внутри ПС: '+text['MNT_OPERATION_ID']+'  ' +
					'Сумма: '+text['MNT_AMOUNT']+' ' +text['MNT_CURRENCY_CODE'];
					newvaluepayanyway = '<span title="'+title+'">'+newvaluepayanyway+'</span>';
				}
				return newvaluepayanyway;
			}
        },{
            header: 'Дата'
            ,dataIndex: 'DATE'
            ,width: 100
        }]
		,tbar: [{
        xtype: 'modx-combo'
		,typeAhead: true
		,triggerAction: 'all'
		,lazyRender:true
		,mode: 'local'
        ,name: 'STATUS'
        ,id: 'orders-filter-status'
        ,emptyText: 'Фильтр по статусам'
        ,store: orderstatus
        ,allowBlank: true
		,displayField: 'NAME'
        ,valueField: 'STATUS'
		,hiddenName: 'STATUS'
        ,width: 250
        ,listeners: {
            'select': {fn: this.filterByStatus, scope:this}
        }
    },{
        xtype: 'button'
        ,id: 'modx-filter-clear'
        ,text: _('filter_clear')
        ,listeners: {
            'click': {fn: this.clearFilter, scope: this}
        }
    }]
    });
    orders.grid.Items.superclass.constructor.call(this,config);
};
Ext.extend(orders.grid.Items,MODx.grid.Grid,{
    windows: {}

    ,getMenu: function() {
        var m = [];
        m.push({
            text: 'Изменить'
            ,handler: this.updateItem
        });
		this.addContextMenuItem(m);
    }
    
    ,updateItem: function(btn,e) {
        if (!this.menu.record || !this.menu.record.ID) return false;
        var r = this.menu.record;

        if (!this.windows.updateItem) {
            this.windows.updateItem = MODx.load({
                xtype: 'orders-window-item-update'
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
	,clearFilter: function() {
		Ext.getCmp('orders-filter-status').reset();
		this.getStore().setBaseParam('STATUS', -1);
    	this.getBottomToolbar().changePage(1);
        this.refresh();
    }
	,filterByStatus: function(cb,rec,ri) {
		this.getStore().setBaseParam('STATUS', rec.data['STATUS']);
        this.getBottomToolbar().changePage(1);
        this.refresh();
    }

});
Ext.reg('orders-grid-items',orders.grid.Items);

orders.window.UpdateItem = function(config) {
    config = config || {};
	var r = config.record;
    this.ident = config.ident || 'meuitem'+Ext.id();
    Ext.applyIf(config,{
        title: _('orders.item_update')
        ,id: this.ident
        ,height: 150
        ,width: 475
        ,url: orders.config.connector_url
        ,action: 'mgr/item/update'
        ,fields: [{ 
			xtype: 'statictextfield'
			,name: 'ID'
			,id: 'orders-'+this.ident+'-id'
			,fieldLabel: 'ID'
			,width: 300
			,value: r.ID
			,submitValue: r.ID
		},{
			xtype: 'modx-combo',
			typeAhead: true,
			triggerAction: 'all',
			lazyRender:true,
			mode: 'local',
			store: orderstatus,
			fields: ['STATUS', 'NAME'],
			baseParams: {},
			valueField: 'STATUS',
			displayField: 'NAME',
			fieldLabel: 'Тип',
			name: 'STATUS',
			width: 300,
			id: 'orders-'+this.ident+'-status',
			hiddenName: 'STATUS'
		}]
    });
    orders.window.UpdateItem.superclass.constructor.call(this,config);
};
Ext.extend(orders.window.UpdateItem,MODx.Window);
Ext.reg('orders-window-item-update',orders.window.UpdateItem);