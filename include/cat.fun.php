<?php
/*
 * [bluecms]��Ȩ���� ��׼���磬��������Ȩ��
 * This is not a freeware, use is subject to license terms
 *
 * $Id��cat.fun.php
 * $author��lucks
 */
 if(!defined('IN_BLUE'))
 {
 	die('Access Denied!');
 }

  function check_catname($cat_name, $parentid, $cat_id = ''){
 	global $db;
 	if(strlen($cat_name)<4 || strlen($cat_name)>20) showmsg('�����������(����)��2-10֮�䣡');
 	$sql = "SELECT COUNT(*) as catnum FROM ".table('category')." WHERE cat_name='$cat_name' and parentid ='$parentid'";
 	if($cat_id){
 		$sql .= " AND cat_id!='$cat_id'";
 	}
 	$row = $db->getone($sql);
 	if($row['catnum']){
 		showmsg('�÷������Ѵ��ڣ�');
 	}
 }
 function get_cat_name($cat_id){
 	global $db;
 	if(!$cat_id){
 		return false;
 	}else{
 		$cat = $db->getone("SELECT cat_name FROM ".table('category')." WHERE cat_id='$cat_id'");
 		return $cat['cat_name'];
 	}
 }
 /**
  *
  * ȡ�÷�������
  * $parentid	����		�ϼ�������
  *
  */
 function get_catindent($parentid){
 	global $db;
 	$row = $db->getone("SELECT cat_indent FROM ".table('category')." WHERE cat_id='$parentid'");
 	return $row[cat_indent];
 }

 function get_areaindent($parentid){
	 global $db;
	 $row = $db->getone("SELECT area_indent FROM ".table('area')." WHERE area_id='$parentid'");
	 return $row[area_indent];
 }
 /**
  *
  * ȡ�÷�����Ϣ���ุ����id
  * $catid	����		��ǰ�����
  *
  */
 function get_parentid($catid){
 	global $db;
 	$cat = $db->getone("SELECT parentid FROM ".table('category')." WHERE cat_id='$catid'");
 	return $cat['parentid'];
 }

 /**
  *
  * ȡ�����ŷ��ุ��id
  *
  * @param $catid ��ǰ����ID
  *
  */
 function get_arc_parentid($catid){
 	global $db;
 	$cat = $db->getone("SELECT parent_id FROM ".table('arc_cat')." WHERE cat_id='$cat_id'");
 	return $cat['parent_id'];
 }

 /**
  *
  * �жϵ�ǰ�����Ƿ����ӷ���
  *
  */
 function ishavechild($catid){
 	global $db;
 	$result = $db->getone("SELECT COUNT(*) as childnum FROM ".table('category')." WHERE parentid = '$catid'");
 	if($result['childnum']) return true;
 	else return false;
 }

 /**
  *
  * �жϵ�ǰ���������Ƿ����ӷ���
  *
  */
 function area_ishavechild($area_id){
 	global $db;
 	$result = $db->getone("SELECT COUNT(*) as childnum FROM ".table('area')." WHERE parentid = ".$area_id);
 	if($result['childnum']) return true;
 	else return false;
 }


?>