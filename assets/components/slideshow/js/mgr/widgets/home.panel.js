slideshow.panel.Home = function(config) {
    config = config || {};
    Ext.apply(config,{
        border: false
        ,baseCls: 'modx-formpanel'
        ,items: [{
            html: '<h2>'+'Управление слайд-шоу'+'</h2>'
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
                title: 'Слайды'
                ,items: [{
                    html: '<p>'+'Здесь можно добавлять, изменять и удалять слайды'+'</p><br />'
                    ,border: false
                },{
                    xtype: 'slideshow-grid-items'
                    ,preventRender: true
                }]
            }]
        }]
    });
    slideshow.panel.Home.superclass.constructor.call(this,config);
};
Ext.extend(slideshow.panel.Home,MODx.Panel);
Ext.reg('slideshow-panel-home',slideshow.panel.Home);
