<?php
require_once(BLUE_ROOT."include/payment/alipay/alipay_config.php");
require_once(BLUE_ROOT."include/payment/alipay/alipay_service.php");
//if($data[0]['fee'] < 0) $data[0]['fee'] = 0;
$parameter = array(
"service" 					=> "trade_create_by_buyer", 	//�������ͣ�����ʵ�ｻ�ף�trade_create_by_buyer����Ҫ��д������
"partner" 					=> $partner,	 				//�����̻���
"return_url" 				=> $return_url,  				//ͬ������
"notify_url" 				=> $notify_url,  				//�첽����
"_input_charset" 			=> $_input_charset,         	//�ַ�����Ĭ��ΪGBK
"subject" 					=> $name,                   	//��Ʒ���ƣ�����
"body" 						=> $name,                   	//��Ʒ����������
"out_trade_no" 				=> $id,                         //��Ʒ�ⲿ���׺ţ�����,ÿ�β��Զ����޸�
"price" 					=> sprintf("%01.2f", $price),   //��Ʒ���ۣ�����
"payment_type"				=>"1",                          // Ĭ��Ϊ1,����Ҫ�޸�
"quantity" 					=> "1",                         //��Ʒ����������
"show_url" 					=> $show_url,            		//��Ʒ�����վ
"seller_email" 				=> $seller_email                //�������䣬����
);
$alipay = new alipay_service($parameter,$security_code,$sign_type);
$link	= $alipay->create_url();

echo '<html>
<head>
	<title>ת��֧����֧��ҳ��</title>
</head>
<body onload="document.alipay.submit();">
	<form name="alipay" action="'.$link.'" method="post">
	</form>
</body>
</html>';
exit;