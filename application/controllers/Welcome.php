<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends REST_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
	public function index_get()
	{
	    $this->response(['msg'=>false],200);
		$this->load->view('welcome_message');
	}
	public function index_post()
    {
        $data = json_decode(trim(file_get_contents('php://input')), true);
        $mm = json_decode($this->input->raw_input_stream,true);
        $a = $this->post('user');
        var_dump($a);
    }
}
