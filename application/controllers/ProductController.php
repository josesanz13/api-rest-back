<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class ProductController extends REST_Controller {
    public function __construct(){
		parent::__construct();
        // Load helpers
        $this->load->helper('jwt');

        // Load model
        $this->load->model('ProductModel','ProductModel');
    }

    /**
     * Get product list
     * @return JSON $response
     */
    public function get_product_get(){
        $response = array(
            'status' => 500,
            'message' => '',
            'data' => array()
        );

        try {
            // Validate header and request
            $headers = $this->input->request_headers();
            $request = $this->validate_request( $headers );
            
            if ( $request != 'success' ) {
                throw new Exception( $request );
            }

            // Get products
            $list_products = $this->ProductModel->get_product();

            // Response
            $response['status'] = 200;
            $response['message'] = 'success';
            $response['data']['products_list'] = $list_products;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage();
        }

        $this->response($response,false);
    }

    /**
     * Get product filtered by id
     * @return JSON $response
     */
    public function get_product_by_id_get(){
        $response = array(
            'status' => 500,
            'message' => '',
            'data' => array()
        );

        try {
            // Validate header and request
            $headers = $this->input->request_headers();
            $request = $this->validate_request( $headers );
            
            if ( $request != 'success' ) {
                throw new Exception( $request );
            }

            // Product ID validation
            if( !isset( $_GET["id"] ) || $_GET["id"] == "" || $_GET["id"] == null ){
                throw new Exception("The product ID has not been found");
            }

            // Get product
            $this->ProductModel->id = $_GET["id"];
            $data_product = $this->ProductModel->get_product_by_id();

            // Response
            $response['status'] = 200;
            $response['message'] = 'success';
            $response['data']['product'] = $data_product;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage();
        }

        $this->response( $response,false );
        
    }

    /**
     * Add products to the list
     * @return JSON $response
     */
    public function add_product_post(){
        $response = array(
            'status' => 500,
            'message' => '',
            'data' => array()
        );
        
        try {
            // Validate header and request
            $headers = $this->input->request_headers();
            $request = $this->validate_request( $headers );
            
            if ( $request != 'success' ) {
                throw new Exception( $request );
            }

            // Get POST data
            $post_data = count(json_decode(file_get_contents("php://input"), true)) > 0 ? json_decode(file_get_contents("php://input"), true) : $this->input->post();

            if ( !$this->validate_data_add_product( $post_data ) ) {
                $message = "
                    Required data not found

                    data payload 
                    {
                        status : 1,
                        code : XXX-01,
                        stock : 5,
                        name : Leche deslactosada,
                        reference : Colanta x 1L,
                        price : 2700,
                        description : Leche deslactosada colanta 1L
                    }
                ";

                throw new Exception($message);
            }

            // Validate price of product
            if ( !is_numeric( $post_data["price"] ) ) {
                throw new Exception("The product price is not a valid data type");
            }

            // Save product information
            $this->ProductModel->status = $post_data["status"];
            $this->ProductModel->code = trim($post_data["code"]);
            $this->ProductModel->stock = $post_data["stock"];
            $this->ProductModel->name = $post_data["name"];
            $this->ProductModel->reference = $post_data["reference"];
            $this->ProductModel->price = $post_data["price"];
            $this->ProductModel->description = $post_data["description"];
            
            $last_product_id = $this->ProductModel->add_product();
            if ( $last_product_id == 0 ) {
                throw new Exception("The product information has not been saved");
            }

            // Get last product insert
            $this->ProductModel->id = $last_product_id;
            $data_product = $this->ProductModel->get_product_by_id();

            $response['status'] = 200;
            $response['message'] = 'The product information has been saved';
            $response['data']['product'] = $data_product;
            
        } catch (Exception $e) {
            $response['message'] = $e->getMessage();
        }

        $this->response( $response,false );
    }

    /**
     * Update product list
     * @return JSON $response
     */
    public function update_product_post(){
        $response = array(
            'status' => 500,
            'message' => '',
            'data' => array()
        );

        try {
            // Validate header and request
            $headers = $this->input->request_headers();
            $request = $this->validate_request( $headers );
            
            if ( $request != 'success' ) {
                throw new Exception( $request );
            }

            // Get POST data
            $post_data = count(json_decode(file_get_contents("php://input"), true)) > 0 ? json_decode(file_get_contents("php://input"), true) : $this->input->post();

            if ( !isset( $post_data['id'] ) ) {
                throw new Exception("The product ID has not been found");
            }

            $id_product = $post_data['id'];

            // Get last product insert
            $this->ProductModel->id = $id_product;
            $data_product = $this->ProductModel->get_product_by_id();

            // Validate if product exist
            if ( count($data_product) == 0 ) {
                throw new Exception("The product information has not been found");
            }

            // Validate price of product
            if ( isset( $post_data["price"] ) ) {
                if ( !is_numeric( $post_data["price"] ) ) {
                    throw new Exception("The product price is not a valid data type");
                }
            }

            // Update product
            $this->ProductModel->status = !isset( $post_data["status"] ) ? $data_product["status"] : $post_data["status"];
            $this->ProductModel->code = !isset( $post_data["code"] ) ? $data_product["code"] : trim($post_data["code"]);
            $this->ProductModel->stock = !isset( $post_data["stock"] ) ? $data_product["stock"] : $post_data["stock"];
            $this->ProductModel->name = !isset( $post_data["name"] ) ? $data_product["name"] : $post_data["name"];
            $this->ProductModel->reference = !isset( $post_data["reference"] ) ? $data_product["reference"] : $post_data["reference"];
            $this->ProductModel->price = !isset( $post_data["price"] ) ? $data_product["price"] : $post_data["price"];
            $this->ProductModel->description = !isset( $post_data["description"] ) ? $data_product["description"] : $post_data["description"];

            if ( !$this->ProductModel->update_product() ) {
                throw new Exception("The product has not been updated");
            }

            // Get product updated information
            $data_product = $this->ProductModel->get_product_by_id();

            // Response
            $response['status'] = 200;
            $response['message'] = 'The product information has been updated';
            $response['data']['product'] = $data_product;

        } catch (Exception $e) {
            $response['message'] = $e->getMessage();
        }

        $this->response( $response,false );
    }

    /**
     * Delete products from the list
     * @return JSON $response
     */
    public function delete_product_get(){
        $response = array(
            'status' => 500,
            'message' => '',
            'data' => array()
        );

        try {
            // Validate header and request
            $headers = $this->input->request_headers();
            $request = $this->validate_request( $headers );
            
            if ( $request != 'success' ) {
                throw new Exception( $request );
            }

            // Product ID validation
            if( !isset( $_GET["id"] ) || $_GET["id"] == "" || $_GET["id"] == null ){
                throw new Exception("The product ID has not been found");
            }
            $this->ProductModel->id = $_GET["id"];

            // Get product
            $this->ProductModel->id = $_GET["id"];
            $data_product = $this->ProductModel->get_product_by_id();

            // Validate if product exist
            if ( count($data_product) == 0 ) {
                throw new Exception("The product information has not been found");
            }
            
            // Delete product
            if ( !$this->ProductModel->delete_product() ) {
                throw new Exception("The product has not been deleted");
            }

            // Response
            $response['status'] = 200;
            $response['message'] = 'The product has been deleted';
        } catch (Exception $e) {
            $response['message'] = $e->getMessage();
        }

        $this->response( $response,false );
    }

    /**
     * Data validation for saving
     * @param Array $data
     * @return JSON $response
     */
    private function validate_data_add_product( $data ){
        $response = true;

        if ( !isset( $data["status"] ) || $data["status"] == "" ) $response = false;
        if ( !isset( $data["code"] ) || $data["code"] == "" ) $response = false;
        if ( !isset( $data["stock"] ) || $data["stock"] == "" ) $response = false;
        if ( !isset( $data["name"] ) || $data["name"] == "" ) $response = false;
        if ( !isset( $data["reference"] ) || $data["reference"] == "" ) $response = false;
        if ( !isset( $data["price"] ) || $data["price"] == "" ) $response = false;
        if ( !isset( $data["description"] ) || $data["description"] == "" ) $response = false;

        return $response;
    }
}