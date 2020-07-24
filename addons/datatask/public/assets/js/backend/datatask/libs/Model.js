define(['jquery', 'table'], function($, Table) {
    var Obj = {
        table: function(params, callback) {
            config = params.config || {} // layer config
            options = params.options || {}; // table options
            var el = null;
            if (!config.content) {
                var id = 'e' + Date.now() + parseInt(Math.random() * 10000000);
                content = "<table id='" + id + "'  class='table table-striped table-bordered table-hover table-nowrap'></table>";
                el = "#" + id;
            } else {
                el = params.el;
            }
            Layer.open($.extend(true, {}, {
                content: content,
                area: ['80%', '80%'],
                success: function(layor) {
                    Table.api.init();
                    var table = $(el);
                    table.bootstrapTable(options);
                    Table.api.bindevent(table);
                    callback && callback(table, layor);
                }
            }, config));
        }

    };
    return Obj;
});