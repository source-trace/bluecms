<?php
/*
 * [bluecms]��Ȩ���� ��׼���磬��������Ȩ��
 * This is not a freeware, use is subject to license terms
 *
 * $Id��common.fun.php
 * $author��lucks
 */
 if(!defined('IN_BLUE'))
 {
 	die('Access Denied!');
 }

 function install_deep_addslashes($str){
 	if(is_array($str)){
		foreach($str as $key=>$val){
			$str[$key] = install_deep_addslashes($val);
		}
	} else {
		$str = addslashes($str);
	}
	return $str;
 }

 function install_showmsg($msg,$gourl='goback', $is_write = false)
 {
 	global $install_smarty;
 	$install_smarty->assign("msg",$msg);
 	$install_smarty->assign("gourl",$gourl);
 	$install_smarty->display("showmsg.htm");
 	exit();
 }

 function install_template_assign($val1='', $val2=''){
 	global $install_smarty, $_CFG;
 	if(is_array($val1) && is_array($val2)){
 		if(count($val1) != count($val2)){
 			showmsg('���������һ��');
 		}
 		foreach($val1 as $key => $val){
 			$install_smarty->assign($val1[$key],$val2[$key]);
 		}
 	}else{
 		$install_smarty->assign($val1, $val2);
 	}
	$install_smarty->assign('charset', BLUE_CHARSET);
 }

 function check_dirs($dirs)
{
    $checked_dirs = array();

    foreach ($dirs AS $k=> $dir)
    {
        if (!file_exists(BLUE_ROOT .'/'. $dir))
        {
            showmsg($dir.'��Ŀ¼���ļ������ڣ�����');
        }
		$checked_dirs[$k]['dir'] = $dir;
        if (is_readable(BLUE_ROOT.'/'.$dir))
        {
            $checked_dirs[$k]['read'] = '<span style="color:green;">�̿ɶ�</span>';
        }else{
            $checked_dirs[$k]['read'] = '<span sylt="color:red;">�����ɶ�</span>';
        }
        if(is_writable(BLUE_ROOT.'/'.$dir)){
        	$checked_dirs[$k]['write'] = '<span style="color:green;">�̿�д</span>';
        }else{
        	$checked_dirs[$k]['write'] = '<span sylt="color:red;">������д</span>';
        }
    }

    return $checked_dirs;
}



?>
