<?php
/*
 * [Skymps]��Ȩ���� ��׼���磬��������Ȩ��
 * This is not a freeware, use is subject to license terms
 *
 * $Id��database.fun.php Created on 2009-9-3
 * $author��lucks
 */
 function write_head($table){
	global $db;
	$sql = '';
	$sql .= "DROP TABLE IF EXISTS `".$table."`;\r\n";
	$row = $db->getone("SHOW CREATE TABLE ".$table);
	$sql .=$row['Create Table'].";\r\n";
	return $sql;
}

function write_data($table){
	global $db;
	$sql = '';
	$result = $db->query("SELECT * FROM ".$table);
	$field_num = $db->num_fields($result);
	while($data = $db->fetch_array($result)){
		$sql .= "INSERT INTO `".$table."` VALUES (";
		for($i=0;$i<$field_num;$i++){
			$sql .= "'".$data[$i]."',";
		}
		$sql = substr($sql,0,-1).");\r\n";
	}
	return $sql;
}

function write_file($file, $sql){
	if(!$fp=@fopen($file, "w+")){
		showmsg('��Ŀ���ļ�����');
	}
	if(!@fwrite($fp, $sql)){
		showmsg('д�����ݳ���');
	}
	if(!@fclose($fp)){
		showmsg('�ر�Ŀ���ļ�����');
	}
	return true;
}

function get_head($file){
	$file_info = array('bluecms_ver'=>'', 'mysql_ver'=> '', 'add_time'=>'');
    if (!$fp = @fopen($file,'rb'))
	{
		showmsg('���ļ�'.$file.'ʧ��');
	}
    $str = fread($fp, 200);
    @fclose($fp);
    $arr = explode("\n", $str);
    foreach ($arr AS $val){
        $pos = strpos($val, ':');
        if ($pos > 0){
            $type = trim(substr($val, 0, $pos), "-\n\r\t ");
            $value = trim(substr($val, $pos+1), "/\n\r\t ");
            if ($type == 'BlueCMS VERSION'){
                $file_info['bluecms_ver'] = $value;
            }
            elseif ($type == 'Mysql VERSION'){
                $file_info['mysql_ver'] = substr($value,0,3);
            }
            elseif ($type == 'Create time'){
                $file_info['add_time'] = $value;
            }
        }
    }
    return $file_info;
}

function remove_comment($str)
{
    return (substr($str, 0, 2) != '--');
}
?>
