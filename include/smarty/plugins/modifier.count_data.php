<?php
/*
 * [Skymps]��Ȩ���� ��׼���磬��������Ȩ��
 * This is not a freeware, use is subject to license terms
 *
 * $Id��smarty_modifier_count_data.php Created on 2009-9-2
 * $author��lucks
 */
 function smarty_modifier_count_data($table)
{
    global $db;
    $row = $db->getone("SELECT COUNT(*) AS num FROM ".$table);
    return $row[num];
}



?>
