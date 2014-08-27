<?php /* Smarty version Smarty-3.1.14, created on 2014-08-27 10:39:07
         compiled from "C:\xampp\htdocs\v2nextce\trunk\admin\templates\default\cseo_logo.html" */ ?>
<?php /*%%SmartyHeaderCode:411753fd992b9f0002-94154564%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'c57bb7718b045639ec8c0465d30aaaff6216bd6c' => 
    array (
      0 => 'C:\\xampp\\htdocs\\v2nextce\\trunk\\admin\\templates\\default\\cseo_logo.html',
      1 => 1409125210,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '411753fd992b9f0002-94154564',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'txt' => 0,
    'LOGO_FORM' => 0,
    'LOGO_IMAGE' => 0,
    'LOGO_NAME' => 0,
    'LOGO_FILE' => 0,
    'BUTTON_SUBMIT' => 0,
    'FORM_END' => 0,
    'MAIL_FORM' => 0,
    'MAIL_IMAGE' => 0,
    'MAIL_NAME' => 0,
    'MAIL_FILE' => 0,
    'PDF_FORM' => 0,
    'PDF_IMAGE' => 0,
    'PDF_NAME' => 0,
    'PDF_FILE' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.14',
  'unifunc' => 'content_53fd992ba3e218_16539312',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_53fd992ba3e218_16539312')) {function content_53fd992ba3e218_16539312($_smarty_tpl) {?>
<div class="row">
    <div class="col-md-12">
        <h1><span class="glyphicon glyphicon-file"></span> <?php echo $_smarty_tpl->tpl_vars['txt']->value['heading_logo'];?>
</h1>
    </div>
	<hr>
</div>

<div class="row">
    <div class="col-md-12">
	<?php echo $_smarty_tpl->tpl_vars['LOGO_FORM']->value;?>

        <table class="table table-bordered">
            <tr>
                <th><?php echo $_smarty_tpl->tpl_vars['txt']->value['heading_verwendung'];?>
</th>
                <th><?php echo $_smarty_tpl->tpl_vars['txt']->value['heading_logofile'];?>
</th>
                <th><?php echo $_smarty_tpl->tpl_vars['txt']->value['heading_logoname'];?>
</th>
                <th><?php echo $_smarty_tpl->tpl_vars['txt']->value['heading_file'];?>
</th>
                <th><?php echo $_smarty_tpl->tpl_vars['txt']->value['heading_action'];?>
</th>
            </tr>
			<tr>
            <td><?php echo $_smarty_tpl->tpl_vars['txt']->value['logo_upload'];?>
</td>
            <td><?php echo $_smarty_tpl->tpl_vars['LOGO_IMAGE']->value;?>
</td>
            <td><?php echo $_smarty_tpl->tpl_vars['LOGO_NAME']->value;?>
</td>
            <td><?php echo $_smarty_tpl->tpl_vars['LOGO_FILE']->value;?>
</td>
            <td><?php echo $_smarty_tpl->tpl_vars['BUTTON_SUBMIT']->value;?>
</td>
            </tr>
			<?php echo $_smarty_tpl->tpl_vars['FORM_END']->value;?>

			<?php echo $_smarty_tpl->tpl_vars['MAIL_FORM']->value;?>

			<tr>
            <td><?php echo $_smarty_tpl->tpl_vars['txt']->value['logo_email'];?>
</td>
            <td><?php echo $_smarty_tpl->tpl_vars['MAIL_IMAGE']->value;?>
</td>
            <td><?php echo $_smarty_tpl->tpl_vars['MAIL_NAME']->value;?>
</td>
            <td><?php echo $_smarty_tpl->tpl_vars['MAIL_FILE']->value;?>
</td>
            <td><?php echo $_smarty_tpl->tpl_vars['BUTTON_SUBMIT']->value;?>
</td>
            </tr>
			<?php echo $_smarty_tpl->tpl_vars['FORM_END']->value;?>

			<?php echo $_smarty_tpl->tpl_vars['PDF_FORM']->value;?>

			<tr>
            <td><?php echo $_smarty_tpl->tpl_vars['txt']->value['logo_pdf'];?>
</td>
            <td><?php echo $_smarty_tpl->tpl_vars['PDF_IMAGE']->value;?>
</td>
            <td><?php echo $_smarty_tpl->tpl_vars['PDF_NAME']->value;?>
</td>
            <td><?php echo $_smarty_tpl->tpl_vars['PDF_FILE']->value;?>
</td>
            <td><?php echo $_smarty_tpl->tpl_vars['BUTTON_SUBMIT']->value;?>
</td>
            </tr>
			<?php echo $_smarty_tpl->tpl_vars['FORM_END']->value;?>

        </table>
    </div>
</div>


<?php }} ?>