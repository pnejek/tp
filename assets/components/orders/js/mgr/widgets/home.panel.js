orders.panel.Home = function(config) {
    config = config || {};
    Ext.apply(config,{
        border: false
        ,baseCls: 'modx-formpanel'
        ,items: [{
            html: '<h2>'+'Управление заказами пользователей'+'</h2>'
            ,border: false
            ,cls: 'modx-page-header'
        },{
            xtype: 'modx-tabs'
            ,bodyStyle: 'padding: 10px'
            ,defaults: { border: false ,autoHeight: true }
            ,border: true
            ,activeItem: 0
            ,hideMode: 'offsets'
            ,items: [{
                title: 'Заказы'
                ,items: [{
                    html: '<p>'+'Здесь можно просматривать заказы и изменять статус заказов'+'</p><br />'
                    ,border: false
                },{
                    xtype: 'orders-grid-items'
                    ,preventRender: true
                }]
            }]
        }]
    });
    orders.panel.Home.superclass.constructor.call(this,config);
};
Ext.extend(orders.panel.Home,MODx.Panel);
Ext.reg('orders-panel-home',orders.panel.Home);
