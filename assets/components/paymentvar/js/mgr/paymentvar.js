var paymentvar = function(config) {
    config = config || {};
    paymentvar.superclass.constructor.call(this,config);
};
Ext.extend(paymentvar,Ext.Component,{
    page:{},window:{},grid:{},tree:{},panel:{},combo:{},config: {},view: {}
});
Ext.reg('paymentvar',paymentvar);

paymentvar = new paymentvar();