Ext.onReady(function() {
    MODx.load({ xtype: 'slideshow-page-home'});
});

slideshow.page.Home = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        components: [{
            xtype: 'slideshow-panel-home'
            ,renderTo: 'slideshow-panel-home-div'
        }]
    }); 
    slideshow.page.Home.superclass.constructor.call(this,config);
};
Ext.extend(slideshow.page.Home,MODx.Component);
Ext.reg('slideshow-page-home',slideshow.page.Home);