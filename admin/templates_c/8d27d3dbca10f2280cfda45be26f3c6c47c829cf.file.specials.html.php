<?php /* Smarty version Smarty-3.1.14, created on 2014-08-27 10:39:34
         compiled from "C:\xampp\htdocs\v2nextce\trunk\admin\templates\default\specials.html" */ ?>
<?php /*%%SmartyHeaderCode:2522853fd9946c72c93-43376743%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '8d27d3dbca10f2280cfda45be26f3c6c47c829cf' => 
    array (
      0 => 'C:\\xampp\\htdocs\\v2nextce\\trunk\\admin\\templates\\default\\specials.html',
      1 => 1404835805,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '2522853fd9946c72c93-43376743',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'txt' => 0,
    'EDIT' => 0,
    'FORM' => 0,
    'HIDDEN' => 0,
    'BUTTON_SUBMIT' => 0,
    'BUTTON_CANCEL' => 0,
    'PRODUCT_NAME' => 0,
    'HIDDEN_P' => 0,
    'HIDDEN_UP' => 0,
    'SPECIAL_PRICE' => 0,
    'SPECIAL_PRICE_NETTO' => 0,
    'SPECIAL_QUANTITY' => 0,
    'SPECIAL_DATE' => 0,
    'specialgrouparray' => 0,
    'module_data' => 0,
    'JAVASCRIPT' => 0,
    'FORM_END' => 0,
    'LIST' => 0,
    'specialslistarray' => 0,
    'DISPLAY_NUMBER' => 0,
    'DISPLAY_SITE' => 0,
    'NEW_BUTTON' => 0,
    'SITE_BOX' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.14',
  'unifunc' => 'content_53fd9946ce4135_65779438',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_53fd9946ce4135_65779438')) {function content_53fd9946ce4135_65779438($_smarty_tpl) {?><div class="row">
	<div class ="col-xs-6">
		<h2><?php echo $_smarty_tpl->tpl_vars['txt']->value['HEADING_SPECIALS'];?>
</h2>
	</div>
	<?php if ($_smarty_tpl->tpl_vars['EDIT']->value=='true'){?>
	<?php echo $_smarty_tpl->tpl_vars['FORM']->value;?>

	<?php echo $_smarty_tpl->tpl_vars['HIDDEN']->value;?>

	<div class ="col-xs-6 text-right">
		<?php echo $_smarty_tpl->tpl_vars['BUTTON_SUBMIT']->value;?>
 <?php echo $_smarty_tpl->tpl_vars['BUTTON_CANCEL']->value;?>

	</div>
	<table class="table table-striped table-bordered">
		<tr>
			<td><?php echo $_smarty_tpl->tpl_vars['txt']->value['TEXT_SPECIALS_PRODUCT'];?>
</td>
			<td><?php echo $_smarty_tpl->tpl_vars['PRODUCT_NAME']->value;?>
<?php echo $_smarty_tpl->tpl_vars['HIDDEN_P']->value;?>
<?php echo $_smarty_tpl->tpl_vars['HIDDEN_UP']->value;?>
</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td><?php echo $_smarty_tpl->tpl_vars['txt']->value['TEXT_SPECIALS_PRICE'];?>
</td>
			<td><?php echo $_smarty_tpl->tpl_vars['SPECIAL_PRICE']->value;?>
 <?php echo $_smarty_tpl->tpl_vars['SPECIAL_PRICE_NETTO']->value;?>
</td>
			<td><?php echo $_smarty_tpl->tpl_vars['txt']->value['SPECIAL_PRICE_HELP'];?>
</td>
		</tr>
		<tr>
			<td><?php echo $_smarty_tpl->tpl_vars['txt']->value['TEXT_SPECIALS_SPECIAL_QUANTITY'];?>
</td>
			<td><?php echo $_smarty_tpl->tpl_vars['SPECIAL_QUANTITY']->value;?>
</td>
			<td><?php echo $_smarty_tpl->tpl_vars['txt']->value['QUANTITY_HELP'];?>
</td>
		</tr>
		<tr>
			<td><?php echo $_smarty_tpl->tpl_vars['txt']->value['TEXT_SPECIALS_EXPIRES_DATE'];?>
</td>
			<td><?php echo $_smarty_tpl->tpl_vars['SPECIAL_DATE']->value;?>
</td>
			<td><?php echo $_smarty_tpl->tpl_vars['txt']->value['SPECIAL_DATE_HELP'];?>
</td>
		</tr>
	</table>
	<h3><?php echo $_smarty_tpl->tpl_vars['txt']->value['TEXT_SPECIALS_GROUPP'];?>
</h3>
	<table class="table table-striped table-bordered">
		<tr>
			<th><?php echo $_smarty_tpl->tpl_vars['txt']->value['GROUNAME'];?>
</th>
			<th><?php echo $_smarty_tpl->tpl_vars['txt']->value['INPUTFIELD'];?>
</th>
			<th><?php echo $_smarty_tpl->tpl_vars['txt']->value['NETTO'];?>
</th>
		</tr>
		<?php  $_smarty_tpl->tpl_vars['module_data'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['module_data']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['specialgrouparray']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['module_data']->key => $_smarty_tpl->tpl_vars['module_data']->value){
$_smarty_tpl->tpl_vars['module_data']->_loop = true;
?>
		<tr>
			<td><?php echo $_smarty_tpl->tpl_vars['module_data']->value['GROUNAME'];?>
</td>
			<td><?php echo $_smarty_tpl->tpl_vars['module_data']->value['INPUTFIELD'];?>
</td>
			<td><?php echo $_smarty_tpl->tpl_vars['module_data']->value['NETTO'];?>
</td>
		</tr>
		<?php } ?>
	</table>
	<div class ="col-xs-12">
		<p><?php echo $_smarty_tpl->tpl_vars['txt']->value['GROUP_PRICE_TIP'];?>
</p>
	</div>
	<div class ="col-xs-12 text-right">
		<?php echo $_smarty_tpl->tpl_vars['BUTTON_SUBMIT']->value;?>
 <?php echo $_smarty_tpl->tpl_vars['BUTTON_CANCEL']->value;?>

	</div>

	<?php echo $_smarty_tpl->tpl_vars['JAVASCRIPT']->value;?>

	<?php echo $_smarty_tpl->tpl_vars['FORM_END']->value;?>

	<?php }?>
	<?php if ($_smarty_tpl->tpl_vars['LIST']->value=='true'){?>
<table class="table">
	<tr>
		<td>
			<table class="table table-striped table-bordered table-hover">
				<tr>
					<th><?php echo $_smarty_tpl->tpl_vars['txt']->value['TABLE_HEADING_PRODUCTS'];?>
</th>
					<th><?php echo $_smarty_tpl->tpl_vars['txt']->value['TABLE_HEADING_PRODUCTS_PRICE'];?>
</th>
					<th><?php echo $_smarty_tpl->tpl_vars['txt']->value['TABLE_HEADING_STATUS'];?>
</th>
					<th><?php echo $_smarty_tpl->tpl_vars['txt']->value['TABLE_HEADING_ACTION'];?>
</th>
				</tr>
				<?php  $_smarty_tpl->tpl_vars['module_data'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['module_data']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['specialslistarray']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['module_data']->key => $_smarty_tpl->tpl_vars['module_data']->value){
$_smarty_tpl->tpl_vars['module_data']->_loop = true;
?>
				<tr<?php echo $_smarty_tpl->tpl_vars['module_data']->value['TR_ONCLICK'];?>
>
					<td><?php echo $_smarty_tpl->tpl_vars['module_data']->value['PNAME'];?>
</td>
					<td><?php echo $_smarty_tpl->tpl_vars['module_data']->value['OPRICE'];?>
<?php echo $_smarty_tpl->tpl_vars['module_data']->value['SPRICE'];?>
</td>
					<td><?php echo $_smarty_tpl->tpl_vars['module_data']->value['STATUS'];?>
</td>
					<td><?php echo $_smarty_tpl->tpl_vars['module_data']->value['ACTION'];?>
</td>
				</tr>
				<?php } ?>
			</table>
            <table class="table">
                <tr>
                    <td><i><?php echo $_smarty_tpl->tpl_vars['DISPLAY_NUMBER']->value;?>
</i></td>
                    <td align="right"><i><?php echo $_smarty_tpl->tpl_vars['DISPLAY_SITE']->value;?>
</i></td>
                </tr>
            </table>
            <table class="table">
                <tr>
                    <td><a href="<?php echo $_smarty_tpl->tpl_vars['NEW_BUTTON']->value;?>
"><button class="btn btn-success"><?php echo $_smarty_tpl->tpl_vars['txt']->value['NEW_BUTTON'];?>
</button></a></td>
                </tr>
            </table>
        </td>
		<td width="250">
			<?php echo $_smarty_tpl->tpl_vars['SITE_BOX']->value;?>

		</td>
    </tr>
</table>
	<?php }?>
</div><?php }} ?>