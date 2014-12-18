<?php
/* --------------------------------------------------------------
   LanguageTextManager.inc.php 2012-06-27 gambio
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2012 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------

/*
 *
$coo_text_mgr = new LanguageTextManager('index', $_SESSION['languages_id']);
echo 'test:'. $coo_text_mgr->get_text('wishlist');
 */

class LanguageTextManager
{
	var $v_default_section = '';
	var $v_default_language_id = 0;

	var $v_section_content_array = array();


	static function get_instance($p_default_section, $p_default_language_id)
	{
		static $s_instance;

		if($s_instance === NULL)   {
			$s_instance = cseohookfactory::create_object('LanguageTextManager', array($p_default_section, $p_default_language_id));
		}
		else {
			if($s_instance->v_default_section != $p_default_section || $s_instance->v_default_language_id != $p_default_language_id)
			{
				# re-init with new parameters
				$s_instance->init_section($p_default_section, $p_default_language_id);
			}
		}
		return $s_instance;
	}

	function LanguageTextManager($p_default_section, $p_default_language_id)
	{
		$this->v_default_section = $p_default_section;
		$this->v_default_language_id = $p_default_language_id;

		$t_cache_key = 'LanguageTextManager'.$p_default_language_id.$p_default_section;
		$coo_cache =& DataCache::get_instance();
		if($coo_cache->key_exists($t_cache_key, true))
		{
			#use cached data
			$this->v_section_content_array = $coo_cache->get_data($t_cache_key);
		}
		else
		{
			#build new content
			$this->init_section($this->v_default_section, $this->v_default_language_id);
			$coo_cache->set_data($t_cache_key, $this->v_section_content_array, true);
		}
	}

	function init_from_files($p_section, $p_language_id)
	{
		$c_language_id = (int)$p_language_id;
		$t_directory = '';

		if($c_language_id == $_SESSION['languages_id'])
		{
			$t_directory = $_SESSION['language'];
		}
		else
		{
			$t_sql = 'SELECT directory FROM '.TABLE_LANGUAGES.' WHERE languages_id ='.$c_language_id;
			$t_query = xtc_db_query($t_sql);
			if(xtc_db_num_rows($t_query) == 1)
			{
				$t_result = xtc_db_fetch_array($t_query);
				$t_directory = $t_result['directory'];
			}
		}
		
		$t_sections_path	= DIR_FS_CATALOG . 'lang/' .$t_directory. '/sections/';
		$t_lang_file_suffix = 'lang.inc.php';

		$t_lang_files = glob($t_sections_path.$p_section.'.*'.$t_lang_file_suffix);
		
		if(is_array($t_lang_files))
        {
            # target for included lang files     
            foreach ($t_lang_files as $t_lang_file)
            {
				$t_language_text_section_content_array = array();
                include($t_lang_file);
				if(sizeof($t_language_text_section_content_array) > 0)
				{
					$this->add_section($p_section, $t_language_text_section_content_array);
				}
            }
        }
	}

	function init_from_database($p_section, $p_language_id)
	{
		$t_section_array = array();

		# add section content to object cache
		if(sizeof($t_section_array) > 0) $this->add_section($p_section, $t_section_array);
	}

	function init_section($p_section, $p_language_id)
	{
		$c_section = addslashes($p_section);
		$c_language_id = (int)$p_language_id;

		// if(!isset($GLOBALS['coo_stop_watch_array']['init_section'])) $GLOBALS['coo_stop_watch_array']['init_section'] = new StopWatch();

		$this->reset_section($c_section);

		$this->init_from_database($c_section, $c_language_id);
		$this->init_from_files($c_section, $c_language_id);
	}

	function reset_section($p_section)
	{
		$this->v_section_content_array[$p_section] = array();
	}

	function add_section($p_section, $p_section_array)
	{
		$this->v_section_content_array[$p_section] = array_merge($this->v_section_content_array[$p_section],$p_section_array);
	}

	function get_text($p_var_name, $p_section=false, $p_language_id=false)
	{
		if($p_section === false) $t_section = $this->v_default_section; else $t_section = $p_section;
		if($p_language_id === false) $t_language_id = $this->v_default_language_id; else $t_language_id = $p_language_id;

		# section content already available?
		if(isset($this->v_section_content_array[$t_section]) == false)
		{
			# do init if not
			$this->init_section($t_section, $t_language_id);
		}

		# get var value and return
		$t_var_value = $p_var_name;
		if(isset($this->v_section_content_array[$t_section][$p_var_name]))
		{
			$t_var_value = $this->v_section_content_array[$t_section][$p_var_name];
		}
		return $t_var_value;
	}

	function get_section_array($p_section=false, $p_language_id=false)
	{
		if($p_section === false) $t_section = $this->v_default_section; else $t_section = $p_section;
		if($p_language_id === false) $t_language_id = $this->v_default_language_id; else $t_language_id = $p_language_id;

		# section content already available?
		if(isset($this->v_section_content_array[$t_section]) == false)
		{
			# do init if not
			$this->init_section($t_section, $t_language_id);
		}

		$t_section_array = $this->v_section_content_array[$t_section];
		return $t_section_array;
	}

}

?>