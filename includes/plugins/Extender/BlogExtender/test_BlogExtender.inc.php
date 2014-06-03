<?php

class test_BlogExtender extends test_BlogExtender_parent {
	function proceed() {
		//Demo Products Status
		
		// $this->v_output_buffer['TEST'] = 'Test';
		parent::proceed();
	}
	function get_response() {
		//Demo Products Status
		
		//$this->v_output_buffer['TEST'] = 'Test';
		parent::get_response();
	}

}
