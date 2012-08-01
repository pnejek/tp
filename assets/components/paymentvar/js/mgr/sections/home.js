Ext.onReady(function() {
    MODx.load({ xtype: 'paymentvar-page-home'});
});

paymentvar.page.Home = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        components: [{
            xtype: 'paymentvar-panel-home'
            ,renderTo: 'paymentvar-panel-home-div'
        }]
    }); 
    paymentvar.page.Home.superclass.constructor.call(this,config);
};
Ext.extend(paymentvar.page.Home,MODx.Component);
Ext.reg('paymentvar-page-home',paymentvar.page.Home);