xPoller2.page.Questions = function(config) {
	config = config || {};
	Ext.applyIf(config,{
    	buttons: [{
			text: _('back'),
			id: 'xpoller2-questions-btn-cancel',
			handler: function() {
				location.href = '?a=' + MODx.request.a + '&tab=1';
			},
			scope: this
		}],
		components: [{
			xtype: 'xpoller2-panel-questions'
			,renderTo: 'xpoller2-panel-questions-div'
            ,test: MODx.request.test
		}]
	}); 
	xPoller2.page.Questions.superclass.constructor.call(this,config);
};
Ext.extend(xPoller2.page.Questions,MODx.Component);
Ext.reg('xpoller2-page-questions',xPoller2.page.Questions);