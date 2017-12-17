<?php
require_once dirname(__FILE__)."/../../../include/common.inc.php";
require_once BLUE_ROOT.'data/pay.cache.php';
require_once BLUE_ROOT."include/payment/alipay/alipay_config.php";
require_once BLUE_ROOT."include/payment/alipay/alipay_notify.php";

$alipay = new alipay_notify($partner,$security_code,$sign_type,$_input_charset,$transport);
$verify_result = $alipay->return_verify();
//��ȡ֧�����ķ�������
$dingdan		=$_GET['out_trade_no'];   //��ȡ������
$total_fee		=$_GET['total_fee'];    //��ȡ�ܼ۸�

if($verify_result) {
	$id = $dingdan;

	$db->query("UPDATE ".table('card_order')." SET is_pay=1 WHERE id=$id");
	$order = $db->query("SELECT user_id, value FROM ".table('card_order')." WHERE id=$id");
	if (!is_array($order)) {
		showmsg('��ֵ��������ϵ��վ����Ա!', 'user.php');
	}
	$db->query("UPDATE ".table('user')." SET money=money+$order[value] WHERE user_id=$order[user_id]");
	showmsg('��ϲ����ֵ�ɹ�', 'user.php');

	log_result("verify_success"); 
}
else
{
	showmsg('��ֵʧ��');
	log_result ("verify_failed");
	exit;
}

//��־��Ϣ,��֧���������Ĳ�����¼����
function log_result($word)
{
	$fp = fopen(BLUE_ROOT."include/payment/alipay/log.txt","a");
	flock($fp, LOCK_EX) ;
	fwrite($fp,$word."��ִ�����ڣ�".strftime("%Y%m%d%H%I%S",time())."\t\n");
	flock($fp, LOCK_UN);
	fclose($fp);
}
?>