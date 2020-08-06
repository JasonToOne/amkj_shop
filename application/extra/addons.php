<?php

return array (
  'autoload' => false,
  'hooks' => 
  array (
    'app_init' => 
    array (
      0 => 'banip',
      1 => 'cms',
      2 => 'log',
    ),
    'action_begin' => 
    array (
      0 => 'clicaptcha',
    ),
    'view_filter' => 
    array (
      0 => 'cms',
    ),
    'user_sidenav_after' => 
    array (
      0 => 'cms',
    ),
    'xunsearch_config_init' => 
    array (
      0 => 'cms',
    ),
    'xunsearch_index_reset' => 
    array (
      0 => 'cms',
    ),
    'ems_send' => 
    array (
      0 => 'faems',
    ),
    'ems_notice' => 
    array (
      0 => 'faems',
    ),
    'get_cfg' => 
    array (
      0 => 'litestore',
    ),
    'response_send' => 
    array (
      0 => 'loginvideo',
    ),
    'testhook' => 
    array (
      0 => 'recruit',
    ),
    'upload_after' => 
    array (
      0 => 'thumb',
    ),
  ),
  'route' => 
  array (
    '/cms/$' => 'cms/index/index',
    '/cms/t/[:name]$' => 'cms/tags/index',
    '/cms/p/[:diyname]$' => 'cms/page/index',
    '/cms/s$' => 'cms/search/index',
    '/cms/d/[:diyname]' => 'cms/diyform/index',
    '/cms/special/[:diyname]' => 'cms/special/index',
    '/cms/a/[:diyname]$' => 'cms/archives/index',
    '/cms/c/[:diyname]$' => 'cms/channel/index',
    '/u/[:id]' => 'cms/user/index',
    '/example$' => 'example/index/index',
    '/example/d/[:name]' => 'example/demo/index',
    '/example/d1/[:name]' => 'example/demo/demo1',
    '/example/d2/[:name]' => 'example/demo/demo2',
  ),
);