paymentvar.panel.Home = function(config) {
    config = config || {};
    Ext.apply(config,{
        border: false
        ,baseCls: 'modx-formpanel'
        ,items: [{
            html: '<h2>'+'Управление способами оплаты'+'</h2>'
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
                title: 'Способы оплаты'
                ,items: [{
                    html: '<p>'+'Здесь можно добавлять, изменять и удалять способы оплаты'+'</p><br />'
                    ,border: false
                },{
                    xtype: 'paymentvar-grid-items'
                    ,preventRender: true
                }]
            }]
        }]
    });
    paymentvar.panel.Home.superclass.constructor.call(this,config);
};
Ext.extend(paymentvar.panel.Home,MODx.Panel);
Ext.reg('paymentvar-panel-home',paymentvar.panel.Home);
