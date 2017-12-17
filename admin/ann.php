<?php
/*
 * [Skymps]��Ȩ���� ��׼���磬��������Ȩ��
 * This is not a freeware, use is subject to license terms
 *
 * $Id��ann.php
 * $author��lucks
 */
 define('IN_BLUE', true);
 require(dirname(__FILE__) . '/include/common.inc.php');
 $act = !empty($_REQUEST['act']) ? trim($_REQUEST['act']) : 'list';
 $ann_id = !empty($_REQUEST['ann_id']) ? intval($_REQUEST['ann_id']) : '';
 $cid = !empty($_REQUEST['cid']) ? intval($_REQUEST['cid']) : '';

 if($act == 'list'){
	 $ann_c_list = $db->getall("SELECT cid, cat_name, show_order FROM ".table('ann_cat')." ORDER BY cid");
 	$ann_list = $db->getall("SELECT ann_id, author, title, color, content, add_time, click FROM ".table('ann')." ORDER BY add_time DESC");
 	template_assign(
		array(
			'ann_list', 
			'ann_c_list',
			'act', 
			'current_act'
		), 
		array(
			$ann_list,
			$ann_c_list,
			$act, 
			'��Ϣ�б�'
		)
	);
 	$smarty->display('ann.htm');
 }
 elseif($act == 'add'){
	 $ann_cat = $db->getall("SELECT cid, cat_name FROM ".table('ann_cat')." ORDER BY show_order, cid");
 	template_assign(array('act', 'current_act', 'ann_cat'), array($act, '�������Ϣ', $ann_cat));
 	$smarty->display('ann_info.htm');
 }
 elseif($act == 'do_add'){
 	$title		= !empty($_POST['title']) ? trim($_POST['title']) : '';
 	$color		= !empty($_POST['color']) ? trim($_PST['color']) : '';
	$cid		= $_POST['cid'];	
 	$content	= !empty($_POST['content']) ? trim($_POST['content']) : '';
 	if($title == '' || $content == '' || empty($cid)){
 		showmsg('��Ϣ��д������');
 	}
 	if(!$db->query("INSERT INTO ".table('ann')." (ann_id, cid, author, title, color, content, add_time, click) VALUES ('', '$cid', '".$_SESSION['admin_name']."', '$title', '$color', '$content', '$timestamp', '1')")){
 		showmsg('�������Ϣ����', true);
 	}else{
 		showmsg('�������Ϣ�ɹ�', 'ann.php', true);
 	}
 }
 elseif($act == 'edit'){
	if (empty($ann_id)) {
		return false;
	}
	$ann_cat = $db->getall("SELECT * FROM ".table('ann_cat')." ORDER BY show_order, cid");
 	$ann = $db->getone("SELECT ann_id, title, color, content FROM ".table('ann')." WHERE ann_id = ".$ann_id);
 	template_assign(array('ann', 'act', 'current_act', 'ann_cat'), array($ann, $act, '�༭��Ϣ', $ann_cat));
 	$smarty->display('ann_info.htm');
 }
 elseif($act == 'do_edit'){
	if (empty($ann_id)) {
		return false;
	}
 	$title = !empty($_POST['title']) ? trim($_POST['title']) : '';
 	$color = !empty($_POST['color']) ? trim($_POST['color']) : '';
	$cid = intval($_POST['cid']);
 	$content = !empty($_POST['content']) ? trim($_POST['content']) : '';
 	if($title == '' || $content == '' || empty($cid)){
 		showmsg('��Ϣ��д������');
 	}
 	if(!$db->query("UPDATE ".table('ann')." SET cid='$cid', title='$title', color='$color', content='$content' WHERE ann_id=".$ann_id)){
 		showmsg('�༭��Ϣ����', true);
 	}else{
 		showmsg('�༭��Ϣ�ɹ�', 'ann.php', true);
 	}
 }
 elseif($act == 'del'){
	if (empty($ann_id)) {
		return false;
	}
 	if(!$db->query("DELETE FROM ".table('ann')." WHERE ann_id=".$ann_id)){
 		showmsg('ɾ����Ϣ����', true);
 	}else{
 		showmsg('ɾ����Ϣ�ɹ�', 'ann.php', true);
 	}
 }
 elseif	($act == 'add_cat') {
	template_assign(array('current_act', 'act'), array('���һ���������', $act));
	$smarty->display('ann_cat_info.htm');
 }
 elseif ($act == 'do_add_cat') {
	 $cat_name = !empty($_POST['cat_name']) ? trim($_POST['cat_name']) : '';
	 $show_order = !empty($_POST['show_order']) ? intval($_POST['show_order']) : 0;
	 if (empty($cat_name)) {
		 showmsg('��Ϣ�������Ʋ���Ϊ��');
	 }
	 $db->query("INSERT INTO ".table('ann_cat')." (cid, cat_name, show_order ) VALUES ('', '$cat_name', '$show_order')");
	 showmsg('�����Ϣ����ɹ�', 'ann.php');
 }
 elseif ($act == 'edit_cat') {
	 if (empty($cid)) {
		 return false;
	 }
	 $ann_cat = $db->getone("SELECT cid, cat_name, show_order FROM ".table('ann_cat')." WHERE cid=".$cid);
	 template_assign(array('current_act', 'act', 'ann_cat'), array('�༭��Ϣ����', $act, $ann_cat));
	 $smarty->display('ann_cat_info.htm');
 }
 elseif ($act == 'do_edit_cat') {
	 if (empty($cid)) {
		 return false;
	 }
	 $cat_name= !empty($_POST['cat_name']) ? trim($_POST['cat_name']) : '';
	 $show_order = !empty($_POST['show_order']) ? intval($_POST['show_order']) : 0;
	 if (empty($cat_name)) {
		 showmsg('��Ϣ�������Ʋ���Ϊ��');
	 }
	 $db->query("UPDATE ".table('ann_cat')." SET cat_name='$cat_name', show_order='$show_order' WHERE cid=".$cid);
	 showmsg('�༭��Ϣ����ɹ�', 'ann.php');
 }
 elseif ($act == 'del_ann_cat') {
	 if (empty($cid)) {
		 return false;
	 }
	 $db->query("DELETE FROM ".table('ann_cat')." WHERE cid=".$ann_id);
	 showmsg('ɾ��һ����Ϣ����ɹ�', 'ann.php');
 }
?>