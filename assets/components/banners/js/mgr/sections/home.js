Ext.onReady(function() {
    MODx.load({ xtype: 'banners-page-home'});
});

banners.page.Home = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        components: [{
            xtype: 'banners-panel-home'
            ,renderTo: 'banners-panel-home-div'
        }]
    }); 
    banners.page.Home.superclass.constructor.call(this,config);
};
Ext.extend(banners.page.Home,MODx.Component);
Ext.reg('banners-page-home',banners.page.Home);