xPoller2.grid.Questions = function(config) {
	config = config || {};
    this.sm = new Ext.grid.CheckboxSelectionModel();
	Ext.applyIf(config,{
		id: 'xpoller2-grid-questions'
		,url: xPoller2.config.connector_url
		,baseParams: {
			action: 'mgr/question/getlist'
            ,type:  'question'
            ,tid: parseInt(MODx.request.test)
		}
		,fields: ['id','text','type','closed']
		,autoHeight: true
		,paging: true
		,remoteSort: true
        ,sm: this.sm
		,columns: [
			{header: _('id'),dataIndex: 'id',width: 50}
			,{header: _('xpoller2_question_text'),dataIndex: 'text',width: 300}
			,{header: _('xpoller2_question_type'),dataIndex: 'type',width: 80}
			,{header: _('xpoller2_question_closed'),dataIndex: 'closed',width: 80}
		]
		,tbar: [{
			text: _('xpoller2_question_create')
			,handler: this.createItem
			,scope: this
		}]
		,listeners: {
			rowDblClick: function(grid, rowIndex, e) {
				var row = grid.store.getAt(rowIndex);
				this.updateItem(grid, e, row);
			}
		}
	});
	xPoller2.grid.Questions.superclass.constructor.call(this,config);
};
Ext.extend(xPoller2.grid.Questions,MODx.grid.Grid,{
	windows: {}
    
    ,renderBoolean: function(val,cell,row) {
		return val == '' || val == 0
			? '<span style="color:red">' + _('no') + '<span>'
			: '<span style="color:green">' + _('yes') + '<span>';
	}

	,renderImage: function(val,cell,row) {
		return val != ''
			? '<img src="' + val + '" alt="" height="50" />'
			: '';
	}
    
	,getMenu: function() {
        var cs = this.getSelectedAsList();
        var m = [];
        if (cs.split(',').length > 1) {
            m.push({
    			text: _('xpoller2_questions_remove')
    			,handler: this.removeSelected
    		});
        } else {
    		m.push({
    			text: _('xpoller2_question_update')
    			,handler: this.updateItem
    		});
    		m.push('-');
    		m.push({
    			text: _('xpoller2_question_remove')
    			,handler: this.removeItem
    		});
        }
		this.addContextMenuItem(m);
	}
	
	,createItem: function(btn,e) {
		if (!this.windows.createItem) {
			this.windows.createItem = MODx.load({
				xtype: 'xpoller2-window-question-create'
				,listeners: {
					'success': {fn:function(response) {
                        var row = {'data':response.a.result.object};
                        this.updateItem(btn,e,row);
                        /*this.refresh();*/
                    },scope:this}
				}
			});
		}
		this.windows.createItem.fp.getForm().reset();
		this.windows.createItem.show(e.target);
	}

	,updateItem: function(btn,e,row) {
		if (typeof(row) != 'undefined') {this.menu.record = row.data;}
		var id = this.menu.record.id;

		MODx.Ajax.request({
			url: xPoller2.config.connector_url
			,params: {
				action: 'mgr/question/get'
				,id: id
			}
			,listeners: {
				success: {fn:function(r) {
					this.windows.updateItem = MODx.load({
						xtype: 'xpoller2-window-question-update'
						,record: r
						,listeners: {
							'success': {fn:function() { this.refresh(); },scope:this}
						}
					});
					this.windows.updateItem.fp.getForm().reset();
					this.windows.updateItem.fp.getForm().setValues(r.object);
					this.windows.updateItem.show(e.target);
				},scope:this}
			}
		});
	}

	,removeItem: function(btn,e) {
		if (!this.menu.record) return false;
		
		MODx.msg.confirm({
			title: _('xpoller2_question_remove')
			,text: _('xpoller2_question_remove_confirm')
			,url: this.config.url
			,params: {
				action: 'mgr/question/remove'
				,id: this.menu.record.id
			}
			,listeners: {
				'success': {fn:function(r) { this.refresh(); },scope:this}
			}
		});
	}

    ,getSelectedAsList: function() {
        var sels = this.getSelectionModel().getSelections();
        if (sels.length <= 0) return false;

        var cs = '';
        for (var i=0;i<sels.length;i++) {
            cs += ','+sels[i].data.id;
        }
        cs = cs.substr(1);
        return cs;
    }

    ,removeSelected: function(act,btn,e) {
        var cs = this.getSelectedAsList();
        if (cs === false) return false;

        MODx.msg.confirm({
			title: _('xpoller2_questions_remove')
			,text: _('xpoller2_questions_remove_confirm')
			,url: this.config.url
			,params: {
                action: 'mgr/questions/remove'
                ,items: cs
            }
            ,listeners: {
                'success': {fn:function(r) {
                    this.getSelectionModel().clearSelections(true);
                    this.refresh();
                       var t = Ext.getCmp('modx-resource-tree');
                       if (t) { t.refresh(); }
                },scope:this}
            }
        });
        return true;
    }
});
Ext.reg('xpoller2-grid-questions',xPoller2.grid.Questions);




