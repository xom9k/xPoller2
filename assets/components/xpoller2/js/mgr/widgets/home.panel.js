xPoller2.panel.Home = function(config) {
	config = config || {};
	Ext.apply(config,{
		border: false
		,baseCls: 'modx-formpanel'
		,items: [{
			html: '<h2>'+_('xpoller2')+'</h2>'
			,border: false
			,cls: 'modx-page-header container'
		},{
			xtype: 'modx-tabs'
			,bodyStyle: 'padding: 10px'
			,defaults: { border: false ,autoHeight: true }
			,border: true
			,activeItem: parseInt(MODx.request.tab)
			,hideMode: 'offsets'
			,items: [{
    			title: _('xpoller2_polls')
				,items: [{
					html: _('xpoller2_intro_msg')
					,border: false
					,bodyCssClass: 'panel-desc'
					,bodyStyle: 'margin-bottom: 10px'
				},{
					xtype: 'xpoller2-grid-questions'
					,preventRender: true
				}]
			},{
    			title: _('xpoller2_tests')
				,items: [{
					xtype: 'xpoller2-grid-tests'
					,preventRender: true
				}]
			},{
    			title: _('xpoller2_lexicon')
				,items: [{
					xtype: 'xpoller2-grid-lexicon'
				}]
			}]
		}]
	});
	xPoller2.panel.Home.superclass.constructor.call(this,config);
};
Ext.extend(xPoller2.panel.Home,MODx.Panel);
Ext.reg('xpoller2-panel-home',xPoller2.panel.Home);



