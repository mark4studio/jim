<?php
namespace your\nmspace\Controllers\Admin;
use mark4tech\v2_1\WPController;

class MainController extends WPController{
	public function index(){
		$this->view('admin/dashboard.view');
	}
}