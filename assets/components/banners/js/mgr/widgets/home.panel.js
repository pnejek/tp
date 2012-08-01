banners.panel.Home = function(config) {
    config = config || {};
    Ext.apply(config,{
        border: false
        ,baseCls: 'modx-formpanel'
        ,items: [{
            html: '<h2>'+'Управление баннерами'+'</h2>'
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
                    html: '<p>Здесь можно добавлять, изменять и удалять слайды</p><p>СОРТИРОВКА: 1 позиция - баннер на главной вверху, 2 позиция - баннер на главной внизу. Остальные баннеры на странице брендов по порядку</p>'
                    ,border: false
                },{
                    xtype: 'banners-grid-items'
                    ,preventRender: true
                }]
            }]
        }]
    });
    banners.panel.Home.superclass.constructor.call(this,config);
};
Ext.extend(banners.panel.Home,MODx.Panel);
Ext.reg('banners-panel-home',banners.panel.Home);
