<?php
use Restserver\Libraries\REST_Controller;
defined('BASEPATH') OR exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
/** @noinspection PhpIncludeInspection */
//To Solve File REST_Controller not found
require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class Compra extends REST_Controller {

	function __construct()
	{

		header('Access-Control-Allow-Origin: *');
		header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
		header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
		$method = $_SERVER['REQUEST_METHOD'];
		if($method == "OPTIONS") {
			die();
		}

		parent::__construct();

		$this->methods['users_get']['limit'] = 500; 
		$this->methods['users_post']['limit'] = 100; 
		$this->methods['users_delete']['limit'] = 50;
	}

	public function index_post()
	{
		
        $this->load->model('Compra_model', 'compra');
        $this->load->model('Compra_parcela', 'compra');

		$loja = $this->post('loja');
		$tipo_pagamento = $this->post('tipo_pagamento');
		$modo_pagamento = $this->post('modo_pagamento');
		$numero_parcelas = $this->post('numero_parcelas');
		$valor = $this->post('valor');
		
		$data = array(
			'loja' 				=> $loja,
			'tipo_pagamento' 	=> $tipo_pagamento,
			'modo_pagamento' 	=> $modo_pagamento,
			'valor' 			=> $valor,
			'numero_parcelas' 	=> $numero_parcelas,
			'datahora_cadastro' => date("Y-m-d H:i:s")
		);
		

        $this->db->insert('compra', $data);
        $q  = $this->db->insert_id();  

        $result = array();
        $valor_parcela = $valor / $numero_parcelas ;

        for ($i=1; $i <= $numero_parcelas; $i++) { 
        	$parcela = array(
        		'cd_compra' => $q,
        		'status' => 'EM ABERTO',	
        		'ordem' => $i,
        		'datahora_cadastro' => date("Y-m-d H:i:s"),
        		'valor' => $valor_parcela
        	);

        	$result[] = $this->db->insert('compra_parcela' , $parcela);
        }

        //$q  = $this->db->select('*')->from('compra')->get()->row();


		//debug($result);
		//debug($q,1);



		
		$response = array(
			'data' => $result
		);
		$this->response($response,REST_Controller::HTTP_OK);
	}

	public function index_get()
	{


		if (!empty($this->get('cd_compra'))) {

			$response = $this->db->select('*')->where('id',$this->get('cd_compra'))->get('compra')->result();

			$this->response($response,REST_Controller::HTTP_OK);
			
		}else{	
			$compras = $this->db->get('compra')->result(); 
			
			$response = array();

			foreach ($compras as $compra) 
			{
			
				$compra->parcelas = $this->db->select('*')->where('cd_compra' , $compra->id)->get('compra_parcela')->result();
			
				$response[] = $compra;
			}

			$this->response($response,REST_Controller::HTTP_OK);
		}

	}



}