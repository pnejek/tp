var delivery = function(config) {
    config = config || {};
    delivery.superclass.constructor.call(this,config);
};
Ext.extend(delivery,Ext.Component,{
    page:{},window:{},grid:{},tree:{},panel:{},combo:{},config: {},view: {}
});
Ext.reg('delivery',delivery);

delivery = new delivery();