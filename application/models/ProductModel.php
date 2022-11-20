<?php 
class ProductModel extends CI_Model {

    protected $table_db_products="products";
    
    public function __construct(){
		parent::__construct();
    }

    public function get_product(){
        $response = array();

        try {
            $sql = "
                SELECT * 
                FROM {$this->table_db_products}
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

    public function get_product_by_id(){
        $response = array();

        try {
            $sql = "
                SELECT * 
                FROM {$this->table_db_products}
                WHERE id = {$this->db->escape($this->id)}
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

    public function add_product(){
        $response = 0;

        try {
            $sql = "
                INSERT INTO {$this->table_db_products} (
                    status,
                    code,
                    stock,
                    name,
                    reference,
                    price,
                    description
                )
                VALUES (
                    {$this->db->escape($this->status)},
                    {$this->db->escape($this->code)},
                    {$this->db->escape($this->stock)},
                    {$this->db->escape($this->name)},
                    {$this->db->escape($this->reference)},
                    {$this->db->escape($this->price)},
                    {$this->db->escape($this->description)}
                )
            ";

            if ( $this->db->query( $sql ) ) {
                $response = $this->db->insert_id();
            }
        } catch (\Throwable $th) {
        }

        return $response;
    }

    public function update_product(){
        $response = false;

        try {
            $sql = "
                UPDATE {$this->table_db_products} 
                SET 
                    status = {$this->db->escape($this->status)},
                    code = {$this->db->escape($this->code)},
                    stock = {$this->db->escape($this->stock)},
                    name = {$this->db->escape($this->name)},
                    reference = {$this->db->escape($this->reference)},
                    price = {$this->db->escape($this->price)},
                    description = {$this->db->escape($this->description)}
                WHERE id = {$this->db->escape($this->id)}
            ";

            if ( $this->db->query( $sql ) ) {
                $response = true;
            }
        } catch (\Throwable $th) {
        }

        return $response;
    }

    public function delete_product(){
        $response = false;

        try {
            $sql = "DELETE FROM {$this->table_db_products} WHERE id = {$this->db->escape($this->id)}";

            if ( $this->db->query( $sql ) ) {
                $response = true;
            }
        } catch (\Throwable $th) {
        }

        return $response;
    }
}