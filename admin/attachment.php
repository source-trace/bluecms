<?php
/*
 * [bluecms]��Ȩ���� ��׼���磬��������Ȩ��
 * This is not a freeware, use is subject to license terms
 *
 * $Id��attachment.php
 * $author��lucks
 */
 define('IN_BLUE', true);
 require(dirname(__FILE__).'/include/common.inc.php');
 $act = !empty($_REQUEST['act']) ? trim($_REQUEST['act']) : 'list';
 $att_arr = array('0'=>'�����ı���', '1'=>'�������ֱ�', '2'=>'�����б��͸�����Ϣ', '3'=>'����ѡ���͸�����Ϣ', '4'=>'����ѡ���͸�����Ϣ');
 if($act=='list')
 {
 	$perpage = '20';
 	$page = new page(array('total'=>get_total("SELECT COUNT(*) AS num FROM ".table('attachment')), 'perpage'=>$perpage));
 	$currenpage=$page->nowindex;
 	$offset=($currenpage-1)*$perpage;

 	$att_list = get_list("SELECT att_id, att_name, is_required, att_type, unit, att_val, model_name, a.show_order FROM ".table('attachment')." AS a LEFT JOIN ".table('model')." AS b ON a.modelid = b.model_id", $offset, $perpage);
 	//$att_list = $db->getall("SELECT att_id, att_name, is_required, att_type, unit, att_val, model_name, a.show_order FROM ".table('attachment')." AS a LEFT JOIN ".table('model')." AS b ON a.modelid = b.model_id");
 	template_assign(array('att_list', 'current_act', 'page'), array($att_list, '���������б�', $page->show(3)));
 	$smarty->display('attachment.htm');
 }
 elseif($act == 'add')
 {
 	$model_list = model();
	template_assign(array('model_list', 'act', 'current_act'), array($model_list, $act, '����¸�������'));
	$smarty->display('attachment_info.htm');
 }
 elseif($act == 'doadd')
 {
 	$att_name = !empty($_POST['att_name']) ? trim($_POST['att_name']) : '';
 	$modelid = isset($_POST['modelid']) ? intval($_POST['modelid']) : '';
 	$is_required = intval($_POST['is_required']);
 	$att_type = intval($_POST['att_type']);
 	$unit = !empty($_POST['unit']) ? trim($_POST['unit']) : '';
 	$att_val = !empty($_POST['att_val']) ? trim($_POST['att_val']) : '';
 	$show_order = !empty($_POST['show_order']) ? intval($_POST['show_order']) : 0;
 	if($att_name == '' || $modelid == '')
 	{
 		showmsg('��Ϣ��д������');
 	}
 	$sql = "INSERT INTO ".table('attachment')."(att_id, modelid, att_name, is_required, att_type, unit, att_val, show_order) VALUES ('', '$modelid', '$att_name', '$is_required', '$att_type', '$unit', '$att_val', '$show_order')";
 	if(!$db->query($sql)){
 		showmsg('��Ӹ������Գ���', true);
 	}
 	showmsg('��Ӹ������Գɹ�','attachment.php', true);
 }
 elseif($act == 'edit')
 {
	$model_list = model();
 	$sql = "SELECT att_id, modelid, att_name, is_required, att_type, unit, att_val, show_order FROM ".table('attachment')." WHERE att_id =".intval($_GET['att_id']);
 	$att = $db->getone($sql);
 	template_assign(array('model_list', 'att_options', 'att_list', 'att', 'act', 'current_act'), array($model_list, $att_arr, $att_list, $att, $act, '�༭��������'));
 	$smarty->display('attachment_info.htm');
 }
 elseif($act == 'doedit')
 {
 	$att_name = !empty($_POST['att_name']) ? trim($_POST['att_name']) : '';
 	$modelid = !empty($_POST['modelid']) ? intval($_POST['modelid']) : '';
 	$is_required = intval($_POST['is_required']);
 	$att_type = intval($_POST['att_type']);
 	$unit = !empty($_POST['unit']) ? trim($_POST['unit']) : '';
 	$att_val = !empty($_POST['att_val']) ? trim($_POST['att_val']) : '';
 	$show_order = !empty($_POST['show_order']) ? intval($_POST['show_order']) : 0;
 	if($att_name == '' || $modelid == '' || ($att_type != 0 &&$att_type!= 1 && $att_val == ''))
 	{
 		showmsg('��Ϣ��д������');
 	}
 	$sql = "UPDATE ".table('attachment')." SET att_name='$att_name', modelid='$modelid', is_required='$is_required', att_type='$att_type', unit='$unit', att_val='$att_val', show_order='$show_order' WHERE att_id=".intval($_POST['att_id']);
 	if(!$db->query($sql)){
 		showmsg('�༭�������Գ���', true);
 	}else{
 		showmsg('�༭�������Գɹ�', 'attachment.php', true);
 	}
 }
 elseif($_REQUEST['act'] == 'del')
 {
 	$sql = "DELETE FROM ".table('attachment')." WHERE att_id = ".$_GET['att_id'];
 	if(!$db->query($sql)){
 		showmsg('ɾ���������Գ���', true);
 	}
 	showmsg('ɾ���������Գɹ�','attachment.php', true);
 }




?>