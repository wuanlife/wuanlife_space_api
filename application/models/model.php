<?php
class model extends CI_Model {

    public function __construct()
    {
        $this->load->database();
    }

    public function get_data(){
        $query=$this->db->get('user_base');
        return $query->result_array();
    }

}