<?php
defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . '/libraries/API_Controller.php';

require_once APPPATH . '/libraries/JWT.php';

use \Firebase\JWT\JWT;

class User extends API_Controller
{

	private $secret = 'this is key secret';

	public function __construct()
	{
		parent::__construct();
		$this->load->model('api/UserModel', 'user');
	}

	public function login()
	{
		header("Access-Control-Allow-Origin: *");
		$this->_apiConfig([
			'methods' => ['POST'],
		]);

		$data = json_decode(file_get_contents('php://input'), true);

		$output = $this->user->login($data);

		if ($output['status'] == false) {

			return $this->api_return(
				[
					'status' => false,
					'data' => array(
						'jwt_token' => '',
						'username' => $output['email'],
						'name' => $output['name']
					),
					'message' => "Username atau Password salah",
				],
				200
			);
		}

		$payload['email'] = $output['email'];
		$payload['name'] = $output['name'];
		$payload['id'] = $output['id'];

		// $payload = array(
		// 	'email' => $output['email'],
		// 	'name' => $output['name'],
		// 	'id' => $output['id'],
		// );

		return $this->api_return(
			[
				'status' => true,
				'data' => array(
					'jwt_token' => JWT::encode($payload, $this->secret),
					'username' => $output['email'],
					'name' => $output['name']
				),
				'message' => "Berhasil login",
			],
			200
		);
	}

	public function register()
	{
		header("Access-Control-Allow-Origin: *");
		$this->_apiConfig([
			'methods' => ['POST'],
		]);

		$data = json_decode(file_get_contents('php://input'), true);

		$output = $this->user->register($data);

		$payload['email'] = $output['email'];

		return $this->api_return(
			[
				'status' => false,
				'data' => array(
					'jwt_token' => JWT::encode($payload, $this->secret),
					'username' => $output['email'],
					'nama' => $output['name']
				),
				'message' => "Berhasil register",
			],
			201
		);
	}

	public function cek_user()
	{
		header("Access-Control-Allow-Origin: *");
		$this->_apiConfig([
			'methods' => ['GET'],
		]);

		if ($this->check_token()) {
			$user = $this->db->
			get_where('users', ['id' => $this->check_token()->id])
			->row();
			
			$data = [
				'status' => 'succes',
				'data' => $user,
				'message' => 'berhasil di get',
			];
			return  $this->api_return($data, 200);
		}
	}

	// ====================== JWT Config ==========================
	// ============================================================

	public function response($data, $status = 200)
	{
		$this->output
			->set_content_type('application/json')
			->set_status_header($status)
			->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
			->_display();

		exit;
	}

	public function check_token()
	{
		$jwt = $this->input->get_request_header('Authorization');

		try {
			$decoded = JWT::decode($jwt, $this->secret, array('HS256'));
			return $decoded;
		} catch (\Exception $e) {
			return $this->api_return(
				[
					'status' => false,
					'auth' => false,
					'message' => 'Gagal, error token',
				],
				401
			);
		}
	}
}
