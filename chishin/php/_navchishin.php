<?php
require_once('/php/_blogcomments.php');
//require_once('/php/layout.php');
require_once('/php/nav.php');
require_once('/php/ui/link.php');

function ChishinNavigateBlogGroup($bChinese)
{
    $arBlog = array('20170512', '20170517', '20170523', '20170524', '20180426', '20180429', '20180504', '20180508', '20180512', '20180513', '20180515', '20180519', '20180521');
    
	NavBegin();
    NavDirFirstLast($arBlog);
    NavEnd();
}

function _ChishinLayoutTopLeft()
{
    LayoutTopLeft('ChishinNavigateBlogGroup');
}

function _ChishinLayoutBottom()
{
    EchoBlogComments();
    LayoutTailLogin();
}

   	$acct = new EditCommentAccount();
?>
