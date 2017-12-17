<?php
/*
 * [bluecms]��Ȩ���� ��׼���磬��������Ȩ��
 * This is not a freeware, use is subject to license terms
 *
 * $Id��arc_cat.php
 * $author��lucks
 */
 define('IN_BLUE', true);
 require_once(dirname(__FILE__) . '/include/common.inc.php');
 $act = $_REQUEST['act'] ? trim($_REQUEST['act']) : 'list';
 $pid = $_REQUEST['pid'] ? intval($_REQUEST['pid']) : 0;
 $cid = $_REQUEST['cid'] ? intval($_REQUEST['cid']) : '';

 if($act == 'list'){
	$sql = "SELECT cat_id, cat_indent, cat_name, is_havechild, show_order FROM ".table('arc_cat')." WHERE parent_id=".$pid." ORDER BY show_order, cat_id";
	$cat_list = $db->getall($sql);
	template_assign(array('parentid', 'dparentid', 'cat_list', 'current_act'),array($pid, get_parentid($pid), $cat_list, '��Ŀ�б�'));
	$smarty->display('arc_cat.htm');
 }

 elseif($act == 'add'){
	template_assign(array('current_act', 'act'), array('�������Ŀ', $act));
	$smarty->display('arc_cat_info.htm');
 }

 elseif($act == 'do_add'){
 	$cat_name = trim($_POST['cat_name']);
 	$parent_id = intval($_POST['parent_id']);
 	$title = !empty($_POST['title']) ? trim($_POST['title']) : '';
 	$keywords = !empty($_POST['keywords']) ? trim($_POST['keywords']) : '';
 	$description = !empty($_POST['description']) ? trim($_POST['description']) : '';
 	$show_order = !empty($_POST['show_order']) ? intval($_POST['show_order']) : '';
 	if($parentid == 0){
 		$cat_indent = 0;
 	}else{
 		$cat_indent = get_catindent($parentid)+1;
 	}

	$sql = "INSERT INTO ".table('arc_cat')." (cat_id, cat_name, parent_id, title, keywords, description, cat_indent, is_havechild, show_order ) VALUES ('', '$cat_name', '$parent_id', '$title', '$keywords', '$description', '$cat_indent', '0', '$show_order')";
	if(!$db->query($sql)){
		showmsg('�����Ŀ����', true);
	}else{
		$sql = "UPDATE ".table('arc_cat')." SET is_havechild='1' where cat_id=$parent_id";
		if(!$db->query($sql)){
			showmsg('������Ŀ����','arc_cat.php', true);
			$db->query("DELETE FROM ".table('arc_cat')." WHERE cat_id=$cat_id");
		}
		showmsg('�����Ŀ�ɹ�','arc_cat.php?pid='.$parent_id, true);
	}
 }

 elseif($act == 'edit'){
 	$sql = "SELECT cat_id, cat_name, parent_id, title, keywords, description, show_order FROM ".table('arc_cat')." WHERE cat_id = $cid";
	$cat = $db->getone($sql);

	template_assign(array('cat', 'act', 'current_act'), array($cat, $act, '�༭��Ŀ'));
	$smarty->display('arc_cat_info.htm');
 }

 elseif($act == 'do_edit'){
 	$cid = intval($_POST['cid']);
	$cat_name = trim($_POST['cat_name']);
 	$parent_id = intval($_POST['parent_id']);
 	$title = !empty($_POST['title']) ? trim($_POST['title']) : '';
 	$keywords = !empty($_POST['keywords']) ? trim($_POST['keywords']) : '';
 	$description = !empty($_POST['description']) ? trim($_POST['description']) : '';
 	$show_order = !empty($_POST['show_order']) ? intval($_POST['show_order']) : '';
 	if($parentid == 0){
 		$cat_indent = 0;
 	}else{
 		$cat_indent = get_catindent($parentid)+1;
 	}

	$sql = "UPDATE ".table('arc_cat')." SET cat_name = '$cat_name',  parent_id = '$parent_id', title = '$title', keywords = '$keywords', description = '$description', cat_indent = '$cat_indent',show_order = '$show_order' WHERE cat_id =$cid";
	if(!$db->query($sql)){
		showmsg('������Ŀ����','arc_cat.php?pid='.$parentid, true);
	}else{
		showmsg('������Ŀ�ɹ�','arc_cat.php', true);
	}
 }

 elseif($act == 'del'){
  	$parent_id = get_arc_parentid($cid);
  	if(empty($cid)){
  		return false;
  	}
  	$arr = $db->getone("SELECT COUNT(*) AS num FROM ".table('article')." WHERE cid = ".$cid);
  	if($arr['num'] > 0){
  		showmsg('�÷����������ţ���ת�����ź���ɾ��');
  	}
  	if(!$db->query("DELETE FROM ".table('arc_cat')." WHERE cat_id = $cid")){
  		showmsg('ɾ���������', true);
  	}else{
  		showmsg('ɾ������ɹ�','arc_cat.php?pid='.$parent_id, true);
  	}
 }



?>