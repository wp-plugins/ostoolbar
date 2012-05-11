<?php header("Content-Type:text/css");?>
li.toplevel_page_ostoolbar .wp-menu-image a img
{
	display:none;
}

li.toplevel_page_ostoolbar .wp-menu-image
{
	background:url(../images/<?php echo($_REQUEST["icon"]);?>) no-repeat;
	background-position:0 -32px;
}

li.current.toplevel_page_ostoolbar .wp-menu-image, li.toplevel_page_ostoolbar:hover .wp-menu-image
{
	background-position:0 0;
}
