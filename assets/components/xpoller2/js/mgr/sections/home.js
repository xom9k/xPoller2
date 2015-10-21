xPoller2.page.Home = function(config) {
	config = config || {};
	Ext.applyIf(config,{
		components: [{
			xtype: 'xpoller2-panel-home'
			,renderTo: 'xpoller2-panel-home-div'
		}]
	}); 
	xPoller2.page.Home.superclass.constructor.call(this,config);
};
Ext.extend(xPoller2.page.Home,MODx.Component);
Ext.reg('xpoller2-page-home',xPoller2.page.Home);