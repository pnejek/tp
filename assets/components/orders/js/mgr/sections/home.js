Ext.onReady(function() {
    MODx.load({ xtype: 'orders-page-home'});
});

orders.page.Home = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        components: [{
            xtype: 'orders-panel-home'
            ,renderTo: 'orders-panel-home-div'
        }]
    }); 
    orders.page.Home.superclass.constructor.call(this,config);
};
Ext.extend(orders.page.Home,MODx.Component);
Ext.reg('orders-page-home',orders.page.Home);