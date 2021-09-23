<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');


class TransaksiModel extends CI_Model
{

	public function get_data()
	{
		$data = [];
		$transaksis = $this->db
			->get('transaksis')
			->result();

		foreach ($transaksis as $key => $transaksi) {
			$row['id'] = $transaksi->id;
			$row['qty'] = $transaksi->qty;
			$row['total'] = $transaksi->total;
			$row['produk'] = $this->get_produk($transaksi->produk_id);

			$data[] = $row;
		}

		return $data;
	}

	private function get_produk($id)
	{
		return $this->db->get_where('produks', ['id' => $id])->row_array();
	}
}
