<?php
/*
 * [bluecms]��Ȩ���� ��׼���磬��������Ȩ��
 * This is not a freeware, use is subject to license terms
 *
 * $Id��
 * $author��lucks
 */
define('IN_BLUE', true);

require_once dirname(__FILE__) . '/include/common.inc.php';
require BLUE_ROOT . 'include/ip.class.php';
$ip = new ip();
$bannedip = !empty($_REQUEST['ip']) ? trim($_REQUEST['ip']) : '';

$act = !empty($_REQUEST['act']) ? trim($_REQUEST['act']) : 'list';

if ($act == 'list') {
	$ip_arr = $ip->list_ip();
	template_assign(array('current_act', 'bannedip_arr'), array('��ֹIP�б�', $ip_arr));
	$smarty->display('ipbanned.htm');
}

elseif ($act == 'add') {
	template_assign(array('current_act', 'act'), array('��ӽ�ֹIP', $act));
	$smarty->display('ipbanned_info.htm');
}

elseif ($act == 'do_add') {
	$exp = !empty($_POST['exp']) ? trim($_POST['exp']) : '';
	if ($ip->check_exists($bannedip)) {
		showmsg('���ѽ�ֹ��IP', 'ipbanned.php');
	} else {
		$ip->add_ip($bannedip, $exp);
	}
	showmsg('��ֹ��IP�ɹ�', 'ipbanned.php');
}

elseif ($act == 'edit') {
	$bannedip_info = $ip->get_ip($bannedip);
	template_assign(array('current_act', 'act', 'bannedip_info'), array('�༭��ֹIP',$act, $bannedip_info));
	$smarty->display('ipbanned_info.htm');
}

elseif ($act == 'do_edit') {
	$old_ip = !empty($_POST['old_ip']) ? trim($_POST['old_ip']) : '';
	$exp = !empty($_POST['exp']) ? trim($_POST['exp']) : '';

	if($ip->edit_ip($old_ip, $bannedip, $exp)) {
		showmsg('��ϲ���༭��ֹIP�ɹ�', 'ipbanned.php', true);
	} else {
		showmsg('�༭��ֹIP����', '', true);
	}
}

elseif ($act == 'del') {
	if ($ip->del_ip($bannedip)) {
		showmsg('��ϲ����ɾ����ֹIP�ɹ�', 'ipbanned.php', true);
	} else {
		showmsg('ɾ����ֹIPʧ��', '', true);
	}

}

 ?>