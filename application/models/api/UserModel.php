<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');


class UserModel extends CI_Model
{

	public function register($post)
	{
		$data = [
			'name' => $post['name'],
			'email' => $post['email'],
			'password' => $this->makeHash($post['password']),
		];

		$this->db->insert('users', $data);

		return $data;
	}

	public function login($data)
	{
		$email = $data['email'];
		$password = $data['password'];

		$rt = array(
			'status' => false,
			'email' => '',
			'name' => '',
			'id' => '',
		);

		$hasil = $this->db->get_where('users', array('email' => $email));

		if ($hasil->num_rows() > 0) {
			$ro = $hasil->row();
			if (password_verify($password, $ro->password)) {
				$name = $ro->name;
				$rt['status'] = true;
				$rt['email'] = $ro->email;
				$rt['name'] = $name;
				$rt['id'] = $ro->id;
			} else {
				$name = $ro->name;
				$rt['status'] = false;
				$rt['email'] = $ro->email;
				$rt['name'] = $name;
			}
		}
		return $rt;
	}


	function makeHash($string)
	{
		$options = array('cost' => 11);
		$hash    = password_hash($string, PASSWORD_BCRYPT, $options);
		return $hash;
	}
}
