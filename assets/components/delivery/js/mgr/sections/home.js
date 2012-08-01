Ext.onReady(function() {
    MODx.load({ xtype: 'delivery-page-home'});
});

delivery.page.Home = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        components: [{
            xtype: 'delivery-panel-home'
            ,renderTo: 'delivery-panel-home-div'
        }]
    }); 
    delivery.page.Home.superclass.constructor.call(this,config);
};
Ext.extend(delivery.page.Home,MODx.Component);
Ext.reg('delivery-page-home',delivery.page.Home);