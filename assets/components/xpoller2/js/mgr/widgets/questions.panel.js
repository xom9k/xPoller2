xPoller2.panel.Questions = function(config) {
    config = config || {};
	Ext.apply(config,{
		border: false
		,baseCls: 'modx-formpanel'
		,items: [{
			html: '<h2>'+_('xpoller2_questions')+'</h2>'
			,border: false
			,cls: 'modx-page-header container'
            ,id: 'xpoller2-questions-page-header',
		},{
			xtype: 'modx-panel'
			,bodyStyle: 'padding: 10px'
			,defaults: { border: false ,autoHeight: true }
			,border: true
			,activeItem: 0
			,hideMode: 'offsets'
			,items: [{
    			/*title: _('xpoller2_questions')
				,*/items: [/*{
					html: _('xpoller2_intro_msg')
					,border: false
					,bodyCssClass: 'panel-desc'
					,bodyStyle: 'margin-bottom: 10px'
				},*/{
					xtype: 'xpoller2-grid-questions'
					,preventRender: true
				}]
			}]
		}],
        listeners: {
    		'added': { fn: function() {
            	MODx.Ajax.request({
        			url: xPoller2.config.connector_url,
        			params: {
        				action: 'mgr/test/get',
        				id: this.config.test
        			},
        			listeners: {
        				'success': {
        					fn: function(r) {
        						//this.getForm().setValues(r.object);
                                console.log(r.object);
        						Ext.getCmp('xpoller2-questions-page-header').getEl().update('<h2>' + r.object.name + ' — ' + _('xpoller2_questions') + '</h2>');
        						this.fireEvent('ready', r.object);
        					},
        					scope: this
        				}
        			}
        		});
    		}, scope: this }
		}
	});
	xPoller2.panel.Questions.superclass.constructor.call(this,config);
};
/*
Ext.extend(xPoller2.panel.Questions,MODx.FormPanel, {
    setup: function() {
		if(!this.config.test) {
			return;
		}

		MODx.Ajax.request({
			url: this.config.url,
			params: {
				action: 'mgr/test/get',
				id: this.config.test
			},
			listeners: {
				'success': {
					fn: function(r) {
						//this.getForm().setValues(r.object);
                        console.log(r.object);
						Ext.getCmp('xpoller2-questions-page-header').getEl().update('<h2>' + _('xpoller2_questions') + ': ' + r.object.name + '</h2>');
						this.fireEvent('ready', r.object);
					},
					scope: this
				}
			}
		});
	}
});*/
Ext.extend(xPoller2.panel.Questions,MODx.Panel);
Ext.reg('xpoller2-panel-questions',xPoller2.panel.Questions);