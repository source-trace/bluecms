<?php
/*
 * [Skymps] ��Ȩ���� ��׼���磬��������Ȩ��
 * This is not a freeware, use is subject to license terms
 *
 * $Id��model.php
 * $author��lucks
 */
 define('IN_BLUE', true);

 require(dirname(__FILE__).'/include/common.inc.php');
 $act = !empty($_REQUEST['act']) ? trim($_REQUEST['act']) : 'list';
 if($act == 'list'){
 	$model_list = $db->getall("SELECT model_id, model_name, show_order FROM ".table('model')." ORDER BY model_id");
 	template_assign(array('model_list', 'current_act'), array($model_list, '��վģ���б�'));
 	$smarty->display('model.htm');
 }
 elseif($act == 'add'){
 	template_assign(array('act', 'current_act'), array($act, '�����ģ��'));
 	$smarty->display('model_info.htm');
 }
 elseif($act == 'doadd'){
 	$model_name = !empty($_POST['model_name']) ? trim($_POST['model_name']) : '';
 	$show_order = !isset($_POST['show_order']) ? intval($_POST['show_order']) : '';
 	if($model_name == ''){
 		showmsg('ģ�����Ʋ���Ϊ��');
 	}
 	if(!$db->query("INSERT INTO ".table('model')." (model_id, model_name, show_order) VALUES ('', '$model_name', '$show_order')")){
 		showmsg('������ģ�ͳ���', true);
 	}else{
 		showmsg('������ģ�ͳɹ�', 'model.php', true);
 	}
 }
 elseif($act == 'edit'){
 	$model = $db->getone("SELECT model_id, model_name, show_order FROM ".table('model')." WHERE model_id=".intval($_GET['model_id']));
 	template_assign(array('model', 'act', 'current_act'), array($model, $act, '�༭ģ��'));
 	$smarty->display('model_info.htm');
 }
 elseif($act == 'doedit'){
 	$model_name = !empty($_POST['model_name']) ? trim($_POST['model_name']) : '';
 	$show_order = !isset($_POST['show_order']) ? intval($_POST['show_order']) : '';
 	if($model_name == ''){
 		showmsg('ģ�����Ʋ���Ϊ��');
 	}
 	if(!$db->query("UPDATE ".table('model')." SET model_name='$model_name', show_order='$show_order' WHERE model_id=".intval($_POST['model_id']))){
 		showmsg('�༭ģ�ͳ���', true);
 	}else{
 		showmsg('�༭ģ�ͳɹ�', 'model.php', true);
 	}
 }
 elseif($act == 'del'){
 	if(model_has_child($_REQUEST['model_id'])){
 		showmsg('��ģ�ͺ�����Ŀ������ɾ��');
 	}
 	if(!$db->query("DELETE FROM ".table('model')." WHERE model_id=".$_GET['model_id']))
 	{
 		showmsg('ɾ����ģ�ͳ���', true);
 	}else{
 		showmsg('ɾ����ģ�ͳɹ�', 'model.php', true);
 	}
 }






















?>
