<?php
/*
 * [bluecms]��Ȩ���� ��׼���磬��������Ȩ��
 * This is not a freeware, use is subject to license terms
 *
 * $Id��filter.inc.php
 * $author��lucks
 */
if (!defined('IN_BLUE'))
{
	die('Access Denied!');
}

foreach (array($_GET, $_POST) as $v)
{
	foreach ($v as $k1 => $v1)
	{
		$$k1 = filter($k1, $v1);
	}
}

function filter($k, &$v)
{
	global $_CFG;
	if (is_array($v))
	{
		foreach($v as $k1 => $v1)
		{
			filter($k1, $v1);
		}
	}
	else
	{
		if ($_CFG['replace_word'] != '')
		{
			$new_v = preg_replace('/'.$_CFG['replace_word'].'/', '**', $v);
		}
	}
	return $new_v;
}

function check_data($data)
{
	
}

?>