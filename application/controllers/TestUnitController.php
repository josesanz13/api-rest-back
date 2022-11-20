<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class TestUnitController extends CI_Controller {
	public function __construct(){
        parent::__construct();
        $this->load->library('unit_test');

        // Load model
        $this->load->model('ProductModel','ProductModel');
    }

    private function get_product_by_id(){
        $_GET = array(
            "id" => 10
        );
        
        // Product ID validation
        if( !isset( $_GET["id"] ) || $_GET["id"] == "" || $_GET["id"] == null ){
            return "The product ID has not been found";
        }

        // Get product
        $this->ProductModel->id = $_GET["id"];
        $data_product = $this->ProductModel->get_product_by_id();

        return $data_product;
    }

    private function get_product(){
        // Get products
        $list_products = $this->ProductModel->get_product();

        return $list_products;
    }

    public function index(){

        $test_name = 'Function : get_product_by_id()';
        $this->unit->run( $this->get_product_by_id(),'is_array', $test_name );

        $test_name = 'Function : get_product()';
        $this->unit->run( $this->get_product(),'is_array', $test_name );

        echo $this->unit->report();
    }
}