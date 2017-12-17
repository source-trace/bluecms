<?php
/*
 * [bluecms]��Ȩ���� ��׼���磬��������Ȩ��
 * This is not a freeware, use is subject to license terms
 *
 * $Id��pay.php
 * $author��lucks
 */
define('IN_BLUE', true);
require_once dirname(__FILE__) . '/include/common.inc.php';

if (!file_exists(BLUE_ROOT.'data/pay.cache.php') || (is_array($pay_list) && count($pay_list) <= 0)) {
		showmsg('֧�������ļ���ʧ');
}

$act = $_REQUEST['act'] ? trim($_REQUEST['act']) : 'list';
$id = $_REQUEST['id'] ? intval($_REQUEST['id']) : '';

if($act == 'list'){
	$pay_list = $db->getall("SELECT * 
							FROM ".table('pay')." 
							ORDER BY id");
	template_assign(
	    array(
	    	'current_act', 
	    	'act', 
	    	'pay_list'
	    ), 
	    array(
	    	'֧����ʽ�б�', 
	        $act, 
	        $pay_list
        )
    );
	$smarty->display('pay.htm');
}

elseif ($act == 'edit')
{
	if (empty($id))
	{
		return false;
	}
	$pay = $db->getone("SELECT * 
						FROM ".table('pay').
						" WHERE id=".$id);
	template_assign(
	    array(
	    	'current_act', 
	    	'act', 
	    	'pay'
	    ), 
	    array(
	    	'�༭֧����ʽ', 
	        $act, 
	        $pay
        )
    );
	$smarty->display('pay.htm');
}

elseif ($act == 'do_edit') {
	$name = !empty($_POST['name']) ? trim($_POST['name']) : '';
	$userid	= !empty($_POST['userid']) ? trim($_POST['userid']) : '';
	$email = !empty($_POST['email']) ? trim($_POST['email']) : '';
	$key = !empty($_POST['key']) ? trim($_POST['key']) : '';
	$fee = $_POST['fee'];
	$description = !empty($_POST['description']) ? trim($_POST['description']) : '';
	$is_open = intval($_POST['is_open']);
	if (empty($name))
	{
		showmsg('֧����ʽ���Ʋ���Ϊ��');
	}
	
	$db->query("UPDATE ".table('pay')." SET `name`='$name', `userid`='$userid', email='$email', `key`='$key', `fee`='$fee', `description`='$description', `is_open`='$is_open' WHERE `id`=".$id);
	update_pay_cache();
	showmsg('�༭֧���ӿ����óɹ�', 'pay.php');
}
	
?>