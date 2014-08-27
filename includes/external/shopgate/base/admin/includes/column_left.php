<?php
defined( '_VALID_XTC' ) or die( 'Direct Access to this location is not allowed.' );
if(strpos(MODULE_PAYMENT_INSTALLED, 'shopgate.php') !== false){
$sg_language_get = (!empty($_GET['sg_language'])
	? '&sg_language='.$_GET['sg_language']
	: ''
);

	echo ('<li class="dropdown menu">');
	echo ('<a href="' . xtc_href_link(FILENAME_SHOPGATE."?sg_option=info{$sg_language_get}", '', 'NONSSL') . '" class="dropdown-toggle">'.BOX_SHOPGATE.' <b class="caret"></b></a>');
	echo ('<ul class="dropdown-menu">');
		echo '<li><a href="' . xtc_href_link(FILENAME_SHOPGATE."?sg_option=info{$sg_language_get}", '', 'NONSSL') . '">' . BOX_SHOPGATE_INFO . '</a></li>';
		echo '<li><a href="' . xtc_href_link(FILENAME_SHOPGATE."?sg_option=help{$sg_language_get}", '', 'NONSSL') . '">'.BOX_SHOPGATE_HELP.'</a></li>';
		echo '<li><a href="' . xtc_href_link(FILENAME_SHOPGATE."?sg_option=register{$sg_language_get}", '', 'NONSSL') . '">'.BOX_SHOPGATE_REGISTER.'</a></li>';
		echo '<li><a href="' . xtc_href_link(FILENAME_SHOPGATE."?sg_option=config{$sg_language_get}", '', 'NONSSL') . '">'.BOX_SHOPGATE_CONFIG.'</a></li>';
		echo '<li><a href="' . xtc_href_link(FILENAME_SHOPGATE."?sg_option=merchant{$sg_language_get}", '', 'NONSSL') . '">'.BOX_SHOPGATE_MERCHANT.'</a></li>';
	
	echo ('</ul>');
	echo ('</li>');

}