INSERT INTO `blue_ann_cat` (`cid`, `cat_name`, `show_order`) VALUES
(1, '��վ����', 0),
(2, '�����ƹ�', 0),
(3, '��������', 0),
(4, '���ڱ�վ', 0);

INSERT INTO `blue_area` (`area_id`, `area_name`, `parentid`, `area_indent`, `ishavechild`, `show_order`) VALUES ('1', '����һ', '0', '0', '0', '0');

INSERT INTO `blue_card_type` (`id`, `name`, `value`, `price`, `is_close`) VALUES
(1, '����', 100, 30, 0);

INSERT INTO `blue_config` (`name`, `value`) VALUES
('site_name', '��ʾ��վ'),
('site_url', 'http://www.bluecms.net'),
('description', ''),
('keywords', ''),
('tel', '1234567|1234567'),
('icp', ''),
('count', ''),
('isclose', '0'),
('reason', ''),
('cookie_hash', ''),
('url_rewrite', '0'),
('qq', '1234567|1234567'),
('qq_group', '1234567|1234567'),
('right', 'BlueCMS �� ��һ����ѿ�Դ��רҵ�ط��Ż�ϵͳ��רע�ڵط��Ż���CMS��'),
('info_is_check', '0'),
('comment_is_check', '0'),
('news_is_check', '0'),
('is_gzip', '0');

INSERT INTO `blue_pay` (`id`, `code`, `name`, `userid`, `key`, `email`, `description`, `fee`, `logo`, `is_open`, `show_order`) VALUES
(1, 'alipay', '֧����', '', '', '', '֧������վ(www.alipay.com)�ǹ����Ƚ�������֧��ƽ̨����ȫ�����B2B��˾����Ͱ͹�˾���죬������Ϊ���罻���û��ṩ���ʵİ�ȫ֧������', 0.00, 'images/alipay.jpg', 1, 0),
(2, 'bank', '����ת��', '', '', '', '�˺�:\r\n����:dd\r\n������:', 0.00, '', 1, 0);

INSERT INTO `blue_service` (`id`, `name`, `type`, `service`, `price`) VALUES
(1, '�����ö�', 'info', 'top2', '10.00'),
(2, 'С���ö�', 'info', 'top1', '5.00'),
(3, '������Ϣ�Ƽ�', 'info', 'rec', '10.00'),
(4, '������Ϣͷ��', 'info', 'head_line', '10.00'),
(5, '�����ö�', 'company', 'top2', '10.00'),
(6, 'С���ö�', 'company', 'top1', '5.00'),
(7, '�̼һ�ҳ�Ƽ�', 'company', 'rec', '10.00'),
(8, '�̼һ�ҳͷ��', 'company', 'head_line', '10.00');

INSERT INTO `blue_task` (`id`, `name`, `last_time`, `exp`) VALUES
(1, 'update_info', 0, 1);