xPoller2.window.CreateItem = function(config) {

	config = config || {};
	this.ident = config.ident || 'mecitem'+Ext.id();
	Ext.applyIf(config,{
		title: _('xpoller2_question_create')
		,id: this.ident
		,height: 300
		,width: 475
		,url: xPoller2.config.connector_url
        ,baseParams: {
            action: 'mgr/question/create'
            ,tid: parseInt(MODx.request.test)
        }
		,fields: [
			{xtype: 'textfield',fieldLabel: _('xpoller2_question_text'),name: 'text',id: 'xpoller2-'+this.ident+'-text',anchor: '99%'},
			{
				xtype: 'fieldset',
				title: _('xpoller2_question_type'),
				items: [{
		                xtype: 'radiogroup'
		                ,hideLabel: true
		                ,items: [{
		                    boxLabel: _('xpoller2_question_type_radio')
		                    ,hideLabel: true
		                    ,name: 'type'
		                    ,inputValue: 'radio'
		                    ,listeners: {fn:function(p) {
		                            Ext.getCmp('xpoller2-'+this.ident+'-type').setValue(true);
		                        },scope:this}
		                },{
		                    boxLabel: _('xpoller2_question_type_checkbox')
		                    ,hideLabel: true
		                    ,name: 'type'
		                    ,inputValue: 'checkbox'
		                }]
		            }]
			}
		]
		,keys: [{key: Ext.EventObject.ENTER,shift: true,fn: function() {this.submit() },scope: this}]
	});
	xPoller2.window.CreateItem.superclass.constructor.call(this,config);
};
Ext.extend(xPoller2.window.CreateItem,MODx.Window);
Ext.reg('xpoller2-window-question-create',xPoller2.window.CreateItem);


xPoller2.window.UpdateItem = function(config) {
	config = config || {};
	this.ident = config.ident || 'meuitem'+Ext.id();
	Ext.applyIf(config,{
		title: _('xpoller2_question_update')
		,id: this.ident
		,height: 500
		,width: 750
		,url: xPoller2.config.connector_url
		,action: 'mgr/question/update'
		,fields: [
			{xtype: 'hidden',name: 'id',id: 'xpoller2-'+this.ident+'-id'}
			,{xtype: 'textfield',fieldLabel: _('xpoller2_question_text'),name: 'text',id: 'xpoller2-'+this.ident+'-text',anchor: '99%'}
			,{
				xtype: 'fieldset',
				title: _('xpoller2_question_type'),
				items: [{
		                xtype: 'radiogroup'
		                ,hideLabel: true
		                ,items: [{
		                    boxLabel: _('xpoller2_question_type_radio')
		                    ,hideLabel: true
		                    ,name: 'type'
		                    ,inputValue: 'radio'
		                    ,value: 'radio'
		                    ,checked: true
		                },{
		                    boxLabel: _('xpoller2_question_type_checkbox')
		                    ,hideLabel: true
		                    ,name: 'type'
		                    ,inputValue: 'checkbox'
		                    ,value: 'radio'
		                }]
		            }]
			}
            ,{
            	xtype: 'xpoller2-grid-options'
        		,preventRender: true
                ,record: config.record.object
        	}
		]
		,keys: [{key: Ext.EventObject.ENTER,shift: true,fn: function() {this.submit() },scope: this}]
	});
	xPoller2.window.UpdateItem.superclass.constructor.call(this,config);
};
Ext.extend(xPoller2.window.UpdateItem,MODx.Window);
Ext.reg('xpoller2-window-question-update',xPoller2.window.UpdateItem);