delivery.panel.Home = function(config) {
    config = config || {};
    Ext.apply(config,{
        border: false
        ,baseCls: 'modx-formpanel'
        ,items: [{
            html: '<h2>'+'Управление способами доставки'+'</h2>'
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
                title: 'Способы доставки'
                ,items: [{
                    html: '<p>'+'Здесь можно добавлять, изменять и удалять способы доставки'+'</p><br />'
                    ,border: false
                },{
                    xtype: 'delivery-grid-items'
                    ,preventRender: true
                }]
            }]
        }]
    });
    delivery.panel.Home.superclass.constructor.call(this,config);
};
Ext.extend(delivery.panel.Home,MODx.Panel);
Ext.reg('delivery-panel-home',delivery.panel.Home);
