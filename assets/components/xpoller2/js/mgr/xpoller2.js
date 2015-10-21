var xPoller2 = function(config) {
	config = config || {};
	xPoller2.superclass.constructor.call(this,config);
};
Ext.extend(xPoller2,Ext.Component,{
	page:{},window:{},grid:{},tree:{},panel:{},combo:{},config: {},view: {}
});
Ext.reg('xpoller2',xPoller2);

xPoller2 = new xPoller2();