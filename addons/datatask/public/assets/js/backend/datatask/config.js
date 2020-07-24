define(['jquery', 'bootstrap', 'backend', 'table', 'form', 'backend/datatask/libs/Model'], function ($, undefined, Backend, Table, Form, Model) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'datatask/config/index' + location.search,
                    add_url: 'datatask/config/add',
                    edit_url: 'datatask/config/edit',
                    del_url: 'datatask/config/del',
                    multi_url: 'datatask/config/multi',
                    table: 'datatask_config',
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
                        {field: 'name', title: __('Name')},
                        {field: 'desc', title: __('Desc')},
                        {field: 'token', title: __('Token')},
                        {field: 'max_down', title: __('Max_down')},
                        {field: 'back_count', title: __('Back_count')},
                        {field: 'down_count', title: __('Down_count')},
                        {field: 'create_time', title: __('Create_time'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate, buttons: [
                            {
                                name: 'show', text: '详情',
                                classname: 'btn btn-xs btn-primary btn-dialog',
                                url: function(row) {
                                    return 'datatask/backlog?config_id=' + row.id
                                }
                            },
                            {
                                name: 'run', text: '备份',
                                classname: 'btn btn-xs btn-info btn-run'
                            },
                            {
                                name: 'clear', text: '清理备份',
                                classname: 'btn btn-xs btn-danger btn-clear'
                            }
                        ]}
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
            table.on('post-body.bs.table', function() {
                $('.btn-run', this).on('click', function(){
                    var row = table.bootstrapTable('getData')[$(this).data('row-index')];
                    Fast.api.ajax({
                        url: 'datatask/config/run?id=' + row.id
                    })
                });
                $('.btn-clear', this).on('click', function() {
                    var row = table.bootstrapTable('getData')[$(this).data('row-index')];
                    Layer.confirm("该操作将只保留最近的一条记录及备份文件，确定吗？", {
                        yes: function(index) {
                            Fast.api.ajax({
                                url: 'datatask/config/clear?id=' + row.id
                            })
                            Layer.close(index)
                        }
                    })
                })
            })
        },
        add: function () {
            Controller.api.bindevent();
            Controller.api.bindSelectTable();
            Controller.api.bindRefreshToken();
        },
        edit: function () {
            Controller.api.bindevent();
            Controller.api.bindSelectTable();
            Controller.api.bindRefreshToken();
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            },
            bindRefreshToken() {
                $('#btn-refresh').on('click', function() {
                    Fast.api.ajax({
                        url: 'datatask/index/refresh_token'
                    }, function(data) {
                        $("#c-token").val(data)
                        return true;
                    })
                })
            },
            bindSelectTable: function() {
                var tables = [];
                var options = {
                    url: 'datatask/index/get_tables',
                    pk: 'id',
                    sortName: 'id',
                    pagination: false,
                    columns: [
                        [
                            {checkbox: true, formatter: function(val, row, index) {
                                if (tables.indexOf(row.TABLE_NAME) != -1) return true;
                                return false;
                            }},
                            {field: 'TABLE_NAME', title: '表名', formatter: function(val, row, index) {
                                return val + '[' + row.TABLE_COMMENT + ']'
                            }}
                        ]
                    ]
                }
                $('#btn-table').click(function() {
                    tables = Controller.api.getTableValue();
                    var table = null;
                    var config = {
                        area: ['90%', '90%'],
                        yes: function(index) {
                            var rows = Controller.api.getSelections(table);
                            var value = [];
                            $.each(rows, function(index, item) {
                                value.push(item.TABLE_NAME);
                            })
                            Controller.api.setTableValue(value.join(','))
                            Layer.close(index);
                        }
                    };
                    Model.table({options: options, config: config}, function(_table) {
                        table = _table;
                    })
                })
            },
            getSelections: function(table) {
                return table.bootstrapTable('getSelections');
            },
            setTableValue: function(value) {
                $('#c-tables').val(value)
            },
            getTableValue: function() {
                return $("#c-tables").val().split(',');
            }
        }
    };
    return Controller;
});