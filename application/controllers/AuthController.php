<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class AuthController extends REST_Controller {
    public function __construct(){
		parent::__construct();
        // Load helpers
        $this->load->helper('jwt');

        // Load model
        $this->load->model('AuthModel','AuthModel');
    }

    public function login_post(){
        $response = array(
            'status' => 500,
            'message' => '',
            'data' => array()
        );

        try {
            // Validate headers
            if ( !validate_headers_api( $this->input->request_headers(),true ) ) {
                throw new Exception("The 'Content-Type' header does not exist or is not correctly entered [ Content-Type : application/json ]");
            }

            // Validate user and password to generate JWT
            if ( $this->input->server('PHP_AUTH_USER') == "" || $this->input->server('PHP_AUTH_PW') == "" ) {
                throw new Exception("The user or password information has not been found. [ Auth : Basic ]");
            }

            $user_login = $this->input->server('PHP_AUTH_USER');
            $password_login = md5($this->input->server('PHP_AUTH_PW'));
            $token = '';

            $this->AuthModel->user = $user_login;
            $this->AuthModel->password = $password_login;
            $data_login = $this->AuthModel->login_user();

            if ( count( $data_login ) == 0 ) {
                throw new Exception("The user information has not been found.");
            }

            $token = $this->validate_token_api( $data_login );
            if ( $token == "" ){
                $token = generate_token_api( $data_login );
                $this->renew_token_api( $data_login,$token );
            }

            $response['status'] = 200;
            $response['message'] = 'success';
            $response['data']['token'] = $token;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage();
        }

        $this->response($response,false);
    }

    public function generate_token(){
        return "token";
    }
}