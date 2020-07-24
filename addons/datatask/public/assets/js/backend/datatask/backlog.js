define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'datatask/backlog/index' + location.search,
                    add_url: 'datatask/backlog/add',
                    edit_url: 'datatask/backlog/edit',
                    del_url: 'datatask/backlog/del',
                    multi_url: 'datatask/backlog/multi',
                    table: 'datatask_backlog',
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
                        {field: 'datataskconfig.name', title: __('Datataskconfig.name'), formatter: Table.api.formatter.search},
                        {field: 'datataskconfig.desc', title: __('Datataskconfig.desc')},
                        {field: 'path', title: __('Path')},
                        {field: 'client_down_count', title: __('Client_down_count')},
                        {field: 'create_time', title: __('Create_time'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate, buttons: [
                            {name: 'add'}, {name: 'edit'},
                            {
                                name: 'down', text: '下载', classname: 'btn btn-xs btn-primary', url: function(row, index) {
                                    return 'backlog/down?id=' + row.id
                                }
                            },
                            {   
                                name: 'restore', text: '还原', classname: 'btn btn-xs btn-danger btn-restore'
                            }
                        ]}
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
            table.on('post-body.bs.table', function() {
                $('.btn-restore', this).on('click', function() {
                    var row = table.bootstrapTable('getData')[$(this).data('row-index')];
                    Layer.confirm("确定还原吗？", {
                        yes: function(index) {
                            Fast.api.ajax({
                                url: 'datatask/backlog/restorelog?id=' + row.id
                            })
                            Layer.close(index)
                        }
                    })
                })
            })
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