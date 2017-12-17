<?php
/*
 * [bluecms]��Ȩ���� ��׼���磬��������Ȩ��
 * This is not a freeware, use is subject to license terms
 *
 * $Id��area.php
 * $author��lucks
 */
define('IN_BLUE', true);
 require_once(dirname(__FILE__) . '/include/common.inc.php');
 $act = $_REQUEST['act'] ? trim($_REQUEST['act']) : 'list';
 $pid = $_REQUEST['pid'] ? intval($_REQUEST['pid']) : 0;
 $aid = $_REQUEST['aid'] ? intval($_REQUEST['aid']) : '';
 if($act == 'list'){
	$sql = "SELECT area_id, area_indent, area_name, parentid, ishavechild, show_order FROM ".table('area')." WHERE parentid=".$pid." ORDER BY show_order, area_id";
	$area_list = $db->getall($sql);
	template_assign(array('parentid', 'dparentid', 'area_list', 'current_act'),array($pid, get_area_parentid($pid), $area_list, '�����б�'));
	$smarty->display('area.htm');
 }
/**
  *
  * ��ӵ�������
  *
  */
 elseif($act=='add'){
 	$area_list = get_area_option(0);
	template_assign(array('area_list', 'current_act', 'act'), array($area_list, '����µ���', $act));
	$smarty->display('area_info.htm');
 }
 /**
  *
  * ��ӵ����ύ
  *
  */
 elseif($act=='doadd'){
 	$area_name = empty($_POST['area_name']) ? '' : trim($_POST['area_name']);
 	$parentid = intval($_POST['parentid']);
 	$show_order = !empty($_POST['showorder']) ? intval($_POST['showorder']) : '';
 	if($parentid == 0){
 		$area_indent = 0;
 	}else{
 		$area_indent = get_areaindent($parentid)+1;
 	}
	if(empty($area_name)){
		showmsg('�������Ʋ���Ϊ��');
	}
	$sql = "INSERT INTO ".table('area')." (area_id, area_name, parentid, area_indent, ishavechild, show_order ) VALUES ('', '$area_name', '$parentid', '$area_indent', '0', '$show_order')";
	if(!$db->query($sql)){
		showmsg('����µ�������', true);
	}else{
		$sql = "UPDATE ".table('area')." SET ishavechild='1' where area_id=$parentid";
		if(!$db->query($sql)){
			showmsg('���µ�������','area.php', true);
			$db->query("DELETE FROM ".table('area')." WHERE area_id='$area_id'");
		}
		showmsg('��ӷ���ɹ�','area.php?pid='.$parentid, true);
	}
 }
 /**
  *
  * �༭��������
  *
  */
 elseif($act=='edit'){
	$sql = "SELECT area_id, area_name, parentid, show_order FROM ".table('area')." WHERE area_id = ".$aid;
	$area = $db->getone($sql);
	$area_list = get_area_option(0,$area[parentid], $area[area_id]);
	template_assign(array('area_list', 'area', 'act', 'current_act'), array($area_list, $area, $act, '�༭����'));
	$smarty->display('area_info.htm');
 }
 /**
  *
  * �༭�����ύ
  *
  */
 elseif($act=='doedit'){
	$aid = intval($_POST['aid']);
	$area_name = empty($_POST['area_name']) ? '' : trim($_POST['area_name']);
 	$parentid = intval($_POST['parentid']);
 	$show_order = !empty($_POST['show_order']) ? intval($_POST['show_order']) : '';
 	if($parentid == 0){
 		$area_indent = 0;
 	}else{
 		$area_indent = get_areaindent($parentid)+1;
 	}
	if(empty($area_name)){
		showmsg('�������Ʋ���Ϊ��');
	}
	$old_parentid = get_area_parentid($aid);
	$sql = "UPDATE ".table('area')." SET area_name = '$area_name', parentid = '$parentid', area_indent = '$area_indent',show_order = '$show_order' WHERE area_id =".$aid;
	if(!$db->query($sql)){
		showmsg('���µ����������','area.php', true);
	}else{
		//�������ϼ�����
		if($parentid <> $old_parentid){
			if($parentid != 0){
				$db->query("UPDATE ".table('area')." SET ishavechild = '1' WHERE area_id='$parentid'");
			}
			//����ԭ�ϼ�Ŀ¼��Ϣ
			if(!area_ishavechild($old_parentid)){
				$db->query("UPDATE ".table('area')." SET ishavechild = '0' WHERE area_id = '$old_parentid'");
			}
		}
		showmsg('���µ�������ɹ�','area.php?pid='.$parentid, true);
	}
 }
 /**
  *
  * ɾ��������Ϣ
  *
  */
  elseif($act=='del'){
  	if(ishavechild($aid)){
  		showmsg('�÷������¼����࣬����ɾ����');
  	}
  	$parentid = get_area_parentid($aid);
  	if(!$db->query("DELETE FROM ".table('area')." WHERE area_id = ".$aid)){
  		showmsg('ɾ���������', true);
  	}else{
  		showmsg('ɾ������ɹ�','area.php?pid='.$parentid, true);
  	}
  }




































?>