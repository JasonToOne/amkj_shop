-- --------------------------------------------------------

--
-- 表的结构 `__PREFIX__datatask_backlog`
--

CREATE TABLE IF NOT EXISTS `__PREFIX__datatask_backlog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `config_id` int(11) NOT NULL,
  `path` varchar(1000) NOT NULL COMMENT '文件地址',
  `client_down_count` int(11) DEFAULT 0 COMMENT '客户端下载次数',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `__PREFIX__datatask_config`
--

CREATE TABLE IF NOT EXISTS `__PREFIX__datatask_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(55) NOT NULL COMMENT '名称',
  `desc` varchar(1000) NOT NULL COMMENT '描述',
  `tables` text NOT NULL COMMENT '数据表',
  `token` varchar(64) NOT NULL COMMENT '下载凭证',
  `max_down` int(5) NOT NULL DEFAULT '1' COMMENT '最大下载次数',
  `back_count` int(5) NOT NULL DEFAULT '0' COMMENT '备份次数',
  `down_count` int(5) NOT NULL DEFAULT '0' COMMENT '下载次数',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `update_time` int(10) NOT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `__PREFIX__datatask_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` int(1) DEFAULT '0' COMMENT '类型',
  `client_type` int(1) NOT NULL DEFAULT '0' COMMENT '客户端',
  `data` text NOT NULL COMMENT '数据',
  `url` varchar(1000) NOT NULL COMMENT 'URL',
  `ip` varchar(20) NOT NULL COMMENT 'Ip',
  `user` varchar(255) NOT NULL COMMENT '用户',
  `useragent` text NOT NULL COMMENT 'user-agent',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
