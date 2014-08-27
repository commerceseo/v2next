<?php /* Smarty version Smarty-3.1.14, created on 2014-08-27 10:37:28
         compiled from "C:\xampp\htdocs\v2nextce\trunk\admin\templates\default\box_manager.html" */ ?>
<?php /*%%SmartyHeaderCode:2566753fd98c8906ee9-00617492%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'c3f12d8ff49e8920efa4eea5841cb608221c3f15' => 
    array (
      0 => 'C:\\xampp\\htdocs\\v2nextce\\trunk\\admin\\templates\\default\\box_manager.html',
      1 => 1404899801,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '2566753fd98c8906ee9-00617492',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'BOX_LIST' => 0,
    'txt' => 0,
    'FORMSORT' => 0,
    'BOXFILTERNAME' => 0,
    'BOXFILTERINTERN' => 0,
    'BOXFILTERPOSITION' => 0,
    'BOXFILTERAKTIV' => 0,
    'BOXFILTERMOBILEAKTIV' => 0,
    'BOXFILTERNAMEAKTIV' => 0,
    'BOX_NEW' => 0,
    'BOX_NEW_POS' => 0,
    'HIDDENSORT' => 0,
    'FORMEND' => 0,
    'BOX_SORT_TITLE' => 0,
    'BOX_SORT_NAME' => 0,
    'BOX_SORT_POSITION' => 0,
    'BOX_SORT_SORT' => 0,
    'BOX_SORT_STATUS' => 0,
    'BOX_SORT_MOBILE' => 0,
    'BOX_SORT_NAMESTATUS' => 0,
    'BOX_SORT_TYP' => 0,
    'boxlistarray' => 0,
    'module_data' => 0,
    'FORM' => 0,
    'BUTTON_SUBMIT' => 0,
    'BUTTON_CANCEL' => 0,
    'NEW_POSITION' => 0,
    'NEW_SORT' => 0,
    'NEW_STATUS' => 0,
    'NEW_NAME_MOBILE' => 0,
    'NEW_NAME_STATUS' => 0,
    'BOX_TYPE' => 0,
    'NEW_NAME' => 0,
    'boxnewarray' => 0,
    'module_edit' => 0,
    'HIDDEN_SAVE' => 0,
    'HIDDEN_NAME' => 0,
    'SCRIPT' => 0,
    'FORM_END' => 0,
    'BOX_EDIT' => 0,
    'boxeditarray' => 0,
    'module_boxedit' => 0,
    'BOX_POSITION' => 0,
    'BOX_SORT' => 0,
    'BOX_STATUS' => 0,
    'BOX_MOBILE' => 0,
    'BOX_NAME_STATUS' => 0,
    'HIDDEN' => 0,
    'BOX_POS_NAME' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.14',
  'unifunc' => 'content_53fd98c8a04d91_56336157',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_53fd98c8a04d91_56336157')) {function content_53fd98c8a04d91_56336157($_smarty_tpl) {?><?php if ($_smarty_tpl->tpl_vars['BOX_LIST']->value=='true'){?>
<div class="row">
    <div class="col-md-12">
        <h1><span class="glyphicon glyphicon-th-large"></span> <?php echo $_smarty_tpl->tpl_vars['txt']->value['heading_box_manager'];?>
</h1>
    </div>

</div>
<div class="row">
    <div class="col-md-12">
		<?php echo $_smarty_tpl->tpl_vars['FORMSORT']->value;?>

		<table class="table table-bordered table-striped">
			<tr>
				<td><?php echo $_smarty_tpl->tpl_vars['txt']->value['filter'];?>
</td>
				<td class="text-center"><?php echo $_smarty_tpl->tpl_vars['txt']->value['filter_name'];?>
 <?php echo $_smarty_tpl->tpl_vars['BOXFILTERNAME']->value;?>
</td>
				<td class="text-center"><?php echo $_smarty_tpl->tpl_vars['txt']->value['filter_intern'];?>
 <?php echo $_smarty_tpl->tpl_vars['BOXFILTERINTERN']->value;?>
</td>
				<td class="text-center"><?php echo $_smarty_tpl->tpl_vars['txt']->value['filter_position'];?>
 <?php echo $_smarty_tpl->tpl_vars['BOXFILTERPOSITION']->value;?>
</td>
				<td class="text-center"><?php echo $_smarty_tpl->tpl_vars['txt']->value['filter_aktiv'];?>
 <?php echo $_smarty_tpl->tpl_vars['BOXFILTERAKTIV']->value;?>
</td>
				<td class="text-center"><?php echo $_smarty_tpl->tpl_vars['txt']->value['filter_mobileaktiv'];?>
 <?php echo $_smarty_tpl->tpl_vars['BOXFILTERMOBILEAKTIV']->value;?>
</td>
				<td class="text-center"><?php echo $_smarty_tpl->tpl_vars['txt']->value['filter_nameaktiv'];?>
 <?php echo $_smarty_tpl->tpl_vars['BOXFILTERNAMEAKTIV']->value;?>
</td>
				<td class="text-center"><?php echo $_smarty_tpl->tpl_vars['BOX_NEW']->value;?>
</td>
				<td class="text-center"><?php echo $_smarty_tpl->tpl_vars['BOX_NEW_POS']->value;?>
</td>
			</tr>
		</table>
		<?php echo $_smarty_tpl->tpl_vars['HIDDENSORT']->value;?>

		<?php echo $_smarty_tpl->tpl_vars['FORMEND']->value;?>

    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <table class="table table-bordered table-striped">
            <tr>
                <th><?php echo $_smarty_tpl->tpl_vars['txt']->value['heading_name'];?>
 <?php echo $_smarty_tpl->tpl_vars['BOX_SORT_TITLE']->value;?>
</th>
                <th><?php echo $_smarty_tpl->tpl_vars['txt']->value['heading_name_intern'];?>
 <?php echo $_smarty_tpl->tpl_vars['BOX_SORT_NAME']->value;?>
</th>
                <th><?php echo $_smarty_tpl->tpl_vars['txt']->value['heading_position'];?>
 <?php echo $_smarty_tpl->tpl_vars['BOX_SORT_POSITION']->value;?>
</th>
                <th><?php echo $_smarty_tpl->tpl_vars['txt']->value['heading_sort'];?>
 <?php echo $_smarty_tpl->tpl_vars['BOX_SORT_SORT']->value;?>
</th>
                <th><?php echo $_smarty_tpl->tpl_vars['txt']->value['heading_active'];?>
 <?php echo $_smarty_tpl->tpl_vars['BOX_SORT_STATUS']->value;?>
</th>
                <th><?php echo $_smarty_tpl->tpl_vars['txt']->value['heading_mobile'];?>
 <?php echo $_smarty_tpl->tpl_vars['BOX_SORT_MOBILE']->value;?>
</th>
                <th><?php echo $_smarty_tpl->tpl_vars['txt']->value['heading_active_name'];?>
 <?php echo $_smarty_tpl->tpl_vars['BOX_SORT_NAMESTATUS']->value;?>
</th>
                <th><?php echo $_smarty_tpl->tpl_vars['txt']->value['heading_typ'];?>
 <?php echo $_smarty_tpl->tpl_vars['BOX_SORT_TYP']->value;?>
</th>
                <th><?php echo $_smarty_tpl->tpl_vars['txt']->value['heading_delete'];?>
</th>
            </tr>
            <?php  $_smarty_tpl->tpl_vars['module_data'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['module_data']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['boxlistarray']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['module_data']->key => $_smarty_tpl->tpl_vars['module_data']->value){
$_smarty_tpl->tpl_vars['module_data']->_loop = true;
?>
            <tr>
                <td><?php echo $_smarty_tpl->tpl_vars['module_data']->value['BOX_TITLE'];?>
 <?php echo $_smarty_tpl->tpl_vars['module_data']->value['BOX_EDIT'];?>
</td>
                <td><?php echo $_smarty_tpl->tpl_vars['module_data']->value['BOX_NAME'];?>
 <?php echo $_smarty_tpl->tpl_vars['module_data']->value['BOX_EDIT'];?>
</td>
                <td><?php echo $_smarty_tpl->tpl_vars['module_data']->value['BOX_POSITION'];?>
</td>
                <td><?php echo $_smarty_tpl->tpl_vars['module_data']->value['BOX_SORT_ID'];?>
</td>
                <td id="box_bs_<?php echo $_smarty_tpl->tpl_vars['module_data']->value['BOX_BID'];?>
"><?php echo $_smarty_tpl->tpl_vars['module_data']->value['BOX_STATUS'];?>
</td>
                <td id="box_bm_<?php echo $_smarty_tpl->tpl_vars['module_data']->value['BOX_BMID'];?>
"><?php echo $_smarty_tpl->tpl_vars['module_data']->value['BOX_MOBILE'];?>
</td>
                <td id="box_bn_<?php echo $_smarty_tpl->tpl_vars['module_data']->value['BOX_BNID'];?>
"><?php echo $_smarty_tpl->tpl_vars['module_data']->value['BOX_N_STATUS'];?>
</td>
                <td><?php echo $_smarty_tpl->tpl_vars['module_data']->value['BOX_TYPE'];?>
</td>
                <td><?php echo $_smarty_tpl->tpl_vars['module_data']->value['BOX_TYPE_LINK'];?>
</td>

            </tr>
            <?php } ?>
        </table>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
		<?php echo $_smarty_tpl->tpl_vars['FORMSORT']->value;?>

		<table class="table table-bordered table-striped">
			<tr>
				<td><?php echo $_smarty_tpl->tpl_vars['txt']->value['filter'];?>
</td>
				<td class="text-center"><?php echo $_smarty_tpl->tpl_vars['txt']->value['filter_name'];?>
 <?php echo $_smarty_tpl->tpl_vars['BOXFILTERNAME']->value;?>
</td>
				<td class="text-center"><?php echo $_smarty_tpl->tpl_vars['txt']->value['filter_intern'];?>
 <?php echo $_smarty_tpl->tpl_vars['BOXFILTERINTERN']->value;?>
</td>
				<td class="text-center"><?php echo $_smarty_tpl->tpl_vars['txt']->value['filter_position'];?>
 <?php echo $_smarty_tpl->tpl_vars['BOXFILTERPOSITION']->value;?>
</td>
				<td class="text-center"><?php echo $_smarty_tpl->tpl_vars['txt']->value['filter_aktiv'];?>
 <?php echo $_smarty_tpl->tpl_vars['BOXFILTERAKTIV']->value;?>
</td>
				<td class="text-center"><?php echo $_smarty_tpl->tpl_vars['txt']->value['filter_mobileaktiv'];?>
 <?php echo $_smarty_tpl->tpl_vars['BOXFILTERMOBILEAKTIV']->value;?>
</td>
				<td class="text-center"><?php echo $_smarty_tpl->tpl_vars['txt']->value['filter_nameaktiv'];?>
 <?php echo $_smarty_tpl->tpl_vars['BOXFILTERNAMEAKTIV']->value;?>
</td>
				<td class="text-center"><?php echo $_smarty_tpl->tpl_vars['BOX_NEW']->value;?>
</td>
				<td class="text-center"><?php echo $_smarty_tpl->tpl_vars['BOX_NEW_POS']->value;?>
</td>
			</tr>
		</table>
		<?php echo $_smarty_tpl->tpl_vars['HIDDENSORT']->value;?>

		<?php echo $_smarty_tpl->tpl_vars['FORMEND']->value;?>

    </div>
</div>
<?php }?>

<?php if ($_smarty_tpl->tpl_vars['BOX_NEW']->value=='true'){?>
<?php echo $_smarty_tpl->tpl_vars['FORM']->value;?>

<div class="row">
    <div class="col-md-8">
        <h4><span class="glyphicon glyphicon-th-large"></span> <?php echo $_smarty_tpl->tpl_vars['txt']->value['heading_new_box'];?>
</h4>
    </div>
    <div class="col-md-4 text-right">
        <?php echo $_smarty_tpl->tpl_vars['BUTTON_SUBMIT']->value;?>

        <?php echo $_smarty_tpl->tpl_vars['BUTTON_CANCEL']->value;?>

    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <table class="table table-bordered table-striped">
            <tr>
                <th><?php echo $_smarty_tpl->tpl_vars['txt']->value['box_config'];?>
</th>
                <th><?php echo $_smarty_tpl->tpl_vars['txt']->value['box_name'];?>
</th>
            </tr>
            <tr>
                <td>
                    <table class="table table-bordered table-striped">
                        <tr>
                            <td><?php echo $_smarty_tpl->tpl_vars['txt']->value['box_position'];?>
</td>
                            <td><?php echo $_smarty_tpl->tpl_vars['NEW_POSITION']->value;?>
</td>
                        </tr>
                        <tr>
                            <td><?php echo $_smarty_tpl->tpl_vars['txt']->value['box_sort'];?>
</td>
                            <td><?php echo $_smarty_tpl->tpl_vars['NEW_SORT']->value;?>
</td>
                        </tr>
                        <tr>
                            <td><?php echo $_smarty_tpl->tpl_vars['txt']->value['box_status'];?>
</td>
                            <td><?php echo $_smarty_tpl->tpl_vars['NEW_STATUS']->value;?>
</td>
                        </tr>
                        <tr>
                            <td><?php echo $_smarty_tpl->tpl_vars['txt']->value['box_mobile'];?>
</td>
                            <td><?php echo $_smarty_tpl->tpl_vars['NEW_NAME_MOBILE']->value;?>
</td>
                        </tr>
                        <tr>
                            <td><?php echo $_smarty_tpl->tpl_vars['txt']->value['box_status_title'];?>
</td>
                            <td><?php echo $_smarty_tpl->tpl_vars['NEW_NAME_STATUS']->value;?>
</td>
                        </tr>
                        <tr>
                            <td><?php echo $_smarty_tpl->tpl_vars['txt']->value['box_type'];?>
</td>
                            <td><?php echo $_smarty_tpl->tpl_vars['BOX_TYPE']->value;?>
</td>
                        </tr>
                        <tr>
                            <td><?php echo $_smarty_tpl->tpl_vars['txt']->value['box_intern'];?>
</td>
                            <td><?php echo $_smarty_tpl->tpl_vars['NEW_NAME']->value;?>
</td>
                        </tr>
                    </table>
                </td>
                <td>
                    <table class="table table-bordered table-striped">
                        <tr>
                            <td>
								<div class="tab-content">
									<?php  $_smarty_tpl->tpl_vars['module_edit'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['module_edit']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['boxnewarray']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['module_edit']->key => $_smarty_tpl->tpl_vars['module_edit']->value){
$_smarty_tpl->tpl_vars['module_edit']->_loop = true;
?>
										<div class ="row">
											<div class ="col-md-2 col-xs-12 col-lg-2 col-sm-12">
												<?php echo $_smarty_tpl->tpl_vars['module_edit']->value['lang_images'];?>
&nbsp;<?php echo $_smarty_tpl->tpl_vars['txt']->value['box_title'];?>

											</div>
											<div class ="col-md-6 col-xs-12 col-lg-6 col-sm-12">
												<?php echo $_smarty_tpl->tpl_vars['module_edit']->value['boxtitle'];?>

											</div>
										</div>
										<div class ="row">
											<div class ="col-md-2 col-xs-12 col-lg-2 col-sm-12">
												<?php echo $_smarty_tpl->tpl_vars['module_edit']->value['lang_images'];?>
&nbsp;<?php echo $_smarty_tpl->tpl_vars['txt']->value['box_text'];?>

											</div>
											<div class ="col-md-10 col-xs-12 col-lg-10 col-sm-12">
												<?php echo $_smarty_tpl->tpl_vars['module_edit']->value['boxtextfiled'];?>

												<?php if ($_smarty_tpl->tpl_vars['module_edit']->value['field_sdesc_wy']=='1'){?>
												<script type="text/javascript">
													var newCKEdit = CKEDITOR.replace(new_box_<?php echo $_smarty_tpl->tpl_vars['module_edit']->value['langid'];?>
);
															CKFinder.setupCKEditor(newCKEdit, 'includes/editor/ckfinder/');
												</script>
												<?php }?>
											</div>
										</div>
									<hr>
									<?php } ?>
								</div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>
</div>
<?php echo $_smarty_tpl->tpl_vars['HIDDEN_SAVE']->value;?>

<?php echo $_smarty_tpl->tpl_vars['HIDDEN_NAME']->value;?>


<div class="row">
    <div class="col-md-12 text-right">
        <?php echo $_smarty_tpl->tpl_vars['BUTTON_SUBMIT']->value;?>

        <?php echo $_smarty_tpl->tpl_vars['BUTTON_CANCEL']->value;?>

    </div>
    <br>
</div>

<?php echo $_smarty_tpl->tpl_vars['SCRIPT']->value;?>

<?php echo $_smarty_tpl->tpl_vars['FORM_END']->value;?>

<?php }?>



<?php if ($_smarty_tpl->tpl_vars['BOX_EDIT']->value=='true'){?>
<?php echo $_smarty_tpl->tpl_vars['FORM']->value;?>

<div class="row">
    <div class="col-md-8">
        <h4><span class="glyphicon glyphicon-th-large"></span> <?php echo $_smarty_tpl->tpl_vars['txt']->value['heading_edit_box'];?>
</h4>
    </div>
    <div class="col-md-4 text-right">
        <?php echo $_smarty_tpl->tpl_vars['BUTTON_SUBMIT']->value;?>

        <?php echo $_smarty_tpl->tpl_vars['BUTTON_CANCEL']->value;?>

    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <table class="table table-bordered table-striped">
            <tr>
                <th><?php echo $_smarty_tpl->tpl_vars['txt']->value['heading_title'];?>
</th>
                <th><?php echo $_smarty_tpl->tpl_vars['txt']->value['heading_position'];?>
</th>
                <th><?php echo $_smarty_tpl->tpl_vars['txt']->value['heading_sort'];?>
</th>
                <th><?php echo $_smarty_tpl->tpl_vars['txt']->value['box_active'];?>
</th>
                <th><?php echo $_smarty_tpl->tpl_vars['txt']->value['box_mobile'];?>
</th>
                <th><?php echo $_smarty_tpl->tpl_vars['txt']->value['box_active_name'];?>
</th>

            </tr>
            <tr>
                <td>
                    <?php  $_smarty_tpl->tpl_vars['module_boxedit'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['module_boxedit']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['boxeditarray']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['module_boxedit']->key => $_smarty_tpl->tpl_vars['module_boxedit']->value){
$_smarty_tpl->tpl_vars['module_boxedit']->_loop = true;
?>
						<?php echo $_smarty_tpl->tpl_vars['module_boxedit']->value['lang_images'];?>
&nbsp;<?php echo $_smarty_tpl->tpl_vars['module_boxedit']->value['langname'];?>
: <?php echo $_smarty_tpl->tpl_vars['module_boxedit']->value['boxtitle'];?>
<br>
                    <?php } ?>
                </td>
                <td><?php echo $_smarty_tpl->tpl_vars['BOX_POSITION']->value;?>
</td>
                <td><?php echo $_smarty_tpl->tpl_vars['BOX_SORT']->value;?>
</td>
                <td><?php echo $_smarty_tpl->tpl_vars['BOX_STATUS']->value;?>
</td>
                <td><?php echo $_smarty_tpl->tpl_vars['BOX_MOBILE']->value;?>
</td>
                <td><?php echo $_smarty_tpl->tpl_vars['BOX_NAME_STATUS']->value;?>
</td>
            </tr>
        </table>
    </div>
</div>
<?php echo $_smarty_tpl->tpl_vars['HIDDEN']->value;?>


<div class="row">
    <div class="col-md-12 text-right">
        <?php echo $_smarty_tpl->tpl_vars['BUTTON_SUBMIT']->value;?>

        <?php echo $_smarty_tpl->tpl_vars['BUTTON_CANCEL']->value;?>

    </div>
    <br>
</div>

<?php echo $_smarty_tpl->tpl_vars['SCRIPT']->value;?>

<?php echo $_smarty_tpl->tpl_vars['FORM_END']->value;?>

<?php }?>

<?php if ($_smarty_tpl->tpl_vars['BOX_NEW_POS']->value=='true'){?>
<?php echo $_smarty_tpl->tpl_vars['FORM']->value;?>

<div class="row">
    <div class="col-md-8">
        <h4><span class="glyphicon glyphicon-th-large"></span> <?php echo $_smarty_tpl->tpl_vars['txt']->value['heading_new_box'];?>
</h4>
    </div>
    <div class="col-md-4 text-right">
        <?php echo $_smarty_tpl->tpl_vars['BUTTON_SUBMIT']->value;?>

        <?php echo $_smarty_tpl->tpl_vars['BUTTON_CANCEL']->value;?>

    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <table class="table table-bordered table-striped">
            <tr>
                <th><?php echo $_smarty_tpl->tpl_vars['txt']->value['box_config'];?>
</th>
                <th><?php echo $_smarty_tpl->tpl_vars['txt']->value['box_name'];?>
</th>
            </tr>
            <tr>
				<td><?php echo $_smarty_tpl->tpl_vars['txt']->value['box_position'];?>
</td>
				<td><?php echo $_smarty_tpl->tpl_vars['BOX_POS_NAME']->value;?>
</td>
             </tr>
        </table>
    </div>
</div>
<?php echo $_smarty_tpl->tpl_vars['HIDDEN_SAVE']->value;?>


<div class="row">
    <div class="col-md-12 text-right">
        <?php echo $_smarty_tpl->tpl_vars['BUTTON_SUBMIT']->value;?>

        <?php echo $_smarty_tpl->tpl_vars['BUTTON_CANCEL']->value;?>

    </div>
    <br>
</div>

<?php echo $_smarty_tpl->tpl_vars['FORM_END']->value;?>

<?php }?>
<?php }} ?>