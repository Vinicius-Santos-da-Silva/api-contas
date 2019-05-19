<?php
class Compra_model extends CI_Model {

    public function __construct(){

        parent::__construct();
        $this->table        = 'compra';
        $this->primary_key  = 'id';

    }  
}