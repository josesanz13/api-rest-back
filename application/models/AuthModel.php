<?php 
class AuthModel extends CI_Model {

    protected $table_db_users="users";
    
    public function __construct(){
		parent::__construct();
    }

    public function login_user(){
        $response = array();

        try {
            $sql = "
                SELECT * 
                FROM {$this->table_db_users}
                WHERE user = {$this->db->escape($this->user)} AND password = {$this->db->escape($this->password)} AND status = 1
            ";
            
            if ( $query = $this->db->query( $sql ) ) {
                if ( $query->num_rows() > 0 ) {
                    $response = $query->row_array();
                }
            }
        } catch (\Throwable $th) {
        }

        return $response;
    }
}