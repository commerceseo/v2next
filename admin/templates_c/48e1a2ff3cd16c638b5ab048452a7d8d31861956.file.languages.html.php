<?php /* Smarty version Smarty-3.1.14, created on 2014-08-27 10:39:13
         compiled from "C:\xampp\htdocs\v2nextce\trunk\admin\templates\default\languages.html" */ ?>
<?php /*%%SmartyHeaderCode:1913653fd9931726744-79540472%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '48e1a2ff3cd16c638b5ab048452a7d8d31861956' => 
    array (
      0 => 'C:\\xampp\\htdocs\\v2nextce\\trunk\\admin\\templates\\default\\languages.html',
      1 => 1409125210,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1913653fd9931726744-79540472',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'HEADING_TITLE' => 0,
    'lang_button' => 0,
    'TABLE_HEADING_LANGUAGE_NAME' => 0,
    'TABLE_HEADING_LANGUAGE_CODE' => 0,
    'TABLE_HEADING_LANGUAGE_STATUS' => 0,
    'TABLE_HEADING_LANGUAGE_STATUS_ADMIN' => 0,
    'TABLE_HEADING_ACTION' => 0,
    'languagearray' => 0,
    'module_data' => 0,
    'DISPLAY_NUMBER' => 0,
    'DISPLAY_SITE' => 0,
    'NEW_BUTTON' => 0,
    'SITE_BOX' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.14',
  'unifunc' => 'content_53fd99317610d8_17181992',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_53fd99317610d8_17181992')) {function content_53fd99317610d8_17181992($_smarty_tpl) {?><div class="row">
	<div class ="col-xs-12">
		<h3><?php echo $_smarty_tpl->tpl_vars['HEADING_TITLE']->value;?>
</h3>
	</div>
	<hr>
</div>

<table class="table">
    <tr>
        <td>
            Welche Sprache soll installiert werden?
            <?php echo $_smarty_tpl->tpl_vars['lang_button']->value;?>

        </td>
    </tr>
</table>
<table class="table">
	<tr>
		<td>
			<table class="table table-striped table-bordered table-hover">
				<tr>
					<th>&nbsp;</th>
					<th><?php echo $_smarty_tpl->tpl_vars['TABLE_HEADING_LANGUAGE_NAME']->value;?>
</th>
					<th><?php echo $_smarty_tpl->tpl_vars['TABLE_HEADING_LANGUAGE_CODE']->value;?>
</th>
					<th><?php echo $_smarty_tpl->tpl_vars['TABLE_HEADING_LANGUAGE_STATUS']->value;?>
</th>
					<th><?php echo $_smarty_tpl->tpl_vars['TABLE_HEADING_LANGUAGE_STATUS_ADMIN']->value;?>
</th>
					<th><?php echo $_smarty_tpl->tpl_vars['TABLE_HEADING_ACTION']->value;?>
</th>
				</tr>
				<?php  $_smarty_tpl->tpl_vars['module_data'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['module_data']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['languagearray']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['module_data']->key => $_smarty_tpl->tpl_vars['module_data']->value){
$_smarty_tpl->tpl_vars['module_data']->_loop = true;
?>
				<?php echo $_smarty_tpl->tpl_vars['module_data']->value['LANG_TR'];?>

					<td><?php echo $_smarty_tpl->tpl_vars['module_data']->value['LANG_ICON'];?>
</td>
					<td><?php echo $_smarty_tpl->tpl_vars['module_data']->value['TD_ROW_LANG'];?>
</td>
					<td><?php echo $_smarty_tpl->tpl_vars['module_data']->value['TD_ROW_CODE'];?>
</td>
					<td><?php echo $_smarty_tpl->tpl_vars['module_data']->value['TD_ROW_STATUS_LANG'];?>
</td>
					<td><?php echo $_smarty_tpl->tpl_vars['module_data']->value['TD_ROW_STATUS_ADMIN_LANG'];?>
</td>
					<td><?php echo $_smarty_tpl->tpl_vars['module_data']->value['TD_ROW_ACTION'];?>
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
                    <td><?php echo $_smarty_tpl->tpl_vars['NEW_BUTTON']->value;?>
</td>
                </tr>
            </table>
        </td>
		<td width="250">
			<?php echo $_smarty_tpl->tpl_vars['SITE_BOX']->value;?>

		</td>
    </tr>
</table>
<?php }} ?>