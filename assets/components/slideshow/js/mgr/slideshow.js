var slideshow = function(config) {
    config = config || {};
    slideshow.superclass.constructor.call(this,config);
};
Ext.extend(slideshow,Ext.Component,{
    page:{},window:{},grid:{},tree:{},panel:{},combo:{},config: {},view: {}
});
Ext.reg('slideshow',slideshow);

slideshow = new slideshow();