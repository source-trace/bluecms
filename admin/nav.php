<?php
/*
 * [bluecms]��Ȩ���� ��׼���磬��������Ȩ��
 * This is not a freeware, use is subject to license terms
 *
 * $Id��nav.php
 * $author��lucks
 */
 define('IN_BLUE', true);
 require(dirname(__FILE__) . '/include/common.inc.php');
 $act = !empty($_REQUEST['act']) ? trim($_REQUEST['act']) : 'list';

 if($act == 'list')
 {
 	$navlist = getnav();
 	template_assign(array('current_act','navlist'),array('�������б�', $navlist));
 	$smarty->display('nav.htm');
 }
  elseif($act == 'do_add')
 {
	$navname = !empty($_POST['navname']) ? trim($_POST['navname']) : '';
	$navlink = !empty($_POST['navlink']) ? trim($_POST['navlink']) : '';
	$opennew = intval($_POST['opennew']);
	$type = intval($_POST['type']);
	$showorder = !empty($_POST['showorder']) ? intval($_POST['showorder']) : '0';
	if($navname == '' || $navlink == '')
	{
		$msg = '��Ϣ��д������';
		showmsg($msg);
	}
	$sql="insert into ".table('navigate')." value('','$navname','$navlink','$opennew','$showorder','$type')";
	if(!$db->query($sql)){
		showmsg('�����µ�������', true);
	}else{
		showmsg('�����µ����ɹ�', 'nav.php', true);
	}
 }
 elseif($act=='add')
 {
 	template_assign(array('act','current_act'),array($act, '����µ���'));
	$smarty->display('nav_info.htm');
 }
 elseif($act == 'do_edit')
 {
 	$navname = trim($_POST['navname']);
 	$navlink = trim($_POST['navlink']);
	$opennew = intval($_POST['opennew']);
	$type = intval($_POST['type']);
	$showorder = intval($_POST['showorder']);
	$navid = intval($_POST['navid']);
	if($navname == '' || $navlink == '')
	{
		$msg = '��Ϣ��д������';
		showmsg($msg);
	}
	$sql="update ".table('navigate')." set navname='$navname',navlink='$navlink',opennew='$opennew',showorder = '$showorder', type='$type' where navid=$navid";
	if(!$db->query($sql)){
		showmsg('�༭��������', true);
	}else{
		showmsg('�༭�����ɹ�', 'nav.php', true);
	}
 }
 elseif($act=='edit')
 {
 	$sql = "select * from ".table('navigate')." where navid = ".$_GET['navid'];
 	$nav = $db->getone($sql);
 	$smarty->assign('nav',$nav);
 	$smarty->assign('act', $act	);
 	$smarty->display('nav_info.htm');
 }
 elseif($act == 'del')
 {
	 $navid = intval($_GET['navid']);
	if(empty($navid)){
		return false;
	}
 	$sql = "delete from ".table('navigate')." where navid = ".$navid;
 	if(!$db->query($sql)){
 		showmsg('ɾ������ʧ��', true);
 	}else{
 		showmsg('ɾ�������ɹ�', 'nav.php', true);
 	}
 }


 function getnav()
 {
 	global $db;
 	$sql = "select * from ".table('navigate')." order by type";
 	return $db->getall($sql);
 }

?>
