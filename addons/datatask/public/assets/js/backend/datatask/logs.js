define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'datatask/logs/index' + location.search,
                    add_url: 'datatask/logs/add',
                    edit_url: 'datatask/logs/edit',
                    del_url: 'datatask/logs/del',
                    multi_url: 'datatask/logs/multi',
                    table: 'datatask_logs',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'type', title: __('Type'), formatter: function(val, row, index) {
                            var text = {
                                0: '备份',
                                1: '还原',
                                2: '下载',
                                3: '清理'
                            }[val];
                            return '<a href="javascript:;" class="searchit"  data-toggle="tooltip" data-field="' + this.field + '" data-value="' + val + '">' + text +'</a>';
                        }},
                        {field: 'client_type', title: __('访问方式'), formatter: function(val, row, index) {
                            var text = {
                                0: 'CLI',
                                1: 'URL'
                            }[val]
                            return '<a href="javascript:;" class="searchit"  data-toggle="tooltip" data-field="' + this.field + '" data-value="' + val + '">' + text +'</a>';
                        }},
                        {field: 'url', title: __('Url'), formatter: Table.api.formatter.url},
                        {field: 'user', title: __('User')},
                        {field: 'ip', title: __('Ip')},
                        {field: 'useragent', title: 'UserAgent'},
                        {field: 'create_time', title: __('Create_time'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        add: function () {
            Controller.api.bindevent();
        },
        edit: function () {
            Controller.api.bindevent();
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            }
        }
    };
    return Controller;
});