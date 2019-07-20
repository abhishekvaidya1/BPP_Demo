<?php

class Login_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function login() {

        /*
         * Getting MAC Address using PHP
         * Md. Nazmul Basher
         */
//ob_start(); // Turn on output buffering
//system('ipconfig /all'); //Execute external program to display output
//$mycom=ob_get_contents(); // Capture the output into a variable
//ob_clean(); // Clean (erase) the output buffer
//$findme = "Physical";
//$pmac = strpos($mycom, $findme); // Find the position of Physical text
//$mac=substr($mycom,($pmac+36),17); // Get Physical Address
//echo $mac;
//        

        $username = $this->input->post('username');
        $password = $this->input->post('pass');
        $this->db->select('*');
        $this->db->from('tbl_user');
        $this->db->where('name', $username);
        $this->db->where('password', $password);
        //$this->db->where('mac_address', $mac);
        return $this->db->get()->row();
    }

    
    
    public function save_prod_entry_form()
    {
        $data = $_POST;
       
        $date = new DateTime('now', new DateTimeZone('Asia/Kolkata'));
        $date1 = $date->format('Y-m-d');
        $a1 = array(
            'c_date' => $data['c_date'],
            'type_of_machine' => $data['type_of_machine'],
            'shift' => $data['shift'],
            'opening' => $data['opening'],
            'closing' => $data['closing'],
            'created_by' => $this->session->userdata('user_name'),
            'created_on' => $date1,
        );
         
        $this->db->insert('tbl_extrusion_log_sheet', $a1);
        echo $insert_id = $this->db->insert_id();
    }
    public function save_tbl_prod_entry_form()
    {
        $data = $_POST;
       
        $date = new DateTime('now', new DateTimeZone('Asia/Kolkata'));
        $data['created_on'] = $date->format('Y-m-d');
        $data['created_by'] = $this->session->userdata('user_name');
        
        $this->db->insert('tbl_prod_entry_form', $data);
        
    }
    public function save_tbl_winders()
    {
        $data = $_POST;
        $data['operator_name']= implode(",", $data['operator_name']);
        $date = new DateTime('now', new DateTimeZone('Asia/Kolkata'));
        $data['created_on'] = $date->format('Y-m-d');
        $data['created_by'] = $this->session->userdata('user_name');
        
        $this->db->insert('tbl_winders', $data);
        
    }
    
    public function save_tbl_row_material()
    {
        $data = $_POST;
        $date = new DateTime('now', new DateTimeZone('Asia/Kolkata'));
        $date1 = $date->format('Y-m-d');
        $total_production= array_sum($data['kg']);
        
        for($i=0;$i<count($data['row_material']);$i++) {
        $a1 = array(
            'logsheet_id' => $data['logsheet_id'],
            'row_material' => $data['row_material'][$i],
            'grade' => $data['grade'][$i],
            'bags' => $data['bags'][$i],
            'kg' => $data['kg'][$i],
            'per_mixing' => $data['per_mixing'][$i],
            'total_production' => $total_production,
            'created_by' => $this->session->userdata('user_name'),
            'created_on' => $date1,
            'type_of_machine' => $data['type_of_machine'],
            'shift' => $data['shift'],
            'c_date' => $data['c_date'],
        );
         
        $this->db->insert('tbl_row_material', $a1);
        }
    }
    
    public function save_tbl_denier()
    {
        $data = $_POST;
        $date = new DateTime('now', new DateTimeZone('Asia/Kolkata'));
        $date1 = $date->format('Y-m-d');
        
        for($i=0;$i<count($data['denier']);$i++) {
        $a1 = array(
            'logsheet_id' => $data['logsheet_id'],
            'denier' => $data['denier'][$i],
            'tape_type' => $data['tape_type'][$i],
            'caco3' => $data['caco3'][$i],
            'marking' => $data['marking'][$i],
            'output_kg' => $data['output_kg'][$i],
            
            'created_by' => $this->session->userdata('user_name'),
            'created_on' => $date1,
            'type_of_machine' => $data['type_of_machine'],
            'shift' => $data['shift'],
            'c_date' => $data['c_date'],
        );
         
        $this->db->insert('tbl_denier', $a1);
        }
        
        $a2 = array(
            'logsheet_id' => $data['logsheet_id'],
            'production' => array_sum($data['output_kg']),
            'reqd_cal_per' => array_sum($data['caco3']),
            'cons_kg' => array_sum($data['caco3']) / 100 * array_sum($data['output_kg']) ,
            'type_of_machine' => $data['type_of_machine'],
            'shift' => $data['shift'],
            'c_date' => $data['c_date'],
        );
        $this->db->insert('tbl_excess_less', $a2);
        
    }
    
    public function save_tbl_wastage()
    {
        $data = $_POST;
        $date = new DateTime('now', new DateTimeZone('Asia/Kolkata'));
        $data['created_on'] = $date->format('Y-m-d');
        $data['created_by'] = $this->session->userdata('user_name');

        $this->db->insert('tbl_wastage', $data);
    }
    public function save_tbl_breakdown()
    {
        $data = $_POST;
        $date = new DateTime('now', new DateTimeZone('Asia/Kolkata'));
        $date1 = $date->format('Y-m-d');
        for ($i = 0; $i < count($data['hrs']); $i++) {
            $a1 = array(
                'logsheet_id' => $data['logsheet_id'],
                'hrs' => $data['hrs'][$i],
                'reasons' => $data['reasons'][$i],
                'created_by' => $this->session->userdata('user_name'),
                'created_on' => $date1,
                'type_of_machine' => $data['type_of_machine'],
            'shift' => $data['shift'],
            'c_date' => $data['c_date'],
            );
            $this->db->insert('tbl_breakdown', $a1);
        }
    }

    

   
    
    
    
    

    // END CAMSHAFT SCRAP
    // Hell
}
