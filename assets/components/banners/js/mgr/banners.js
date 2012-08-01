var banners = function(config) {
    config = config || {};
    banners.superclass.constructor.call(this,config);
};
Ext.extend(banners,Ext.Component,{
    page:{},window:{},grid:{},tree:{},panel:{},combo:{},config: {},view: {}
});
Ext.reg('banners',banners);

banners = new banners();