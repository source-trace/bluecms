<?php
$partner = $data[0]['userid'];				//�������ID
$security_code 	= $data[0]['key'];		//��ȫ������
$seller_email 	= $data[0]['email'];	//��������
$_input_charset = 'GBK'; 				//�ַ������ʽ  Ŀǰ֧�� GBK �� utf-8
$sign_type = "MD5"; 					//���ܷ�ʽ  ϵͳĬ��(��Ҫ�޸�)
$transport = "http";					//����ģʽ,����Ը����Լ��ķ������Ƿ�֧��ssl���ʶ�ѡ��http�Լ�https����ģʽ(ϵͳĬ��,��Ҫ�޸�)
$notify_url = BLUE_ROOT."include/payment/alipay/notify_url.php";// �첽���ص�ַ ��Ҫ��д������·��
$return_url = BLUE_ROOT."include/payment/alipay/return_url.php"; //ͬ�����ص�ַ  ��Ҫ��д�������·��
$show_url = ""  						//����վ��Ʒ��չʾ��ַ,����Ϊ��
?>