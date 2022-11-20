<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Validate headers API
 * 
 * @param Array $headers
 */
if (!function_exists('validate_headers_api')) {
    function validate_headers_api( $headers,$validate_auth=false ){
        $response = true;

        if ( !isset( $headers['Content-Type'] ) || $headers['Content-Type'] != 'application/json' ) {
            $response = false;
        }

        // Validate JWT header
        if ( $validate_auth ) {
            if ( !isset( $headers['Authorization'] ) || $headers['Authorization'] == '' ) {
                $response = false;
            }

        }
        
        return $response;
    }
}

if (!function_exists('generate_token_api')) {
    function generate_token_api( $data ){
        $jwt = new JWT();

        $jwt_secret_key = JWT_ACCESS_KEY;

        $data_jwt =  array(
            'user_id' => $data['id'],
            'user' => $data['user'],
            'name' => $data['name'].$data['last_name'],
            'rand' => uniqid()
        );

        $token = $jwt->encode($data_jwt,$jwt_secret_key);

        return $token;
    }
}

if (!function_exists('decode_token_api')) {
    function decode_token_api( $headers ){
        $jwt = new JWT();
        $token = $headers['Authorization'];
        $jwt_secret_key = JWT_ACCESS_KEY;

        $data_token = $jwt->decode($token,$jwt_secret_key);

        return $data_token;
    }
}