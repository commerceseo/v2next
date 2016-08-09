<?php
/* -----------------------------------------------------------------------------------------
   $Id: it-recht-kanzlei.php 2011-11-24 modified-shop $

   modified eCommerce Shopsoftware
   http://www.modified-shop.org

   Copyright (c) 2009 - 2013 [www.modified-shop.org]
   -----------------------------------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(configuration.php,v 1.40 2002/12/29); www.oscommerce.com
   (c) 2003   nextcommerce (configuration.php,v 1.16 2003/08/19); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: configuration.php 1125 2005-07-28 09:59:44Z novalis $)
   (c) 2008 Gambio OHG (gm_trusted_info.php 2008-08-10 gambio)

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

require('includes/application_top.php');
define('PAGE_URL', HTTP_SERVER.DIR_WS_ADMIN.basename(__FILE__));

ob_start();

$messages_ns = 'messages_'.basename(__FILE__);
if(!isset($_SESSION[$messages_ns])) {
	$_SESSION[$messages_ns] = array();
}

function replaceLanguagePlaceholders($content) {
	$coo_txt = new LanguageTextManager('itrecht', $_SESSION['languages_id']);
	while(preg_match('/##(\w+)\b/', $content, $matches) == 1) {
		$replacement = $coo_txt->get_text($matches[1]);
		if(empty($replacement)) {
			$replacement = $matches[1];
		}
		$content = preg_replace('/##'.$matches[1].'/', $replacement.'$1', $content, 1);
	}
	return $content;
}

function filelink($file) {
	$fullpath = DIR_FS_CATALOG.$file;
	if(file_exists($fullpath)) {
		$fdate = filemtime($fullpath);
		$text = date('c', $fdate);
		$url = HTTP_SERVER.DIR_WS_CATALOG.$file;
		$out = '<a href="'.$url.'" target="_new">'.$text.'</a>';
	}
	else {
		$out = "<em>$file ##not_received_yet</em>";
	}
	return $out;
}

function getCmGroupIdForType($type) {
	$mapping = array(
		'agb' => 3,
		'impressum' => 4,
		'datenschutz' => 2,
		'widerruf' => 10,
	);
	if(array_key_exists($type, $mapping)) {
		return $mapping[$type];
	}
	return false;
}


function cmConfigured($languages_id, $type, $filename) {
	$group_id = getCmGroupIdForType($type);
	if($group_id === false) {
		return false;
	}
	$query = "SELECT content_id FROM content_manager WHERE content_group = ".$group_id." AND languages_id = ".$languages_id." AND content_file = '".$filename."'";
	$result = xtc_db_query($query);
	if(xtc_db_num_rows($result) > 0) {
		return true;
	}
	return false;
}

$supported_languages = array('de');
$languages_result = xtc_db_query("SELECT languages_id, code FROM languages;");
$languages = array();
while($lang_row = xtc_db_fetch_array($languages_result)) {
	if(in_array($lang_row['code'], $supported_languages)) {
		$languages[$lang_row['code']] = $lang_row['languages_id'];
	}
}

$rechtstext_types = array('agb', 'impressum', 'datenschutz', 'widerruf');

$files = array();

foreach($languages as $code => $l_id) {
	foreach($rechtstext_types as $rttype) {
		if(!isset($files[$rtype])) {
			$file[$rtype] = array();
		}
		$files[$rttype][$code] = array(
			'txt' => 'media/content/'.$rttype.'_'.$code.'.txt',
			'html' => 'media/content/'.$rttype.'_'.$code.'.html',
			'pdf' => 'media/content/'.$rttype.'_'.$code.'.pdf',
		);
	}
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
	if(isset($_POST['gen_token'])) {
		$token = md5(uniqid().uniqid());
		cseo_set_conf('ITRECHT_TOKEN', $token);
		$_SESSION[$messages_ns][] = '##token_generated';
	}
	else if(isset($_POST['use_in_cm'])) {
		$lang_id = $languages[$_POST['lang']];
		$content_group = getCmGroupIdForType($_POST['type']);
		if($content_group !== false) {
			$query = "UPDATE content_manager SET content_file = ':file' WHERE languages_id = :languages_id AND content_group = :content_group";
			$query = strtr($query, array(':file' => xtc_db_input($_POST['file']), ':languages_id' => (int)$lang_id, ':content_group' => $content_group));
			xtc_db_query($query);
			$_SESSION[$messages_ns][] = '##legal_text_copied_to_content_manager';
		}
		else {
			$_SESSION[$messages_ns][] = '##not_copied_to_cm_type_incompatible';
		}
	} else {
		cseo_set_conf('ITRECHT_TOKEN', xtc_db_input(trim($_POST['token'])));
		$_SESSION[$messages_ns][] = '##configuration_saved';
	}
	
	xtc_redirect(PAGE_URL);
}

$messages = $_SESSION[$messages_ns];
$_SESSION[$messages_ns] = array();

$data = array(
	'token' => cseo_get_conf('ITRECHT_TOKEN'),
);

require (DIR_WS_INCLUDES.'header.php');
?>
<table class="table table-striped table-bordered">
	<tr>
		<td>
			<?php foreach($messages as $msg): ?>
			<p class="message"><?php echo $msg ?></p>
			<?php endforeach; ?>

			<?php if(!(ini_get('allow_url_fopen') == 1)): ?>
			<p class="warning">##ITRECHTTXT_CONFIG_WARNING_URL_FOPEN</p>
			<?php endif ?>
			<h2>##configuration</h2>
			<form class="bluegray" action="<?php echo PAGE_URL ?>" method="POST">
				<dl class="adminform">
					<dt><label for="token">##ITRECHTTXT_CONFIG_TOKEN</label></dt>
					<dd>
						<input id="token" name="token" type="text" value="<?php echo $data['token'] ?>" size="60">
						<input type="submit" value="##ITRECHTTXT_CONFIG_GENERATE_TOKEN" name="gen_token">
						<br>
						##your_api_url: <tt><?php echo HTTP_SERVER.DIR_WS_CATALOG.'api-it-recht-kanzlei.php'; ?></tt>
					</dd>
				</dl>
				<input class="button btn_wide" type="submit" value="##ITRECHTTXT_CONFIG_SAVE">
			</form>
		</td>
	</tr>
</table>
<h2>##texts_received</h2>
<table class="table table-striped table-bordered">
	<tr>
		<th>##legal_text</th>
		<th>##type_text</th>
		<th>##type_html</th>
		<th>##type_pdf</th>
		<th>&nbsp;</th>
	</tr>
	<?php foreach($files as $rtype => $lang): ?>
		<?php foreach($lang as $code => $langfiles): ?>
			<tr>
				<td><?php echo $rtype.' ('.$code.')' ?></td>
				<?php foreach($langfiles as $type => $file): ?>
					<td><?php echo filelink($file); ?></td>
				<?php endforeach ?>
				<td>
					<?php 
					$cmfile = $rtype.'_'.$code.'.html'; 
					if(file_exists(DIR_FS_CATALOG.'media/content/'.$cmfile)):
						?>
						<?php if(!cmConfigured($languages[$code], $rtype, $cmfile)): ?>
							<form action="" method="post">
								<input type="hidden" name="lang" value="<?php echo $code ?>">
								<input type="hidden" name="type" value="<?php echo $rtype ?>">
								<input type="hidden" name="file" value="<?php echo $cmfile ?>">
								<input type="submit" name="use_in_cm" value="##use_in_content_manager">
							</form>
						<?php else: ?>
							<div class="green">##used_in_content_manager</div>
						<?php endif ?>
					<?php else: ?>
						<div class="red">##html_file_not_available</div>
					<?php endif ?>
				</td>
			</tr>
		<?php endforeach ?>
	<?php endforeach ?>
</table>
<?php 
require(DIR_WS_INCLUDES . 'footer.php');
echo replaceLanguagePlaceholders(ob_get_clean());
require(DIR_WS_INCLUDES . 'application_bottom.php');
  