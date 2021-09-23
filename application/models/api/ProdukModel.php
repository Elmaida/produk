<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');


class ProdukModel extends CI_Model
{

	public function get_data()
	{
		return $this->db->get('produks')->result();
	}
}
