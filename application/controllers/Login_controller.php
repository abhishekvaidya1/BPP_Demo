<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
require_once APPPATH . "/third_party/PHPExcel.php";
require_once APPPATH . "/third_party/PHPExcel/IOFactory.php";

class Login_controller extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Login_model');
    }

    public function index() {
    $this->load->view('login');
    }

    public function login() {
        $this->load->model('Login_model');
        $login_result = $this->Login_model->login();
        if (empty($login_result)) {
            $this->session->sess_destroy();
            redirect('');
        } else {
            $session_data['user_name'] = $login_result->name;
            $session_data['access'] = $login_result->access;
            $session_data['dept'] = $login_result->dept;
            $session_data['login_status'] = 'TRUE';
            $this->session->set_userdata($session_data);
            if ($login_result->access == "5" || $login_result->access == "4") {
                redirect('logsheet');
            } else {
                redirect('logsheet');
            }
        }
    }

    public function logout() {
        $this->session->sess_destroy();
        $this->load->view('login');
    }

    // CAMSHFAT
    public function logsheet() {
        $data['template'] = 'logsheet';
        $data['title'] = 'Extrusion LogSheet';
        $this->layout_admin($data);
    }
    public function CaCo3_report() {
        $data['template'] = 'CaCo3_report';
        $data['title'] = 'CaCo3 Report';
        $this->layout_admin($data);
    }
    public function logsheet_report() {
        $data['template'] = 'logsheet_report';
        $data['title'] = 'logsheet Report';
        $this->layout_admin($data);
    }
    public function dashboard() {
        $data['template'] = 'dashboard';
        $data['title'] = 'Dashboard';
        $this->layout_admin($data);
    }
    
    public function dashboard_page()
    {
    $result['s_date']=$this->input->post('select_date');    
    $this->load->view('pages/dashboard_page', $result);    
    }
    
    public function excess_change()
    {
    $result['plant_nm']=$this->input->post('plant');    
    $result['ss_date']=$this->input->post('s_date'); 
    $this->load->view('pages/excess_change', $result);    
    }
    
    public function break_change()
    {
    $result['plant_nm']=$this->input->post('plant');    
    $result['ss_date']=$this->input->post('s_date'); 
    $this->load->view('pages/break_change', $result);    
    }
    
    public function wastage_change()
    {
    $result['plant_nm']=$this->input->post('plant');    
    $result['ss_date']=$this->input->post('s_date'); 
    $this->load->view('pages/wastage_change', $result);    
    }
    
    public function check_data_exist()
    {
     $shift = $this->input->post('shift');   
     $machine = $this->input->post('machine');   
     $c_date = $this->input->post('c_date');  
     $query="select id from tbl_extrusion_log_sheet where c_date='$c_date' and type_of_machine='$machine' and shift='$shift'";
     $result=$this->db->query($query)->row();
     if($result)
     {
         echo "1";
     }
    }
    
    public function submit_form()
    {
     $shift = $this->input->post('shift');   
     $machine = $this->input->post('machine');   
     $c_date = $this->input->post('c_date');  
     print_r($shift);
     $update_data = array(
                'status' => "1",
            );
            $this->db->where('c_date', $c_date);
            $this->db->where('type_of_machine', $machine);
            $this->db->where('shift', $shift);
            $this->db->update('tbl_extrusion_log_sheet', $update_data);
    }

        
    public function get_raw_list()
    {
        $raw_id = array();
        $rawarray = $this->input->post('rawarray');
        if($rawarray) {
        $raw_id = "'" . implode("', '", $rawarray) . "'"; 
        $query = "select * from tbl_raw_material_list where id IN ($raw_id) order by id ASC";
        $result['data'] = $this->db->query($query)->result();
        $this->load->view('pages/get_raw_list', $result);
        }
    }
    
    
    public function get_caco3_report()
    {
        $d2=array();
        $d3=array();
        $d4=array();
        $d5=array();
        $d6=array();
        $d7=array();
        $d8=array();
        $d9=array();
        
        
    $s_date = $this->input->post('s_date');
    $query="select * from tbl_extrusion_log_sheet where c_date='$s_date'";
    $data['d1']=$main_data=$this->db->query($query)->result();
    

    for($i=0;$i<count($main_data);$i++)
    {
    $query2="select * from tbl_denier where logsheet_id='".$main_data[$i]->id."'";
    $d2[]=$this->db->query($query2)->result();
    $query3="select * from tbl_wastage where logsheet_id='".$main_data[$i]->id."'";
    $d3[]=$this->db->query($query3)->row();
    $query4="select SUM(`kg`) as kg_ag_total from tbl_row_material where logsheet_id='".$main_data[$i]->id."' and grade='AF' group by grade ";
    $d4[]=$this->db->query($query4)->row();
    $query5="select * from tbl_prod_entry_form where logsheet_id='".$main_data[$i]->id."'";
    $d5[]=$this->db->query($query5)->row();
    
    }
     $query6="select SUM(rm.kg) as kg_shift_total,es.type_of_machine,GROUP_CONCAT(DISTINCT es.id) as count_mach from tbl_row_material rm "
    . "LEFT JOIN tbl_extrusion_log_sheet es ON es.id=rm.logsheet_id where es.c_date='$s_date' "
    . "group by es.type_of_machine order by es.id ASC";
    $d6=$this->db->query($query6)->result();
    
     $query7="select SUM(rm.start_wastage) as start_wastage,SUM(rm.run_wastage) as run_wastage,"
             . "SUM(rm.dress_wastage) as dress_wastage"
             . ",es.type_of_machine,GROUP_CONCAT(DISTINCT es.id) as count_mach from tbl_wastage rm "
    . "LEFT JOIN tbl_extrusion_log_sheet es ON es.id=rm.logsheet_id where es.c_date='$s_date' "
    . "group by es.type_of_machine order by es.id ASC";
    $d7=$this->db->query($query7)->result();
    
     $query8="select SUM(rm.caco3) as caco3,es.type_of_machine,GROUP_CONCAT(DISTINCT es.id) as count_mach from tbl_denier rm "
    . "LEFT JOIN tbl_extrusion_log_sheet es ON es.id=rm.logsheet_id where es.c_date='$s_date' "
    . "group by es.type_of_machine order by es.id ASC";
    $d8=$this->db->query($query8)->result();
    
     $query9="select SUM(rm.kg) as kg,es.type_of_machine,GROUP_CONCAT(DISTINCT es.id) as count_mach from tbl_row_material rm "
    . "LEFT JOIN tbl_extrusion_log_sheet es ON es.id=rm.logsheet_id where es.c_date='$s_date' and rm.grade='AF' "
    . "group by es.type_of_machine order by es.id ASC";
    $d9=$this->db->query($query9)->result();
    
    $data['d2']=$d2;
    $data['d3']=$d3;
    $data['d4']=$d4;
    $data['d5']=$d5;
    $data['d6']=$d6;
    $data['d7']=$d7;
    $data['d8']=$d8;
    $data['d9']=$d9;
    $this->load->view('pages/get_caco3_report', $data);
    }
    
    public function get_logsheet_report()
    {
    $s_date = $this->input->post('s_date');    
    $machine = $this->input->post('machine');    
    $shift = $this->input->post('shift'); 
    $query="select * from tbl_extrusion_log_sheet where c_date='$s_date' and type_of_machine='$machine' and shift='$shift'";
    $data['d1']=$main_data=$this->db->query($query)->row();
    if($main_data)
    {
    $query2="select * from tbl_row_material where logsheet_id='$main_data->id'";
    $data['d2']=$this->db->query($query2)->result();
    
    $query3="select * from tbl_denier where logsheet_id='$main_data->id'";
    $data['d3']=$this->db->query($query3)->result();
    
    $query4="select * from tbl_wastage where logsheet_id='$main_data->id'";
    $data['d4']=$this->db->query($query4)->row();
    
    $query5="select * from tbl_breakdown where logsheet_id='$main_data->id'";
    $data['d5']=$this->db->query($query5)->result();
    
    
    }
    $this->load->view('pages/get_logsheet_report', $data);
    }

    public function save_prod_entry_form() {
        $this->Login_model->save_prod_entry_form();
    }
    public function save_tbl_prod_entry_form() {
        $this->Login_model->save_tbl_prod_entry_form();
    }
    public function save_tbl_winders() {
        $this->Login_model->save_tbl_winders();
    }
    public function save_tbl_row_material() {
        $this->Login_model->save_tbl_row_material();
    }
    public function save_tbl_denier() {
        $this->Login_model->save_tbl_denier();
    }
    public function save_tbl_wastage() {
        $this->Login_model->save_tbl_wastage();
    }
    public function save_tbl_breakdown() {
        $this->Login_model->save_tbl_breakdown();
    }
    
    
    
    
    

    public function camshaft_scrap_itemwise_page() {
        $item_name = $_POST["item_name"];
        $c_date = $_POST["c_date"];
        $data['c_date'] = $_POST["c_date"];
        $data['count'] = $_POST["count"];
        $data['item_name'] = $_POST["item_name"];
        $dept=$this->session->userdata('dept');
        $query = "select * from tbl_defects_main where `item_no`='$item_name' and `c_date`='$c_date' and dept='$dept'";

        $id_data = $this->db->query($query)->row();
        if ($id_data) {
            $id = $id_data->id;
            $data['main_data'] = $id_data;
            $query1 = "select * from tbl_shaft_cell_rejection_defect where main_id='$id' and dept='$dept'";
            $data['SHAFT'] = $this->db->query($query1)->result();

            
            $this->load->view('pages/edit_camshaft_scrap', $data);
        } else {
            $data['main_data'] = '';
            $data['MOULD'] = '';
            $data['MELT'] = '';
            $data['FELT'] = '';
            $data['PATTERN'] = '';
            $query5 = "select cust_name from tbl_item_details where `item`='$item_name' and dept='$dept'";
            $data['cust_name'] = $this->db->query($query5)->row();
            $this->load->view('pages/camshaft_scrap', $data);
        }
    }

    public function item_validation_camshaft_scrap() {
        $item = $_POST['item_name'];
        $c_date = $_POST['c_date'];
        $dept=$this->session->userdata('dept');
        $query = "select id from tbl_camshaft_scrap where item='$item' and c_date='$c_date' and status='1' and dept='$dept'";
        $result = $this->db->query($query)->row();
        if ($result) {
            echo "1";
        }
        exit();
    }

    public function report_pareto_analysis() {
        $data['template'] = 'report_pareto_analysis';
        $data['title'] = 'Report Pareto Analysis';
        $this->layout_admin($data);
    }

    public function overall_dashboard() {
        echo json_encode($this->Login_model->overall_dashboard());
    }

    public function save_camshaft_scrap() {
        $this->Login_model->save_camshaft_scrap();
    }

    public function edit_camshaft_scrap_data() {
        $this->Login_model->edit_camshaft_scrap_data();
    }

    public function add_item_form() {
       $data['item_data']=$this->Login_model->get_all_item();
        $data['template'] = 'add_item_form';
        $data['title'] = 'Add Item';
        $this->layout_admin($data);
    }
    public function update_item_type()
    {
        $item_id = $this->input->post('item_id');
        $item_type = $this->input->post('item_type');
        $item_name = $this->input->post('item_name');
        $dept = $this->session->userdata('dept');
        $update_data = array('item_type' => $item_type);
        $this->db->where('id', $item_id);
        $this->db->where('dept', $dept);
        $this->db->update('tbl_item_details', $update_data);

        $date = new DateTime('now', new DateTimeZone('Asia/Kolkata'));
        $date1 = $date->format('Y-m-d');
        $c_month = date("m", strtotime($date1));
        $c_year = date("Y", strtotime($date1));
        $q = "UPDATE tbl_camshaft_scrap SET item_type='$item_type' WHERE MONTH(c_date)='$c_month' AND YEAR(c_date)='$c_year' and item='$item_name' and dept='$dept'";
        $this->db->query($q);
    }

    public function get_part_no_date_wise() {
        $s_date = $_POST["s_date"];
        $dept = $this->session->userdata('dept');
        if ($s_date) {
            $condition = "where dept='$dept'";
        }
        $query = "select item,cust_name from tbl_item_details  $condition";
        $result = $this->db->query($query)->result();
        $msg = '';
        if ($result) {
            $msg .= '<select class="form-control select2 item">
                        <option value="">Select</option>';
            foreach ($result as $row) {
                $msg .= "<option value='$row->item'>$row->cust_name</option>";
            }
            $msg .= "<option value='ALL_D'>ALL Development Item</option>";
            $msg .= "<option value='ALL_R'>ALL Regular Item</option>";
            $msg .= '</select>';
            echo $msg;
        }
    }

    public function get_pareto_data_item() {
        $msg = '';
        $selected_date = $this->input->post('select_date');
        $months = date("m", strtotime($selected_date));
        $year = date("Y", strtotime($selected_date));
        $dept=$this->session->userdata('dept');
        $query = "select item,cust_name from tbl_camshaft_scrap where MONTH(c_date)='$months' AND YEAR(c_date)='$year' and dept='$dept' group by item";
        $result = $this->db->query($query)->result();
        if ($result) {
            $msg .= "<select class='form-control select2 item'>";
            $msg .= '<option value="">Select</option>';
            foreach ($result as $row) {
                $msg .= "<option value='$row->item'>$row->cust_name</option>";
            }
            $msg .= "<option value='ALL_R'>ALL REGULAR ITEM</option>";
            $msg .= "<option value='ALL_D'>ALL DEVELOPMENT ITEM</option>";
            $msg .= "</select>";
        }
        echo $msg;
    }
    public function get_pareto_data_item_yearly() {
        $msg = '';
        $selected_date = $this->input->post('select_date');
        $dept=$this->session->userdata('dept');
        $query = "select item,cust_name from tbl_camshaft_scrap where YEAR(c_date)='$selected_date' and dept='$dept' group by item";
        $result = $this->db->query($query)->result();
        if ($result) {
            $msg .= "<select class='form-control select2 item'>";
            $msg .= '<option value="">Select</option>';
            foreach ($result as $row) {
                $msg .= "<option value='$row->item'>$row->cust_name</option>";
            }
            $msg .= "<option value='ALL'>ALL ITEM</option>";
            $msg .= "</select>";
        }
        echo $msg;
    }


    public function report_daily_item_wise() {
        $data['template'] = 'report_daily_item_wise';
        $data['title'] = 'Report Daily Item Wise Rejection Details';
        $this->layout_admin($data);
    }

    public function get_part_no() {
        $s_date = $_POST["s_date"];
        $dept = $this->session->userdata('dept');
        if ($s_date) {
            $condition = "where dept='$dept' group by item";
        }
        $query = "select item,cust_name from tbl_item_details  $condition";
        $result = $this->db->query($query)->result();
        $msg = '';
        if ($result) {
            $msg .= '<select class="form-control select2 item">
                        <option value="">Select</option>';
            foreach ($result as $row) {
                $msg .= "<option value='$row->item'>$row->cust_name</option>";
            }
            $msg .= "<option value='ALL_D'>ALL Development Item</option>";
            $msg .= "<option value='ALL_R'>ALL Regular Item</option>";
            $msg .= '</select>';
            echo $msg;
        }
    }
    public function get_part_no_scrap() {
        $s_date = $_POST["s_date"];
        $dept = $this->session->userdata('dept');
        if ($s_date) {
            $condition = "where c_date ='$s_date' and dept='$dept'";
        }
        $query = "select item,cust_name from tbl_camshaft_scrap  $condition";
        $result = $this->db->query($query)->result();
        $msg = '';
        if ($result) {
            $msg .= '<select class="form-control select2 item">
                        <option value="">Select</option>';
            foreach ($result as $row) {
                $msg .= "<option value='$row->item'>$row->cust_name</option>";
            }
            $msg .= '</select>';
            echo $msg;
        }
    }

    public function get_camshaft_scrap_data() {
        $item = $_POST["item"];
        $s_date = $_POST["s_date"];
        $dept=$this->session->userdata('dept');
        $query = "select * from tbl_camshaft_scrap where item='$item' and c_date='$s_date' and dept='$dept'";
        $result['data'] = $id_data = $this->db->query($query)->row();
        $cam_id = $id_data->id;

        $query1 = "select * from tbl_cam_moulding_deffects where camshaft_scrap_id='$cam_id' and dept='$dept'";
        $result['D1'] = $this->db->query($query1)->result();

        $query2 = "select * from tbl_cam_melting_deffects where camshaft_scrap_id='$cam_id' and dept='$dept'";
        $result['D2'] = $this->db->query($query2)->result();

        $query3 = "select * from tbl_cam_fettling_deffects where camshaft_scrap_id='$cam_id' and dept='$dept'";
        $result['D3'] = $this->db->query($query3)->result();

        $query4 = "select * from tbl_cam_pattern_deffects where camshaft_scrap_id='$cam_id' and dept='$dept'";
        $result['D4'] = $this->db->query($query4)->result();

        $this->load->view('pages/get_camshaft_scrap_data', $result);
    }

    public function get_daily_rejection_data() {
        $item = $_POST["item"];
        $s_date = $_POST["s_date"];
        $result['actual_date']=$_POST["s_date"];
        if($item!="ALL_D" && $item!="ALL_R")
        {
        $dept=$this->session->userdata('dept');
        $query = "select * from tbl_camshaft_scrap where item='$item' and c_date='$s_date' and dept='$dept'";
        $check_data=$this->db->query($query)->row();
        // IF DATA EXIST THAT DATE
        if ($check_data) {
                $result['data'] = $check_data;
                $cam_id = $check_data->id;
                $query2 = "select checked.total_checked,good.total_good,rej.total_rej,fr_cut.total_fr_cut,met_rej.total_met_rej from"
                        . "(select SUM(total_checked) as total_checked from tbl_camshaft_scrap where item ='$item' AND c_date='$s_date' and dept='$dept') as checked,"
                        . "(select SUM(total_good) as total_good from tbl_camshaft_scrap where item ='$item'  AND c_date='$s_date' and dept='$dept') as good,"
                        . "(select SUM(total_rej) as total_rej from tbl_camshaft_scrap where item ='$item'  AND c_date='$s_date' and dept='$dept') as rej,"
                        . "(select SUM(fr_cut) as total_fr_cut from tbl_camshaft_scrap where item ='$item'  AND c_date='$s_date' and dept='$dept') as fr_cut,"
                        . "(select SUM(met_rej) as total_met_rej from tbl_camshaft_scrap where item ='$item'  AND c_date='$s_date' and dept='$dept') as met_rej";
                $result['today_all'] = $id_data = $this->db->query($query2)->row();

                $mould_q1 = "select defect_name,defects_sum from tbl_cam_moulding_deffects where camshaft_scrap_id='$cam_id' and dept='$dept'";
                $day_mould = $this->db->query($mould_q1)->result();
                // IF DATA NOT EXISTS
                if (!empty($day_mould)) {
                    $result['day_mould'] = $day_mould;
                } else {
                    $mould_q1 = "select defect_name,CASE WHEN defects_sum IS NULL THEN 0 ELSE 0 END AS defects_sum from tbl_defects_name where defects_section='MOULD' group by defect_name order by id ASC";
                    $result['day_mould'] = $this->db->query($mould_q1)->result();
                }
                $melt_q1 = "select defect_name,defects_sum from tbl_cam_melting_deffects where camshaft_scrap_id='$cam_id' and dept='$dept'";
                $day_melt = $this->db->query($melt_q1)->result();
                if (!empty($day_melt)) {
                    $result['day_melt'] = $day_melt;
                } else {
                    $melt_q1 = "select defect_name,CASE WHEN defects_sum IS NULL THEN 0 ELSE 0 END AS defects_sum from tbl_defects_name where defects_section='MELT' group by defect_name order by id ASC";
                    $result['day_melt'] = $this->db->query($melt_q1)->result();
                }
                $felt_q1 = "select defect_name,defects_sum from tbl_cam_fettling_deffects where camshaft_scrap_id='$cam_id' and dept='$dept'";
                $day_felt = $this->db->query($felt_q1)->result();
                if (!empty($day_felt)) {
                    $result['day_felt'] = $day_felt;
                } else {
                    $felt_q1 = "select defect_name,CASE WHEN defects_sum IS NULL THEN 0 ELSE 0 END AS defects_sum from tbl_defects_name where defects_section='FELT' group by defect_name order by id ASC";
                    $result['day_felt'] = $this->db->query($felt_q1)->result();
                }
                $pattern_q1 = "select defect_name,defects_sum from tbl_cam_pattern_deffects where camshaft_scrap_id='$cam_id' and dept='$dept'";
                $day_pattern = $this->db->query($pattern_q1)->result();
                if (!empty($day_pattern)) {
                    $result['day_pattern'] = $day_pattern;
                } else {
                    $pattern_q1 = "select defect_name,CASE WHEN defects_sum IS NULL THEN 0 ELSE 0 END AS defects_sum from tbl_defects_name where defects_section='PATTERN' group by defect_name order by id ASC";
                    $result['day_pattern'] = $this->db->query($pattern_q1)->result();
                }

                // END
            }
            // ELSE DATA NOT EXIST THAT DATE
            else {
                $query = "select * from tbl_camshaft_scrap where item='$item' and dept='$dept' order by c_date DESC LIMIT 1";
                $check_data = $this->db->query($query)->row();
                $result['data'] = $check_data;
                $result['no_data'] = "NO";
                $result['no_data_date'] = $s_date;
                $query2 = "select checked.total_checked,good.total_good,rej.total_rej,fr_cut.total_fr_cut,met_rej.total_met_rej from"
                        . "(select SUM(total_checked) as total_checked from tbl_camshaft_scrap where item ='' AND c_date='' and dept='') as checked,"
                        . "(select SUM(total_good) as total_good from tbl_camshaft_scrap where item =''  AND c_date='' and dept='') as good,"
                        . "(select SUM(total_rej) as total_rej from tbl_camshaft_scrap where item =''  AND c_date='' and dept='') as rej,"
                        . "(select SUM(fr_cut) as total_fr_cut from tbl_camshaft_scrap where item =''  AND c_date='' and dept='') as fr_cut,"
                        . "(select SUM(met_rej) as total_met_rej from tbl_camshaft_scrap where item =''  AND c_date='' and dept='') as met_rej";
                $result['today_all'] = $today_all = $this->db->query($query2)->row();
                $mould_q1 = "select defect_name,CASE WHEN defects_sum IS NULL THEN 0 ELSE 0 END AS defects_sum from tbl_cam_moulding_deffects where  dept='$dept' group by defect_name order by id ASC";
                $result['day_mould'] = $this->db->query($mould_q1)->result();
                $melt_q1 = "select defect_name,CASE WHEN defects_sum IS NULL THEN 0 ELSE 0 END AS defects_sum from tbl_cam_melting_deffects where  dept='$dept' group by defect_name order by id ASC";
                $result['day_melt'] = $this->db->query($melt_q1)->result();
                $felt_q1 = "select defect_name,CASE WHEN defects_sum IS NULL THEN 0 ELSE 0 END AS defects_sum from tbl_cam_fettling_deffects where dept='$dept' group by defect_name order by id ASC";
                $result['day_felt'] = $this->db->query($felt_q1)->result();
                $pattern_q1 = "select defect_name,CASE WHEN defects_sum IS NULL THEN 0 ELSE 0 END AS defects_sum from tbl_cam_pattern_deffects where dept='$dept' group by defect_name order by id ASC";
                $result['day_pattern'] = $this->db->query($pattern_q1)->result();
            }

            //
        $prev_month_ts = strtotime($s_date . ' -1 month');
        $c_month = date("m", strtotime($s_date));
        $c_year = date("Y", strtotime($s_date));
        $prev_month = date('Y-m-d', $prev_month_ts);
        $first_date_find = strtotime(date("Y-m-d", strtotime($s_date)) . ", first day of this month");
        $first_date = date("Y-m-d", $first_date_find);
        $months = date("m", strtotime($s_date));
        $result['current_year']=$year = date("Y", strtotime($s_date));
        $pre_months = date("m", strtotime($prev_month));
        $pre_year = date("Y", strtotime($prev_month));
        // Year Functioanlity
        $f_next_year = strtotime($s_date . ' -3 year');
         $s_pre_year = strtotime($s_date . ' -1 year');
         $t_pre_year = strtotime($s_date . ' -2 year');
         $result['f_next_year1']=$f_next_year1 = date("Y", $f_next_year);
         $result['s_pre_year2']=$s_pre_year2 = date("Y", $s_pre_year);
         $result['t_pre_year3']=$t_pre_year3 = date("Y", $t_pre_year);
        // END
        $query1 = "select checked.total_checked,good.total_good,rej.total_rej,fr_cut.total_fr_cut,met_rej.total_met_rej from"
                . "(select SUM(total_checked) as total_checked from tbl_camshaft_scrap where item ='$item' and c_date>='$first_date' AND c_date<='$s_date' and dept='$dept') as checked,"
                . "(select SUM(total_good) as total_good from tbl_camshaft_scrap where item ='$item' and c_date>='$first_date' AND c_date<='$s_date' and dept='$dept') as good,"
                . "(select SUM(total_rej) as total_rej from tbl_camshaft_scrap where item ='$item' and c_date>='$first_date' AND c_date<='$s_date' and dept='$dept') as rej,"
                . "(select SUM(fr_cut) as total_fr_cut from tbl_camshaft_scrap where item ='$item' and c_date>='$first_date' AND c_date<='$s_date' and dept='$dept') as fr_cut,"
                . "(select SUM(met_rej) as total_met_rej from tbl_camshaft_scrap where item ='$item' and c_date>='$first_date' AND c_date<='$s_date' and dept='$dept') as met_rej";
        $result['total_all'] = $id_data = $this->db->query($query1)->row();

        // MOULD
        $mould_q2 = "select SUM(mould.defects_sum) as total_sum_mould,mould.defect_name from tbl_camshaft_scrap cs "
                    . "LEFT JOIN tbl_cam_moulding_deffects mould ON cs.id=mould.camshaft_scrap_id where cs.item='$item' "
                    . "and cs.c_date>='$first_date' AND cs.c_date<='$s_date' and cs.dept='$dept' and mould.defect_name!='' group by mould.defect_name "
                    . "order by mould.id ASC";
            $cumm_mould = $this->db->query($mould_q2)->result();

            if (!empty($cumm_mould)) {
                $result['cumm_mould'] = $cumm_mould;
            } else {
                $mould_q2 = "select SUM(defects_sum) as total_sum_mould from tbl_defects_name
                where defects_section='MOULD' group by defect_name order by id ASC";
                $result['cumm_mould'] = $this->db->query($mould_q2)->result();
            }
        //END MOULD
        // MELT
        $melt_q2 = "select SUM(mould.defects_sum) as total_sum_mould,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_melting_deffects mould ON cs.id=mould.camshaft_scrap_id where cs.item='$item' "
                . "and cs.c_date>='$first_date' AND cs.c_date<='$s_date' and cs.dept='$dept' and mould.defect_name!='' group by mould.defect_name "
                . "order by mould.id ASC";
        $cumm_melt= $this->db->query($melt_q2)->result();

                if (!empty($cumm_melt)) {
                    $result['cumm_melt'] = $cumm_melt;
                    } else {
                        $melt_q2 = "select SUM(defects_sum) as total_sum_mould from tbl_defects_name
                where defects_section='MELT' group by defect_name order by id ASC";
                $result['cumm_melt'] =$this->db->query($melt_q2)->result();
                    }
        //END MELT
        // FELT
        $felt_q2 = "select SUM(mould.defects_sum) as total_sum_mould,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_fettling_deffects mould ON cs.id=mould.camshaft_scrap_id where cs.item='$item' "
                . "and cs.c_date>='$first_date' AND cs.c_date<='$s_date' and cs.dept='$dept' and mould.defect_name!='' group by mould.defect_name "
                . "order by mould.id ASC";
        $cumm_felt= $this->db->query($felt_q2)->result();
        if (!empty($cumm_felt)) {
                    $result['cumm_felt'] = $cumm_felt;
                    } else {
                        $felt_q2 = "select SUM(defects_sum) as total_sum_mould from tbl_defects_name
                where defects_section='FELT' group by defect_name order by id ASC";
                $result['cumm_felt'] =$this->db->query($felt_q2)->result();
                    }
        //END FELT
        // PATTERN
        $pattern_q2 = "select SUM(mould.defects_sum) as total_sum_mould,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_pattern_deffects mould ON cs.id=mould.camshaft_scrap_id where cs.item='$item' "
                . "and cs.c_date>='$first_date' AND cs.c_date<='$s_date' and cs.dept='$dept' and mould.defect_name!='' group by mould.defect_name "
                . "order by mould.id ASC";
        $cumm_pattern= $this->db->query($pattern_q2)->result();
        if (!empty($cumm_pattern)) {
                    $result['cumm_pattern'] = $cumm_pattern;
                    } else {
                        $pattern_q2 = "select SUM(defects_sum) as total_sum_mould from tbl_defects_name
                where defects_section='PATTERN' group by defect_name order by id ASC";
                $result['cumm_pattern'] =$this->db->query($pattern_q2)->result();
                    }
        //END PATTERN
        $r1 = "select r1.total_mould,r2.total_pouring,r3.total_fettling,r4.total_pattern from"
                . "(select SUM(total_mould) as total_mould  from tbl_camshaft_scrap where item ='$item' and c_date='$s_date' and dept='$dept') as r1,"
                . "(select SUM(total_pouring) as total_pouring from tbl_camshaft_scrap where item ='$item' and c_date='$s_date' and dept='$dept') as r2,"
                . "(select SUM(total_fettling) as total_fettling from tbl_camshaft_scrap where item ='$item' and c_date='$s_date' and dept='$dept') as r3,"
                . "(select SUM(total_pattern) as total_pattern from tbl_camshaft_scrap where item ='$item' and c_date='$s_date' and dept='$dept') as r4";
        $result['daily_rej_qty'] = $this->db->query($r1)->row();
        $r2 = "select r1.total_mould,r2.total_pouring,r3.total_fettling,r4.total_pattern from"
                . "(select SUM(total_mould) as total_mould  from tbl_camshaft_scrap where item ='$item' and MONTH(c_date)='$months' AND YEAR(c_date)='$year' and dept='$dept') as r1,"
                . "(select SUM(total_pouring) as total_pouring from tbl_camshaft_scrap where item ='$item' and MONTH(c_date)='$months' AND YEAR(c_date)='$year'  and dept='$dept') as r2,"
                . "(select SUM(total_fettling) as total_fettling from tbl_camshaft_scrap where item ='$item' and MONTH(c_date)='$months' AND YEAR(c_date)='$year'  and dept='$dept') as r3,"
                . "(select SUM(total_pattern) as total_pattern from tbl_camshaft_scrap where item ='$item' and MONTH(c_date)='$months' AND YEAR(c_date)='$year' and dept='$dept') as r4";
        $result['cumm_rej_qty'] = $this->db->query($r2)->row();

        //LAST MONTH DATA
        $query_pre_mon = "select checked.total_checked,good.total_good,rej.total_rej,fr_cut.total_fr_cut,met_rej.total_met_rej from"
                . "(select SUM(total_checked) as total_checked from tbl_camshaft_scrap where item ='$item' and MONTH(c_date)='$pre_months' AND YEAR(c_date)='$pre_year' and dept='$dept') as checked,"
                . "(select SUM(total_good) as total_good from tbl_camshaft_scrap where item ='$item' and MONTH(c_date)='$pre_months' AND YEAR(c_date)='$pre_year' and dept='$dept') as good,"
                . "(select SUM(total_rej) as total_rej from tbl_camshaft_scrap where item ='$item' and MONTH(c_date)='$pre_months' AND YEAR(c_date)='$pre_year' and dept='$dept') as rej,"
                . "(select SUM(fr_cut) as total_fr_cut from tbl_camshaft_scrap where item ='$item' and MONTH(c_date)='$pre_months' AND YEAR(c_date)='$pre_year' and dept='$dept') as fr_cut,"
                . "(select SUM(met_rej) as total_met_rej from tbl_camshaft_scrap where item ='$item' and MONTH(c_date)='$pre_months' AND YEAR(c_date)='$pre_year' and dept='$dept') as met_rej";
        $result['pre_month_total_all'] = $id_data = $this->db->query($query_pre_mon)->row();

        $pre_month_mould = "select SUM(mould.defects_sum) as total_sum_mould,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_moulding_deffects mould ON cs.id=mould.camshaft_scrap_id where cs.item='$item' "
                . "and MONTH(cs.c_date)='$pre_months' AND YEAR(cs.c_date)='$pre_year' and cs.dept='$dept' and mould.defect_name!='' group by mould.defect_name "
                . "order by mould.id ASC";
        $result['pre_month_mould'] = $this->db->query($pre_month_mould)->result();

        $pre_month_melt = "select SUM(mould.defects_sum) as total_sum_mould,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_melting_deffects mould ON cs.id=mould.camshaft_scrap_id where cs.item='$item' "
                . "and MONTH(cs.c_date)='$pre_months' AND YEAR(cs.c_date)='$pre_year' and cs.dept='$dept' and mould.defect_name!='' group by mould.defect_name "
                . "order by mould.id ASC";
        $result['pre_month_melt'] = $this->db->query($pre_month_melt)->result();

        $pre_month_felt = "select SUM(mould.defects_sum) as total_sum_mould,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_fettling_deffects mould ON cs.id=mould.camshaft_scrap_id where cs.item='$item' "
                . "and MONTH(cs.c_date)='$pre_months' AND YEAR(cs.c_date)='$pre_year' and cs.dept='$dept' and mould.defect_name!='' group by mould.defect_name "
                . "order by mould.id ASC";
        $result['pre_month_felt'] = $this->db->query($pre_month_felt)->result();

        $pre_month_pattern = "select SUM(mould.defects_sum) as total_sum_mould,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_pattern_deffects mould ON cs.id=mould.camshaft_scrap_id where cs.item='$item' "
                . "and MONTH(cs.c_date)='$pre_months' AND YEAR(cs.c_date)='$pre_year' and cs.dept='$dept' and mould.defect_name!='' group by mould.defect_name "
                . "order by mould.id ASC";
        $result['pre_month_pattern'] = $this->db->query($pre_month_pattern)->result();
        // END LAST MONTH DATA
        //LAST LAST YEAR
        $current_year = "select checked.total_checked,good.total_good,rej.total_rej,fr_cut.total_fr_cut,met_rej.total_met_rej from"
                . "(select SUM(total_checked) as total_checked from tbl_camshaft_scrap where item ='$item' and c_date>='".$f_next_year1."-04-01' AND c_date<='".$t_pre_year3."-03-31' and dept='$dept') as checked,"
                . "(select SUM(total_good) as total_good from tbl_camshaft_scrap where item ='$item' and c_date>='".$f_next_year1."-04-01' AND c_date<='".$t_pre_year3."-03-31' and dept='$dept') as good,"
                . "(select SUM(total_rej) as total_rej from tbl_camshaft_scrap where item ='$item' and  c_date>='".$f_next_year1."-04-01' AND c_date<='".$t_pre_year3."-03-31' and dept='$dept') as rej,"
                . "(select SUM(fr_cut) as total_fr_cut from tbl_camshaft_scrap where item ='$item' and c_date>='".$f_next_year1."-04-01' AND c_date<='".$t_pre_year3."-03-31' and dept='$dept') as fr_cut,"
                . "(select SUM(met_rej) as total_met_rej from tbl_camshaft_scrap where item ='$item' and c_date>='".$f_next_year1."-04-01' AND c_date<='".$t_pre_year3."-03-31' and dept='$dept') as met_rej";
        //echo $current_year;
        $result['current_year_total_all']= $this->db->query($current_year)->row();

        $current_year1 = "select SUM(mould.defects_sum) as total_sum_mould,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_moulding_deffects mould ON cs.id=mould.camshaft_scrap_id where cs.item='$item' "
                . "and cs.c_date>='".$f_next_year1."-04-01' AND cs.c_date<='".$t_pre_year3."-03-31' and cs.dept='$dept' and mould.defect_name!='' group by mould.defect_name "
                . "order by mould.id ASC";
        $result['current_year_mould'] = $this->db->query($current_year1)->result();

        $current_year2 = "select SUM(mould.defects_sum) as total_sum_mould,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_melting_deffects mould ON cs.id=mould.camshaft_scrap_id where cs.item='$item' "
                . "and cs.c_date>='".$f_next_year1."-04-01' AND cs.c_date<='".$t_pre_year3."-03-31' and cs.dept='$dept' and mould.defect_name!='' group by mould.defect_name "
                . "order by mould.id ASC";
        $result['current_year_melt'] = $this->db->query($current_year2)->result();

        $current_year3 = "select SUM(mould.defects_sum) as total_sum_mould,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_fettling_deffects mould ON cs.id=mould.camshaft_scrap_id where cs.item='$item' "
                . "and cs.c_date>='".$f_next_year1."-04-01' AND cs.c_date<='".$t_pre_year3."-03-31' and cs.dept='$dept' and mould.defect_name!='' group by mould.defect_name "
                . "order by mould.id ASC";
        $result['current_year_felt'] = $this->db->query($current_year3)->result();

        $current_year4 = "select SUM(mould.defects_sum) as total_sum_mould,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_pattern_deffects mould ON cs.id=mould.camshaft_scrap_id where cs.item='$item' "
                . "and cs.c_date>='".$f_next_year1."-04-01' AND cs.c_date<='".$t_pre_year3."-03-31' and cs.dept='$dept' and mould.defect_name!='' group by mould.defect_name "
                . "order by mould.id ASC";
        $result['current_year_pattern'] = $this->db->query($current_year4)->result();
        // LAST LAST YEAR
        // Last Year
        $t_year = "select checked.total_checked,good.total_good,rej.total_rej,fr_cut.total_fr_cut,met_rej.total_met_rej from"
                . "(select SUM(total_checked) as total_checked from tbl_camshaft_scrap where item ='$item' and c_date>='".$t_pre_year3."-04-01' AND c_date<='".$s_pre_year2."-03-31' and dept='$dept') as checked,"
                . "(select SUM(total_good) as total_good from tbl_camshaft_scrap where item ='$item' and c_date>='".$t_pre_year3."-04-01' AND c_date<='".$s_pre_year2."-03-31' and dept='$dept') as good,"
                . "(select SUM(total_rej) as total_rej from tbl_camshaft_scrap where item ='$item' and  c_date>='".$t_pre_year3."-04-01' AND c_date<='".$s_pre_year2."-03-31' and dept='$dept') as rej,"
                . "(select SUM(fr_cut) as total_fr_cut from tbl_camshaft_scrap where item ='$item' and c_date>='".$t_pre_year3."-04-01' AND c_date<='".$s_pre_year2."-03-31' and dept='$dept') as fr_cut,"
                . "(select SUM(met_rej) as total_met_rej from tbl_camshaft_scrap where item ='$item' and c_date>='".$t_pre_year3."-04-01' AND c_date<='".$s_pre_year2."-03-31' and dept='$dept') as met_rej";
        $result['t_year_total_all']= $this->db->query($t_year)->row();

        $t_year1 = "select SUM(mould.defects_sum) as total_sum_mould,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_moulding_deffects mould ON cs.id=mould.camshaft_scrap_id where cs.item='$item' "
                . "and cs.c_date>='".$t_pre_year3."-04-01' AND cs.c_date<='".$s_pre_year2."-03-31' and cs.dept='$dept' and mould.defect_name!='' group by mould.defect_name "
                . "order by mould.id ASC";
        $result['t_year_mould'] = $this->db->query($t_year1)->result();

        $t_year2 = "select SUM(mould.defects_sum) as total_sum_mould,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_melting_deffects mould ON cs.id=mould.camshaft_scrap_id where cs.item='$item' "
                . "and cs.c_date>='".$t_pre_year3."-04-01' AND cs.c_date<='".$s_pre_year2."-03-31' and cs.dept='$dept' and mould.defect_name!='' group by mould.defect_name "
                . "order by mould.id ASC";
        $result['t_year_melt'] = $this->db->query($t_year2)->result();

        $t_year3 = "select SUM(mould.defects_sum) as total_sum_mould,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_fettling_deffects mould ON cs.id=mould.camshaft_scrap_id where cs.item='$item' "
                . "and cs.c_date>='".$t_pre_year3."-04-01' AND cs.c_date<='".$s_pre_year2."-03-31' and cs.dept='$dept' and mould.defect_name!='' group by mould.defect_name "
                . "order by mould.id ASC";
        $result['t_year_felt'] = $this->db->query($t_year3)->result();

        $t_year4 = "select SUM(mould.defects_sum) as total_sum_mould,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_pattern_deffects mould ON cs.id=mould.camshaft_scrap_id where cs.item='$item' "
                . "and cs.c_date>='".$t_pre_year3."-04-01' AND cs.c_date<='".$s_pre_year2."-03-31' and cs.dept='$dept' and mould.defect_name!='' group by mould.defect_name "
                . "order by mould.id ASC";
        $result['t_year_pattern'] = $this->db->query($t_year4)->result();
        // Last Year
        //Current Year
        $s_year = "select checked.total_checked,good.total_good,rej.total_rej,fr_cut.total_fr_cut,met_rej.total_met_rej from"
                . "(select SUM(total_checked) as total_checked from tbl_camshaft_scrap where item ='$item' and c_date>='".$s_pre_year2."-04-01' AND c_date<='".$year."-03-31' and c_date NOT BETWEEN '".$c_year."-".$c_month."-01' AND '".$c_year."-".$c_month."-31' and dept='$dept') as checked,"
                . "(select SUM(total_good) as total_good from tbl_camshaft_scrap where item ='$item' and c_date>='".$s_pre_year2."-04-01' AND c_date<='".$year."-03-31' and c_date NOT BETWEEN '".$c_year."-".$c_month."-01' AND '".$c_year."-".$c_month."-31' and dept='$dept') as good,"
                . "(select SUM(total_rej) as total_rej from tbl_camshaft_scrap where item ='$item' and  c_date>='".$s_pre_year2."-04-01' AND c_date<='".$year."-03-31' and c_date NOT BETWEEN '".$c_year."-".$c_month."-01' AND '".$c_year."-".$c_month."-31' and dept='$dept') as rej,"
                . "(select SUM(fr_cut) as total_fr_cut from tbl_camshaft_scrap where item ='$item' and c_date>='".$s_pre_year2."-04-01' AND c_date<='".$year."-03-31' and c_date NOT BETWEEN '".$c_year."-".$c_month."-01' AND '".$c_year."-".$c_month."-31' and dept='$dept') as fr_cut,"
                . "(select SUM(met_rej) as total_met_rej from tbl_camshaft_scrap where item ='$item' and c_date>='".$s_pre_year2."-04-01' AND c_date<='".$year."-03-31' and c_date NOT BETWEEN '".$c_year."-".$c_month."-01' AND '".$c_year."-".$c_month."-31' and dept='$dept') as met_rej";
        $result['s_year_total_all']= $this->db->query($s_year)->row();
//        echo $s_year;
//        exit();
        $s_year1 = "select SUM(mould.defects_sum) as total_sum_mould,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_moulding_deffects mould ON cs.id=mould.camshaft_scrap_id where cs.item='$item' "
                . "and cs.c_date>='".$s_pre_year2."-04-01' AND cs.c_date<='".$year."-03-31' and cs.c_date NOT BETWEEN '".$c_year."-".$c_month."-01' AND '".$c_year."-".$c_month."-31' and cs.dept='$dept' and mould.defect_name!='' group by mould.defect_name "
                . "order by mould.id ASC";
        $result['s_year_mould'] = $this->db->query($s_year1)->result();

        $s_year2 = "select SUM(mould.defects_sum) as total_sum_mould,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_melting_deffects mould ON cs.id=mould.camshaft_scrap_id where cs.item='$item' "
                . "and cs.c_date>='".$s_pre_year2."-04-01' AND cs.c_date<='".$year."-03-31' and cs.c_date NOT BETWEEN '".$c_year."-".$c_month."-01' AND '".$c_year."-".$c_month."-31' and cs.dept='$dept' and mould.defect_name!='' group by mould.defect_name "
                . "order by mould.id ASC";
        $result['s_year_melt'] = $this->db->query($s_year2)->result();

        $s_year3 = "select SUM(mould.defects_sum) as total_sum_mould,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_fettling_deffects mould ON cs.id=mould.camshaft_scrap_id where cs.item='$item' "
                . "and cs.c_date>='".$s_pre_year2."-04-01' AND cs.c_date<='".$year."-03-31' and cs.c_date NOT BETWEEN '".$c_year."-".$c_month."-01' AND '".$c_year."-".$c_month."-31' and cs.dept='$dept' and mould.defect_name!='' group by mould.defect_name "
                . "order by mould.id ASC";
        $result['s_year_felt'] = $this->db->query($s_year3)->result();

        $s_year4 = "select SUM(mould.defects_sum) as total_sum_mould,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_pattern_deffects mould ON cs.id=mould.camshaft_scrap_id where cs.item='$item' "
                . "and cs.c_date>='".$s_pre_year2."-04-01' AND cs.c_date<='".$year."-03-31' and cs.c_date NOT BETWEEN '".$c_year."-".$c_month."-01' AND '".$c_year."-".$c_month."-31' and cs.dept='$dept' group by mould.defect_name "
                . "order by mould.id ASC";
        $result['s_year_pattern'] = $this->db->query($s_year4)->result();
        // Current Year
        $this->load->view('pages/get_daily_rejection_data', $result);
        }
        // ALL ITEM FOR D
        else if($item=="ALL_D"){
                $result['All_item']="ALL DEVELOPMENT ITEM";
                $dept=$this->session->userdata('dept');
        $query = "select *,SUM(total_checked) as total_checked,SUM(total_good) as total_good,SUM(total_rej) as total_rej,"
                . "SUM(fr_cut) as fr_cut,"
                . "GROUP_CONCAT(CONCAT('''', id, '''' )) as ids "
                . "from tbl_camshaft_scrap where  c_date='$s_date' and dept='$dept' and item_type='D'";
        $id_data = $this->db->query($query)->row();
        $cam_id =$id_data->ids;
        if($id_data->ids=='' || $id_data->ids==null)
        {
        $query2 = "select CASE WHEN fr_cut IS NULL THEN 0 ELSE 0 END AS fr_cut,CASE WHEN total_checked IS NULL THEN 0 ELSE 0 END AS total_checked,CASE WHEN total_good IS NULL THEN 0 ELSE 0 END AS total_good,
            CASE WHEN total_rej IS NULL THEN 0 ELSE 0 END AS total_rej,id as ids
         from tbl_camshaft_scrap where dept='$dept' and item_type='D'";
        $id_data = $this->db->query($query2)->row();
        $cam_id ='';
        }
        $result['data'] = $id_data;

        $prev_month_ts = strtotime($s_date . ' -1 month');
        $c_month = date("m", strtotime($s_date));
        $c_year = date("Y", strtotime($s_date));
        $prev_month = date('Y-m-d', $prev_month_ts);
        $first_date_find = strtotime(date("Y-m-d", strtotime($s_date)) . ", first day of this month");
        $first_date = date("Y-m-d", $first_date_find);
        $months = date("m", strtotime($s_date));
        $result['current_year']=$year = date("Y", strtotime($s_date));
        $pre_months = date("m", strtotime($prev_month));
        $pre_year = date("Y", strtotime($prev_month));
        // Year Functioanlity
        $f_next_year = strtotime($s_date . ' -3 year');
         $s_pre_year = strtotime($s_date . ' -1 year');
         $t_pre_year = strtotime($s_date . ' -2 year');
         $result['f_next_year1']=$f_next_year1 = date("Y", $f_next_year);
         $result['s_pre_year2']=$s_pre_year2 = date("Y", $s_pre_year);
         $result['t_pre_year3']=$t_pre_year3 = date("Y", $t_pre_year);
        // END
        $query1 = "select checked.total_checked,good.total_good,rej.total_rej,fr_cut.total_fr_cut,met_rej.total_met_rej from"
                . "(select SUM(total_checked) as total_checked from tbl_camshaft_scrap where  c_date>='$first_date' AND c_date<='$s_date' and dept='$dept' and item_type='D') as checked,"
                . "(select SUM(total_good) as total_good from tbl_camshaft_scrap where  c_date>='$first_date' AND c_date<='$s_date' and dept='$dept' and item_type='D') as good,"
                . "(select SUM(total_rej) as total_rej from tbl_camshaft_scrap where  c_date>='$first_date' AND c_date<='$s_date' and dept='$dept' and item_type='D') as rej,"
                . "(select SUM(fr_cut) as total_fr_cut from tbl_camshaft_scrap where  c_date>='$first_date' AND c_date<='$s_date' and dept='$dept' and item_type='D') as fr_cut,"
                . "(select SUM(met_rej) as total_met_rej from tbl_camshaft_scrap where  c_date>='$first_date' AND c_date<='$s_date' and dept='$dept' and item_type='D') as met_rej";
            $result['total_all'] = $id_data = $this->db->query($query1)->row();

        $query2 = "select checked.total_checked,good.total_good,rej.total_rej,fr_cut.total_fr_cut,met_rej.total_met_rej from"
                . "(select SUM(total_checked) as total_checked from tbl_camshaft_scrap where  c_date='$s_date' and dept='$dept' and item_type='D') as checked,"
                . "(select SUM(total_good) as total_good from tbl_camshaft_scrap where  c_date='$s_date' and dept='$dept' and item_type='D') as good,"
                . "(select SUM(total_rej) as total_rej from tbl_camshaft_scrap where  c_date='$s_date' and dept='$dept' and item_type='D') as rej,"
                . "(select SUM(fr_cut) as total_fr_cut from tbl_camshaft_scrap where  c_date='$s_date' and dept='$dept' and item_type='D') as fr_cut,"
                . "(select SUM(met_rej) as total_met_rej from tbl_camshaft_scrap where  c_date='$s_date' and dept='$dept' and item_type='D') as met_rej";
            $result['today_all'] = $id_data = $this->db->query($query2)->row();

        // MOULD
        if($cam_id){
        $mould_q1 = "select defect_name,SUM(defects_sum) as defects_sum from tbl_cam_moulding_deffects where camshaft_scrap_id IN ($cam_id) and dept='$dept' group by defect_name order by id ASC";
        $result['day_mould'] = $this->db->query($mould_q1)->result();}
        else{
        $mould_q1 = "select defect_name,CASE WHEN defects_sum IS NULL THEN 0 ELSE 0 END AS defects_sum from tbl_cam_moulding_deffects where  dept='$dept' group by defect_name order by id ASC";
        $result['day_mould'] = $this->db->query($mould_q1)->result();}

        $mould_q2 = "select SUM(mould.defects_sum) as total_sum_mould,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_moulding_deffects mould ON cs.id=mould.camshaft_scrap_id where "
                . "cs.c_date>='$first_date' AND cs.c_date<='$s_date' and cs.dept='$dept' and mould.defect_name!='' and cs.item_type='D'  group by mould.defect_name "
                . "order by mould.id ASC";
        $result['cumm_mould'] = $this->db->query($mould_q2)->result();

        //END MOULD
        // MELT
        if($cam_id){
        $melt_q1 = "select defect_name,SUM(defects_sum) as defects_sum  from tbl_cam_melting_deffects where camshaft_scrap_id IN ($cam_id) and dept='$dept' group by defect_name order by id ASC";
        $result['day_melt'] = $this->db->query($melt_q1)->result();}
        else {
        $melt_q1 = "select defect_name,CASE WHEN defects_sum IS NULL THEN 0 ELSE 0 END AS defects_sum from tbl_cam_melting_deffects where  dept='$dept' group by defect_name order by id ASC";
        $result['day_melt'] = $this->db->query($melt_q1)->result(); }

        $melt_q2 = "select SUM(mould.defects_sum) as total_sum_mould,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_melting_deffects mould ON cs.id=mould.camshaft_scrap_id where "
                . " cs.c_date>='$first_date' AND cs.c_date<='$s_date' and cs.dept='$dept' and mould.defect_name!='' and cs.item_type='D' group by mould.defect_name "
                . "order by mould.id ASC";
        $result['cumm_melt'] = $this->db->query($melt_q2)->result();

        //END MELT
        // FELT
        if($cam_id){
        $felt_q1 = "select defect_name,SUM(defects_sum) as defects_sum from tbl_cam_fettling_deffects where camshaft_scrap_id IN ($cam_id) and dept='$dept' group by defect_name order by id ASC";
        $result['day_felt'] = $this->db->query($felt_q1)->result();}
        else {
        $felt_q1 = "select defect_name,CASE WHEN defects_sum IS NULL THEN 0 ELSE 0 END AS defects_sum from tbl_cam_fettling_deffects where dept='$dept' group by defect_name order by id ASC";
        $result['day_felt'] = $this->db->query($felt_q1)->result();}

        $felt_q2 = "select SUM(mould.defects_sum) as total_sum_mould,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_fettling_deffects mould ON cs.id=mould.camshaft_scrap_id where "
                . "cs.c_date>='$first_date' AND cs.c_date<='$s_date' and cs.dept='$dept' and mould.defect_name!='' and cs.item_type='D' group by mould.defect_name "
                . "order by mould.id ASC";
        $result['cumm_felt'] = $this->db->query($felt_q2)->result();
        //END FELT
        // PATTERN
        if($cam_id){
        $pattern_q1 = "select defect_name,SUM(defects_sum) as defects_sum from tbl_cam_pattern_deffects where camshaft_scrap_id IN ($cam_id) and dept='$dept' group by defect_name order by id ASC";
        $result['day_pattern'] = $this->db->query($pattern_q1)->result();}
        else {
          $pattern_q1 = "select defect_name,CASE WHEN defects_sum IS NULL THEN 0 ELSE 0 END AS defects_sum from tbl_cam_pattern_deffects where dept='$dept' group by defect_name order by id ASC";
          $result['day_pattern'] = $this->db->query($pattern_q1)->result();}

        $result['day_pattern'] = $this->db->query($pattern_q1)->result();
        $pattern_q2 = "select SUM(mould.defects_sum) as total_sum_mould,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_pattern_deffects mould ON cs.id=mould.camshaft_scrap_id where"
                . " cs.c_date>='$first_date' AND cs.c_date<='$s_date' and cs.dept='$dept' and mould.defect_name!='' and cs.item_type='D' group by mould.defect_name "
                . "order by mould.id ASC";
        $result['cumm_pattern'] = $this->db->query($pattern_q2)->result();

        //END PATTERN

        $r1 = "select r1.total_mould,r2.total_pouring,r3.total_fettling,r4.total_pattern from"
                . "(select SUM(total_mould) as total_mould  from tbl_camshaft_scrap where  c_date='$s_date' and dept='$dept' and item_type='D') as r1,"
                . "(select SUM(total_pouring) as total_pouring from tbl_camshaft_scrap where  c_date='$s_date' and dept='$dept' and item_type='D') as r2,"
                . "(select SUM(total_fettling) as total_fettling from tbl_camshaft_scrap where  c_date='$s_date' and dept='$dept' and item_type='D') as r3,"
                . "(select SUM(total_pattern) as total_pattern from tbl_camshaft_scrap where  c_date='$s_date' and dept='$dept' and item_type='D') as r4";
        $result['daily_rej_qty'] = $this->db->query($r1)->row();

        $r2 = "select r1.total_mould,r2.total_pouring,r3.total_fettling,r4.total_pattern from"
                . "(select SUM(total_mould) as total_mould  from tbl_camshaft_scrap where  MONTH(c_date)='$months' AND YEAR(c_date)='$year' and dept='$dept' and item_type='D') as r1,"
                . "(select SUM(total_pouring) as total_pouring from tbl_camshaft_scrap where  MONTH(c_date)='$months' AND YEAR(c_date)='$year'  and dept='$dept' and item_type='D') as r2,"
                . "(select SUM(total_fettling) as total_fettling from tbl_camshaft_scrap where  MONTH(c_date)='$months' AND YEAR(c_date)='$year'  and dept='$dept' and item_type='D') as r3,"
                . "(select SUM(total_pattern) as total_pattern from tbl_camshaft_scrap where  MONTH(c_date)='$months' AND YEAR(c_date)='$year' and dept='$dept' and item_type='D') as r4";
        $result['cumm_rej_qty'] = $this->db->query($r2)->row();
        //LAST MONTH DATA
        $query_pre_mon = "select checked.total_checked,good.total_good,rej.total_rej,fr_cut.total_fr_cut,met_rej.total_met_rej from"
                . "(select SUM(total_checked) as total_checked from tbl_camshaft_scrap where  MONTH(c_date)='$pre_months' AND YEAR(c_date)='$pre_year' and dept='$dept' and item_type='D') as checked,"
                . "(select SUM(total_good) as total_good from tbl_camshaft_scrap where  MONTH(c_date)='$pre_months' AND YEAR(c_date)='$pre_year' and dept='$dept' and item_type='D') as good,"
                . "(select SUM(total_rej) as total_rej from tbl_camshaft_scrap where  MONTH(c_date)='$pre_months' AND YEAR(c_date)='$pre_year' and dept='$dept' and item_type='D') as rej,"
                . "(select SUM(fr_cut) as total_fr_cut from tbl_camshaft_scrap where  MONTH(c_date)='$pre_months' AND YEAR(c_date)='$pre_year' and dept='$dept' and item_type='D') as fr_cut,"
                . "(select SUM(met_rej) as total_met_rej from tbl_camshaft_scrap where  MONTH(c_date)='$pre_months' AND YEAR(c_date)='$pre_year' and dept='$dept' and item_type='D') as met_rej";
        $result['pre_month_total_all'] = $id_data = $this->db->query($query_pre_mon)->row();

        $pre_month_mould = "select SUM(mould.defects_sum) as total_sum_mould,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_moulding_deffects mould ON cs.id=mould.camshaft_scrap_id where"
                . " MONTH(cs.c_date)='$pre_months' AND YEAR(cs.c_date)='$pre_year' and cs.dept='$dept' and cs.item_type='D' and mould.defect_name!='' group by mould.defect_name "
                . "order by mould.id ASC";
        $result['pre_month_mould'] = $this->db->query($pre_month_mould)->result();

        $pre_month_melt = "select SUM(mould.defects_sum) as total_sum_mould,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_melting_deffects mould ON cs.id=mould.camshaft_scrap_id where "
                . " MONTH(cs.c_date)='$pre_months' AND YEAR(cs.c_date)='$pre_year' and cs.dept='$dept' and cs.item_type='D' and mould.defect_name!='' group by mould.defect_name "
                . "order by mould.id ASC";
        $result['pre_month_melt'] = $this->db->query($pre_month_melt)->result();

        $pre_month_felt = "select SUM(mould.defects_sum) as total_sum_mould,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_fettling_deffects mould ON cs.id=mould.camshaft_scrap_id where "
                . " MONTH(cs.c_date)='$pre_months' AND YEAR(cs.c_date)='$pre_year' and cs.dept='$dept' and cs.item_type='D' and mould.defect_name!='' group by mould.defect_name "
                . "order by mould.id ASC";
        $result['pre_month_felt'] = $this->db->query($pre_month_felt)->result();

        $pre_month_pattern = "select SUM(mould.defects_sum) as total_sum_mould,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_pattern_deffects mould ON cs.id=mould.camshaft_scrap_id where"
                . " MONTH(cs.c_date)='$pre_months' AND YEAR(cs.c_date)='$pre_year' and cs.dept='$dept' and cs.item_type='D' and mould.defect_name!='' group by mould.defect_name "
                . "order by mould.id ASC";
        $result['pre_month_pattern'] = $this->db->query($pre_month_pattern)->result();
        // END LAST MONTH DATA
        //LAST LAST YEAR
        $current_year = "select checked.total_checked,good.total_good,rej.total_rej,fr_cut.total_fr_cut,met_rej.total_met_rej from"
                . "(select SUM(total_checked) as total_checked from tbl_camshaft_scrap where  c_date>='".$f_next_year1."-04-01' AND c_date<='".$t_pre_year3."-03-31' and dept='$dept' and item_type='D') as checked,"
                . "(select SUM(total_good) as total_good from tbl_camshaft_scrap where  c_date>='".$f_next_year1."-04-01' AND c_date<='".$t_pre_year3."-03-31' and dept='$dept' and item_type='D') as good,"
                . "(select SUM(total_rej) as total_rej from tbl_camshaft_scrap where  c_date>='".$f_next_year1."-04-01' AND c_date<='".$t_pre_year3."-03-31' and dept='$dept' and item_type='D') as rej,"
                . "(select SUM(fr_cut) as total_fr_cut from tbl_camshaft_scrap where c_date>='".$f_next_year1."-04-01' AND c_date<='".$t_pre_year3."-03-31' and dept='$dept' and item_type='D') as fr_cut,"
                . "(select SUM(met_rej) as total_met_rej from tbl_camshaft_scrap where c_date>='".$f_next_year1."-04-01' AND c_date<='".$t_pre_year3."-03-31' and dept='$dept' and item_type='D') as met_rej";
        //echo $current_year;
        $result['current_year_total_all']= $this->db->query($current_year)->row();

        $current_year1 = "select SUM(mould.defects_sum) as total_sum_mould,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_moulding_deffects mould ON cs.id=mould.camshaft_scrap_id where"
                . " cs.c_date>='".$f_next_year1."-04-01' AND cs.c_date<='".$t_pre_year3."-03-31' and cs.dept='$dept' and cs.item_type='D' and mould.defect_name!='' group by mould.defect_name "
                . "order by mould.id ASC";
        $result['current_year_mould'] = $this->db->query($current_year1)->result();

        $current_year2 = "select SUM(mould.defects_sum) as total_sum_mould,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_melting_deffects mould ON cs.id=mould.camshaft_scrap_id where "
                . " cs.c_date>='".$f_next_year1."-04-01' AND cs.c_date<='".$t_pre_year3."-03-31' and cs.dept='$dept' and cs.item_type='D' and mould.defect_name!='' group by mould.defect_name "
                . "order by mould.id ASC";
        $result['current_year_melt'] = $this->db->query($current_year2)->result();

        $current_year3 = "select SUM(mould.defects_sum) as total_sum_mould,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_fettling_deffects mould ON cs.id=mould.camshaft_scrap_id where "
                . " cs.c_date>='".$f_next_year1."-04-01' AND cs.c_date<='".$t_pre_year3."-03-31' and cs.dept='$dept' and cs.item_type='D' and mould.defect_name!='' group by mould.defect_name "
                . "order by mould.id ASC";
        $result['current_year_felt'] = $this->db->query($current_year3)->result();

        $current_year4 = "select SUM(mould.defects_sum) as total_sum_mould,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_pattern_deffects mould ON cs.id=mould.camshaft_scrap_id where "
                . " cs.c_date>='".$f_next_year1."-04-01' AND cs.c_date<='".$t_pre_year3."-03-31' and cs.dept='$dept' and cs.item_type='D' and mould.defect_name!='' group by mould.defect_name "
                . "order by mould.id ASC";
        $result['current_year_pattern'] = $this->db->query($current_year4)->result();
        // LAST LAST YEAR
        // Last Year
        $t_year = "select checked.total_checked,good.total_good,rej.total_rej,fr_cut.total_fr_cut,met_rej.total_met_rej from"
                . "(select SUM(total_checked) as total_checked from tbl_camshaft_scrap where c_date>='".$t_pre_year3."-04-01' AND c_date<='".$s_pre_year2."-03-31' and dept='$dept' and item_type='D') as checked,"
                . "(select SUM(total_good) as total_good from tbl_camshaft_scrap where c_date>='".$t_pre_year3."-04-01' AND c_date<='".$s_pre_year2."-03-31' and dept='$dept' and item_type='D') as good,"
                . "(select SUM(total_rej) as total_rej from tbl_camshaft_scrap where c_date>='".$t_pre_year3."-04-01' AND c_date<='".$s_pre_year2."-03-31' and dept='$dept' and item_type='D') as rej,"
                . "(select SUM(fr_cut) as total_fr_cut from tbl_camshaft_scrap where c_date>='".$t_pre_year3."-04-01' AND c_date<='".$s_pre_year2."-03-31' and dept='$dept' and item_type='D') as fr_cut,"
                . "(select SUM(met_rej) as total_met_rej from tbl_camshaft_scrap where c_date>='".$t_pre_year3."-04-01' AND c_date<='".$s_pre_year2."-03-31' and dept='$dept' and item_type='D') as met_rej";
        $result['t_year_total_all']= $this->db->query($t_year)->row();

        $t_year1 = "select SUM(mould.defects_sum) as total_sum_mould,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_moulding_deffects mould ON cs.id=mould.camshaft_scrap_id where"
                . " cs.c_date>='".$t_pre_year3."-04-01' AND cs.c_date<='".$s_pre_year2."-03-31' and cs.dept='$dept' and cs.item_type='D' and mould.defect_name!='' group by mould.defect_name "
                . "order by mould.id ASC";
        $result['t_year_mould'] = $this->db->query($t_year1)->result();

        $t_year2 = "select SUM(mould.defects_sum) as total_sum_mould,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_melting_deffects mould ON cs.id=mould.camshaft_scrap_id where"
                . " cs.c_date>='".$t_pre_year3."-04-01' AND cs.c_date<='".$s_pre_year2."-03-31' and cs.dept='$dept' and cs.item_type='D' and mould.defect_name!='' group by mould.defect_name "
                . "order by mould.id ASC";
        $result['t_year_melt'] = $this->db->query($t_year2)->result();

        $t_year3 = "select SUM(mould.defects_sum) as total_sum_mould,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_fettling_deffects mould ON cs.id=mould.camshaft_scrap_id where"
                . " cs.c_date>='".$t_pre_year3."-04-01' AND cs.c_date<='".$s_pre_year2."-03-31' and cs.dept='$dept' and cs.item_type='D' and mould.defect_name!='' group by mould.defect_name "
                . "order by mould.id ASC";
        $result['t_year_felt'] = $this->db->query($t_year3)->result();

        $t_year4 = "select SUM(mould.defects_sum) as total_sum_mould,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_pattern_deffects mould ON cs.id=mould.camshaft_scrap_id where"
                . " cs.c_date>='".$t_pre_year3."-04-01' AND cs.c_date<='".$s_pre_year2."-03-31' and cs.dept='$dept' and cs.item_type='D' and mould.defect_name!='' group by mould.defect_name "
                . "order by mould.id ASC";
        $result['t_year_pattern'] = $this->db->query($t_year4)->result();
        // Last Year
        //Current Year
        $s_year = "select checked.total_checked,good.total_good,rej.total_rej,fr_cut.total_fr_cut,met_rej.total_met_rej from"
                . "(select SUM(total_checked) as total_checked from tbl_camshaft_scrap where  c_date>='".$s_pre_year2."-04-01' AND c_date<='".$year."-03-31' and c_date NOT BETWEEN '".$c_year."-".$c_month."-01' AND '".$c_year."-".$c_month."-31' and dept='$dept' and item_type='D') as checked,"
                . "(select SUM(total_good) as total_good from tbl_camshaft_scrap where  c_date>='".$s_pre_year2."-04-01' AND c_date<='".$year."-03-31' and c_date NOT BETWEEN '".$c_year."-".$c_month."-01' AND '".$c_year."-".$c_month."-31' and dept='$dept' and item_type='D') as good,"
                . "(select SUM(total_rej) as total_rej from tbl_camshaft_scrap where  c_date>='".$s_pre_year2."-04-01' AND c_date<='".$year."-03-31' and c_date NOT BETWEEN '".$c_year."-".$c_month."-01' AND '".$c_year."-".$c_month."-31' and dept='$dept' and item_type='D') as rej,"
                . "(select SUM(fr_cut) as total_fr_cut from tbl_camshaft_scrap where  c_date>='".$s_pre_year2."-04-01' AND c_date<='".$year."-03-31' and c_date NOT BETWEEN '".$c_year."-".$c_month."-01' AND '".$c_year."-".$c_month."-31' and dept='$dept' and item_type='D') as fr_cut,"
                . "(select SUM(met_rej) as total_met_rej from tbl_camshaft_scrap where  c_date>='".$s_pre_year2."-04-01' AND c_date<='".$year."-03-31' and c_date NOT BETWEEN '".$c_year."-".$c_month."-01' AND '".$c_year."-".$c_month."-31' and dept='$dept' and item_type='D') as met_rej";
        $result['s_year_total_all']= $this->db->query($s_year)->row();

        $s_year1 = "select SUM(mould.defects_sum) as total_sum_mould,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_moulding_deffects mould ON cs.id=mould.camshaft_scrap_id where"
                . " cs.c_date>='".$s_pre_year2."-04-01' AND cs.c_date<='".$year."-03-31' and cs.c_date NOT BETWEEN '".$c_year."-".$c_month."-01' AND '".$c_year."-".$c_month."-31' and cs.dept='$dept' and cs.item_type='D' and mould.defect_name!='' group by mould.defect_name "
                . "order by mould.id ASC";
        $result['s_year_mould'] = $this->db->query($s_year1)->result();

        $s_year2 = "select SUM(mould.defects_sum) as total_sum_mould,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_melting_deffects mould ON cs.id=mould.camshaft_scrap_id where"
                . " cs.c_date>='".$s_pre_year2."-04-01' AND cs.c_date<='".$year."-03-31' and cs.c_date NOT BETWEEN '".$c_year."-".$c_month."-01' AND '".$c_year."-".$c_month."-31' and cs.dept='$dept' and cs.item_type='D' and mould.defect_name!='' group by mould.defect_name "
                . "order by mould.id ASC";
        $result['s_year_melt'] = $this->db->query($s_year2)->result();

        $s_year3 = "select SUM(mould.defects_sum) as total_sum_mould,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_fettling_deffects mould ON cs.id=mould.camshaft_scrap_id where"
                . " cs.c_date>='".$s_pre_year2."-04-01' AND cs.c_date<='".$year."-03-31' and cs.c_date NOT BETWEEN '".$c_year."-".$c_month."-01' AND '".$c_year."-".$c_month."-31' and cs.dept='$dept' and cs.item_type='D' and mould.defect_name!='' group by mould.defect_name "
                . "order by mould.id ASC";
        $result['s_year_felt'] = $this->db->query($s_year3)->result();

        $s_year4 = "select SUM(mould.defects_sum) as total_sum_mould,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_pattern_deffects mould ON cs.id=mould.camshaft_scrap_id where"
                . " cs.c_date>='".$s_pre_year2."-04-01' AND cs.c_date<='".$year."-03-31' and cs.c_date NOT BETWEEN '".$c_year."-".$c_month."-01' AND '".$c_year."-".$c_month."-31' and cs.dept='$dept' and cs.item_type='D' and mould.defect_name!='' group by mould.defect_name "
                . "order by mould.id ASC";
        $result['s_year_pattern'] = $this->db->query($s_year4)->result();

        $this->load->view('pages/get_daily_rejection_data', $result);
        }
        // ALL REGULAR ITEM FOR R
        else if($item=="ALL_R"){
                $result['All_item']="ALL REGULAR ITEM";
                $dept=$this->session->userdata('dept');
            $query = "select *,SUM(total_checked) as total_checked,SUM(total_good) as total_good,SUM(total_rej) as total_rej,"
                . "SUM(fr_cut) as fr_cut,"
                . "GROUP_CONCAT(CONCAT('''', id, '''' )) as ids "
                . "from tbl_camshaft_scrap where  c_date='$s_date' and dept='$dept' and item_type='R'";
        $id_data = $this->db->query($query)->row();
        $cam_id =$id_data->ids;
        if($id_data->ids=='' || $id_data->ids==null)
        {
        $query2 = "select CASE WHEN fr_cut IS NULL THEN 0 ELSE 0 END AS fr_cut,CASE WHEN total_checked IS NULL THEN 0 ELSE 0 END AS total_checked,CASE WHEN total_good IS NULL THEN 0 ELSE 0 END AS total_good,
            CASE WHEN total_rej IS NULL THEN 0 ELSE 0 END AS total_rej,id as ids
         from tbl_camshaft_scrap where dept='$dept' and item_type='R'";
        $id_data = $this->db->query($query2)->row();
        $cam_id ='';
        }
        $result['data'] = $id_data;

        $prev_month_ts = strtotime($s_date . ' -1 month');
        $c_month = date("m", strtotime($s_date));
        $c_year = date("Y", strtotime($s_date));
        $prev_month = date('Y-m-d', $prev_month_ts);
        $first_date_find = strtotime(date("Y-m-d", strtotime($s_date)) . ", first day of this month");
        $first_date = date("Y-m-d", $first_date_find);
        $months = date("m", strtotime($s_date));
        $result['current_year']=$year = date("Y", strtotime($s_date));
        $pre_months = date("m", strtotime($prev_month));
        $pre_year = date("Y", strtotime($prev_month));
        // Year Functioanlity
         $f_next_year = strtotime($s_date . ' -3 year');
         $s_pre_year = strtotime($s_date . ' -1 year');
         $t_pre_year = strtotime($s_date . ' -2 year');
         $result['f_next_year1']=$f_next_year1 = date("Y", $f_next_year);
         $result['s_pre_year2']=$s_pre_year2 = date("Y", $s_pre_year);
         $result['t_pre_year3']=$t_pre_year3 = date("Y", $t_pre_year);
        // END
        $query1 = "select checked.total_checked,good.total_good,rej.total_rej,fr_cut.total_fr_cut,met_rej.total_met_rej from"
                . "(select SUM(total_checked) as total_checked from tbl_camshaft_scrap where  c_date>='$first_date' AND c_date<='$s_date' and dept='$dept' and item_type='R') as checked,"
                . "(select SUM(total_good) as total_good from tbl_camshaft_scrap where  c_date>='$first_date' AND c_date<='$s_date' and dept='$dept' and item_type='R') as good,"
                . "(select SUM(total_rej) as total_rej from tbl_camshaft_scrap where  c_date>='$first_date' AND c_date<='$s_date' and dept='$dept' and item_type='R') as rej,"
                . "(select SUM(fr_cut) as total_fr_cut from tbl_camshaft_scrap where  c_date>='$first_date' AND c_date<='$s_date' and dept='$dept' and item_type='R') as fr_cut,"
                . "(select SUM(met_rej) as total_met_rej from tbl_camshaft_scrap where  c_date>='$first_date' AND c_date<='$s_date' and dept='$dept' and item_type='R') as met_rej";
            $result['total_all'] = $id_data = $this->db->query($query1)->row();

        $query2 = "select checked.total_checked,good.total_good,rej.total_rej,fr_cut.total_fr_cut,met_rej.total_met_rej from"
                . "(select SUM(total_checked) as total_checked from tbl_camshaft_scrap where  c_date='$s_date' and dept='$dept' and item_type='R') as checked,"
                . "(select SUM(total_good) as total_good from tbl_camshaft_scrap where  c_date='$s_date' and dept='$dept' and item_type='R') as good,"
                . "(select SUM(total_rej) as total_rej from tbl_camshaft_scrap where  c_date='$s_date' and dept='$dept' and item_type='R') as rej,"
                . "(select SUM(fr_cut) as total_fr_cut from tbl_camshaft_scrap where  c_date='$s_date' and dept='$dept' and item_type='R') as fr_cut,"
                . "(select SUM(met_rej) as total_met_rej from tbl_camshaft_scrap where  c_date='$s_date' and dept='$dept' and item_type='R') as met_rej";
            $result['today_all'] = $id_data = $this->db->query($query2)->row();

        // MOULD
        if($cam_id){
        $mould_q1 = "select defect_name,SUM(defects_sum) as defects_sum from tbl_cam_moulding_deffects where camshaft_scrap_id IN ($cam_id) and dept='$dept' group by defect_name order by id ASC";
        $result['day_mould'] = $this->db->query($mould_q1)->result();}
        else{
        $mould_q1 = "select defect_name,CASE WHEN defects_sum IS NULL THEN 0 ELSE 0 END AS defects_sum from tbl_cam_moulding_deffects where  dept='$dept' group by defect_name order by id ASC";
        $result['day_mould'] = $this->db->query($mould_q1)->result();}

        $mould_q2 = "select SUM(mould.defects_sum) as total_sum_mould,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_moulding_deffects mould ON cs.id=mould.camshaft_scrap_id where "
                . "cs.c_date>='$first_date' AND cs.c_date<='$s_date' and cs.dept='$dept' and mould.defect_name!='' and cs.item_type='R'  group by mould.defect_name "
                . "order by mould.id ASC";
        $result['cumm_mould'] = $this->db->query($mould_q2)->result();

        //END MOULD
        // MELT
        if($cam_id){
        $melt_q1 = "select defect_name,SUM(defects_sum) as defects_sum  from tbl_cam_melting_deffects where camshaft_scrap_id IN ($cam_id) and dept='$dept' group by defect_name order by id ASC";
        $result['day_melt'] = $this->db->query($melt_q1)->result();}
        else {
        $melt_q1 = "select defect_name,CASE WHEN defects_sum IS NULL THEN 0 ELSE 0 END AS defects_sum from tbl_cam_melting_deffects where  dept='$dept' group by defect_name order by id ASC";
        $result['day_melt'] = $this->db->query($melt_q1)->result();}

        $melt_q2 = "select SUM(mould.defects_sum) as total_sum_mould,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_melting_deffects mould ON cs.id=mould.camshaft_scrap_id where "
                . " cs.c_date>='$first_date' AND cs.c_date<='$s_date' and cs.dept='$dept' and mould.defect_name!='' and cs.item_type='R' group by mould.defect_name "
                . "order by mould.id ASC";
        $result['cumm_melt'] = $this->db->query($melt_q2)->result();

        //END MELT
        // FELT
        if($cam_id) {
        $felt_q1 = "select defect_name,SUM(defects_sum) as defects_sum from tbl_cam_fettling_deffects where camshaft_scrap_id IN ($cam_id) and dept='$dept' group by defect_name order by id ASC";
        $result['day_felt'] = $this->db->query($felt_q1)->result(); }
        else {
        $felt_q1 = "select defect_name,CASE WHEN defects_sum IS NULL THEN 0 ELSE 0 END AS defects_sum from tbl_cam_fettling_deffects where dept='$dept' group by defect_name order by id ASC";
        $result['day_felt'] = $this->db->query($felt_q1)->result(); }

        $felt_q2 = "select SUM(mould.defects_sum) as total_sum_mould,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_fettling_deffects mould ON cs.id=mould.camshaft_scrap_id where "
                . "cs.c_date>='$first_date' AND cs.c_date<='$s_date' and cs.dept='$dept' and mould.defect_name!='' and cs.item_type='R' group by mould.defect_name "
                . "order by mould.id ASC";
        $result['cumm_felt'] = $this->db->query($felt_q2)->result();
        //END FELT
        // PATTERN
        if($cam_id) {
        $pattern_q1 = "select defect_name,SUM(defects_sum) as defects_sum from tbl_cam_pattern_deffects where camshaft_scrap_id IN ($cam_id) and dept='$dept' group by defect_name order by id ASC";
        $result['day_pattern'] = $this->db->query($pattern_q1)->result(); }
        else {
        $pattern_q1 = "select defect_name,CASE WHEN defects_sum IS NULL THEN 0 ELSE 0 END AS defects_sum from tbl_cam_pattern_deffects where dept='$dept' group by defect_name order by id ASC";
        $result['day_pattern'] = $this->db->query($pattern_q1)->result(); }

        $pattern_q2 = "select SUM(mould.defects_sum) as total_sum_mould,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_pattern_deffects mould ON cs.id=mould.camshaft_scrap_id where"
                . " cs.c_date>='$first_date' AND cs.c_date<='$s_date' and cs.dept='$dept' and mould.defect_name!='' and cs.item_type='R' group by mould.defect_name "
                . "order by mould.id ASC";
        $result['cumm_pattern'] = $this->db->query($pattern_q2)->result();

        //END PATTERN

        $r1 = "select r1.total_mould,r2.total_pouring,r3.total_fettling,r4.total_pattern from"
                . "(select SUM(total_mould) as total_mould  from tbl_camshaft_scrap where  c_date='$s_date' and dept='$dept' and item_type='R') as r1,"
                . "(select SUM(total_pouring) as total_pouring from tbl_camshaft_scrap where  c_date='$s_date' and dept='$dept' and item_type='R') as r2,"
                . "(select SUM(total_fettling) as total_fettling from tbl_camshaft_scrap where  c_date='$s_date' and dept='$dept' and item_type='R') as r3,"
                . "(select SUM(total_pattern) as total_pattern from tbl_camshaft_scrap where  c_date='$s_date' and dept='$dept' and item_type='R') as r4";
        $result['daily_rej_qty'] = $this->db->query($r1)->row();

        $r2 = "select r1.total_mould,r2.total_pouring,r3.total_fettling,r4.total_pattern from"
                . "(select SUM(total_mould) as total_mould  from tbl_camshaft_scrap where  MONTH(c_date)='$months' AND YEAR(c_date)='$year' and dept='$dept' and item_type='R') as r1,"
                . "(select SUM(total_pouring) as total_pouring from tbl_camshaft_scrap where  MONTH(c_date)='$months' AND YEAR(c_date)='$year'  and dept='$dept' and item_type='R') as r2,"
                . "(select SUM(total_fettling) as total_fettling from tbl_camshaft_scrap where  MONTH(c_date)='$months' AND YEAR(c_date)='$year'  and dept='$dept' and item_type='R') as r3,"
                . "(select SUM(total_pattern) as total_pattern from tbl_camshaft_scrap where  MONTH(c_date)='$months' AND YEAR(c_date)='$year' and dept='$dept' and item_type='R') as r4";
        $result['cumm_rej_qty'] = $this->db->query($r2)->row();
        //LAST MONTH DATA
        $query_pre_mon = "select checked.total_checked,good.total_good,rej.total_rej,fr_cut.total_fr_cut,met_rej.total_met_rej from"
                . "(select SUM(total_checked) as total_checked from tbl_camshaft_scrap where  MONTH(c_date)='$pre_months' AND YEAR(c_date)='$pre_year' and dept='$dept' and item_type='R') as checked,"
                . "(select SUM(total_good) as total_good from tbl_camshaft_scrap where  MONTH(c_date)='$pre_months' AND YEAR(c_date)='$pre_year' and dept='$dept' and item_type='R') as good,"
                . "(select SUM(total_rej) as total_rej from tbl_camshaft_scrap where  MONTH(c_date)='$pre_months' AND YEAR(c_date)='$pre_year' and dept='$dept' and item_type='R') as rej,"
                . "(select SUM(fr_cut) as total_fr_cut from tbl_camshaft_scrap where  MONTH(c_date)='$pre_months' AND YEAR(c_date)='$pre_year' and dept='$dept' and item_type='R') as fr_cut,"
                . "(select SUM(met_rej) as total_met_rej from tbl_camshaft_scrap where  MONTH(c_date)='$pre_months' AND YEAR(c_date)='$pre_year' and dept='$dept' and item_type='R') as met_rej";
        $result['pre_month_total_all'] = $id_data = $this->db->query($query_pre_mon)->row();

        $pre_month_mould = "select SUM(mould.defects_sum) as total_sum_mould,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_moulding_deffects mould ON cs.id=mould.camshaft_scrap_id where"
                . " MONTH(cs.c_date)='$pre_months' AND YEAR(cs.c_date)='$pre_year' and cs.dept='$dept' and cs.item_type='R' and mould.defect_name!='' group by mould.defect_name "
                . "order by mould.id ASC";
        $result['pre_month_mould'] = $this->db->query($pre_month_mould)->result();

        $pre_month_melt = "select SUM(mould.defects_sum) as total_sum_mould,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_melting_deffects mould ON cs.id=mould.camshaft_scrap_id where "
                . " MONTH(cs.c_date)='$pre_months' AND YEAR(cs.c_date)='$pre_year' and cs.dept='$dept' and cs.item_type='R' and mould.defect_name!='' group by mould.defect_name "
                . "order by mould.id ASC";
        $result['pre_month_melt'] = $this->db->query($pre_month_melt)->result();

        $pre_month_felt = "select SUM(mould.defects_sum) as total_sum_mould,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_fettling_deffects mould ON cs.id=mould.camshaft_scrap_id where "
                . " MONTH(cs.c_date)='$pre_months' AND YEAR(cs.c_date)='$pre_year' and cs.dept='$dept' and cs.item_type='R' and mould.defect_name!='' group by mould.defect_name "
                . "order by mould.id ASC";
        $result['pre_month_felt'] = $this->db->query($pre_month_felt)->result();

        $pre_month_pattern = "select SUM(mould.defects_sum) as total_sum_mould,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_pattern_deffects mould ON cs.id=mould.camshaft_scrap_id where"
                . " MONTH(cs.c_date)='$pre_months' AND YEAR(cs.c_date)='$pre_year' and cs.dept='$dept' and cs.item_type='R' and mould.defect_name!='' group by mould.defect_name "
                . "order by mould.id ASC";
        $result['pre_month_pattern'] = $this->db->query($pre_month_pattern)->result();
        // END LAST MONTH DATA
        //LAST LAST YEAR
        $current_year = "select checked.total_checked,good.total_good,rej.total_rej,fr_cut.total_fr_cut,met_rej.total_met_rej from"
                . "(select SUM(total_checked) as total_checked from tbl_camshaft_scrap where  c_date>='".$f_next_year1."-04-01' AND c_date<='".$t_pre_year3."-03-31' and dept='$dept' and item_type='R') as checked,"
                . "(select SUM(total_good) as total_good from tbl_camshaft_scrap where  c_date>='".$f_next_year1."-04-01' AND c_date<='".$t_pre_year3."-03-31' and dept='$dept' and item_type='R') as good,"
                . "(select SUM(total_rej) as total_rej from tbl_camshaft_scrap where  c_date>='".$f_next_year1."-04-01' AND c_date<='".$t_pre_year3."-03-31' and dept='$dept' and item_type='R') as rej,"
                . "(select SUM(fr_cut) as total_fr_cut from tbl_camshaft_scrap where c_date>='".$f_next_year1."-04-01' AND c_date<='".$t_pre_year3."-03-31' and dept='$dept' and item_type='R') as fr_cut,"
                . "(select SUM(met_rej) as total_met_rej from tbl_camshaft_scrap where c_date>='".$f_next_year1."-04-01' AND c_date<='".$t_pre_year3."-03-31' and dept='$dept' and item_type='R') as met_rej";
        $result['current_year_total_all']= $this->db->query($current_year)->row();

        $current_year1 = "select SUM(mould.defects_sum) as total_sum_mould,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_moulding_deffects mould ON cs.id=mould.camshaft_scrap_id where"
                . " cs.c_date>='".$f_next_year1."-04-01' AND cs.c_date<='".$t_pre_year3."-03-31' and cs.dept='$dept' and cs.item_type='R' and mould.defect_name!='' group by mould.defect_name "
                . "order by mould.id ASC";
        $result['current_year_mould'] = $this->db->query($current_year1)->result();

        $current_year2 = "select SUM(mould.defects_sum) as total_sum_mould,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_melting_deffects mould ON cs.id=mould.camshaft_scrap_id where "
                . " cs.c_date>='".$f_next_year1."-04-01' AND cs.c_date<='".$t_pre_year3."-03-31' and cs.dept='$dept' and cs.item_type='R' and mould.defect_name!='' group by mould.defect_name "
                . "order by mould.id ASC";
        $result['current_year_melt'] = $this->db->query($current_year2)->result();

        $current_year3 = "select SUM(mould.defects_sum) as total_sum_mould,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_fettling_deffects mould ON cs.id=mould.camshaft_scrap_id where "
                . " cs.c_date>='".$f_next_year1."-04-01' AND cs.c_date<='".$t_pre_year3."-03-31' and cs.dept='$dept' and cs.item_type='R' and mould.defect_name!='' group by mould.defect_name "
                . "order by mould.id ASC";
        $result['current_year_felt'] = $this->db->query($current_year3)->result();

        $current_year4 = "select SUM(mould.defects_sum) as total_sum_mould,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_pattern_deffects mould ON cs.id=mould.camshaft_scrap_id where "
                . " cs.c_date>='".$f_next_year1."-04-01' AND cs.c_date<='".$t_pre_year3."-03-31' and cs.dept='$dept' and cs.item_type='R' and mould.defect_name!='' group by mould.defect_name "
                . "order by mould.id ASC";
        $result['current_year_pattern'] = $this->db->query($current_year4)->result();
        // LAST LAST YEAR
        // Last Year
        $t_year = "select checked.total_checked,good.total_good,rej.total_rej,fr_cut.total_fr_cut,met_rej.total_met_rej from"
                . "(select SUM(total_checked) as total_checked from tbl_camshaft_scrap where c_date>='".$t_pre_year3."-04-01' AND c_date<='".$s_pre_year2."-03-31' and dept='$dept' and item_type='R') as checked,"
                . "(select SUM(total_good) as total_good from tbl_camshaft_scrap where c_date>='".$t_pre_year3."-04-01' AND c_date<='".$s_pre_year2."-03-31' and dept='$dept' and item_type='R') as good,"
                . "(select SUM(total_rej) as total_rej from tbl_camshaft_scrap where c_date>='".$t_pre_year3."-04-01' AND c_date<='".$s_pre_year2."-03-31' and dept='$dept' and item_type='R') as rej,"
                . "(select SUM(fr_cut) as total_fr_cut from tbl_camshaft_scrap where c_date>='".$t_pre_year3."-04-01' AND c_date<='".$s_pre_year2."-03-31' and dept='$dept' and item_type='R') as fr_cut,"
                . "(select SUM(met_rej) as total_met_rej from tbl_camshaft_scrap where c_date>='".$t_pre_year3."-04-01' AND c_date<='".$s_pre_year2."-03-31' and dept='$dept' and item_type='R') as met_rej";
        $result['t_year_total_all']= $this->db->query($t_year)->row();

        $t_year1 = "select SUM(mould.defects_sum) as total_sum_mould,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_moulding_deffects mould ON cs.id=mould.camshaft_scrap_id where"
                . " cs.c_date>='".$t_pre_year3."-04-01' AND cs.c_date<='".$s_pre_year2."-03-31' and cs.dept='$dept' and cs.item_type='R' and mould.defect_name!='' group by mould.defect_name "
                . "order by mould.id ASC";
        $result['t_year_mould'] = $this->db->query($t_year1)->result();

        $t_year2 = "select SUM(mould.defects_sum) as total_sum_mould,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_melting_deffects mould ON cs.id=mould.camshaft_scrap_id where"
                . " cs.c_date>='".$t_pre_year3."-04-01' AND cs.c_date<='".$s_pre_year2."-03-31' and cs.dept='$dept' and cs.item_type='R' and mould.defect_name!='' group by mould.defect_name "
                . "order by mould.id ASC";
        $result['t_year_melt'] = $this->db->query($t_year2)->result();

        $t_year3 = "select SUM(mould.defects_sum) as total_sum_mould,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_fettling_deffects mould ON cs.id=mould.camshaft_scrap_id where"
                . " cs.c_date>='".$t_pre_year3."-04-01' AND cs.c_date<='".$s_pre_year2."-03-31' and cs.dept='$dept' and cs.item_type='R' and mould.defect_name!='' group by mould.defect_name "
                . "order by mould.id ASC";
        $result['t_year_felt'] = $this->db->query($t_year3)->result();

        $t_year4 = "select SUM(mould.defects_sum) as total_sum_mould,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_pattern_deffects mould ON cs.id=mould.camshaft_scrap_id where"
                . " cs.c_date>='".$t_pre_year3."-04-01' AND cs.c_date<='".$s_pre_year2."-03-31' and cs.dept='$dept' and cs.item_type='R' and mould.defect_name!='' group by mould.defect_name "
                . "order by mould.id ASC";
        $result['t_year_pattern'] = $this->db->query($t_year4)->result();

        // Last Year
        //Current Year
        $s_year = "select checked.total_checked,good.total_good,rej.total_rej,fr_cut.total_fr_cut,met_rej.total_met_rej from"
                . "(select SUM(total_checked) as total_checked from tbl_camshaft_scrap where  c_date>='".$s_pre_year2."-04-01' AND c_date<='".$year."-03-31' and c_date NOT BETWEEN '".$c_year."-".$c_month."-01' AND '".$c_year."-".$c_month."-31' and dept='$dept' and item_type='R') as checked,"
                . "(select SUM(total_good) as total_good from tbl_camshaft_scrap where  c_date>='".$s_pre_year2."-04-01' AND c_date<='".$year."-03-31' and c_date NOT BETWEEN '".$c_year."-".$c_month."-01' AND '".$c_year."-".$c_month."-31' and dept='$dept' and item_type='R') as good,"
                . "(select SUM(total_rej) as total_rej from tbl_camshaft_scrap where  c_date>='".$s_pre_year2."-04-01' AND c_date<='".$year."-03-31' and c_date NOT BETWEEN '".$c_year."-".$c_month."-01' AND '".$c_year."-".$c_month."-31' and dept='$dept' and item_type='R') as rej,"
                . "(select SUM(fr_cut) as total_fr_cut from tbl_camshaft_scrap where  c_date>='".$s_pre_year2."-04-01' AND c_date<='".$year."-03-31' and c_date NOT BETWEEN '".$c_year."-".$c_month."-01' AND '".$c_year."-".$c_month."-31' and dept='$dept' and item_type='R') as fr_cut,"
                . "(select SUM(met_rej) as total_met_rej from tbl_camshaft_scrap where  c_date>='".$s_pre_year2."-04-01' AND c_date<='".$year."-03-31' and c_date NOT BETWEEN '".$c_year."-".$c_month."-01' AND '".$c_year."-".$c_month."-31' and dept='$dept' and item_type='R') as met_rej";
        $result['s_year_total_all']= $this->db->query($s_year)->row();

        $s_year1 = "select SUM(mould.defects_sum) as total_sum_mould,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_moulding_deffects mould ON cs.id=mould.camshaft_scrap_id where"
                . " cs.c_date>='".$s_pre_year2."-04-01' AND cs.c_date<='".$year."-03-31' and cs.c_date NOT BETWEEN '".$c_year."-".$c_month."-01' AND '".$c_year."-".$c_month."-31' and cs.dept='$dept' and cs.item_type='R' and mould.defect_name!='' group by mould.defect_name "
                . "order by mould.id ASC";
        $result['s_year_mould'] = $this->db->query($s_year1)->result();

        $s_year2 = "select SUM(mould.defects_sum) as total_sum_mould,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_melting_deffects mould ON cs.id=mould.camshaft_scrap_id where"
                . " cs.c_date>='".$s_pre_year2."-04-01' AND cs.c_date<='".$year."-03-31' and cs.c_date NOT BETWEEN '".$c_year."-".$c_month."-01' AND '".$c_year."-".$c_month."-31' and cs.dept='$dept' and cs.item_type='R' and mould.defect_name!='' group by mould.defect_name "
                . "order by mould.id ASC";
        $result['s_year_melt'] = $this->db->query($s_year2)->result();

        $s_year3 = "select SUM(mould.defects_sum) as total_sum_mould,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_fettling_deffects mould ON cs.id=mould.camshaft_scrap_id where"
                . " cs.c_date>='".$s_pre_year2."-04-01' AND cs.c_date<='".$year."-03-31' and cs.c_date NOT BETWEEN '".$c_year."-".$c_month."-01' AND '".$c_year."-".$c_month."-31' and cs.dept='$dept' and cs.item_type='R' and mould.defect_name!='' group by mould.defect_name "
                . "order by mould.id ASC";
        $result['s_year_felt'] = $this->db->query($s_year3)->result();

        $s_year4 = "select SUM(mould.defects_sum) as total_sum_mould,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_pattern_deffects mould ON cs.id=mould.camshaft_scrap_id where"
                . " cs.c_date>='".$s_pre_year2."-04-01' AND cs.c_date<='".$year."-03-31' and cs.c_date NOT BETWEEN '".$c_year."-".$c_month."-01' AND '".$c_year."-".$c_month."-31' and cs.dept='$dept' and cs.item_type='R' and mould.defect_name!='' group by mould.defect_name "
                . "order by mould.id ASC";
        $result['s_year_pattern'] = $this->db->query($s_year4)->result();

        $this->load->view('pages/get_daily_rejection_data', $result);
        }
}





    public function download_report($item,$s_date) {
            $result['actual_date']=$s_date;
            if($item!="ALL_D" && $item!="ALL_R")
        {
        $dept=$this->session->userdata('dept');
        $query = "select * from tbl_camshaft_scrap where item='$item' and c_date='$s_date' and dept='$dept'";
        $check_data=$this->db->query($query)->row();
        // IF DATA EXIST THAT DATE
        if($check_data)
 {
         $result['data'] = $check_data;
                $cam_id = $check_data->id;
                $query2 = "select checked.total_checked,good.total_good,rej.total_rej,fr_cut.total_fr_cut,met_rej.total_met_rej from"
                        . "(select SUM(total_checked) as total_checked from tbl_camshaft_scrap where item ='$item' AND c_date='$s_date' and dept='$dept') as checked,"
                        . "(select SUM(total_good) as total_good from tbl_camshaft_scrap where item ='$item'  AND c_date='$s_date' and dept='$dept') as good,"
                        . "(select SUM(total_rej) as total_rej from tbl_camshaft_scrap where item ='$item'  AND c_date='$s_date' and dept='$dept') as rej,"
                        . "(select SUM(fr_cut) as total_fr_cut from tbl_camshaft_scrap where item ='$item'  AND c_date='$s_date' and dept='$dept') as fr_cut,"
                        . "(select SUM(met_rej) as total_met_rej from tbl_camshaft_scrap where item ='$item'  AND c_date='$s_date' and dept='$dept') as met_rej";
                $result['today_all'] = $id_data = $this->db->query($query2)->row();

                $mould_q1 = "select defect_name,defects_sum from tbl_cam_moulding_deffects where camshaft_scrap_id='$cam_id' and dept='$dept'";
                $day_mould = $this->db->query($mould_q1)->result();
                // IF DATA NOT EXISTS
                if (!empty($day_mould)) {
                    $result['day_mould'] = $day_mould;
                } else {
                    $mould_q1 = "select defect_name,CASE WHEN defects_sum IS NULL THEN 0 ELSE 0 END AS defects_sum from tbl_defects_name where defects_section='MOULD' group by defect_name order by id ASC";
                    $result['day_mould'] = $this->db->query($mould_q1)->result();
                }
                $melt_q1 = "select defect_name,defects_sum from tbl_cam_melting_deffects where camshaft_scrap_id='$cam_id' and dept='$dept'";
                $day_melt = $this->db->query($melt_q1)->result();
                if (!empty($day_melt)) {
                    $result['day_melt'] = $day_melt;
                } else {
                    $melt_q1 = "select defect_name,CASE WHEN defects_sum IS NULL THEN 0 ELSE 0 END AS defects_sum from tbl_defects_name where defects_section='MELT' group by defect_name order by id ASC";
                    $result['day_melt'] = $this->db->query($melt_q1)->result();
                }
                $felt_q1 = "select defect_name,defects_sum from tbl_cam_fettling_deffects where camshaft_scrap_id='$cam_id' and dept='$dept'";
                $day_felt = $this->db->query($felt_q1)->result();
                if (!empty($day_felt)) {
                    $result['day_felt'] = $day_felt;
                } else {
                    $felt_q1 = "select defect_name,CASE WHEN defects_sum IS NULL THEN 0 ELSE 0 END AS defects_sum from tbl_defects_name where defects_section='FELT' group by defect_name order by id ASC";
                    $result['day_felt'] = $this->db->query($felt_q1)->result();
                }
                $pattern_q1 = "select defect_name,defects_sum from tbl_cam_pattern_deffects where camshaft_scrap_id='$cam_id' and dept='$dept'";
                $day_pattern = $this->db->query($pattern_q1)->result();
                if (!empty($day_pattern)) {
                    $result['day_pattern'] = $day_pattern;
                } else {
                    $pattern_q1 = "select defect_name,CASE WHEN defects_sum IS NULL THEN 0 ELSE 0 END AS defects_sum from tbl_defects_name where defects_section='PATTERN' group by defect_name order by id ASC";
                    $result['day_pattern'] = $this->db->query($pattern_q1)->result();
                }
            }
            // ELSE DATA NOT EXIST THAT DATE
            else {
                $query = "select * from tbl_camshaft_scrap where item='$item' and dept='$dept' order by c_date DESC LIMIT 1";
                $check_data = $this->db->query($query)->row();
                $result['data'] = $check_data;
                $result['no_data'] = "NO";
                $result['no_data_date'] = $s_date;

                $query2 = "select checked.total_checked,good.total_good,rej.total_rej,fr_cut.total_fr_cut,met_rej.total_met_rej from"
                        . "(select SUM(total_checked) as total_checked from tbl_camshaft_scrap where item ='' AND c_date='' and dept='') as checked,"
                        . "(select SUM(total_good) as total_good from tbl_camshaft_scrap where item =''  AND c_date='' and dept='') as good,"
                        . "(select SUM(total_rej) as total_rej from tbl_camshaft_scrap where item =''  AND c_date='' and dept='') as rej,"
                        . "(select SUM(fr_cut) as total_fr_cut from tbl_camshaft_scrap where item =''  AND c_date='' and dept='') as fr_cut,"
                        . "(select SUM(met_rej) as total_met_rej from tbl_camshaft_scrap where item =''  AND c_date='' and dept='') as met_rej";
                $result['today_all'] = $today_all = $this->db->query($query2)->row();
                $mould_q1 = "select defect_name,CASE WHEN defects_sum IS NULL THEN 0 ELSE 0 END AS defects_sum from tbl_cam_moulding_deffects where  dept='$dept' group by defect_name order by id ASC";
                $result['day_mould'] = $this->db->query($mould_q1)->result();
                $melt_q1 = "select defect_name,CASE WHEN defects_sum IS NULL THEN 0 ELSE 0 END AS defects_sum from tbl_cam_melting_deffects where  dept='$dept' group by defect_name order by id ASC";
                $result['day_melt'] = $this->db->query($melt_q1)->result();
                $felt_q1 = "select defect_name,CASE WHEN defects_sum IS NULL THEN 0 ELSE 0 END AS defects_sum from tbl_cam_fettling_deffects where dept='$dept' group by defect_name order by id ASC";
                $result['day_felt'] = $this->db->query($felt_q1)->result();
                $pattern_q1 = "select defect_name,CASE WHEN defects_sum IS NULL THEN 0 ELSE 0 END AS defects_sum from tbl_cam_pattern_deffects where dept='$dept' group by defect_name order by id ASC";
                $result['day_pattern'] = $this->db->query($pattern_q1)->result();
            }

            //
        $prev_month_ts = strtotime($s_date . ' -1 month');
        $c_month = date("m", strtotime($s_date));
        $c_year = date("Y", strtotime($s_date));
        $prev_month = date('Y-m-d', $prev_month_ts);
        $first_date_find = strtotime(date("Y-m-d", strtotime($s_date)) . ", first day of this month");
        $first_date = date("Y-m-d", $first_date_find);
        $months = date("m", strtotime($s_date));
        $result['current_year']=$year = date("Y", strtotime($s_date));
        $pre_months = date("m", strtotime($prev_month));
        $pre_year = date("Y", strtotime($prev_month));
        // Year Functioanlity
        $f_next_year = strtotime($s_date . ' -3 year');
         $s_pre_year = strtotime($s_date . ' -1 year');
         $t_pre_year = strtotime($s_date . ' -2 year');
         $result['f_next_year1']=$f_next_year1 = date("Y", $f_next_year);
         $result['s_pre_year2']=$s_pre_year2 = date("Y", $s_pre_year);
         $result['t_pre_year3']=$t_pre_year3 = date("Y", $t_pre_year);
        // END
        $query1 = "select checked.total_checked,good.total_good,rej.total_rej,fr_cut.total_fr_cut,met_rej.total_met_rej from"
                . "(select SUM(total_checked) as total_checked from tbl_camshaft_scrap where item ='$item' and c_date>='$first_date' AND c_date<='$s_date' and dept='$dept') as checked,"
                . "(select SUM(total_good) as total_good from tbl_camshaft_scrap where item ='$item' and c_date>='$first_date' AND c_date<='$s_date' and dept='$dept') as good,"
                . "(select SUM(total_rej) as total_rej from tbl_camshaft_scrap where item ='$item' and c_date>='$first_date' AND c_date<='$s_date' and dept='$dept') as rej,"
                . "(select SUM(fr_cut) as total_fr_cut from tbl_camshaft_scrap where item ='$item' and c_date>='$first_date' AND c_date<='$s_date' and dept='$dept') as fr_cut,"
                . "(select SUM(met_rej) as total_met_rej from tbl_camshaft_scrap where item ='$item' and c_date>='$first_date' AND c_date<='$s_date' and dept='$dept') as met_rej";
        $result['total_all'] = $id_data = $this->db->query($query1)->row();

        // MOULD
        $mould_q2 = "select SUM(mould.defects_sum) as total_sum_mould,mould.defect_name from tbl_camshaft_scrap cs "
                    . "LEFT JOIN tbl_cam_moulding_deffects mould ON cs.id=mould.camshaft_scrap_id where cs.item='$item' "
                    . "and cs.c_date>='$first_date' AND cs.c_date<='$s_date' and cs.dept='$dept' and mould.defect_name!='' group by mould.defect_name "
                    . "order by mould.id ASC";
            $cumm_mould = $this->db->query($mould_q2)->result();

            if (!empty($cumm_mould)) {
                $result['cumm_mould'] = $cumm_mould;
            } else {
                $mould_q2 = "select SUM(defects_sum) as total_sum_mould from tbl_defects_name
                where defects_section='MOULD' group by defect_name order by id ASC";
                $result['cumm_mould'] = $this->db->query($mould_q2)->result();
            }
        //END MOULD
        // MELT
        $melt_q2 = "select SUM(mould.defects_sum) as total_sum_mould,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_melting_deffects mould ON cs.id=mould.camshaft_scrap_id where cs.item='$item' "
                . "and cs.c_date>='$first_date' AND cs.c_date<='$s_date' and cs.dept='$dept' and mould.defect_name!='' group by mould.defect_name "
                . "order by mould.id ASC";
        $cumm_melt= $this->db->query($melt_q2)->result();

                if (!empty($cumm_melt)) {
                    $result['cumm_melt'] = $cumm_melt;
                    } else {
                        $melt_q2 = "select SUM(defects_sum) as total_sum_mould from tbl_defects_name
                where defects_section='MELT' group by defect_name order by id ASC";
                $result['cumm_melt'] =$this->db->query($melt_q2)->result();
                    }
        //END MELT
        // FELT
        $felt_q2 = "select SUM(mould.defects_sum) as total_sum_mould,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_fettling_deffects mould ON cs.id=mould.camshaft_scrap_id where cs.item='$item' "
                . "and cs.c_date>='$first_date' AND cs.c_date<='$s_date' and cs.dept='$dept' and mould.defect_name!='' group by mould.defect_name "
                . "order by mould.id ASC";
        $cumm_felt= $this->db->query($felt_q2)->result();
        if (!empty($cumm_felt)) {
                    $result['cumm_felt'] = $cumm_felt;
                    } else {
                        $felt_q2 = "select SUM(defects_sum) as total_sum_mould from tbl_defects_name
                where defects_section='FELT' group by defect_name order by id ASC";
                $result['cumm_felt'] =$this->db->query($felt_q2)->result();
                    }
        //END FELT
        // PATTERN
        $pattern_q2 = "select SUM(mould.defects_sum) as total_sum_mould,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_pattern_deffects mould ON cs.id=mould.camshaft_scrap_id where cs.item='$item' "
                . "and cs.c_date>='$first_date' AND cs.c_date<='$s_date' and cs.dept='$dept' and mould.defect_name!='' group by mould.defect_name "
                . "order by mould.id ASC";
        $cumm_pattern= $this->db->query($pattern_q2)->result();

        if (!empty($cumm_pattern)) {
                    $result['cumm_pattern'] = $cumm_pattern;
                    } else {
                        $pattern_q2 = "select SUM(defects_sum) as total_sum_mould from tbl_defects_name
                where defects_section='PATTERN' group by defect_name order by id ASC";
                $result['cumm_pattern'] =$this->db->query($pattern_q2)->result();
                    }
        //END PATTERN
        $r1 = "select r1.total_mould,r2.total_pouring,r3.total_fettling,r4.total_pattern from"
                . "(select SUM(total_mould) as total_mould  from tbl_camshaft_scrap where item ='$item' and c_date='$s_date' and dept='$dept') as r1,"
                . "(select SUM(total_pouring) as total_pouring from tbl_camshaft_scrap where item ='$item' and c_date='$s_date' and dept='$dept') as r2,"
                . "(select SUM(total_fettling) as total_fettling from tbl_camshaft_scrap where item ='$item' and c_date='$s_date' and dept='$dept') as r3,"
                . "(select SUM(total_pattern) as total_pattern from tbl_camshaft_scrap where item ='$item' and c_date='$s_date' and dept='$dept') as r4";
        $result['daily_rej_qty'] = $this->db->query($r1)->row();
        $r2 = "select r1.total_mould,r2.total_pouring,r3.total_fettling,r4.total_pattern from"
                . "(select SUM(total_mould) as total_mould  from tbl_camshaft_scrap where item ='$item' and MONTH(c_date)='$months' AND YEAR(c_date)='$year' and dept='$dept') as r1,"
                . "(select SUM(total_pouring) as total_pouring from tbl_camshaft_scrap where item ='$item' and MONTH(c_date)='$months' AND YEAR(c_date)='$year'  and dept='$dept') as r2,"
                . "(select SUM(total_fettling) as total_fettling from tbl_camshaft_scrap where item ='$item' and MONTH(c_date)='$months' AND YEAR(c_date)='$year'  and dept='$dept') as r3,"
                . "(select SUM(total_pattern) as total_pattern from tbl_camshaft_scrap where item ='$item' and MONTH(c_date)='$months' AND YEAR(c_date)='$year' and dept='$dept') as r4";
        $result['cumm_rej_qty'] = $this->db->query($r2)->row();

        //LAST MONTH DATA
        $query_pre_mon = "select checked.total_checked,good.total_good,rej.total_rej,fr_cut.total_fr_cut,met_rej.total_met_rej from"
                . "(select SUM(total_checked) as total_checked from tbl_camshaft_scrap where item ='$item' and MONTH(c_date)='$pre_months' AND YEAR(c_date)='$pre_year' and dept='$dept') as checked,"
                . "(select SUM(total_good) as total_good from tbl_camshaft_scrap where item ='$item' and MONTH(c_date)='$pre_months' AND YEAR(c_date)='$pre_year' and dept='$dept') as good,"
                . "(select SUM(total_rej) as total_rej from tbl_camshaft_scrap where item ='$item' and MONTH(c_date)='$pre_months' AND YEAR(c_date)='$pre_year' and dept='$dept') as rej,"
                . "(select SUM(fr_cut) as total_fr_cut from tbl_camshaft_scrap where item ='$item' and MONTH(c_date)='$pre_months' AND YEAR(c_date)='$pre_year' and dept='$dept') as fr_cut,"
                . "(select SUM(met_rej) as total_met_rej from tbl_camshaft_scrap where item ='$item' and MONTH(c_date)='$pre_months' AND YEAR(c_date)='$pre_year' and dept='$dept') as met_rej";
        $result['pre_month_total_all'] = $id_data = $this->db->query($query_pre_mon)->row();

        $pre_month_mould = "select SUM(mould.defects_sum) as total_sum_mould,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_moulding_deffects mould ON cs.id=mould.camshaft_scrap_id where cs.item='$item' "
                . "and MONTH(cs.c_date)='$pre_months' AND YEAR(cs.c_date)='$pre_year' and cs.dept='$dept' and mould.defect_name!='' group by mould.defect_name "
                . "order by mould.id ASC";
        $result['pre_month_mould'] = $this->db->query($pre_month_mould)->result();

        $pre_month_melt = "select SUM(mould.defects_sum) as total_sum_mould,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_melting_deffects mould ON cs.id=mould.camshaft_scrap_id where cs.item='$item' "
                . "and MONTH(cs.c_date)='$pre_months' AND YEAR(cs.c_date)='$pre_year' and cs.dept='$dept' and mould.defect_name!='' group by mould.defect_name "
                . "order by mould.id ASC";
        $result['pre_month_melt'] = $this->db->query($pre_month_melt)->result();

        $pre_month_felt = "select SUM(mould.defects_sum) as total_sum_mould,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_fettling_deffects mould ON cs.id=mould.camshaft_scrap_id where cs.item='$item' "
                . "and MONTH(cs.c_date)='$pre_months' AND YEAR(cs.c_date)='$pre_year' and cs.dept='$dept' and mould.defect_name!='' group by mould.defect_name "
                . "order by mould.id ASC";
        $result['pre_month_felt'] = $this->db->query($pre_month_felt)->result();

        $pre_month_pattern = "select SUM(mould.defects_sum) as total_sum_mould,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_pattern_deffects mould ON cs.id=mould.camshaft_scrap_id where cs.item='$item' "
                . "and MONTH(cs.c_date)='$pre_months' AND YEAR(cs.c_date)='$pre_year' and cs.dept='$dept' and mould.defect_name!='' group by mould.defect_name "
                . "order by mould.id ASC";
        $result['pre_month_pattern'] = $this->db->query($pre_month_pattern)->result();
        // END LAST MONTH DATA
        //LAST LAST YEAR
        $current_year = "select checked.total_checked,good.total_good,rej.total_rej,fr_cut.total_fr_cut,met_rej.total_met_rej from"
                . "(select SUM(total_checked) as total_checked from tbl_camshaft_scrap where item ='$item' and c_date>='".$f_next_year1."-04-01' AND c_date<='".$t_pre_year3."-03-31' and dept='$dept') as checked,"
                . "(select SUM(total_good) as total_good from tbl_camshaft_scrap where item ='$item' and c_date>='".$f_next_year1."-04-01' AND c_date<='".$t_pre_year3."-03-31' and dept='$dept') as good,"
                . "(select SUM(total_rej) as total_rej from tbl_camshaft_scrap where item ='$item' and  c_date>='".$f_next_year1."-04-01' AND c_date<='".$t_pre_year3."-03-31' and dept='$dept') as rej,"
                . "(select SUM(fr_cut) as total_fr_cut from tbl_camshaft_scrap where item ='$item' and c_date>='".$f_next_year1."-04-01' AND c_date<='".$t_pre_year3."-03-31' and dept='$dept') as fr_cut,"
                . "(select SUM(met_rej) as total_met_rej from tbl_camshaft_scrap where item ='$item' and c_date>='".$f_next_year1."-04-01' AND c_date<='".$t_pre_year3."-03-31' and dept='$dept') as met_rej";
        $result['current_year_total_all']= $this->db->query($current_year)->row();

        $current_year1 = "select SUM(mould.defects_sum) as total_sum_mould,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_moulding_deffects mould ON cs.id=mould.camshaft_scrap_id where cs.item='$item' "
                . "and cs.c_date>='".$f_next_year1."-04-01' AND cs.c_date<='".$t_pre_year3."-03-31' and cs.dept='$dept' and mould.defect_name!='' group by mould.defect_name "
                . "order by mould.id ASC";
        $result['current_year_mould'] = $this->db->query($current_year1)->result();

        $current_year2 = "select SUM(mould.defects_sum) as total_sum_mould,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_melting_deffects mould ON cs.id=mould.camshaft_scrap_id where cs.item='$item' "
                . "and cs.c_date>='".$f_next_year1."-04-01' AND cs.c_date<='".$t_pre_year3."-03-31' and cs.dept='$dept' and mould.defect_name!='' group by mould.defect_name "
                . "order by mould.id ASC";
        $result['current_year_melt'] = $this->db->query($current_year2)->result();

        $current_year3 = "select SUM(mould.defects_sum) as total_sum_mould,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_fettling_deffects mould ON cs.id=mould.camshaft_scrap_id where cs.item='$item' "
                . "and cs.c_date>='".$f_next_year1."-04-01' AND cs.c_date<='".$t_pre_year3."-03-31' and cs.dept='$dept' and mould.defect_name!='' group by mould.defect_name "
                . "order by mould.id ASC";
        $result['current_year_felt'] = $this->db->query($current_year3)->result();

        $current_year4 = "select SUM(mould.defects_sum) as total_sum_mould,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_pattern_deffects mould ON cs.id=mould.camshaft_scrap_id where cs.item='$item' "
                . "and cs.c_date>='".$f_next_year1."-04-01' AND cs.c_date<='".$t_pre_year3."-03-31' and cs.dept='$dept' and mould.defect_name!='' group by mould.defect_name "
                . "order by mould.id ASC";
        $result['current_year_pattern'] = $this->db->query($current_year4)->result();
        // LAST LAST YEAR
        // Last Year
        $t_year = "select checked.total_checked,good.total_good,rej.total_rej,fr_cut.total_fr_cut,met_rej.total_met_rej from"
                . "(select SUM(total_checked) as total_checked from tbl_camshaft_scrap where item ='$item' and c_date>='".$t_pre_year3."-04-01' AND c_date<='".$s_pre_year2."-03-31' and dept='$dept') as checked,"
                . "(select SUM(total_good) as total_good from tbl_camshaft_scrap where item ='$item' and c_date>='".$t_pre_year3."-04-01' AND c_date<='".$s_pre_year2."-03-31' and dept='$dept') as good,"
                . "(select SUM(total_rej) as total_rej from tbl_camshaft_scrap where item ='$item' and  c_date>='".$t_pre_year3."-04-01' AND c_date<='".$s_pre_year2."-03-31' and dept='$dept') as rej,"
                . "(select SUM(fr_cut) as total_fr_cut from tbl_camshaft_scrap where item ='$item' and c_date>='".$t_pre_year3."-04-01' AND c_date<='".$s_pre_year2."-03-31' and dept='$dept') as fr_cut,"
                . "(select SUM(met_rej) as total_met_rej from tbl_camshaft_scrap where item ='$item' and c_date>='".$t_pre_year3."-04-01' AND c_date<='".$s_pre_year2."-03-31' and dept='$dept') as met_rej";
        $result['t_year_total_all']= $this->db->query($t_year)->row();

        $t_year1 = "select SUM(mould.defects_sum) as total_sum_mould,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_moulding_deffects mould ON cs.id=mould.camshaft_scrap_id where cs.item='$item' "
                . "and cs.c_date>='".$t_pre_year3."-04-01' AND cs.c_date<='".$s_pre_year2."-03-31' and cs.dept='$dept' and mould.defect_name!='' group by mould.defect_name "
                . "order by mould.id ASC";
        $result['t_year_mould'] = $this->db->query($t_year1)->result();

        $t_year2 = "select SUM(mould.defects_sum) as total_sum_mould,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_melting_deffects mould ON cs.id=mould.camshaft_scrap_id where cs.item='$item' "
                . "and cs.c_date>='".$t_pre_year3."-04-01' AND cs.c_date<='".$s_pre_year2."-03-31' and cs.dept='$dept' and mould.defect_name!='' group by mould.defect_name "
                . "order by mould.id ASC";
        $result['t_year_melt'] = $this->db->query($t_year2)->result();

        $t_year3 = "select SUM(mould.defects_sum) as total_sum_mould,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_fettling_deffects mould ON cs.id=mould.camshaft_scrap_id where cs.item='$item' "
                . "and cs.c_date>='".$t_pre_year3."-04-01' AND cs.c_date<='".$s_pre_year2."-03-31' and cs.dept='$dept' and mould.defect_name!='' group by mould.defect_name "
                . "order by mould.id ASC";
        $result['t_year_felt'] = $this->db->query($t_year3)->result();

        $t_year4 = "select SUM(mould.defects_sum) as total_sum_mould,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_pattern_deffects mould ON cs.id=mould.camshaft_scrap_id where cs.item='$item' "
                . "and cs.c_date>='".$t_pre_year3."-04-01' AND cs.c_date<='".$s_pre_year2."-03-31' and cs.dept='$dept' and mould.defect_name!='' group by mould.defect_name "
                . "order by mould.id ASC";
        $result['t_year_pattern'] = $this->db->query($t_year4)->result();
        // Last Year
        //Current Year
        $s_year = "select checked.total_checked,good.total_good,rej.total_rej,fr_cut.total_fr_cut,met_rej.total_met_rej from"
                . "(select SUM(total_checked) as total_checked from tbl_camshaft_scrap where item ='$item' and c_date>='".$s_pre_year2."-04-01' AND c_date<='".$year."-03-31' and c_date NOT BETWEEN '".$c_year."-".$c_month."-01' AND '".$c_year."-".$c_month."-31' and dept='$dept') as checked,"
                . "(select SUM(total_good) as total_good from tbl_camshaft_scrap where item ='$item' and c_date>='".$s_pre_year2."-04-01' AND c_date<='".$year."-03-31' and c_date NOT BETWEEN '".$c_year."-".$c_month."-01' AND '".$c_year."-".$c_month."-31' and dept='$dept') as good,"
                . "(select SUM(total_rej) as total_rej from tbl_camshaft_scrap where item ='$item' and  c_date>='".$s_pre_year2."-04-01' AND c_date<='".$year."-03-31' and c_date NOT BETWEEN '".$c_year."-".$c_month."-01' AND '".$c_year."-".$c_month."-31' and dept='$dept') as rej,"
                . "(select SUM(fr_cut) as total_fr_cut from tbl_camshaft_scrap where item ='$item' and c_date>='".$s_pre_year2."-04-01' AND c_date<='".$year."-03-31' and c_date NOT BETWEEN '".$c_year."-".$c_month."-01' AND '".$c_year."-".$c_month."-31' and dept='$dept') as fr_cut,"
                . "(select SUM(met_rej) as total_met_rej from tbl_camshaft_scrap where item ='$item' and c_date>='".$s_pre_year2."-04-01' AND c_date<='".$year."-03-31' and c_date NOT BETWEEN '".$c_year."-".$c_month."-01' AND '".$c_year."-".$c_month."-31' and dept='$dept') as met_rej";
        $result['s_year_total_all']= $this->db->query($s_year)->row();

        $s_year1 = "select SUM(mould.defects_sum) as total_sum_mould,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_moulding_deffects mould ON cs.id=mould.camshaft_scrap_id where cs.item='$item' "
                . "and cs.c_date>='".$s_pre_year2."-04-01' AND cs.c_date<='".$year."-03-31' and cs.c_date NOT BETWEEN '".$c_year."-".$c_month."-01' AND '".$c_year."-".$c_month."-31' and cs.dept='$dept' and mould.defect_name!='' group by mould.defect_name "
                . "order by mould.id ASC";
        $result['s_year_mould'] = $this->db->query($s_year1)->result();

        $s_year2 = "select SUM(mould.defects_sum) as total_sum_mould,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_melting_deffects mould ON cs.id=mould.camshaft_scrap_id where cs.item='$item' "
                . "and cs.c_date>='".$s_pre_year2."-04-01' AND cs.c_date<='".$year."-03-31' and cs.c_date NOT BETWEEN '".$c_year."-".$c_month."-01' AND '".$c_year."-".$c_month."-31' and cs.dept='$dept' and mould.defect_name!='' group by mould.defect_name "
                . "order by mould.id ASC";
        $result['s_year_melt'] = $this->db->query($s_year2)->result();

        $s_year3 = "select SUM(mould.defects_sum) as total_sum_mould,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_fettling_deffects mould ON cs.id=mould.camshaft_scrap_id where cs.item='$item' "
                . "and cs.c_date>='".$s_pre_year2."-04-01' AND cs.c_date<='".$year."-03-31' and cs.c_date NOT BETWEEN '".$c_year."-".$c_month."-01' AND '".$c_year."-".$c_month."-31' and cs.dept='$dept' and mould.defect_name!='' group by mould.defect_name "
                . "order by mould.id ASC";
        $result['s_year_felt'] = $this->db->query($s_year3)->result();

        $s_year4 = "select SUM(mould.defects_sum) as total_sum_mould,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_pattern_deffects mould ON cs.id=mould.camshaft_scrap_id where cs.item='$item' "
                . "and cs.c_date>='".$s_pre_year2."-04-01' AND cs.c_date<='".$year."-03-31' and cs.c_date NOT BETWEEN '".$c_year."-".$c_month."-01' AND '".$c_year."-".$c_month."-31' and cs.dept='$dept' group by mould.defect_name "
                . "order by mould.id ASC";
        $result['s_year_pattern'] = $this->db->query($s_year4)->result();
        // Current Year
        $this->load->view('pages/download_report', $result);
        }
        // ALL ITEM FOR D
        else if($item=="ALL_D"){
             $result['All_item']="ALL DEVELOPMENT ITEM";
            $dept=$this->session->userdata('dept');
        $query = "select *,SUM(total_checked) as total_checked,SUM(total_good) as total_good,SUM(total_rej) as total_rej,"
                . "SUM(fr_cut) as fr_cut,"
                . "GROUP_CONCAT(CONCAT('''', id, '''' )) as ids "
                . "from tbl_camshaft_scrap where  c_date='$s_date' and dept='$dept' and item_type='D'";

        $id_data = $this->db->query($query)->row();
        $cam_id =$id_data->ids;
        if($id_data->ids=='' || $id_data->ids==null)
        {
        $query2 = "select CASE WHEN fr_cut IS NULL THEN 0 ELSE 0 END AS fr_cut,CASE WHEN total_checked IS NULL THEN 0 ELSE 0 END AS total_checked,CASE WHEN total_good IS NULL THEN 0 ELSE 0 END AS total_good,
            CASE WHEN total_rej IS NULL THEN 0 ELSE 0 END AS total_rej,id as ids
         from tbl_camshaft_scrap where dept='$dept' and item_type='D'";
        $id_data = $this->db->query($query2)->row();
        $cam_id ='';
        }
        $result['data'] = $id_data;



        $prev_month_ts = strtotime($s_date . ' -1 month');
        $c_month = date("m", strtotime($s_date));
        $c_year = date("Y", strtotime($s_date));
        $prev_month = date('Y-m-d', $prev_month_ts);
        $first_date_find = strtotime(date("Y-m-d", strtotime($s_date)) . ", first day of this month");
        $first_date = date("Y-m-d", $first_date_find);
        $months = date("m", strtotime($s_date));
        $result['current_year']=$year = date("Y", strtotime($s_date));
        $pre_months = date("m", strtotime($prev_month));
        $pre_year = date("Y", strtotime($prev_month));
        // Year Functioanlity
        $f_next_year = strtotime($s_date . ' -3 year');
         $s_pre_year = strtotime($s_date . ' -1 year');
         $t_pre_year = strtotime($s_date . ' -2 year');
         $result['f_next_year1']=$f_next_year1 = date("Y", $f_next_year);
         $result['s_pre_year2']=$s_pre_year2 = date("Y", $s_pre_year);
         $result['t_pre_year3']=$t_pre_year3 = date("Y", $t_pre_year);
        // END
        $query1 = "select checked.total_checked,good.total_good,rej.total_rej,fr_cut.total_fr_cut,met_rej.total_met_rej from"
                . "(select SUM(total_checked) as total_checked from tbl_camshaft_scrap where  c_date>='$first_date' AND c_date<='$s_date' and dept='$dept' and item_type='D') as checked,"
                . "(select SUM(total_good) as total_good from tbl_camshaft_scrap where  c_date>='$first_date' AND c_date<='$s_date' and dept='$dept' and item_type='D') as good,"
                . "(select SUM(total_rej) as total_rej from tbl_camshaft_scrap where  c_date>='$first_date' AND c_date<='$s_date' and dept='$dept' and item_type='D') as rej,"
                . "(select SUM(fr_cut) as total_fr_cut from tbl_camshaft_scrap where  c_date>='$first_date' AND c_date<='$s_date' and dept='$dept' and item_type='D') as fr_cut,"
                . "(select SUM(met_rej) as total_met_rej from tbl_camshaft_scrap where  c_date>='$first_date' AND c_date<='$s_date' and dept='$dept' and item_type='D') as met_rej";
            $result['total_all'] = $id_data = $this->db->query($query1)->row();

        $query2 = "select checked.total_checked,good.total_good,rej.total_rej,fr_cut.total_fr_cut,met_rej.total_met_rej from"
                . "(select SUM(total_checked) as total_checked from tbl_camshaft_scrap where  c_date='$s_date' and dept='$dept' and item_type='D') as checked,"
                . "(select SUM(total_good) as total_good from tbl_camshaft_scrap where  c_date='$s_date' and dept='$dept' and item_type='D') as good,"
                . "(select SUM(total_rej) as total_rej from tbl_camshaft_scrap where  c_date='$s_date' and dept='$dept' and item_type='D') as rej,"
                . "(select SUM(fr_cut) as total_fr_cut from tbl_camshaft_scrap where  c_date='$s_date' and dept='$dept' and item_type='D') as fr_cut,"
                . "(select SUM(met_rej) as total_met_rej from tbl_camshaft_scrap where  c_date='$s_date' and dept='$dept' and item_type='D') as met_rej";

        $result['today_all'] = $id_data = $this->db->query($query2)->row();

        // MOULD
        if($cam_id)
        {
        $mould_q1 = "select defect_name,SUM(defects_sum) as defects_sum from tbl_cam_moulding_deffects where camshaft_scrap_id IN ($cam_id) and dept='$dept' group by defect_name order by id ASC";
        $result['day_mould'] = $this->db->query($mould_q1)->result();
        }
        else
        {
        $mould_q1 = "select defect_name,CASE WHEN defects_sum IS NULL THEN 0 ELSE 0 END AS defects_sum from tbl_cam_moulding_deffects where  dept='$dept' group by defect_name order by id ASC";
        $result['day_mould'] = $this->db->query($mould_q1)->result();
        }

        $mould_q2 = "select SUM(mould.defects_sum) as total_sum_mould,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_moulding_deffects mould ON cs.id=mould.camshaft_scrap_id where "
                . "cs.c_date>='$first_date' AND cs.c_date<='$s_date' and cs.dept='$dept' and mould.defect_name!='' and cs.item_type='D'  group by mould.defect_name "
                . "order by mould.id ASC";
        $result['cumm_mould'] = $this->db->query($mould_q2)->result();

        //END MOULD
        // MELT

        if($cam_id)
        {
        $melt_q1 = "select defect_name,SUM(defects_sum) as defects_sum  from tbl_cam_melting_deffects where camshaft_scrap_id IN ($cam_id) and dept='$dept' group by defect_name order by id ASC";
        $result['day_melt'] = $this->db->query($melt_q1)->result();
        }
        else {
        $melt_q1 = "select defect_name,CASE WHEN defects_sum IS NULL THEN 0 ELSE 0 END AS defects_sum from tbl_cam_melting_deffects where  dept='$dept' group by defect_name order by id ASC";
                $result['day_melt'] = $this->db->query($melt_q1)->result();
        }
        $melt_q2 = "select SUM(mould.defects_sum) as total_sum_mould,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_melting_deffects mould ON cs.id=mould.camshaft_scrap_id where "
                . " cs.c_date>='$first_date' AND cs.c_date<='$s_date' and cs.dept='$dept' and mould.defect_name!='' and cs.item_type='D' group by mould.defect_name "
                . "order by mould.id ASC";
        $result['cumm_melt'] = $this->db->query($melt_q2)->result();

        //END MELT
        // FELT
        if($cam_id)
        {
        $felt_q1 = "select defect_name,SUM(defects_sum) as defects_sum from tbl_cam_fettling_deffects where camshaft_scrap_id IN ($cam_id) and dept='$dept' group by defect_name order by id ASC";
        $result['day_felt'] = $this->db->query($felt_q1)->result();
        }
        else {
        $felt_q1 = "select defect_name,CASE WHEN defects_sum IS NULL THEN 0 ELSE 0 END AS defects_sum from tbl_cam_fettling_deffects where dept='$dept' group by defect_name order by id ASC";
                $result['day_felt'] = $this->db->query($felt_q1)->result();
        }
        $felt_q2 = "select SUM(mould.defects_sum) as total_sum_mould,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_fettling_deffects mould ON cs.id=mould.camshaft_scrap_id where "
                . "cs.c_date>='$first_date' AND cs.c_date<='$s_date' and cs.dept='$dept' and mould.defect_name!='' and cs.item_type='D' group by mould.defect_name "
                . "order by mould.id ASC";
        $result['cumm_felt'] = $this->db->query($felt_q2)->result();
        //END FELT
        // PATTERN
        if($cam_id)
        {
        $pattern_q1 = "select defect_name,SUM(defects_sum) as defects_sum from tbl_cam_pattern_deffects where camshaft_scrap_id IN ($cam_id) and dept='$dept' group by defect_name order by id ASC";
        $result['day_pattern'] = $this->db->query($pattern_q1)->result();
        }
        else {
          $pattern_q1 = "select defect_name,CASE WHEN defects_sum IS NULL THEN 0 ELSE 0 END AS defects_sum from tbl_cam_pattern_deffects where dept='$dept' group by defect_name order by id ASC";
          $result['day_pattern'] = $this->db->query($pattern_q1)->result();

        }

        $result['day_pattern'] = $this->db->query($pattern_q1)->result();
        $pattern_q2 = "select SUM(mould.defects_sum) as total_sum_mould,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_pattern_deffects mould ON cs.id=mould.camshaft_scrap_id where"
                . " cs.c_date>='$first_date' AND cs.c_date<='$s_date' and cs.dept='$dept' and mould.defect_name!='' and cs.item_type='D' group by mould.defect_name "
                . "order by mould.id ASC";
        $result['cumm_pattern'] = $this->db->query($pattern_q2)->result();

        //END PATTERN

        $r1 = "select r1.total_mould,r2.total_pouring,r3.total_fettling,r4.total_pattern from"
                . "(select SUM(total_mould) as total_mould  from tbl_camshaft_scrap where  c_date='$s_date' and dept='$dept' and item_type='D') as r1,"
                . "(select SUM(total_pouring) as total_pouring from tbl_camshaft_scrap where  c_date='$s_date' and dept='$dept' and item_type='D') as r2,"
                . "(select SUM(total_fettling) as total_fettling from tbl_camshaft_scrap where  c_date='$s_date' and dept='$dept' and item_type='D') as r3,"
                . "(select SUM(total_pattern) as total_pattern from tbl_camshaft_scrap where  c_date='$s_date' and dept='$dept' and item_type='D') as r4";
        $result['daily_rej_qty'] = $this->db->query($r1)->row();

        $r2 = "select r1.total_mould,r2.total_pouring,r3.total_fettling,r4.total_pattern from"
                . "(select SUM(total_mould) as total_mould  from tbl_camshaft_scrap where  MONTH(c_date)='$months' AND YEAR(c_date)='$year' and dept='$dept' and item_type='D') as r1,"
                . "(select SUM(total_pouring) as total_pouring from tbl_camshaft_scrap where  MONTH(c_date)='$months' AND YEAR(c_date)='$year'  and dept='$dept' and item_type='D') as r2,"
                . "(select SUM(total_fettling) as total_fettling from tbl_camshaft_scrap where  MONTH(c_date)='$months' AND YEAR(c_date)='$year'  and dept='$dept' and item_type='D') as r3,"
                . "(select SUM(total_pattern) as total_pattern from tbl_camshaft_scrap where  MONTH(c_date)='$months' AND YEAR(c_date)='$year' and dept='$dept' and item_type='D') as r4";
        $result['cumm_rej_qty'] = $this->db->query($r2)->row();
        //LAST MONTH DATA
        $query_pre_mon = "select checked.total_checked,good.total_good,rej.total_rej,fr_cut.total_fr_cut,met_rej.total_met_rej from"
                . "(select SUM(total_checked) as total_checked from tbl_camshaft_scrap where  MONTH(c_date)='$pre_months' AND YEAR(c_date)='$pre_year' and dept='$dept' and item_type='D') as checked,"
                . "(select SUM(total_good) as total_good from tbl_camshaft_scrap where  MONTH(c_date)='$pre_months' AND YEAR(c_date)='$pre_year' and dept='$dept' and item_type='D') as good,"
                . "(select SUM(total_rej) as total_rej from tbl_camshaft_scrap where  MONTH(c_date)='$pre_months' AND YEAR(c_date)='$pre_year' and dept='$dept' and item_type='D') as rej,"
                . "(select SUM(fr_cut) as total_fr_cut from tbl_camshaft_scrap where  MONTH(c_date)='$pre_months' AND YEAR(c_date)='$pre_year' and dept='$dept' and item_type='D') as fr_cut,"
                . "(select SUM(met_rej) as total_met_rej from tbl_camshaft_scrap where  MONTH(c_date)='$pre_months' AND YEAR(c_date)='$pre_year' and dept='$dept' and item_type='D') as met_rej";
        $result['pre_month_total_all'] = $id_data = $this->db->query($query_pre_mon)->row();

        $pre_month_mould = "select SUM(mould.defects_sum) as total_sum_mould,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_moulding_deffects mould ON cs.id=mould.camshaft_scrap_id where"
                . " MONTH(cs.c_date)='$pre_months' AND YEAR(cs.c_date)='$pre_year' and cs.dept='$dept' and cs.item_type='D' and mould.defect_name!='' group by mould.defect_name "
                . "order by mould.id ASC";
        $result['pre_month_mould'] = $this->db->query($pre_month_mould)->result();

        $pre_month_melt = "select SUM(mould.defects_sum) as total_sum_mould,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_melting_deffects mould ON cs.id=mould.camshaft_scrap_id where "
                . " MONTH(cs.c_date)='$pre_months' AND YEAR(cs.c_date)='$pre_year' and cs.dept='$dept' and cs.item_type='D' and mould.defect_name!='' group by mould.defect_name "
                . "order by mould.id ASC";
        $result['pre_month_melt'] = $this->db->query($pre_month_melt)->result();

        $pre_month_felt = "select SUM(mould.defects_sum) as total_sum_mould,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_fettling_deffects mould ON cs.id=mould.camshaft_scrap_id where "
                . " MONTH(cs.c_date)='$pre_months' AND YEAR(cs.c_date)='$pre_year' and cs.dept='$dept' and cs.item_type='D' and mould.defect_name!='' group by mould.defect_name "
                . "order by mould.id ASC";
        $result['pre_month_felt'] = $this->db->query($pre_month_felt)->result();

        $pre_month_pattern = "select SUM(mould.defects_sum) as total_sum_mould,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_pattern_deffects mould ON cs.id=mould.camshaft_scrap_id where"
                . " MONTH(cs.c_date)='$pre_months' AND YEAR(cs.c_date)='$pre_year' and cs.dept='$dept' and cs.item_type='D' and mould.defect_name!='' group by mould.defect_name "
                . "order by mould.id ASC";
        $result['pre_month_pattern'] = $this->db->query($pre_month_pattern)->result();
        // END LAST MONTH DATA
        //LAST LAST YEAR
        $current_year = "select checked.total_checked,good.total_good,rej.total_rej,fr_cut.total_fr_cut,met_rej.total_met_rej from"
                . "(select SUM(total_checked) as total_checked from tbl_camshaft_scrap where  c_date>='".$f_next_year1."-04-01' AND c_date<='".$t_pre_year3."-03-31' and dept='$dept' and item_type='D') as checked,"
                . "(select SUM(total_good) as total_good from tbl_camshaft_scrap where  c_date>='".$f_next_year1."-04-01' AND c_date<='".$t_pre_year3."-03-31' and dept='$dept' and item_type='D') as good,"
                . "(select SUM(total_rej) as total_rej from tbl_camshaft_scrap where  c_date>='".$f_next_year1."-04-01' AND c_date<='".$t_pre_year3."-03-31' and dept='$dept' and item_type='D') as rej,"
                . "(select SUM(fr_cut) as total_fr_cut from tbl_camshaft_scrap where c_date>='".$f_next_year1."-04-01' AND c_date<='".$t_pre_year3."-03-31' and dept='$dept' and item_type='D') as fr_cut,"
                . "(select SUM(met_rej) as total_met_rej from tbl_camshaft_scrap where c_date>='".$f_next_year1."-04-01' AND c_date<='".$t_pre_year3."-03-31' and dept='$dept' and item_type='D') as met_rej";
        //echo $current_year;
        $result['current_year_total_all']= $this->db->query($current_year)->row();

        $current_year1 = "select SUM(mould.defects_sum) as total_sum_mould,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_moulding_deffects mould ON cs.id=mould.camshaft_scrap_id where"
                . " cs.c_date>='".$f_next_year1."-04-01' AND cs.c_date<='".$t_pre_year3."-03-31' and cs.dept='$dept' and cs.item_type='D' and mould.defect_name!='' group by mould.defect_name "
                . "order by mould.id ASC";
        $result['current_year_mould'] = $this->db->query($current_year1)->result();

        $current_year2 = "select SUM(mould.defects_sum) as total_sum_mould,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_melting_deffects mould ON cs.id=mould.camshaft_scrap_id where "
                . " cs.c_date>='".$f_next_year1."-04-01' AND cs.c_date<='".$t_pre_year3."-03-31' and cs.dept='$dept' and cs.item_type='D' and mould.defect_name!='' group by mould.defect_name "
                . "order by mould.id ASC";
        $result['current_year_melt'] = $this->db->query($current_year2)->result();

        $current_year3 = "select SUM(mould.defects_sum) as total_sum_mould,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_fettling_deffects mould ON cs.id=mould.camshaft_scrap_id where "
                . " cs.c_date>='".$f_next_year1."-04-01' AND cs.c_date<='".$t_pre_year3."-03-31' and cs.dept='$dept' and cs.item_type='D' and mould.defect_name!='' group by mould.defect_name "
                . "order by mould.id ASC";
        $result['current_year_felt'] = $this->db->query($current_year3)->result();

        $current_year4 = "select SUM(mould.defects_sum) as total_sum_mould,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_pattern_deffects mould ON cs.id=mould.camshaft_scrap_id where "
                . " cs.c_date>='".$f_next_year1."-04-01' AND cs.c_date<='".$t_pre_year3."-03-31' and cs.dept='$dept' and cs.item_type='D' and mould.defect_name!='' group by mould.defect_name "
                . "order by mould.id ASC";
        $result['current_year_pattern'] = $this->db->query($current_year4)->result();
        // LAST LAST YEAR
        // Last Year
        $t_year = "select checked.total_checked,good.total_good,rej.total_rej,fr_cut.total_fr_cut,met_rej.total_met_rej from"
                . "(select SUM(total_checked) as total_checked from tbl_camshaft_scrap where c_date>='".$t_pre_year3."-04-01' AND c_date<='".$s_pre_year2."-03-31' and dept='$dept' and item_type='D') as checked,"
                . "(select SUM(total_good) as total_good from tbl_camshaft_scrap where c_date>='".$t_pre_year3."-04-01' AND c_date<='".$s_pre_year2."-03-31' and dept='$dept' and item_type='D') as good,"
                . "(select SUM(total_rej) as total_rej from tbl_camshaft_scrap where c_date>='".$t_pre_year3."-04-01' AND c_date<='".$s_pre_year2."-03-31' and dept='$dept' and item_type='D') as rej,"
                . "(select SUM(fr_cut) as total_fr_cut from tbl_camshaft_scrap where c_date>='".$t_pre_year3."-04-01' AND c_date<='".$s_pre_year2."-03-31' and dept='$dept' and item_type='D') as fr_cut,"
                . "(select SUM(met_rej) as total_met_rej from tbl_camshaft_scrap where c_date>='".$t_pre_year3."-04-01' AND c_date<='".$s_pre_year2."-03-31' and dept='$dept' and item_type='D') as met_rej";
        $result['t_year_total_all']= $this->db->query($t_year)->row();

        $t_year1 = "select SUM(mould.defects_sum) as total_sum_mould,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_moulding_deffects mould ON cs.id=mould.camshaft_scrap_id where"
                . " cs.c_date>='".$t_pre_year3."-04-01' AND cs.c_date<='".$s_pre_year2."-03-31' and cs.dept='$dept' and cs.item_type='D' and mould.defect_name!='' group by mould.defect_name "
                . "order by mould.id ASC";
        $result['t_year_mould'] = $this->db->query($t_year1)->result();

        $t_year2 = "select SUM(mould.defects_sum) as total_sum_mould,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_melting_deffects mould ON cs.id=mould.camshaft_scrap_id where"
                . " cs.c_date>='".$t_pre_year3."-04-01' AND cs.c_date<='".$s_pre_year2."-03-31' and cs.dept='$dept' and cs.item_type='D' and mould.defect_name!='' group by mould.defect_name "
                . "order by mould.id ASC";
        $result['t_year_melt'] = $this->db->query($t_year2)->result();

        $t_year3 = "select SUM(mould.defects_sum) as total_sum_mould,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_fettling_deffects mould ON cs.id=mould.camshaft_scrap_id where"
                . " cs.c_date>='".$t_pre_year3."-04-01' AND cs.c_date<='".$s_pre_year2."-03-31' and cs.dept='$dept' and cs.item_type='D' and mould.defect_name!='' group by mould.defect_name "
                . "order by mould.id ASC";
        $result['t_year_felt'] = $this->db->query($t_year3)->result();

        $t_year4 = "select SUM(mould.defects_sum) as total_sum_mould,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_pattern_deffects mould ON cs.id=mould.camshaft_scrap_id where"
                . " cs.c_date>='".$t_pre_year3."-04-01' AND cs.c_date<='".$s_pre_year2."-03-31' and cs.dept='$dept' and cs.item_type='D' and mould.defect_name!='' group by mould.defect_name "
                . "order by mould.id ASC";
        $result['t_year_pattern'] = $this->db->query($t_year4)->result();
        // Last Year
        //Current Year
        $s_year = "select checked.total_checked,good.total_good,rej.total_rej,fr_cut.total_fr_cut,met_rej.total_met_rej from"
                . "(select SUM(total_checked) as total_checked from tbl_camshaft_scrap where  c_date>='".$s_pre_year2."-04-01' AND c_date<='".$year."-03-31' and c_date NOT BETWEEN '".$c_year."-".$c_month."-01' AND '".$c_year."-".$c_month."-31' and dept='$dept' and item_type='D') as checked,"
                . "(select SUM(total_good) as total_good from tbl_camshaft_scrap where  c_date>='".$s_pre_year2."-04-01' AND c_date<='".$year."-03-31' and c_date NOT BETWEEN '".$c_year."-".$c_month."-01' AND '".$c_year."-".$c_month."-31' and dept='$dept' and item_type='D') as good,"
                . "(select SUM(total_rej) as total_rej from tbl_camshaft_scrap where  c_date>='".$s_pre_year2."-04-01' AND c_date<='".$year."-03-31' and c_date NOT BETWEEN '".$c_year."-".$c_month."-01' AND '".$c_year."-".$c_month."-31' and dept='$dept' and item_type='D') as rej,"
                . "(select SUM(fr_cut) as total_fr_cut from tbl_camshaft_scrap where  c_date>='".$s_pre_year2."-04-01' AND c_date<='".$year."-03-31' and c_date NOT BETWEEN '".$c_year."-".$c_month."-01' AND '".$c_year."-".$c_month."-31' and dept='$dept' and item_type='D') as fr_cut,"
                . "(select SUM(met_rej) as total_met_rej from tbl_camshaft_scrap where  c_date>='".$s_pre_year2."-04-01' AND c_date<='".$year."-03-31' and c_date NOT BETWEEN '".$c_year."-".$c_month."-01' AND '".$c_year."-".$c_month."-31' and dept='$dept' and item_type='D') as met_rej";
        $result['s_year_total_all']= $this->db->query($s_year)->row();

        $s_year1 = "select SUM(mould.defects_sum) as total_sum_mould,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_moulding_deffects mould ON cs.id=mould.camshaft_scrap_id where"
                . " cs.c_date>='".$s_pre_year2."-04-01' AND cs.c_date<='".$year."-03-31' and cs.c_date NOT BETWEEN '".$c_year."-".$c_month."-01' AND '".$c_year."-".$c_month."-31' and cs.dept='$dept' and cs.item_type='D' and mould.defect_name!='' group by mould.defect_name "
                . "order by mould.id ASC";
        $result['s_year_mould'] = $this->db->query($s_year1)->result();

        $s_year2 = "select SUM(mould.defects_sum) as total_sum_mould,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_melting_deffects mould ON cs.id=mould.camshaft_scrap_id where"
                . " cs.c_date>='".$s_pre_year2."-04-01' AND cs.c_date<='".$year."-03-31' and cs.c_date NOT BETWEEN '".$c_year."-".$c_month."-01' AND '".$c_year."-".$c_month."-31' and cs.dept='$dept' and cs.item_type='D' and mould.defect_name!='' group by mould.defect_name "
                . "order by mould.id ASC";
        $result['s_year_melt'] = $this->db->query($s_year2)->result();

        $s_year3 = "select SUM(mould.defects_sum) as total_sum_mould,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_fettling_deffects mould ON cs.id=mould.camshaft_scrap_id where"
                . " cs.c_date>='".$s_pre_year2."-04-01' AND cs.c_date<='".$year."-03-31' and cs.c_date NOT BETWEEN '".$c_year."-".$c_month."-01' AND '".$c_year."-".$c_month."-31' and cs.dept='$dept' and cs.item_type='D' and mould.defect_name!='' group by mould.defect_name "
                . "order by mould.id ASC";
        $result['s_year_felt'] = $this->db->query($s_year3)->result();

        $s_year4 = "select SUM(mould.defects_sum) as total_sum_mould,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_pattern_deffects mould ON cs.id=mould.camshaft_scrap_id where"
                . " cs.c_date>='".$s_pre_year2."-04-01' AND cs.c_date<='".$year."-03-31' and cs.c_date NOT BETWEEN '".$c_year."-".$c_month."-01' AND '".$c_year."-".$c_month."-31' and cs.dept='$dept' and cs.item_type='D' and mould.defect_name!='' group by mould.defect_name "
                . "order by mould.id ASC";
        $result['s_year_pattern'] = $this->db->query($s_year4)->result();

        $this->load->view('pages/download_report', $result);
        }
        // ALL REGULAR ITEM FOR R
        else if($item=="ALL_R"){
             $result['All_item']="ALL REGULAR ITEM";
            $dept=$this->session->userdata('dept');
            $query = "select *,SUM(total_checked) as total_checked,SUM(total_good) as total_good,SUM(total_rej) as total_rej,"
                . "SUM(fr_cut) as fr_cut,"
                . "GROUP_CONCAT(CONCAT('''', id, '''' )) as ids "
                . "from tbl_camshaft_scrap where  c_date='$s_date' and dept='$dept' and item_type='R'";

        $id_data = $this->db->query($query)->row();
        $cam_id =$id_data->ids;
        if($id_data->ids=='' || $id_data->ids==null)
        {
        $query2 = "select CASE WHEN fr_cut IS NULL THEN 0 ELSE 0 END AS fr_cut,CASE WHEN total_checked IS NULL THEN 0 ELSE 0 END AS total_checked,CASE WHEN total_good IS NULL THEN 0 ELSE 0 END AS total_good,
            CASE WHEN total_rej IS NULL THEN 0 ELSE 0 END AS total_rej,id as ids
         from tbl_camshaft_scrap where dept='$dept' and item_type='R'";
        $id_data = $this->db->query($query2)->row();
        $cam_id ='';
        }
        $result['data'] = $id_data;

        $prev_month_ts = strtotime($s_date . ' -1 month');
        $c_month = date("m", strtotime($s_date));
        $c_year = date("Y", strtotime($s_date));
        $prev_month = date('Y-m-d', $prev_month_ts);
        $first_date_find = strtotime(date("Y-m-d", strtotime($s_date)) . ", first day of this month");
        $first_date = date("Y-m-d", $first_date_find);
        $months = date("m", strtotime($s_date));
        $result['current_year']=$year = date("Y", strtotime($s_date));
        $pre_months = date("m", strtotime($prev_month));
        $pre_year = date("Y", strtotime($prev_month));
        // Year Functioanlity
         $f_next_year = strtotime($s_date . ' -3 year');
         $s_pre_year = strtotime($s_date . ' -1 year');
         $t_pre_year = strtotime($s_date . ' -2 year');
         $result['f_next_year1']=$f_next_year1 = date("Y", $f_next_year);
         $result['s_pre_year2']=$s_pre_year2 = date("Y", $s_pre_year);
         $result['t_pre_year3']=$t_pre_year3 = date("Y", $t_pre_year);
        // END
        $query1 = "select checked.total_checked,good.total_good,rej.total_rej,fr_cut.total_fr_cut,met_rej.total_met_rej from"
                . "(select SUM(total_checked) as total_checked from tbl_camshaft_scrap where  c_date>='$first_date' AND c_date<='$s_date' and dept='$dept' and item_type='R') as checked,"
                . "(select SUM(total_good) as total_good from tbl_camshaft_scrap where  c_date>='$first_date' AND c_date<='$s_date' and dept='$dept' and item_type='R') as good,"
                . "(select SUM(total_rej) as total_rej from tbl_camshaft_scrap where  c_date>='$first_date' AND c_date<='$s_date' and dept='$dept' and item_type='R') as rej,"
                . "(select SUM(fr_cut) as total_fr_cut from tbl_camshaft_scrap where  c_date>='$first_date' AND c_date<='$s_date' and dept='$dept' and item_type='R') as fr_cut,"
                . "(select SUM(met_rej) as total_met_rej from tbl_camshaft_scrap where  c_date>='$first_date' AND c_date<='$s_date' and dept='$dept' and item_type='R') as met_rej";
            $result['total_all'] = $id_data = $this->db->query($query1)->row();

        $query2 = "select checked.total_checked,good.total_good,rej.total_rej,fr_cut.total_fr_cut,met_rej.total_met_rej from"
                . "(select SUM(total_checked) as total_checked from tbl_camshaft_scrap where  c_date='$s_date' and dept='$dept' and item_type='R') as checked,"
                . "(select SUM(total_good) as total_good from tbl_camshaft_scrap where  c_date='$s_date' and dept='$dept' and item_type='R') as good,"
                . "(select SUM(total_rej) as total_rej from tbl_camshaft_scrap where  c_date='$s_date' and dept='$dept' and item_type='R') as rej,"
                . "(select SUM(fr_cut) as total_fr_cut from tbl_camshaft_scrap where  c_date='$s_date' and dept='$dept' and item_type='R') as fr_cut,"
                . "(select SUM(met_rej) as total_met_rej from tbl_camshaft_scrap where  c_date='$s_date' and dept='$dept' and item_type='R') as met_rej";
            $result['today_all'] = $id_data = $this->db->query($query2)->row();

        // MOULD
        if($cam_id)
        {
        $mould_q1 = "select defect_name,SUM(defects_sum) as defects_sum from tbl_cam_moulding_deffects where camshaft_scrap_id IN ($cam_id) and dept='$dept' group by defect_name order by id ASC";
        $result['day_mould'] = $this->db->query($mould_q1)->result();
        }
        else
        {
        $mould_q1 = "select defect_name,CASE WHEN defects_sum IS NULL THEN 0 ELSE 0 END AS defects_sum from tbl_cam_moulding_deffects where  dept='$dept' group by defect_name order by id ASC";
        $result['day_mould'] = $this->db->query($mould_q1)->result();
        }

        $mould_q2 = "select SUM(mould.defects_sum) as total_sum_mould,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_moulding_deffects mould ON cs.id=mould.camshaft_scrap_id where "
                . "cs.c_date>='$first_date' AND cs.c_date<='$s_date' and cs.dept='$dept' and mould.defect_name!='' and cs.item_type='R'  group by mould.defect_name "
                . "order by mould.id ASC";
        $result['cumm_mould'] = $this->db->query($mould_q2)->result();

        //END MOULD
        // MELT
        if($cam_id)
        {
        $melt_q1 = "select defect_name,SUM(defects_sum) as defects_sum  from tbl_cam_melting_deffects where camshaft_scrap_id IN ($cam_id) and dept='$dept' group by defect_name order by id ASC";
        $result['day_melt'] = $this->db->query($melt_q1)->result();
        }
        else {
        $melt_q1 = "select defect_name,CASE WHEN defects_sum IS NULL THEN 0 ELSE 0 END AS defects_sum from tbl_cam_melting_deffects where  dept='$dept' group by defect_name order by id ASC";
                $result['day_melt'] = $this->db->query($melt_q1)->result();
        }
        $melt_q2 = "select SUM(mould.defects_sum) as total_sum_mould,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_melting_deffects mould ON cs.id=mould.camshaft_scrap_id where "
                . " cs.c_date>='$first_date' AND cs.c_date<='$s_date' and cs.dept='$dept' and mould.defect_name!='' and cs.item_type='R' group by mould.defect_name "
                . "order by mould.id ASC";
        $result['cumm_melt'] = $this->db->query($melt_q2)->result();

        //END MELT
        // FELT
        if($cam_id)
        {
        $felt_q1 = "select defect_name,SUM(defects_sum) as defects_sum from tbl_cam_fettling_deffects where camshaft_scrap_id IN ($cam_id) and dept='$dept' group by defect_name order by id ASC";
        $result['day_felt'] = $this->db->query($felt_q1)->result();
        }
        else {
        $felt_q1 = "select defect_name,CASE WHEN defects_sum IS NULL THEN 0 ELSE 0 END AS defects_sum from tbl_cam_fettling_deffects where dept='$dept' group by defect_name order by id ASC";
        $result['day_felt'] = $this->db->query($felt_q1)->result();
        }
        $felt_q2 = "select SUM(mould.defects_sum) as total_sum_mould,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_fettling_deffects mould ON cs.id=mould.camshaft_scrap_id where "
                . "cs.c_date>='$first_date' AND cs.c_date<='$s_date' and cs.dept='$dept' and mould.defect_name!='' and cs.item_type='R' group by mould.defect_name "
                . "order by mould.id ASC";
        $result['cumm_felt'] = $this->db->query($felt_q2)->result();
        //END FELT
        // PATTERN
        if($cam_id)
        {
        $pattern_q1 = "select defect_name,SUM(defects_sum) as defects_sum from tbl_cam_pattern_deffects where camshaft_scrap_id IN ($cam_id) and dept='$dept' group by defect_name order by id ASC";
        $result['day_pattern'] = $this->db->query($pattern_q1)->result();
        }
        else {
          $pattern_q1 = "select defect_name,CASE WHEN defects_sum IS NULL THEN 0 ELSE 0 END AS defects_sum from tbl_cam_pattern_deffects where dept='$dept' group by defect_name order by id ASC";
          $result['day_pattern'] = $this->db->query($pattern_q1)->result();

        }
        $pattern_q2 = "select SUM(mould.defects_sum) as total_sum_mould,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_pattern_deffects mould ON cs.id=mould.camshaft_scrap_id where"
                . " cs.c_date>='$first_date' AND cs.c_date<='$s_date' and cs.dept='$dept' and mould.defect_name!='' and cs.item_type='R' group by mould.defect_name "
                . "order by mould.id ASC";
        $result['cumm_pattern'] = $this->db->query($pattern_q2)->result();

        //END PATTERN

        $r1 = "select r1.total_mould,r2.total_pouring,r3.total_fettling,r4.total_pattern from"
                . "(select SUM(total_mould) as total_mould  from tbl_camshaft_scrap where  c_date='$s_date' and dept='$dept' and item_type='R') as r1,"
                . "(select SUM(total_pouring) as total_pouring from tbl_camshaft_scrap where  c_date='$s_date' and dept='$dept' and item_type='R') as r2,"
                . "(select SUM(total_fettling) as total_fettling from tbl_camshaft_scrap where  c_date='$s_date' and dept='$dept' and item_type='R') as r3,"
                . "(select SUM(total_pattern) as total_pattern from tbl_camshaft_scrap where  c_date='$s_date' and dept='$dept' and item_type='R') as r4";
        $result['daily_rej_qty'] = $this->db->query($r1)->row();

        $r2 = "select r1.total_mould,r2.total_pouring,r3.total_fettling,r4.total_pattern from"
                . "(select SUM(total_mould) as total_mould  from tbl_camshaft_scrap where  MONTH(c_date)='$months' AND YEAR(c_date)='$year' and dept='$dept' and item_type='R') as r1,"
                . "(select SUM(total_pouring) as total_pouring from tbl_camshaft_scrap where  MONTH(c_date)='$months' AND YEAR(c_date)='$year'  and dept='$dept' and item_type='R') as r2,"
                . "(select SUM(total_fettling) as total_fettling from tbl_camshaft_scrap where  MONTH(c_date)='$months' AND YEAR(c_date)='$year'  and dept='$dept' and item_type='R') as r3,"
                . "(select SUM(total_pattern) as total_pattern from tbl_camshaft_scrap where  MONTH(c_date)='$months' AND YEAR(c_date)='$year' and dept='$dept' and item_type='R') as r4";
        $result['cumm_rej_qty'] = $this->db->query($r2)->row();
        //LAST MONTH DATA
        $query_pre_mon = "select checked.total_checked,good.total_good,rej.total_rej,fr_cut.total_fr_cut,met_rej.total_met_rej from"
                . "(select SUM(total_checked) as total_checked from tbl_camshaft_scrap where  MONTH(c_date)='$pre_months' AND YEAR(c_date)='$pre_year' and dept='$dept' and item_type='R') as checked,"
                . "(select SUM(total_good) as total_good from tbl_camshaft_scrap where  MONTH(c_date)='$pre_months' AND YEAR(c_date)='$pre_year' and dept='$dept' and item_type='R') as good,"
                . "(select SUM(total_rej) as total_rej from tbl_camshaft_scrap where  MONTH(c_date)='$pre_months' AND YEAR(c_date)='$pre_year' and dept='$dept' and item_type='R') as rej,"
                . "(select SUM(fr_cut) as total_fr_cut from tbl_camshaft_scrap where  MONTH(c_date)='$pre_months' AND YEAR(c_date)='$pre_year' and dept='$dept' and item_type='R') as fr_cut,"
                . "(select SUM(met_rej) as total_met_rej from tbl_camshaft_scrap where  MONTH(c_date)='$pre_months' AND YEAR(c_date)='$pre_year' and dept='$dept' and item_type='R') as met_rej";
        $result['pre_month_total_all'] = $id_data = $this->db->query($query_pre_mon)->row();

        $pre_month_mould = "select SUM(mould.defects_sum) as total_sum_mould,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_moulding_deffects mould ON cs.id=mould.camshaft_scrap_id where"
                . " MONTH(cs.c_date)='$pre_months' AND YEAR(cs.c_date)='$pre_year' and cs.dept='$dept' and cs.item_type='R' and mould.defect_name!='' group by mould.defect_name "
                . "order by mould.id ASC";
        $result['pre_month_mould'] = $this->db->query($pre_month_mould)->result();

        $pre_month_melt = "select SUM(mould.defects_sum) as total_sum_mould,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_melting_deffects mould ON cs.id=mould.camshaft_scrap_id where "
                . " MONTH(cs.c_date)='$pre_months' AND YEAR(cs.c_date)='$pre_year' and cs.dept='$dept' and cs.item_type='R' and mould.defect_name!='' group by mould.defect_name "
                . "order by mould.id ASC";
        $result['pre_month_melt'] = $this->db->query($pre_month_melt)->result();

        $pre_month_felt = "select SUM(mould.defects_sum) as total_sum_mould,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_fettling_deffects mould ON cs.id=mould.camshaft_scrap_id where "
                . " MONTH(cs.c_date)='$pre_months' AND YEAR(cs.c_date)='$pre_year' and cs.dept='$dept' and cs.item_type='R' and mould.defect_name!='' group by mould.defect_name "
                . "order by mould.id ASC";
        $result['pre_month_felt'] = $this->db->query($pre_month_felt)->result();

        $pre_month_pattern = "select SUM(mould.defects_sum) as total_sum_mould,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_pattern_deffects mould ON cs.id=mould.camshaft_scrap_id where"
                . " MONTH(cs.c_date)='$pre_months' AND YEAR(cs.c_date)='$pre_year' and cs.dept='$dept' and cs.item_type='R' and mould.defect_name!='' group by mould.defect_name "
                . "order by mould.id ASC";
        $result['pre_month_pattern'] = $this->db->query($pre_month_pattern)->result();
        // END LAST MONTH DATA
        //LAST LAST YEAR
        $current_year = "select checked.total_checked,good.total_good,rej.total_rej,fr_cut.total_fr_cut,met_rej.total_met_rej from"
                . "(select SUM(total_checked) as total_checked from tbl_camshaft_scrap where  c_date>='".$f_next_year1."-04-01' AND c_date<='".$t_pre_year3."-03-31' and dept='$dept' and item_type='R') as checked,"
                . "(select SUM(total_good) as total_good from tbl_camshaft_scrap where  c_date>='".$f_next_year1."-04-01' AND c_date<='".$t_pre_year3."-03-31' and dept='$dept' and item_type='R') as good,"
                . "(select SUM(total_rej) as total_rej from tbl_camshaft_scrap where  c_date>='".$f_next_year1."-04-01' AND c_date<='".$t_pre_year3."-03-31' and dept='$dept' and item_type='R') as rej,"
                . "(select SUM(fr_cut) as total_fr_cut from tbl_camshaft_scrap where c_date>='".$f_next_year1."-04-01' AND c_date<='".$t_pre_year3."-03-31' and dept='$dept' and item_type='R') as fr_cut,"
                . "(select SUM(met_rej) as total_met_rej from tbl_camshaft_scrap where c_date>='".$f_next_year1."-04-01' AND c_date<='".$t_pre_year3."-03-31' and dept='$dept' and item_type='R') as met_rej";
        $result['current_year_total_all']= $this->db->query($current_year)->row();

        $current_year1 = "select SUM(mould.defects_sum) as total_sum_mould,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_moulding_deffects mould ON cs.id=mould.camshaft_scrap_id where"
                . " cs.c_date>='".$f_next_year1."-04-01' AND cs.c_date<='".$t_pre_year3."-03-31' and cs.dept='$dept' and cs.item_type='R' and mould.defect_name!='' group by mould.defect_name "
                . "order by mould.id ASC";
        $result['current_year_mould'] = $this->db->query($current_year1)->result();

        $current_year2 = "select SUM(mould.defects_sum) as total_sum_mould,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_melting_deffects mould ON cs.id=mould.camshaft_scrap_id where "
                . " cs.c_date>='".$f_next_year1."-04-01' AND cs.c_date<='".$t_pre_year3."-03-31' and cs.dept='$dept' and cs.item_type='R' and mould.defect_name!='' group by mould.defect_name "
                . "order by mould.id ASC";
        $result['current_year_melt'] = $this->db->query($current_year2)->result();

        $current_year3 = "select SUM(mould.defects_sum) as total_sum_mould,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_fettling_deffects mould ON cs.id=mould.camshaft_scrap_id where "
                . " cs.c_date>='".$f_next_year1."-04-01' AND cs.c_date<='".$t_pre_year3."-03-31' and cs.dept='$dept' and cs.item_type='R' and mould.defect_name!='' group by mould.defect_name "
                . "order by mould.id ASC";
        $result['current_year_felt'] = $this->db->query($current_year3)->result();

        $current_year4 = "select SUM(mould.defects_sum) as total_sum_mould,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_pattern_deffects mould ON cs.id=mould.camshaft_scrap_id where "
                . " cs.c_date>='".$f_next_year1."-04-01' AND cs.c_date<='".$t_pre_year3."-03-31' and cs.dept='$dept' and cs.item_type='R' and mould.defect_name!='' group by mould.defect_name "
                . "order by mould.id ASC";
        $result['current_year_pattern'] = $this->db->query($current_year4)->result();
        // LAST LAST YEAR
        // Last Year
        $t_year = "select checked.total_checked,good.total_good,rej.total_rej,fr_cut.total_fr_cut,met_rej.total_met_rej from"
                . "(select SUM(total_checked) as total_checked from tbl_camshaft_scrap where c_date>='".$t_pre_year3."-04-01' AND c_date<='".$s_pre_year2."-03-31' and dept='$dept' and item_type='R') as checked,"
                . "(select SUM(total_good) as total_good from tbl_camshaft_scrap where c_date>='".$t_pre_year3."-04-01' AND c_date<='".$s_pre_year2."-03-31' and dept='$dept' and item_type='R') as good,"
                . "(select SUM(total_rej) as total_rej from tbl_camshaft_scrap where c_date>='".$t_pre_year3."-04-01' AND c_date<='".$s_pre_year2."-03-31' and dept='$dept' and item_type='R') as rej,"
                . "(select SUM(fr_cut) as total_fr_cut from tbl_camshaft_scrap where c_date>='".$t_pre_year3."-04-01' AND c_date<='".$s_pre_year2."-03-31' and dept='$dept' and item_type='R') as fr_cut,"
                . "(select SUM(met_rej) as total_met_rej from tbl_camshaft_scrap where c_date>='".$t_pre_year3."-04-01' AND c_date<='".$s_pre_year2."-03-31' and dept='$dept' and item_type='R') as met_rej";
        $result['t_year_total_all']= $this->db->query($t_year)->row();

        $t_year1 = "select SUM(mould.defects_sum) as total_sum_mould,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_moulding_deffects mould ON cs.id=mould.camshaft_scrap_id where"
                . " cs.c_date>='".$t_pre_year3."-04-01' AND cs.c_date<='".$s_pre_year2."-03-31' and cs.dept='$dept' and cs.item_type='R' and mould.defect_name!='' group by mould.defect_name "
                . "order by mould.id ASC";
        $result['t_year_mould'] = $this->db->query($t_year1)->result();

        $t_year2 = "select SUM(mould.defects_sum) as total_sum_mould,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_melting_deffects mould ON cs.id=mould.camshaft_scrap_id where"
                . " cs.c_date>='".$t_pre_year3."-04-01' AND cs.c_date<='".$s_pre_year2."-03-31' and cs.dept='$dept' and cs.item_type='R' and mould.defect_name!='' group by mould.defect_name "
                . "order by mould.id ASC";
        $result['t_year_melt'] = $this->db->query($t_year2)->result();

        $t_year3 = "select SUM(mould.defects_sum) as total_sum_mould,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_fettling_deffects mould ON cs.id=mould.camshaft_scrap_id where"
                . " cs.c_date>='".$t_pre_year3."-04-01' AND cs.c_date<='".$s_pre_year2."-03-31' and cs.dept='$dept' and cs.item_type='R' and mould.defect_name!='' group by mould.defect_name "
                . "order by mould.id ASC";
        $result['t_year_felt'] = $this->db->query($t_year3)->result();

        $t_year4 = "select SUM(mould.defects_sum) as total_sum_mould,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_pattern_deffects mould ON cs.id=mould.camshaft_scrap_id where"
                . " cs.c_date>='".$t_pre_year3."-04-01' AND cs.c_date<='".$s_pre_year2."-03-31' and cs.dept='$dept' and cs.item_type='R' and mould.defect_name!='' group by mould.defect_name "
                . "order by mould.id ASC";
        $result['t_year_pattern'] = $this->db->query($t_year4)->result();

        // Last Year
        //Current Year
        $s_year = "select checked.total_checked,good.total_good,rej.total_rej,fr_cut.total_fr_cut,met_rej.total_met_rej from"
                . "(select SUM(total_checked) as total_checked from tbl_camshaft_scrap where  c_date>='".$s_pre_year2."-04-01' AND c_date<='".$year."-03-31' and c_date NOT BETWEEN '".$c_year."-".$c_month."-01' AND '".$c_year."-".$c_month."-31' and dept='$dept' and item_type='R') as checked,"
                . "(select SUM(total_good) as total_good from tbl_camshaft_scrap where  c_date>='".$s_pre_year2."-04-01' AND c_date<='".$year."-03-31' and c_date NOT BETWEEN '".$c_year."-".$c_month."-01' AND '".$c_year."-".$c_month."-31' and dept='$dept' and item_type='R') as good,"
                . "(select SUM(total_rej) as total_rej from tbl_camshaft_scrap where  c_date>='".$s_pre_year2."-04-01' AND c_date<='".$year."-03-31' and c_date NOT BETWEEN '".$c_year."-".$c_month."-01' AND '".$c_year."-".$c_month."-31' and dept='$dept' and item_type='R') as rej,"
                . "(select SUM(fr_cut) as total_fr_cut from tbl_camshaft_scrap where  c_date>='".$s_pre_year2."-04-01' AND c_date<='".$year."-03-31' and c_date NOT BETWEEN '".$c_year."-".$c_month."-01' AND '".$c_year."-".$c_month."-31' and dept='$dept' and item_type='R') as fr_cut,"
                . "(select SUM(met_rej) as total_met_rej from tbl_camshaft_scrap where  c_date>='".$s_pre_year2."-04-01' AND c_date<='".$year."-03-31' and c_date NOT BETWEEN '".$c_year."-".$c_month."-01' AND '".$c_year."-".$c_month."-31' and dept='$dept' and item_type='R') as met_rej";
        $result['s_year_total_all']= $this->db->query($s_year)->row();

        $s_year1 = "select SUM(mould.defects_sum) as total_sum_mould,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_moulding_deffects mould ON cs.id=mould.camshaft_scrap_id where"
                . " cs.c_date>='".$s_pre_year2."-04-01' AND cs.c_date<='".$year."-03-31' and cs.c_date NOT BETWEEN '".$c_year."-".$c_month."-01' AND '".$c_year."-".$c_month."-31' and cs.dept='$dept' and cs.item_type='R' and mould.defect_name!='' group by mould.defect_name "
                . "order by mould.id ASC";
        $result['s_year_mould'] = $this->db->query($s_year1)->result();

        $s_year2 = "select SUM(mould.defects_sum) as total_sum_mould,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_melting_deffects mould ON cs.id=mould.camshaft_scrap_id where"
                . " cs.c_date>='".$s_pre_year2."-04-01' AND cs.c_date<='".$year."-03-31' and cs.c_date NOT BETWEEN '".$c_year."-".$c_month."-01' AND '".$c_year."-".$c_month."-31' and cs.dept='$dept' and cs.item_type='R' and mould.defect_name!='' group by mould.defect_name "
                . "order by mould.id ASC";
        $result['s_year_melt'] = $this->db->query($s_year2)->result();

        $s_year3 = "select SUM(mould.defects_sum) as total_sum_mould,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_fettling_deffects mould ON cs.id=mould.camshaft_scrap_id where"
                . " cs.c_date>='".$s_pre_year2."-04-01' AND cs.c_date<='".$year."-03-31' and cs.c_date NOT BETWEEN '".$c_year."-".$c_month."-01' AND '".$c_year."-".$c_month."-31' and cs.dept='$dept' and cs.item_type='R' and mould.defect_name!='' group by mould.defect_name "
                . "order by mould.id ASC";
        $result['s_year_felt'] = $this->db->query($s_year3)->result();

        $s_year4 = "select SUM(mould.defects_sum) as total_sum_mould,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_pattern_deffects mould ON cs.id=mould.camshaft_scrap_id where"
                . " cs.c_date>='".$s_pre_year2."-04-01' AND cs.c_date<='".$year."-03-31' and cs.c_date NOT BETWEEN '".$c_year."-".$c_month."-01' AND '".$c_year."-".$c_month."-31' and cs.dept='$dept' and cs.item_type='R' and mould.defect_name!='' group by mould.defect_name "
                . "order by mould.id ASC";
        $result['s_year_pattern'] = $this->db->query($s_year4)->result();

        $this->load->view('pages/download_report', $result);
        }

    }





    public function get_pareto_analysis_data() {
        $item = $_POST["item"];
        $s_date = $_POST["s_date"];
        if($item!="ALL_R" && $item!="ALL_D")
        {
        $months = date("m", strtotime($s_date));
        $year = date("Y", strtotime($s_date));
        $dept=$this->session->userdata('dept');
        $query = "select * from tbl_camshaft_scrap where item='$item' and MONTH(c_date)='$months' AND YEAR(c_date)='$year' and dept='$dept'";
        $result['data'] = $id_data = $this->db->query($query)->row();
        $result['name']=$id_data->cust_name;
        $cam_id = $id_data->id;
        $result['m'] = $months;
        $result['y'] = $year;
        $result['item'] = $item;
        $result['s_date'] = $s_date;
        $query1 = "select checked.total_checked,good.total_good,rej.total_rej,fr_cut.total_fr_cut,met_rej.total_met_rej from"
                . "(select SUM(total_checked) as total_checked from tbl_camshaft_scrap where item ='$item' and MONTH(c_date)='$months' AND YEAR(c_date)='$year' and dept='$dept') as checked,"
                . "(select SUM(total_good) as total_good from tbl_camshaft_scrap where item ='$item' and MONTH(c_date)='$months' AND YEAR(c_date)='$year' and dept='$dept') as good,"
                . "(select SUM(total_rej) as total_rej from tbl_camshaft_scrap where item ='$item' and MONTH(c_date)='$months' AND YEAR(c_date)='$year' and dept='$dept') as rej,"
                . "(select SUM(fr_cut) as total_fr_cut from tbl_camshaft_scrap where item ='$item' and MONTH(c_date)='$months' AND YEAR(c_date)='$year' and dept='$dept') as fr_cut,"
                . "(select SUM(met_rej) as total_met_rej from tbl_camshaft_scrap where item ='$item' and MONTH(c_date)='$months' AND YEAR(c_date)='$year' and dept='$dept') as met_rej";
        $result['total_all'] = $id_data = $this->db->query($query1)->row();

        // MOULD
        $mould_q1 = "select defect_name,defects_sum from tbl_cam_moulding_deffects where camshaft_scrap_id='$cam_id' and dept='$dept'";
        $result['day_mould'] = $this->db->query($mould_q1)->result();
        $mould_q2 = "select SUM(mould.defects_sum) as total_sum_mould,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_moulding_deffects mould ON cs.id=mould.camshaft_scrap_id where cs.item='$item' "
                . "and MONTH(cs.c_date)='$months' AND YEAR(cs.c_date)='$year' and cs.dept='$dept' and mould.defect_name!='' group by mould.defect_name "
                . "order by mould.id ASC";

        $result['cumm_mould'] = $this->db->query($mould_q2)->result();
        //END MOULD
        // MELT
        $melt_q1 = "select defect_name,defects_sum from tbl_cam_melting_deffects where camshaft_scrap_id='$cam_id'";
        $result['day_melt'] = $this->db->query($melt_q1)->result();
        $melt_q2 = "select SUM(mould.defects_sum) as total_sum_melt,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_melting_deffects mould ON cs.id=mould.camshaft_scrap_id where cs.item='$item' "
                . "and MONTH(cs.c_date)='$months' AND YEAR(cs.c_date)='$year' and cs.dept='$dept' and mould.defect_name!='' group by mould.defect_name "
                . "order by mould.id ASC";
        //echo $melt_q2;
        $result['cumm_melt'] = $this->db->query($melt_q2)->result();
        //END MELT
        // FELT
        $felt_q1 = "select defect_name,defects_sum from tbl_cam_fettling_deffects where camshaft_scrap_id='$cam_id'";
        $result['day_felt'] = $this->db->query($felt_q1)->result();
        $felt_q2 = "select SUM(mould.defects_sum) as total_sum_felt,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_fettling_deffects mould ON cs.id=mould.camshaft_scrap_id where cs.item='$item' "
                . "and MONTH(cs.c_date)='$months' AND YEAR(cs.c_date)='$year' and cs.dept='$dept' and mould.defect_name!='' group by mould.defect_name "
                . "order by mould.id ASC";
        $result['cumm_felt'] = $this->db->query($felt_q2)->result();
        //END FELT
        // PATTERN
        $pattern_q1 = "select defect_name,defects_sum from tbl_cam_pattern_deffects where camshaft_scrap_id='$cam_id'";
        $result['day_pattern'] = $this->db->query($pattern_q1)->result();
        $pattern_q2 = "select SUM(mould.defects_sum) as total_sum_pattern,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_pattern_deffects mould ON cs.id=mould.camshaft_scrap_id where cs.item='$item' "
                . "and MONTH(cs.c_date)='$months' AND YEAR(cs.c_date)='$year' and cs.dept='$dept' and mould.defect_name!='' group by mould.defect_name "
                . "order by mould.id ASC";
        //echo $pattern_q2;
        $result['cumm_pattern'] = $this->db->query($pattern_q2)->result();
        }
    else if($item=="ALL_R") {
        $result['name']="ALL REGULAR ITEM";
        $months = date("m", strtotime($s_date));
        $year = date("Y", strtotime($s_date));
        $dept=$this->session->userdata('dept');
        $query = "select * from tbl_camshaft_scrap where MONTH(c_date)='$months' AND YEAR(c_date)='$year' and dept='$dept' and item_type='R'";
        $result['data'] = $id_data = $this->db->query($query)->row();
        if($id_data)
        {
        $cam_id = $id_data->id;
        $result['m'] = $months;
        $result['y'] = $year;
        $result['item'] = $item;
        $result['s_date'] = $s_date;
        $query1 = "select checked.total_checked,good.total_good,rej.total_rej,fr_cut.total_fr_cut,met_rej.total_met_rej from"
                . "(select SUM(total_checked) as total_checked from tbl_camshaft_scrap where  MONTH(c_date)='$months' AND YEAR(c_date)='$year' and dept='$dept' and item_type='R') as checked,"
                . "(select SUM(total_good) as total_good from tbl_camshaft_scrap where MONTH(c_date)='$months' AND YEAR(c_date)='$year' and dept='$dept' and item_type='R') as good,"
                . "(select SUM(total_rej) as total_rej from tbl_camshaft_scrap where  MONTH(c_date)='$months' AND YEAR(c_date)='$year' and dept='$dept' and item_type='R') as rej,"
                . "(select SUM(fr_cut) as total_fr_cut from tbl_camshaft_scrap where MONTH(c_date)='$months' AND YEAR(c_date)='$year' and dept='$dept' and item_type='R') as fr_cut,"
                . "(select SUM(met_rej) as total_met_rej from tbl_camshaft_scrap where MONTH(c_date)='$months' AND YEAR(c_date)='$year' and dept='$dept' and item_type='R') as met_rej";
        $result['total_all'] = $id_data = $this->db->query($query1)->row();

        // MOULD
        $mould_q1 = "select defect_name,defects_sum from tbl_cam_moulding_deffects where camshaft_scrap_id='$cam_id' and dept='$dept'";
        $result['day_mould'] = $this->db->query($mould_q1)->result();
        $mould_q2 = "select SUM(mould.defects_sum) as total_sum_mould,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_moulding_deffects mould ON cs.id=mould.camshaft_scrap_id where"
                . " MONTH(cs.c_date)='$months' AND YEAR(cs.c_date)='$year' and cs.dept='$dept' and mould.defect_name!='' and cs.item_type='R'  group by mould.defect_name "
                . "order by mould.id ASC";

        $result['cumm_mould'] = $this->db->query($mould_q2)->result();
        //END MOULD
        // MELT
        $melt_q1 = "select defect_name,defects_sum from tbl_cam_melting_deffects where camshaft_scrap_id='$cam_id' and dept='$dept'";
        $result['day_melt'] = $this->db->query($melt_q1)->result();
        $melt_q2 = "select SUM(mould.defects_sum) as total_sum_melt,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_melting_deffects mould ON cs.id=mould.camshaft_scrap_id where"
                . " MONTH(cs.c_date)='$months' AND YEAR(cs.c_date)='$year' and cs.dept='$dept' and mould.defect_name!='' and cs.item_type='R' group by mould.defect_name "
                . "order by mould.id ASC";
        //echo $melt_q2;
        $result['cumm_melt'] = $this->db->query($melt_q2)->result();
        //END MELT
        // FELT
        $felt_q1 = "select defect_name,defects_sum from tbl_cam_fettling_deffects where camshaft_scrap_id='$cam_id' and dept='$dept'";
        $result['day_felt'] = $this->db->query($felt_q1)->result();
        $felt_q2 = "select SUM(mould.defects_sum) as total_sum_felt,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_fettling_deffects mould ON cs.id=mould.camshaft_scrap_id where"
                . " MONTH(cs.c_date)='$months' AND YEAR(cs.c_date)='$year' and cs.dept='$dept' and mould.defect_name!='' and cs.item_type='R' group by mould.defect_name "
                . "order by mould.id ASC";
        $result['cumm_felt'] = $this->db->query($felt_q2)->result();
        //END FELT
        // PATTERN
        $pattern_q1 = "select defect_name,defects_sum from tbl_cam_pattern_deffects where camshaft_scrap_id='$cam_id' and dept='$dept'";
        $result['day_pattern'] = $this->db->query($pattern_q1)->result();
        $pattern_q2 = "select SUM(mould.defects_sum) as total_sum_pattern,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_pattern_deffects mould ON cs.id=mould.camshaft_scrap_id where"
                . " MONTH(cs.c_date)='$months' AND YEAR(cs.c_date)='$year' and cs.dept='$dept' and mould.defect_name!='' and cs.item_type='R' group by mould.defect_name "
                . "order by mould.id ASC";
        $result['cumm_pattern'] = $this->db->query($pattern_q2)->result();
 }
    }


 else if($item=="ALL_D"){
        $result['name']="ALL DEVELOPMENT ITEM";
        $months = date("m", strtotime($s_date));
        $year = date("Y", strtotime($s_date));
        $dept=$this->session->userdata('dept');
        $query = "select * from tbl_camshaft_scrap where MONTH(c_date)='$months' AND YEAR(c_date)='$year' and dept='$dept' and item_type='D'";
        $result['data'] = $id_data = $this->db->query($query)->row();
        if($id_data)
        {
        $cam_id = $id_data->id;
        $result['m'] = $months;
        $result['y'] = $year;
        $result['item'] = $item;
        $result['s_date'] = $s_date;
        $query1 = "select checked.total_checked,good.total_good,rej.total_rej,fr_cut.total_fr_cut,met_rej.total_met_rej from"
                . "(select SUM(total_checked) as total_checked from tbl_camshaft_scrap where  MONTH(c_date)='$months' AND YEAR(c_date)='$year' and dept='$dept' and item_type='D') as checked,"
                . "(select SUM(total_good) as total_good from tbl_camshaft_scrap where MONTH(c_date)='$months' AND YEAR(c_date)='$year' and dept='$dept' and item_type='D') as good,"
                . "(select SUM(total_rej) as total_rej from tbl_camshaft_scrap where  MONTH(c_date)='$months' AND YEAR(c_date)='$year' and dept='$dept' and item_type='D') as rej,"
                . "(select SUM(fr_cut) as total_fr_cut from tbl_camshaft_scrap where MONTH(c_date)='$months' AND YEAR(c_date)='$year' and dept='$dept' and item_type='D') as fr_cut,"
                . "(select SUM(met_rej) as total_met_rej from tbl_camshaft_scrap where MONTH(c_date)='$months' AND YEAR(c_date)='$year' and dept='$dept' and item_type='D') as met_rej";
        $result['total_all'] = $id_data = $this->db->query($query1)->row();

        // MOULD
        $mould_q1 = "select defect_name,defects_sum from tbl_cam_moulding_deffects where camshaft_scrap_id='$cam_id' and dept='$dept' ";
        $result['day_mould'] = $this->db->query($mould_q1)->result();
        $mould_q2 = "select SUM(mould.defects_sum) as total_sum_mould,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_moulding_deffects mould ON cs.id=mould.camshaft_scrap_id where"
                . " MONTH(cs.c_date)='$months' AND YEAR(cs.c_date)='$year' and cs.dept='$dept' and mould.defect_name!='' and cs.item_type='D' group by mould.defect_name "
                . "order by mould.id ASC";

        $result['cumm_mould'] = $this->db->query($mould_q2)->result();
        //END MOULD
        // MELT
        $melt_q1 = "select defect_name,defects_sum from tbl_cam_melting_deffects where camshaft_scrap_id='$cam_id' and dept='$dept'";
        $result['day_melt'] = $this->db->query($melt_q1)->result();
        $melt_q2 = "select SUM(mould.defects_sum) as total_sum_melt,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_melting_deffects mould ON cs.id=mould.camshaft_scrap_id where"
                . " MONTH(cs.c_date)='$months' AND YEAR(cs.c_date)='$year' and cs.dept='$dept' and mould.defect_name!='' and cs.item_type='D' group by mould.defect_name "
                . "order by mould.id ASC";
        //echo $melt_q2;
        $result['cumm_melt'] = $this->db->query($melt_q2)->result();
        //END MELT
        // FELT
        $felt_q1 = "select defect_name,defects_sum from tbl_cam_fettling_deffects where camshaft_scrap_id='$cam_id' and dept='$dept'";
        $result['day_felt'] = $this->db->query($felt_q1)->result();
        $felt_q2 = "select SUM(mould.defects_sum) as total_sum_felt,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_fettling_deffects mould ON cs.id=mould.camshaft_scrap_id where"
                . " MONTH(cs.c_date)='$months' AND YEAR(cs.c_date)='$year' and cs.dept='$dept' and mould.defect_name!='' and cs.item_type='D' group by mould.defect_name "
                . "order by mould.id ASC";
        $result['cumm_felt'] = $this->db->query($felt_q2)->result();
        //END FELT
        // PATTERN
        $pattern_q1 = "select defect_name,defects_sum from tbl_cam_pattern_deffects where camshaft_scrap_id='$cam_id' and dept='$dept'";
        $result['day_pattern'] = $this->db->query($pattern_q1)->result();
        $pattern_q2 = "select SUM(mould.defects_sum) as total_sum_pattern,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_pattern_deffects mould ON cs.id=mould.camshaft_scrap_id where"
                . " MONTH(cs.c_date)='$months' AND YEAR(cs.c_date)='$year' and cs.dept='$dept' and mould.defect_name!='' and cs.item_type='D' group by mould.defect_name "
                . "order by mould.id ASC";
        $result['cumm_pattern'] = $this->db->query($pattern_q2)->result();
 }
 }
        $this->load->view('pages/get_pareto_analysis_data', $result);
    }




    public function download_pareto_report($months,$year,$item) {

        if($item!="ALL_R" && $item!="ALL_D")
        {
        $dept=$this->session->userdata('dept');
        $query = "select * from tbl_camshaft_scrap where item='$item' and MONTH(c_date)='$months' AND YEAR(c_date)='$year' and dept='$dept'";
        $result['data'] = $id_data = $this->db->query($query)->row();
        $result['name']=$id_data->cust_name;
        $result['file_nm']="Item_0".$id_data->item;
        $cam_id = $id_data->id;
        $result['m'] = $months;
        $result['y'] = $year;
        $result['item'] = $item;

        $query1 = "select checked.total_checked,good.total_good,rej.total_rej,fr_cut.total_fr_cut,met_rej.total_met_rej from"
                . "(select SUM(total_checked) as total_checked from tbl_camshaft_scrap where item ='$item' and MONTH(c_date)='$months' AND YEAR(c_date)='$year' and dept='$dept') as checked,"
                . "(select SUM(total_good) as total_good from tbl_camshaft_scrap where item ='$item' and MONTH(c_date)='$months' AND YEAR(c_date)='$year' and dept='$dept') as good,"
                . "(select SUM(total_rej) as total_rej from tbl_camshaft_scrap where item ='$item' and MONTH(c_date)='$months' AND YEAR(c_date)='$year' and dept='$dept') as rej,"
                . "(select SUM(fr_cut) as total_fr_cut from tbl_camshaft_scrap where item ='$item' and MONTH(c_date)='$months' AND YEAR(c_date)='$year' and dept='$dept') as fr_cut,"
                . "(select SUM(met_rej) as total_met_rej from tbl_camshaft_scrap where item ='$item' and MONTH(c_date)='$months' AND YEAR(c_date)='$year' and dept='$dept') as met_rej";
        $result['total_all'] = $id_data = $this->db->query($query1)->row();

        // MOULD
        $mould_q1 = "select defect_name,defects_sum from tbl_cam_moulding_deffects where camshaft_scrap_id='$cam_id' and dept='$dept'";
        $result['day_mould'] = $this->db->query($mould_q1)->result();
        $mould_q2 = "select SUM(mould.defects_sum) as total_sum_mould,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_moulding_deffects mould ON cs.id=mould.camshaft_scrap_id where cs.item='$item' "
                . "and MONTH(cs.c_date)='$months' AND YEAR(cs.c_date)='$year' and cs.dept='$dept' and mould.defect_name!='' group by mould.defect_name "
                . "order by mould.id ASC";
        //echo $mould_q2;
        $result['cumm_mould'] = $this->db->query($mould_q2)->result();
        //END MOULD
        // MELT
        $melt_q1 = "select defect_name,defects_sum from tbl_cam_melting_deffects where camshaft_scrap_id='$cam_id' and dept='$dept'";
        $result['day_melt'] = $this->db->query($melt_q1)->result();
        $melt_q2 = "select SUM(mould.defects_sum) as total_sum_melt,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_melting_deffects mould ON cs.id=mould.camshaft_scrap_id where cs.item='$item' "
                . "and MONTH(cs.c_date)='$months' AND YEAR(cs.c_date)='$year' and cs.dept='$dept' and mould.defect_name!='' group by mould.defect_name "
                . "order by mould.id ASC";
        //echo $melt_q2;
        $result['cumm_melt'] = $this->db->query($melt_q2)->result();
        //END MELT
        // FELT
        $felt_q1 = "select defect_name,defects_sum from tbl_cam_fettling_deffects where camshaft_scrap_id='$cam_id' and dept='$dept'";
        $result['day_felt'] = $this->db->query($felt_q1)->result();
        $felt_q2 = "select SUM(mould.defects_sum) as total_sum_felt,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_fettling_deffects mould ON cs.id=mould.camshaft_scrap_id where cs.item='$item' "
                . "and MONTH(cs.c_date)='$months' AND YEAR(cs.c_date)='$year' and cs.dept='$dept' and mould.defect_name!='' group by mould.defect_name "
                . "order by mould.id ASC";
        $result['cumm_felt'] = $this->db->query($felt_q2)->result();
        //END FELT
        // PATTERN
        $pattern_q1 = "select defect_name,defects_sum from tbl_cam_pattern_deffects where camshaft_scrap_id='$cam_id' and dept='$dept'";
        $result['day_pattern'] = $this->db->query($pattern_q1)->result();
        $pattern_q2 = "select SUM(mould.defects_sum) as total_sum_pattern,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_pattern_deffects mould ON cs.id=mould.camshaft_scrap_id where cs.item='$item' "
                . "and MONTH(cs.c_date)='$months' AND YEAR(cs.c_date)='$year' and cs.dept='$dept' and mould.defect_name!='' group by mould.defect_name "
                . "order by mould.id ASC";
        //echo $pattern_q2;
        $result['cumm_pattern'] = $this->db->query($pattern_q2)->result();
        }

        else if($item=="ALL_R") {
        $result['name']="ALL REGULAR ITEM";
        $result['file_nm']="ALL REGULAR ITEM";
        $result['m'] = $months;
        $result['y'] = $year;
        $dept=$this->session->userdata('dept');
        $query = "select * from tbl_camshaft_scrap where MONTH(c_date)='$months' AND YEAR(c_date)='$year' and dept='$dept' and item_type='R'";
        $result['data'] = $id_data = $this->db->query($query)->row();

        $cam_id = $id_data->id;
        $result['m'] = $months;
        $result['y'] = $year;
        $result['item'] = $item;
        $result['s_date'] = $s_date;
        $query1 = "select checked.total_checked,good.total_good,rej.total_rej,fr_cut.total_fr_cut,met_rej.total_met_rej from"
                . "(select SUM(total_checked) as total_checked from tbl_camshaft_scrap where  MONTH(c_date)='$months' AND YEAR(c_date)='$year' and dept='$dept' and item_type='R') as checked,"
                . "(select SUM(total_good) as total_good from tbl_camshaft_scrap where MONTH(c_date)='$months' AND YEAR(c_date)='$year' and dept='$dept' and item_type='R') as good,"
                . "(select SUM(total_rej) as total_rej from tbl_camshaft_scrap where  MONTH(c_date)='$months' AND YEAR(c_date)='$year' and dept='$dept' and item_type='R') as rej,"
                . "(select SUM(fr_cut) as total_fr_cut from tbl_camshaft_scrap where MONTH(c_date)='$months' AND YEAR(c_date)='$year' and dept='$dept' and item_type='R') as fr_cut,"
                . "(select SUM(met_rej) as total_met_rej from tbl_camshaft_scrap where MONTH(c_date)='$months' AND YEAR(c_date)='$year' and dept='$dept' and item_type='R') as met_rej";
        $result['total_all'] = $id_data = $this->db->query($query1)->row();

        // MOULD
        $mould_q1 = "select defect_name,defects_sum from tbl_cam_moulding_deffects where camshaft_scrap_id='$cam_id' and dept='$dept'";
        $result['day_mould'] = $this->db->query($mould_q1)->result();
        $mould_q2 = "select SUM(mould.defects_sum) as total_sum_mould,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_moulding_deffects mould ON cs.id=mould.camshaft_scrap_id where"
                . " MONTH(cs.c_date)='$months' AND YEAR(cs.c_date)='$year' and cs.dept='$dept' and mould.defect_name!='' and cs.item_type='R'  group by mould.defect_name "
                . "order by mould.id ASC";

        $result['cumm_mould'] = $this->db->query($mould_q2)->result();
        //END MOULD
        // MELT
        $melt_q1 = "select defect_name,defects_sum from tbl_cam_melting_deffects where camshaft_scrap_id='$cam_id' and dept='$dept'";
        $result['day_melt'] = $this->db->query($melt_q1)->result();
        $melt_q2 = "select SUM(mould.defects_sum) as total_sum_melt,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_melting_deffects mould ON cs.id=mould.camshaft_scrap_id where"
                . " MONTH(cs.c_date)='$months' AND YEAR(cs.c_date)='$year' and cs.dept='$dept' and mould.defect_name!='' and cs.item_type='R' group by mould.defect_name "
                . "order by mould.id ASC";
        //echo $melt_q2;
        $result['cumm_melt'] = $this->db->query($melt_q2)->result();
        //END MELT
        // FELT
        $felt_q1 = "select defect_name,defects_sum from tbl_cam_fettling_deffects where camshaft_scrap_id='$cam_id' and dept='$dept'";
        $result['day_felt'] = $this->db->query($felt_q1)->result();
        $felt_q2 = "select SUM(mould.defects_sum) as total_sum_felt,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_fettling_deffects mould ON cs.id=mould.camshaft_scrap_id where"
                . " MONTH(cs.c_date)='$months' AND YEAR(cs.c_date)='$year' and cs.dept='$dept' and mould.defect_name!='' and cs.item_type='R' group by mould.defect_name "
                . "order by mould.id ASC";
        $result['cumm_felt'] = $this->db->query($felt_q2)->result();
        //END FELT
        // PATTERN
        $pattern_q1 = "select defect_name,defects_sum from tbl_cam_pattern_deffects where camshaft_scrap_id='$cam_id' and dept='$dept'";
        $result['day_pattern'] = $this->db->query($pattern_q1)->result();
        $pattern_q2 = "select SUM(mould.defects_sum) as total_sum_pattern,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_pattern_deffects mould ON cs.id=mould.camshaft_scrap_id where"
                . " MONTH(cs.c_date)='$months' AND YEAR(cs.c_date)='$year' and cs.dept='$dept' and mould.defect_name!='' and cs.item_type='R' group by mould.defect_name "
                . "order by mould.id ASC";
        //echo $pattern_q2;
        $result['cumm_pattern'] = $this->db->query($pattern_q2)->result();
 }

        else if($item=="ALL_D") {
        $result['name']="ALL DEVELOPMENT ITEM";
        $result['file_nm']="ALL DEVELOPMENT ITEM";
        $result['m'] = $months;
        $result['y'] = $year;
        $dept=$this->session->userdata('dept');
        $query = "select * from tbl_camshaft_scrap where MONTH(c_date)='$months' AND YEAR(c_date)='$year' and dept='$dept' and item_type='D'";
        $result['data'] = $id_data = $this->db->query($query)->row();

        $cam_id = $id_data->id;
        $result['m'] = $months;
        $result['y'] = $year;
        $result['item'] = $item;
        $result['s_date'] = $s_date;
        $query1 = "select checked.total_checked,good.total_good,rej.total_rej,fr_cut.total_fr_cut,met_rej.total_met_rej from"
                . "(select SUM(total_checked) as total_checked from tbl_camshaft_scrap where  MONTH(c_date)='$months' AND YEAR(c_date)='$year' and dept='$dept' and item_type='D') as checked,"
                . "(select SUM(total_good) as total_good from tbl_camshaft_scrap where MONTH(c_date)='$months' AND YEAR(c_date)='$year' and dept='$dept' and item_type='D') as good,"
                . "(select SUM(total_rej) as total_rej from tbl_camshaft_scrap where  MONTH(c_date)='$months' AND YEAR(c_date)='$year' and dept='$dept' and item_type='D') as rej,"
                . "(select SUM(fr_cut) as total_fr_cut from tbl_camshaft_scrap where MONTH(c_date)='$months' AND YEAR(c_date)='$year' and dept='$dept' and item_type='D') as fr_cut,"
                . "(select SUM(met_rej) as total_met_rej from tbl_camshaft_scrap where MONTH(c_date)='$months' AND YEAR(c_date)='$year' and dept='$dept' and item_type='D') as met_rej";
        $result['total_all'] = $id_data = $this->db->query($query1)->row();

        // MOULD
        $mould_q1 = "select defect_name,defects_sum from tbl_cam_moulding_deffects where camshaft_scrap_id='$cam_id' and dept='$dept'";
        $result['day_mould'] = $this->db->query($mould_q1)->result();
        $mould_q2 = "select SUM(mould.defects_sum) as total_sum_mould,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_moulding_deffects mould ON cs.id=mould.camshaft_scrap_id where"
                . " MONTH(cs.c_date)='$months' AND YEAR(cs.c_date)='$year' and cs.dept='$dept' and mould.defect_name!='' and cs.item_type='D' group by mould.defect_name "
                . "order by mould.id ASC";

        $result['cumm_mould'] = $this->db->query($mould_q2)->result();
        //END MOULD
        // MELT
        $melt_q1 = "select defect_name,defects_sum from tbl_cam_melting_deffects where camshaft_scrap_id='$cam_id' and dept='$dept'";
        $result['day_melt'] = $this->db->query($melt_q1)->result();
        $melt_q2 = "select SUM(mould.defects_sum) as total_sum_melt,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_melting_deffects mould ON cs.id=mould.camshaft_scrap_id where"
                . " MONTH(cs.c_date)='$months' AND YEAR(cs.c_date)='$year' and cs.dept='$dept' and mould.defect_name!='' and cs.item_type='D' group by mould.defect_name "
                . "order by mould.id ASC";
        //echo $melt_q2;
        $result['cumm_melt'] = $this->db->query($melt_q2)->result();
        //END MELT
        // FELT
        $felt_q1 = "select defect_name,defects_sum from tbl_cam_fettling_deffects where camshaft_scrap_id='$cam_id' and dept='$dept'";
        $result['day_felt'] = $this->db->query($felt_q1)->result();
        $felt_q2 = "select SUM(mould.defects_sum) as total_sum_felt,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_fettling_deffects mould ON cs.id=mould.camshaft_scrap_id where"
                . " MONTH(cs.c_date)='$months' AND YEAR(cs.c_date)='$year' and cs.dept='$dept' and mould.defect_name!='' and cs.item_type='D' group by mould.defect_name "
                . "order by mould.id ASC";
        $result['cumm_felt'] = $this->db->query($felt_q2)->result();
        //END FELT
        // PATTERN
        $pattern_q1 = "select defect_name,defects_sum from tbl_cam_pattern_deffects where camshaft_scrap_id='$cam_id' and dept='$dept'";
        $result['day_pattern'] = $this->db->query($pattern_q1)->result();
        $pattern_q2 = "select SUM(mould.defects_sum) as total_sum_pattern,mould.defect_name from tbl_camshaft_scrap cs "
                . "LEFT JOIN tbl_cam_pattern_deffects mould ON cs.id=mould.camshaft_scrap_id where"
                . " MONTH(cs.c_date)='$months' AND YEAR(cs.c_date)='$year' and cs.dept='$dept' and mould.defect_name!='' and cs.item_type='D' group by mould.defect_name "
                . "order by mould.id ASC";
        $result['cumm_pattern'] = $this->db->query($pattern_q2)->result();
 }

        $this->load->view('pages/download_pareto_report', $result);
    }




    public function get_daily_itemwise_data()
    {
        $s_date = $this->input->post('s_date');
        $dept=$this->session->userdata('dept');
        $first_date_find = strtotime(date("Y-m-d", strtotime($s_date)) . ", first day of this month");
        $first_date = date("Y-m-d", $first_date_find);
        $result['s_date'] = $s_date;
        $c_month = date("m", strtotime($s_date));
        $c_year = date("Y", strtotime($s_date));
        $c_date = date("d", strtotime($s_date));
        $c_date1 = ($c_date);
        $c_date1 = +$c_date1 - 1;

        if ($c_date1 == "0") {
            $c_date1 = "1";
        }
        if ($c_date1 <= "9") {
            $c_date1 = "0" . $c_date1;
        }
        $last_date=$c_year."-".$c_month."-".$c_date1;

        $r_all_items = '';
        $a1 = "SELECT GROUP_CONCAT(CONCAT('''', item, '''' )) as items FROM `tbl_camshaft_scrap` WHERE `c_date`='$s_date' and item_type='R' and dept='$dept' group by item order by item ASC";
        $a1_result = $this->db->query($a1)->result();
        if ($a1_result) {
            foreach ($a1_result as $row) {
                $r_all_items .= $row->items . ",";
            }
        }
        $r_all_items = rtrim($r_all_items, ",");

        $d_all_items = '';
        $a2 = "SELECT GROUP_CONCAT(CONCAT('''', item, '''' )) as items FROM `tbl_camshaft_scrap` WHERE `c_date`='$s_date' and item_type='D' and dept='$dept' group by item order by item ASC";
        $a2_result = $this->db->query($a2)->result();
        if ($a2_result) {
            foreach ($a2_result as $row) {
                $d_all_items .= $row->items . ",";
            }
        }
        $d_all_items = rtrim($d_all_items, ",");

        // REGULAR ITEM

        $query = "SELECT `item`,cust_name,SUM(`total_checked`) as total_checked,SUM(`total_rej`) as total_rej FROM `tbl_camshaft_scrap` WHERE `c_date`='$s_date' and item_type='R' and dept='$dept' group by `item` order by item ASC";
        $day_wise = $this->db->query($query)->result();
        if(empty($day_wise))
        {
        $query3 = "SELECT `item`,cust_name,CASE WHEN total_checked IS NULL THEN 0 ELSE 0 END AS total_checked ,CASE WHEN total_rej IS NULL THEN 0 ELSE 0 END AS total_rej  FROM `tbl_camshaft_scrap` WHERE `c_date`>='$first_date' and `c_date`<='$s_date' and item_type='R' and dept='$dept' group by `item` order by item ASC";
        $day_wise = $this->db->query($query3)->result();
        }
        $result['day_wise'] = $day_wise;

        $cumm_wise='';
        if ($day_wise) {
            foreach ($day_wise as $row) {

                $query2 = "SELECT `item`,SUM(`total_checked`) as total_checked,SUM(`total_rej`) as total_rej FROM `tbl_camshaft_scrap` WHERE `c_date`>='$first_date' and `c_date`<='$s_date' and item='$row->item' and item_type='R' and dept='$dept' group by `item`";
                $cumm_wise[] = $this->db->query($query2)->row();

            }
        }

        $result['cumm_wise'] = $cumm_wise;
         if($r_all_items)
        {
        $q1 = "SELECT `item`,cust_name,SUM(`total_checked`) as total_checked,SUM(`total_rej`) as total_rej FROM `tbl_camshaft_scrap` WHERE `c_date`>='$first_date' and `c_date`<='$last_date' and item NOT IN ($r_all_items) and  item_type='R' and dept='$dept' group by `item`";
        $result['cumm_wise_all'] = $this->db->query($q1)->result();
        }
        else
        {
        $result['cumm_wise_all'] ='';
        }

        // END REGULAR ITEM


        // DEVELOPMENT ITEM
        $query3 = "SELECT `item`,cust_name,SUM(`total_checked`) as total_checked,SUM(`total_rej`) as total_rej FROM `tbl_camshaft_scrap` WHERE `c_date`='$s_date' and item_type='D' and dept='$dept' group by `item` order by item ASC";
        $day_wise_d = $this->db->query($query3)->result();
        if(empty($day_wise_d))
        {
        $query3 = "SELECT `item`,cust_name,CASE WHEN total_checked IS NULL THEN 0 ELSE 0 END AS total_checked ,CASE WHEN total_rej IS NULL THEN 0 ELSE 0 END AS total_rej  FROM `tbl_camshaft_scrap` WHERE `c_date`>='$first_date' and `c_date`<='$s_date' and item_type='D' and dept='$dept' group by `item` order by item ASC";
        $day_wise_d = $this->db->query($query3)->result();
        }
        $result['day_wise_d'] = $day_wise_d;
        $cumm_wise_d='';
        if ($day_wise_d) {
            foreach ($day_wise_d as $row) {

                $query4 = "SELECT `item`,SUM(`total_checked`) as total_checked,SUM(`total_rej`) as total_rej FROM `tbl_camshaft_scrap` WHERE `c_date`>='$first_date' and `c_date`<='$s_date' and item='$row->item' and item_type='D' and dept='$dept' group by `item`";
                $cumm_wise_d[] = $this->db->query($query4)->row();
            }
        }

        $result['cumm_wise_d'] = $cumm_wise_d;

        if($d_all_items)
        {
        $q2 = "SELECT `item`,cust_name,SUM(`total_checked`) as total_checked,SUM(`total_rej`) as total_rej FROM `tbl_camshaft_scrap` WHERE `c_date`>='$first_date' and `c_date`<='$last_date' and item NOT IN ($d_all_items) and item_type='D' and dept='$dept' group by `item`";
        $result['cumm_wise_all_d'] = $this->db->query($q2)->result();
        }
        else
        {
        $result['cumm_wise_all_d'] ='';
        }

        // END DEVELOPMENT ITEM

        $this->load->view('pages/get_daily_itemwise_data', $result);

    }










    public function get_dates_wise_rejection_data()
    {
        $item = $_POST["item"];
        $s_date = $_POST["s_date"];
        $result['actual_date']=$s_date;

        if($item!="ALL_D" && $item!="ALL_R")
        {
            $dept = $this->session->userdata('dept');
            $q1="select cust_name,item from tbl_item_details where dept='$dept' and item ='$item'";
            $result['cust_name'] = $this->db->query($q1)->row();
            $query = "select *,SUM(total_checked) as total_checked,SUM(total_good) as total_good,SUM(total_rej) as total_rej,"
                    . "SUM(fr_cut) as fr_cut,"
                    . "GROUP_CONCAT(CONCAT('''', id, '''' )) as ids "
                    . "from tbl_camshaft_scrap where c_date='$s_date' and dept='$dept' and item ='$item' and item_type='R'";

            $result['data'] = $id_data = $this->db->query($query)->row();
            $d1 = '';
            $d2 = '';
            $ids = '';
            $d3 = '';
            $defect_sum = '';
            $other_report1 = '';
            $mould1 = '';
            $mould2 = '';
            $mould3 = '';
            $mould4 = '';
            $mould5 = '';
            $mould6 = '';
            $mould7 = '';
            $mould8 = '';
            $mould9 = '';
            $mould10 = '';
            $mould11 = '';
            $mould12 = '';

            $melt1 = '';
            $melt2 = '';
            $melt3 = '';
            $melt4 = '';
            $melt5 = '';
            $melt6 = '';
            $melt7 = '';
            $melt8 = '';


            $felt1 = '';
            $felt2 = '';
            $felt3 = '';
            $felt4 = '';

            $pattern1 = '';
            $pattern2 = '';
            $pattern3 = '';
            $pattern4 = '';
            $pattern5 = '';


            $a1 = date("m", strtotime($s_date));
            $a2 = date("Y", strtotime($s_date));
            $a3 = date("d", strtotime($s_date));

            $mould_name = array(
                'LOOSE SAND', 'PIN HOLE', 'GAS HOLE', 'GLUE', 'MOULD CRACK', 'CORE BROKEN', 'TIR REJECT',
                'CHILL DEFECT', 'THICK FLASH', 'MOULD LEAK', 'EXTRA METAL', 'OTHERS(MOULDING)');
            $melt_name = array(
                'HOT TEAR', 'COLD METAL', 'LC', 'SLAG', 'RIPPLE', 'SHORT POURED', 'PELTCH', 'OTHERS(MELTING & POURING)'
            );
            $felt_name = array('CHIP COLD', 'CHIP HOT', 'EXCESS FET.', 'OTHERS(KNOCKOUT & FETTLING)');
            $pattern_name = array('X-JOINT', 'DIGGING IN', 'LINEAR GAUGE REJ.', 'OTHER(SIM GAP,P/M ETC.)', 'PPAP');

            for ($i = 1; $i < $a3 + 1; $i++) {
                if ($i <= 9) {
                    $i = "0" . $i;
                }
                $d1 = "select total_checked,total_good,total_rej,met_rej,total_mould,total_pouring,total_fettling,total_pattern,c_date,fr_cut,id from tbl_camshaft_scrap "
                        . "where item ='$item' and c_date='$a2-$a1-$i' and dept='$dept' order by c_date;";
                $other_report1 = $this->db->query($d1)->row();
                $idss = @$other_report1->id;

                $result['tc'][] = @$other_report1->total_checked;
                $result['tg'][] = @$other_report1->total_good;
                $result['mr'][] = @$other_report1->met_rej;
                $result['tm'][] = @$other_report1->total_mould;
                $result['tp'][] = @$other_report1->total_pouring;
                $result['tf'][] = @$other_report1->total_fettling;
                $result['tpa'][] = @$other_report1->total_pattern;
                $result['fc'][] = @$other_report1->fr_cut;

                for ($j = 0; $j < count($mould_name); $j++) {
                    $d3 = "select defects_sum,`defect_name`,created_on from tbl_cam_moulding_deffects "
                            . "where camshaft_scrap_id='$idss' and dept='$dept' and defect_name ='$mould_name[$j]' order by created_on,id ASC;";
                    //echo $d3;
                    $other_mould = $this->db->query($d3)->row();

                    if ($mould_name[$j] == "LOOSE SAND") {
                        $mould1 .= @$other_mould->defects_sum . ",";
                    }
                    if ($mould_name[$j] == "PIN HOLE") {
                        $mould2 .= @$other_mould->defects_sum . ",";
                    }
                    if ($mould_name[$j] == "GAS HOLE") {
                        $mould3 .= @$other_mould->defects_sum . ",";
                    }
                    if ($mould_name[$j] == "GLUE") {
                        $mould4 .= @$other_mould->defects_sum . ",";
                    }
                    if ($mould_name[$j] == "MOULD CRACK") {
                        $mould5 .= @$other_mould->defects_sum . ",";
                    }
                    if ($mould_name[$j] == "CORE BROKEN") {
                        $mould6 .= @$other_mould->defects_sum . ",";
                    }
                    if ($mould_name[$j] == "TIR REJECT") {
                        $mould7 .= @$other_mould->defects_sum . ",";
                    }
                    if ($mould_name[$j] == "CHILL DEFECT") {
                        $mould8 .= @$other_mould->defects_sum . ",";
                    }
                    if ($mould_name[$j] == "THICK FLASH") {
                        $mould9 .= @$other_mould->defects_sum . ",";
                    }
                    if ($mould_name[$j] == "MOULD LEAK") {
                        $mould10 .= @$other_mould->defects_sum . ",";
                    }
                    if ($mould_name[$j] == "EXTRA METAL") {
                        $mould11 .= @$other_mould->defects_sum . ",";
                    }
                    if ($mould_name[$j] == "OTHERS(MOULDING)") {
                        $mould12 .= @$other_mould->defects_sum . ",";
                    }
                }
                for ($j = 0; $j < count($melt_name); $j++) {
                    $d4 = "select defects_sum,`defect_name`,created_on from tbl_cam_melting_deffects "
                            . "where camshaft_scrap_id='$idss' and dept='$dept' and defect_name ='$melt_name[$j]' order by created_on,id ASC;";
                    $other_melt = $this->db->query($d4)->row();

                    if ($melt_name[$j] == "HOT TEAR") {
                        $melt1 .= @$other_melt->defects_sum . ",";
                    }
                    if ($melt_name[$j] == "COLD METAL") {
                        $melt2 .= @$other_melt->defects_sum . ",";
                    }
                    if ($melt_name[$j] == "LC") {
                        $melt3 .= @$other_melt->defects_sum . ",";
                    }
                    if ($melt_name[$j] == "SLAG") {
                        $melt4 .= @$other_melt->defects_sum . ",";
                    }
                    if ($melt_name[$j] == "RIPPLE") {
                        $melt5 .= @$other_melt->defects_sum . ",";
                    }
                    if ($melt_name[$j] == "SHORT POURED") {
                        $melt6 .= @$other_melt->defects_sum . ",";
                    }
                    if ($melt_name[$j] == "PELTCH") {
                        $melt7 .= @$other_melt->defects_sum . ",";
                    }
                    if ($melt_name[$j] == "OTHERS(MELTING & POURING)") {
                        $melt8 .= @$other_melt->defects_sum . ",";
                    }
                }


                for ($j = 0; $j < count($felt_name); $j++) {
                    $d4 = "select defects_sum,`defect_name`,created_on from tbl_cam_fettling_deffects "
                            . "where camshaft_scrap_id='$idss' and dept='$dept' and defect_name ='$felt_name[$j]' order by created_on,id ASC;";
                    $other_felt = $this->db->query($d4)->row();

                    if ($felt_name[$j] == "CHIP COLD") {
                        $felt1 .= @$other_felt->defects_sum . ",";
                    }
                    if ($felt_name[$j] == "CHIP HOT") {
                        $felt2 .= @$other_felt->defects_sum . ",";
                    }
                    if ($felt_name[$j] == "EXCESS FET.") {
                        $felt3 .= @$other_felt->defects_sum . ",";
                    }
                    if ($felt_name[$j] == "OTHERS(KNOCKOUT & FETTLING)") {
                        $felt4 .= @$other_felt->defects_sum . ",";
                    }
                }

                for ($j = 0; $j < count($pattern_name); $j++) {
                    $d4 = "select defects_sum,`defect_name`,created_on from tbl_cam_pattern_deffects "
                            . "where camshaft_scrap_id='$idss' and dept='$dept' and defect_name ='$pattern_name[$j]' order by created_on,id ASC;";
                    $other_felt = $this->db->query($d4)->row();

                    if ($pattern_name[$j] == "X-JOINT") {
                        $pattern1 .= @$other_felt->defects_sum . ",";
                    }
                    if ($pattern_name[$j] == "DIGGING IN") {
                        $pattern2 .= @$other_felt->defects_sum . ",";
                    }
                    if ($pattern_name[$j] == "LINEAR GAUGE REJ.") {
                        $pattern3 .= @$other_felt->defects_sum . ",";
                    }
                    if ($pattern_name[$j] == "OTHER(SIM GAP,P/M ETC.)") {
                        $pattern4 .= @$other_felt->defects_sum . ",";
                    }
                    if ($pattern_name[$j] == "PPAP") {
                        $pattern5 .= @$other_felt->defects_sum . ",";
                    }
                }
        }
            $result['mould1'] = "LOOSE SAND," . ($mould1);
            $result['mould2'] = "PIN HOLE," . ($mould2);
            $result['mould3'] = "GAS HOLE," . ($mould3);
            $result['mould4'] = "GLUE," . ($mould4);
            $result['mould5'] = "MOULD CRACK," . ($mould5);
            $result['mould6'] = "CORE BROKEN," . ($mould6);
            $result['mould7'] = "TIR REJECT," . ($mould7);
            $result['mould8'] = "CHILL DEFECT," . ($mould8);
            $result['mould9'] = "THICK FLASH," . ($mould9);
            $result['mould10'] = "MOULD LEAK," . ($mould10);
            $result['mould11'] = "EXTRA METAL," . ($mould11);
            $result['mould12'] = "OTHERS(MOULDING)," . ($mould12);

            $result['melt1'] = "HOT TEAR," . ($melt1);
            $result['melt2'] = "COLD METAL," . ($melt2);
            $result['melt3'] = "LC," . ($melt3);
            $result['melt4'] = "SLAG," . ($melt4);
            $result['melt5'] = "RIPPLE," . ($melt5);
            $result['melt6'] = "SHORT POURED," . ($melt6);
            $result['melt7'] = "PELTCH," . ($melt7);
            $result['melt8'] = "OTHERS(MELTING & POURING)," . ($melt8);

            $result['felt1'] = "CHIP COLD," . ($felt1);
            $result['felt2'] = "CHIP HOT," . ($felt2);
            $result['felt3'] = "EXCESS FET.," . ($felt3);
            $result['felt4'] = "OTHERS(KNOCKOUT & FETTLING)," . ($felt4);


            $result['pattern1'] = "X-JOINT," . ($pattern1);
            $result['pattern2'] = "DIGGING IN," . ($pattern2);
            $result['pattern3'] = "LINEAR GAUGE REJ.," . ($pattern3);
            $result['pattern4'] = "OTHER(SIM GAP-P/M ETC.)," . ($pattern4);
            $result['pattern5'] = "PPAP," . ($pattern5);

$this->load->view('pages/get_dates_wise_rejection_data', $result);

     }
        // ALL ITEM
        else if($item=="ALL_D"){
            $result['All_item'] = "ALL DEVELOPMENT ITEM";
            $dept = $this->session->userdata('dept');
            $query = "select *,SUM(total_checked) as total_checked,SUM(total_good) as total_good,SUM(total_rej) as total_rej,"
                    . "SUM(fr_cut) as fr_cut,"
                    . "GROUP_CONCAT(CONCAT('''', id, '''' )) as ids "
                    . "from tbl_camshaft_scrap where  c_date='$s_date' and dept='$dept' and item_type='R'";
            $result['data'] = $id_data = $this->db->query($query)->row();

            $d1 = '';
            $d2 = '';
            $ids = '';
            $d3 = '';
            $defect_sum = '';
            $other_report1 = '';
            $mould1 = '';
            $mould2 = '';
            $mould3 = '';
            $mould4 = '';
            $mould5 = '';
            $mould6 = '';
            $mould7 = '';
            $mould8 = '';
            $mould9 = '';
            $mould10 = '';
            $mould11 = '';
            $mould12 = '';

            $melt1 = '';
            $melt2 = '';
            $melt3 = '';
            $melt4 = '';
            $melt5 = '';
            $melt6 = '';
            $melt7 = '';
            $melt8 = '';


            $felt1 = '';
            $felt2 = '';
            $felt3 = '';
            $felt4 = '';

            $pattern1 = '';
            $pattern2 = '';
            $pattern3 = '';
            $pattern4 = '';
            $pattern5 = '';


            $a1 = date("m", strtotime($s_date));
            $a2 = date("Y", strtotime($s_date));
            $a3 = date("d", strtotime($s_date));

            $mould_name = array(
                'LOOSE SAND', 'PIN HOLE', 'GAS HOLE', 'GLUE', 'MOULD CRACK', 'CORE BROKEN', 'TIR REJECT',
                'CHILL DEFECT', 'THICK FLASH', 'MOULD LEAK', 'EXTRA METAL', 'OTHERS(MOULDING)');
            $melt_name = array(
                'HOT TEAR', 'COLD METAL', 'LC', 'SLAG', 'RIPPLE', 'SHORT POURED', 'PELTCH', 'OTHERS(MELTING & POURING)'
            );
            $felt_name = array('CHIP COLD', 'CHIP HOT', 'EXCESS FET.', 'OTHERS(KNOCKOUT & FETTLING)');
            $pattern_name = array('X-JOINT', 'DIGGING IN', 'LINEAR GAUGE REJ.', 'OTHER(SIM GAP,P/M ETC.)', 'PPAP');
            $aba='';

            for ($i = 1; $i < $a3 + 1; $i++) {
                if ($i <= 9) {
                    $i = "0" . $i;
                }
                $d1 = "select SUM(total_checked) as total_checked,SUM(total_good) as total_good,SUM(total_rej) as total_rej,SUM(met_rej) as met_rej,"
                        . "SUM(total_mould) as total_mould,SUM(total_pouring) as total_pouring,SUM(total_fettling) as total_fettling,"
                        . "SUM(total_pattern) as total_pattern,c_date,SUM(fr_cut) as fr_cut,GROUP_CONCAT(CONCAT('''', id, '''' )) as ids,id from tbl_camshaft_scrap "
                        . "where c_date='$a2-$a1-$i' and dept='$dept' and item_type='D' order by c_date;";
                $other_report1 = $this->db->query($d1)->row();
                $ids=$other_report1->ids;
                if($ids)
                {
              $aba="and camshaft_scrap_id IN ($ids)";
                }else {
                $aba="and camshaft_scrap_id=''";
                }

        $result['tc'][]=@$other_report1->total_checked;
        $result['tg'][]=@$other_report1->total_good;
        $result['mr'][]=@$other_report1->met_rej;
        $result['tm'][]=@$other_report1->total_mould;
        $result['tp'][]=@$other_report1->total_pouring;
        $result['tf'][]=@$other_report1->total_fettling;
        $result['tpa'][]=@$other_report1->total_pattern;
        $result['fc'][]=@$other_report1->fr_cut;

        for ($j = 0; $j < count($mould_name); $j++) {
                    $d3= "select SUM(defects_sum) as defects_sum from tbl_cam_moulding_deffects
                            where  dept='$dept' and created_on='$a2-$a1-$i' and defect_name ='$mould_name[$j]' $aba group by created_on order by created_on,id ASC;";

                    $other_mould = $this->db->query($d3)->row();

                    if ($mould_name[$j] == "LOOSE SAND") {
                        $mould1 .= @$other_mould->defects_sum . ",";
                    }
                    if ($mould_name[$j] == "PIN HOLE") {
                        $mould2 .= @$other_mould->defects_sum . ",";
                    }
                    if ($mould_name[$j] == "GAS HOLE") {
                        $mould3 .= @$other_mould->defects_sum . ",";
                    }
                    if ($mould_name[$j] == "GLUE") {
                        $mould4 .= @$other_mould->defects_sum . ",";
                    }
                    if ($mould_name[$j] == "MOULD CRACK") {
                        $mould5 .= @$other_mould->defects_sum . ",";
                    }
                    if ($mould_name[$j] == "CORE BROKEN") {
                        $mould6 .= @$other_mould->defects_sum . ",";
                    }
                    if ($mould_name[$j] == "TIR REJECT") {
                        $mould7 .= @$other_mould->defects_sum . ",";
                    }
                    if ($mould_name[$j] == "CHILL DEFECT") {
                        $mould8 .= @$other_mould->defects_sum . ",";
                    }
                    if ($mould_name[$j] == "THICK FLASH") {
                        $mould9 .= @$other_mould->defects_sum . ",";
                    }
                    if ($mould_name[$j] == "MOULD LEAK") {
                        $mould10 .= @$other_mould->defects_sum . ",";
                    }
                    if ($mould_name[$j] == "EXTRA METAL") {
                        $mould11 .= @$other_mould->defects_sum . ",";
                    }
                    if ($mould_name[$j] == "OTHERS(MOULDING)") {
                        $mould12 .= @$other_mould->defects_sum . ",";
                    }
                }
                for ($j = 0; $j < count($melt_name); $j++) {
                    $d4 = "select SUM(defects_sum) as defects_sum from tbl_cam_melting_deffects "
                            . "where  dept='$dept' and created_on='$a2-$a1-$i' and defect_name ='$melt_name[$j]' $aba  group by created_on order by created_on,id ASC;";
                    $other_melt = $this->db->query($d4)->row();

                    if ($melt_name[$j] == "HOT TEAR") {
                        $melt1 .= @$other_melt->defects_sum . ",";
                    }
                    if ($melt_name[$j] == "COLD METAL") {
                        $melt2 .= @$other_melt->defects_sum . ",";
                    }
                    if ($melt_name[$j] == "LC") {
                        $melt3 .= @$other_melt->defects_sum . ",";
                    }
                    if ($melt_name[$j] == "SLAG") {
                        $melt4 .= @$other_melt->defects_sum . ",";
                    }
                    if ($melt_name[$j] == "RIPPLE") {
                        $melt5 .= @$other_melt->defects_sum . ",";
                    }
                    if ($melt_name[$j] == "SHORT POURED") {
                        $melt6 .= @$other_melt->defects_sum . ",";
                    }
                    if ($melt_name[$j] == "PELTCH") {
                        $melt7 .= @$other_melt->defects_sum . ",";
                    }
                    if ($melt_name[$j] == "OTHERS(MELTING & POURING)") {
                        $melt8 .= @$other_melt->defects_sum . ",";
                    }
                }


                for ($j = 0; $j < count($felt_name); $j++) {
                    $d4 = "select SUM(defects_sum) as defects_sum from tbl_cam_fettling_deffects "
                            . "where dept='$dept' and created_on='$a2-$a1-$i' and defect_name ='$felt_name[$j]' $aba  group by created_on order by created_on,id ASC;";
                    $other_felt = $this->db->query($d4)->row();

                    if ($felt_name[$j] == "CHIP COLD") {
                        $felt1 .= @$other_felt->defects_sum . ",";
                    }
                    if ($felt_name[$j] == "CHIP HOT") {
                        $felt2 .= @$other_felt->defects_sum . ",";
                    }
                    if ($felt_name[$j] == "EXCESS FET.") {
                        $felt3 .= @$other_felt->defects_sum . ",";
                    }
                    if ($felt_name[$j] == "OTHERS(KNOCKOUT & FETTLING)") {
                        $felt4 .= @$other_felt->defects_sum . ",";
                    }
                }

                for ($j = 0; $j < count($pattern_name); $j++) {
                    $d4 = "select SUM(defects_sum) as defects_sum from tbl_cam_pattern_deffects "
                            . "where dept='$dept' and created_on='$a2-$a1-$i' and defect_name ='$pattern_name[$j]' $aba group by created_on order by created_on,id ASC;";
                    $other_felt = $this->db->query($d4)->row();

                    if ($pattern_name[$j] == "X-JOINT") {
                        $pattern1 .= @$other_felt->defects_sum . ",";
                    }
                    if ($pattern_name[$j] == "DIGGING IN") {
                        $pattern2 .= @$other_felt->defects_sum . ",";
                    }
                    if ($pattern_name[$j] == "LINEAR GAUGE REJ.") {
                        $pattern3 .= @$other_felt->defects_sum . ",";
                    }
                    if ($pattern_name[$j] == "OTHER(SIM GAP,P/M ETC.)") {
                        $pattern4 .= @$other_felt->defects_sum . ",";
                    }
                    if ($pattern_name[$j] == "PPAP") {
                        $pattern5 .= @$other_felt->defects_sum . ",";
                    }
                }
            }

            $result['mould1'] = "LOOSE SAND," . ($mould1);
            $result['mould2'] = "PIN HOLE," . ($mould2);
            $result['mould3'] = "GAS HOLE," . ($mould3);
            $result['mould4'] = "GLUE," . ($mould4);
            $result['mould5'] = "MOULD CRACK," . ($mould5);
            $result['mould6'] = "CORE BROKEN," . ($mould6);
            $result['mould7'] = "TIR REJECT," . ($mould7);
            $result['mould8'] = "CHILL DEFECT," . ($mould8);
            $result['mould9'] = "THICK FLASH," . ($mould9);
            $result['mould10'] = "MOULD LEAK," . ($mould10);
            $result['mould11'] = "EXTRA METAL," . ($mould11);
            $result['mould12'] = "OTHERS(MOULDING)," . ($mould12);

            $result['melt1'] = "HOT TEAR," . ($melt1);
            $result['melt2'] = "COLD METAL," . ($melt2);
            $result['melt3'] = "LC," . ($melt3);
            $result['melt4'] = "SLAG," . ($melt4);
            $result['melt5'] = "RIPPLE," . ($melt5);
            $result['melt6'] = "SHORT POURED," . ($melt6);
            $result['melt7'] = "PELTCH," . ($melt7);
            $result['melt8'] = "OTHERS(MELTING & POURING)," . ($melt8);

            $result['felt1'] = "CHIP COLD," . ($felt1);
            $result['felt2'] = "CHIP HOT," . ($felt2);
            $result['felt3'] = "EXCESS FET.," . ($felt3);
            $result['felt4'] = "OTHERS(KNOCKOUT & FETTLING)," . ($felt4);


            $result['pattern1'] = "X-JOINT," . ($pattern1);
            $result['pattern2'] = "DIGGING IN," . ($pattern2);
            $result['pattern3'] = "LINEAR GAUGE REJ.," . ($pattern3);
            $result['pattern4'] = "OTHER(SIM GAP-P/M ETC.)," . ($pattern4);
            $result['pattern5'] = "PPAP," . ($pattern5);

        $this->load->view('pages/get_dates_wise_rejection_data', $result);
        }

        else if($item=="ALL_R"){
            $result['All_item'] = "ALL REGULAR ITEM";
            $dept = $this->session->userdata('dept');
            $query = "select *,SUM(total_checked) as total_checked,SUM(total_good) as total_good,SUM(total_rej) as total_rej,"
                    . "SUM(fr_cut) as fr_cut,"
                    . "GROUP_CONCAT(CONCAT('''', id, '''' )) as ids "
                    . "from tbl_camshaft_scrap where  c_date='$s_date' and dept='$dept' and item_type='R'";
            $result['data'] = $id_data = $this->db->query($query)->row();
            $d1 = '';
            $d2 = '';
            $ids = '';
            $d3 = '';
            $defect_sum = '';
            $other_report1 = '';
            $mould1 = '';
            $mould2 = '';
            $mould3 = '';
            $mould4 = '';
            $mould5 = '';
            $mould6 = '';
            $mould7 = '';
            $mould8 = '';
            $mould9 = '';
            $mould10 = '';
            $mould11 = '';
            $mould12 = '';

            $melt1 = '';
            $melt2 = '';
            $melt3 = '';
            $melt4 = '';
            $melt5 = '';
            $melt6 = '';
            $melt7 = '';
            $melt8 = '';


            $felt1 = '';
            $felt2 = '';
            $felt3 = '';
            $felt4 = '';

            $pattern1 = '';
            $pattern2 = '';
            $pattern3 = '';
            $pattern4 = '';
            $pattern5 = '';


            $a1 = date("m", strtotime($s_date));
            $a2 = date("Y", strtotime($s_date));
            $a3 = date("d", strtotime($s_date));

            $mould_name = array(
                'LOOSE SAND', 'PIN HOLE', 'GAS HOLE', 'GLUE', 'MOULD CRACK', 'CORE BROKEN', 'TIR REJECT',
                'CHILL DEFECT', 'THICK FLASH', 'MOULD LEAK', 'EXTRA METAL', 'OTHERS(MOULDING)');
            $melt_name = array(
                'HOT TEAR', 'COLD METAL', 'LC', 'SLAG', 'RIPPLE', 'SHORT POURED', 'PELTCH', 'OTHERS(MELTING & POURING)'
            );
            $felt_name = array('CHIP COLD', 'CHIP HOT', 'EXCESS FET.', 'OTHERS(KNOCKOUT & FETTLING)');
            $pattern_name = array('X-JOINT', 'DIGGING IN', 'LINEAR GAUGE REJ.', 'OTHER(SIM GAP,P/M ETC.)', 'PPAP');

            $aba='';

            for ($i = 1; $i < $a3 + 1; $i++) {
                if ($i <= 9) {
                    $i = "0" . $i;
                }
                $d1 = "select SUM(total_checked) as total_checked,SUM(total_good) as total_good,SUM(total_rej) as total_rej,SUM(met_rej) as met_rej,"
                        . "SUM(total_mould) as total_mould,SUM(total_pouring) as total_pouring,SUM(total_fettling) as total_fettling,"
                        . "SUM(total_pattern) as total_pattern,c_date,SUM(fr_cut) as fr_cut,id,GROUP_CONCAT(CONCAT('''', id, '''' )) as ids from tbl_camshaft_scrap "
                        . "where c_date='$a2-$a1-$i' and dept='$dept' and item_type='R' order by c_date;";
                $other_report1 = $this->db->query($d1)->row();
                $ids = $other_report1->ids;
                if ($ids) {
                    $aba = "and camshaft_scrap_id IN ($ids)";
                } else {
                $aba="and camshaft_scrap_id=''";
                }
                $result['tc'][] = @$other_report1->total_checked;
                $result['tg'][] = @$other_report1->total_good;
                $result['mr'][] = @$other_report1->met_rej;
                $result['tm'][] = @$other_report1->total_mould;
                $result['tp'][] = @$other_report1->total_pouring;
                $result['tf'][] = @$other_report1->total_fettling;
                $result['tpa'][] = @$other_report1->total_pattern;
                $result['fc'][] = @$other_report1->fr_cut;

                for ($j = 0; $j < count($mould_name); $j++) {
                    $d3= "select SUM(defects_sum) as defects_sum from tbl_cam_moulding_deffects "
                            . "where  dept='$dept' and created_on='$a2-$a1-$i' and defect_name ='$mould_name[$j]' $aba  group by created_on order by created_on,id ASC;";
                    $other_mould = $this->db->query($d3)->row();

                    if ($mould_name[$j] == "LOOSE SAND") {
                        $mould1 .= @$other_mould->defects_sum . ",";
                    }
                    if ($mould_name[$j] == "PIN HOLE") {
                        $mould2 .= @$other_mould->defects_sum . ",";
                    }
                    if ($mould_name[$j] == "GAS HOLE") {
                        $mould3 .= @$other_mould->defects_sum . ",";
                    }
                    if ($mould_name[$j] == "GLUE") {
                        $mould4 .= @$other_mould->defects_sum . ",";
                    }
                    if ($mould_name[$j] == "MOULD CRACK") {
                        $mould5 .= @$other_mould->defects_sum . ",";
                    }
                    if ($mould_name[$j] == "CORE BROKEN") {
                        $mould6 .= @$other_mould->defects_sum . ",";
                    }
                    if ($mould_name[$j] == "TIR REJECT") {
                        $mould7 .= @$other_mould->defects_sum . ",";
                    }
                    if ($mould_name[$j] == "CHILL DEFECT") {
                        $mould8 .= @$other_mould->defects_sum . ",";
                    }
                    if ($mould_name[$j] == "THICK FLASH") {
                        $mould9 .= @$other_mould->defects_sum . ",";
                    }
                    if ($mould_name[$j] == "MOULD LEAK") {
                        $mould10 .= @$other_mould->defects_sum . ",";
                    }
                    if ($mould_name[$j] == "EXTRA METAL") {
                        $mould11 .= @$other_mould->defects_sum . ",";
                    }
                    if ($mould_name[$j] == "OTHERS(MOULDING)") {
                        $mould12 .= @$other_mould->defects_sum . ",";
                    }
                }
                for ($j = 0; $j < count($melt_name); $j++) {
                    $d4 = "select SUM(defects_sum) as defects_sum from tbl_cam_melting_deffects "
                            . "where  dept='$dept' and created_on='$a2-$a1-$i' and defect_name ='$melt_name[$j]' $aba  group by created_on order by created_on,id ASC;";
                    $other_melt = $this->db->query($d4)->row();

                    if ($melt_name[$j] == "HOT TEAR") {
                        $melt1 .= @$other_melt->defects_sum . ",";
                    }
                    if ($melt_name[$j] == "COLD METAL") {
                        $melt2 .= @$other_melt->defects_sum . ",";
                    }
                    if ($melt_name[$j] == "LC") {
                        $melt3 .= @$other_melt->defects_sum . ",";
                    }
                    if ($melt_name[$j] == "SLAG") {
                        $melt4 .= @$other_melt->defects_sum . ",";
                    }
                    if ($melt_name[$j] == "RIPPLE") {
                        $melt5 .= @$other_melt->defects_sum . ",";
                    }
                    if ($melt_name[$j] == "SHORT POURED") {
                        $melt6 .= @$other_melt->defects_sum . ",";
                    }
                    if ($melt_name[$j] == "PELTCH") {
                        $melt7 .= @$other_melt->defects_sum . ",";
                    }
                    if ($melt_name[$j] == "OTHERS(MELTING & POURING)") {
                        $melt8 .= @$other_melt->defects_sum . ",";
                    }
                }


                for ($j = 0; $j < count($felt_name); $j++) {
                    $d4 = "select SUM(defects_sum) as defects_sum from tbl_cam_fettling_deffects "
                            . "where dept='$dept' and created_on='$a2-$a1-$i' and defect_name ='$felt_name[$j]' $aba  group by created_on order by created_on,id ASC;";
                    $other_felt = $this->db->query($d4)->row();

                    if ($felt_name[$j] == "CHIP COLD") {
                        $felt1 .= @$other_felt->defects_sum . ",";
                    }
                    if ($felt_name[$j] == "CHIP HOT") {
                        $felt2 .= @$other_felt->defects_sum . ",";
                    }
                    if ($felt_name[$j] == "EXCESS FET.") {
                        $felt3 .= @$other_felt->defects_sum . ",";
                    }
                    if ($felt_name[$j] == "OTHERS(KNOCKOUT & FETTLING)") {
                        $felt4 .= @$other_felt->defects_sum . ",";
                    }
                }

                for ($j = 0; $j < count($pattern_name); $j++) {
                    $d4 = "select SUM(defects_sum) as defects_sum from tbl_cam_pattern_deffects "
                            . "where dept='$dept' and created_on='$a2-$a1-$i' and defect_name ='$pattern_name[$j]' $aba  group by created_on order by created_on,id ASC;";
                    $other_felt = $this->db->query($d4)->row();

                    if ($pattern_name[$j] == "X-JOINT") {
                        $pattern1 .= @$other_felt->defects_sum . ",";
                    }
                    if ($pattern_name[$j] == "DIGGING IN") {
                        $pattern2 .= @$other_felt->defects_sum . ",";
                    }
                    if ($pattern_name[$j] == "LINEAR GAUGE REJ.") {
                        $pattern3 .= @$other_felt->defects_sum . ",";
                    }
                    if ($pattern_name[$j] == "OTHER(SIM GAP,P/M ETC.)") {
                        $pattern4 .= @$other_felt->defects_sum . ",";
                    }
                    if ($pattern_name[$j] == "PPAP") {
                        $pattern5 .= @$other_felt->defects_sum . ",";
                    }
                }
            }

            $result['mould1'] = "LOOSE SAND," . ($mould1);
            $result['mould2'] = "PIN HOLE," . ($mould2);
            $result['mould3'] = "GAS HOLE," . ($mould3);
            $result['mould4'] = "GLUE," . ($mould4);
            $result['mould5'] = "MOULD CRACK," . ($mould5);
            $result['mould6'] = "CORE BROKEN," . ($mould6);
            $result['mould7'] = "TIR REJECT," . ($mould7);
            $result['mould8'] = "CHILL DEFECT," . ($mould8);
            $result['mould9'] = "THICK FLASH," . ($mould9);
            $result['mould10'] = "MOULD LEAK," . ($mould10);
            $result['mould11'] = "EXTRA METAL," . ($mould11);
            $result['mould12'] = "OTHERS(MOULDING)," . ($mould12);

            $result['melt1'] = "HOT TEAR," . ($melt1);
            $result['melt2'] = "COLD METAL," . ($melt2);
            $result['melt3'] = "LC," . ($melt3);
            $result['melt4'] = "SLAG," . ($melt4);
            $result['melt5'] = "RIPPLE," . ($melt5);
            $result['melt6'] = "SHORT POURED," . ($melt6);
            $result['melt7'] = "PELTCH," . ($melt7);
            $result['melt8'] = "OTHERS(MELTING & POURING)," . ($melt8);

            $result['felt1'] = "CHIP COLD," . ($felt1);
            $result['felt2'] = "CHIP HOT," . ($felt2);
            $result['felt3'] = "EXCESS FET.," . ($felt3);
            $result['felt4'] = "OTHERS(KNOCKOUT & FETTLING)," . ($felt4);


            $result['pattern1'] = "X-JOINT," . ($pattern1);
            $result['pattern2'] = "DIGGING IN," . ($pattern2);
            $result['pattern3'] = "LINEAR GAUGE REJ.," . ($pattern3);
            $result['pattern4'] = "OTHER(SIM GAP-P/M ETC.)," . ($pattern4);
            $result['pattern5'] = "PPAP," . ($pattern5);

$this->load->view('pages/get_dates_wise_rejection_data', $result);
    }


    }
    
    
    
    public function get_monthly_yearly_data()
    {
        $s_year = $this->input->post('s_date');
        $item = $this->input->post('item');
        $date = new DateTime('now', new DateTimeZone('Asia/Kolkata'));
        $date1 = $date->format('Y-m-d');
        $c_months = date("m", strtotime($date1));
        $c_year = date("Y", strtotime($date1));
//        echo $c_months."--".$c_year;
        $dept=$this->session->userdata('dept');
        $split_year= explode("-", $s_year);
        $first_year=array('04','05','06','07','08','09','10','11','12');
        $second_year=array('01','02','03');
        
        $first_result='';
        if($item!="ALL_D" && $item!="ALL_R")
        {
        $result['item_name']=$item;
        $result['year_show']=$s_year;
        $aa="select cust_name from tbl_item_details where item='$item'";
        $item_name=$this->db->query($aa)->row();
        $result['cust_name']=$item_name->cust_name;    
        for($i=0;$i<9;$i++)
        {
        $query="SELECT SUM(total_checked) as total_checked,SUM(total_good) as total_good,SUM(total_rej) as total_rej,item,cust_name,"
        . "SUM(total_mould) as total_mould,SUM(total_pouring) as total_pouring,SUM(total_fettling) as total_fettling,SUM(total_pattern) as total_pattern,SUM(fr_cut) as fr_cut,"
        . "CASE WHEN c_date IS NULL THEN $first_year[$i] ELSE $first_year[$i] END AS c_month,"
        . "CASE WHEN c_date IS NULL THEN $split_year[0] ELSE $split_year[0] END AS c_year,SUM(met_rej) as met_rej,"
        . "CASE WHEN id IS NULL THEN 0 ELSE GROUP_CONCAT(CONCAT('''', id, '''' )) END AS ids FROM `tbl_camshaft_scrap` "
        . "WHERE `item`='$item' and Month(c_date)='$first_year[$i]' and Year(c_date)='$split_year[0]' and dept='$dept' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
        $first_result[]=$this->db->query($query)->row();
       
        $q1="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_moulding_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$first_year[$i]' and Year(cs.c_date)='$split_year[0]' and cs.dept='$dept' and cs.item='$item' and md.defect_name='LOOSE SAND'";
          $mould1[]=$this->db->query($q1)->row();
          $q2="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_moulding_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$first_year[$i]' and Year(cs.c_date)='$split_year[0]' and cs.dept='$dept' and cs.item='$item' and md.defect_name='PIN HOLE'";
          $mould2[]=$this->db->query($q2)->row();
          $q3="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_moulding_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$first_year[$i]' and Year(cs.c_date)='$split_year[0]' and cs.dept='$dept' and cs.item='$item' and md.defect_name='GAS HOLE'";
          $mould3[]=$this->db->query($q3)->row();
          $q4="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_moulding_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$first_year[$i]' and Year(cs.c_date)='$split_year[0]' and cs.dept='$dept' and cs.item='$item' and md.defect_name='GLUE'";
          $mould4[]=$this->db->query($q4)->row();
          $q5="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_moulding_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$first_year[$i]' and Year(cs.c_date)='$split_year[0]' and cs.dept='$dept' and cs.item='$item' and md.defect_name='MOULD CRACK'";
          $mould5[]=$this->db->query($q5)->row();
          $q6="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_moulding_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$first_year[$i]' and Year(cs.c_date)='$split_year[0]' and cs.dept='$dept' and cs.item='$item' and md.defect_name='CORE BROKEN'";
          $mould6[]=$this->db->query($q6)->row();
          $q7="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_moulding_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$first_year[$i]' and Year(cs.c_date)='$split_year[0]' and cs.dept='$dept' and cs.item='$item' and md.defect_name='TIR REJECT'";
          $mould7[]=$this->db->query($q7)->row();
          $q8="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_moulding_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$first_year[$i]' and Year(cs.c_date)='$split_year[0]' and cs.dept='$dept' and cs.item='$item' and md.defect_name='CHILL DEFECT'";
          $mould8[]=$this->db->query($q8)->row();
          $q9="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_moulding_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$first_year[$i]' and Year(cs.c_date)='$split_year[0]' and cs.dept='$dept' and cs.item='$item' and md.defect_name='THICK FLASH'";
          $mould9[]=$this->db->query($q9)->row();
          $q10="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_moulding_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$first_year[$i]' and Year(cs.c_date)='$split_year[0]' and cs.dept='$dept' and cs.item='$item' and md.defect_name='MOULD LEAK'";
          $mould10[]=$this->db->query($q10)->row();
          $q11="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_moulding_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$first_year[$i]' and Year(cs.c_date)='$split_year[0]' and cs.dept='$dept' and cs.item='$item' and md.defect_name='EXTRA METAL'";
          $mould11[]=$this->db->query($q11)->row();
          $q12="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_moulding_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$first_year[$i]' and Year(cs.c_date)='$split_year[0]' and cs.dept='$dept' and cs.item='$item' and md.defect_name='OTHERS(MOULDING)'";
          $mould12[]=$this->db->query($q12)->row();
          
          $q13="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_melting_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$first_year[$i]' and Year(cs.c_date)='$split_year[0]' and cs.dept='$dept' and cs.item='$item' and md.defect_name='HOT TEAR'";
          $mould13[]=$this->db->query($q13)->row();
          $q14="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_melting_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$first_year[$i]' and Year(cs.c_date)='$split_year[0]' and cs.dept='$dept' and cs.item='$item' and md.defect_name='COLD METAL'";
          $mould14[]=$this->db->query($q14)->row();
          $q15="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_melting_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$first_year[$i]' and Year(cs.c_date)='$split_year[0]' and cs.dept='$dept' and cs.item='$item' and md.defect_name='LC'";
          $mould15[]=$this->db->query($q15)->row();
          $q16="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_melting_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$first_year[$i]' and Year(cs.c_date)='$split_year[0]' and cs.dept='$dept' and cs.item='$item' and md.defect_name='SLAG'";
          $mould16[]=$this->db->query($q16)->row();
          $q17="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_melting_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$first_year[$i]' and Year(cs.c_date)='$split_year[0]' and cs.dept='$dept' and cs.item='$item' and md.defect_name='RIPPLE'";
          $mould17[]=$this->db->query($q17)->row();
          $q18="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_melting_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$first_year[$i]' and Year(cs.c_date)='$split_year[0]' and cs.dept='$dept' and cs.item='$item' and md.defect_name='SHORT POURED'";
          $mould18[]=$this->db->query($q18)->row();
          $q19="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_melting_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$first_year[$i]' and Year(cs.c_date)='$split_year[0]' and cs.dept='$dept' and cs.item='$item' and md.defect_name='PELTCH'";
          $mould19[]=$this->db->query($q19)->row();
          $q20="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_melting_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$first_year[$i]' and Year(cs.c_date)='$split_year[0]' and cs.dept='$dept' and cs.item='$item' and md.defect_name='OTHERS(MELTING & POURING)'";
          $mould20[]=$this->db->query($q20)->row();
          
          $q21="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_fettling_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$first_year[$i]' and Year(cs.c_date)='$split_year[0]' and cs.dept='$dept' and cs.item='$item' and md.defect_name='CHIP COLD'";
          $mould21[]=$this->db->query($q21)->row();
          $q22="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_fettling_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$first_year[$i]' and Year(cs.c_date)='$split_year[0]' and cs.dept='$dept' and cs.item='$item' and md.defect_name='CHIP HOT'";
          $mould22[]=$this->db->query($q22)->row();
          $q23="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_fettling_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$first_year[$i]' and Year(cs.c_date)='$split_year[0]' and cs.dept='$dept' and cs.item='$item' and md.defect_name='EXCESS FET.'";
          $mould23[]=$this->db->query($q23)->row();
          $q24="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_fettling_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$first_year[$i]' and Year(cs.c_date)='$split_year[0]' and cs.dept='$dept' and cs.item='$item' and md.defect_name='OTHERS(KNOCKOUT & FETTLING)'";
          $mould24[]=$this->db->query($q24)->row();
          
          $q25="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_pattern_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$first_year[$i]' and Year(cs.c_date)='$split_year[0]' and cs.dept='$dept' and cs.item='$item' and md.defect_name='X-JOINT'";
          $mould25[]=$this->db->query($q25)->row();
          $q26="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_pattern_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$first_year[$i]' and Year(cs.c_date)='$split_year[0]' and cs.dept='$dept' and cs.item='$item' and md.defect_name='DIGGING IN'";
          $mould26[]=$this->db->query($q26)->row();
          $q27="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_pattern_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$first_year[$i]' and Year(cs.c_date)='$split_year[0]' and cs.dept='$dept' and cs.item='$item' and md.defect_name='LINEAR GAUGE REJ.'";
          $mould27[]=$this->db->query($q27)->row();
          $q28="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_pattern_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$first_year[$i]' and Year(cs.c_date)='$split_year[0]' and cs.dept='$dept' and cs.item='$item' and md.defect_name='OTHER(SIM GAP,P/M ETC.)'";
          $mould28[]=$this->db->query($q28)->row();
          $q29="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_pattern_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$first_year[$i]' and Year(cs.c_date)='$split_year[0]' and cs.dept='$dept' and cs.item='$item' and md.defect_name='PPAP'";
          $mould29[]=$this->db->query($q29)->row();
        
        }
         
        for($j=0;$j<3;$j++)
        {
        $query2="SELECT SUM(total_checked) as total_checked,SUM(total_good) as total_good,SUM(total_rej) as total_rej,item,cust_name,"
        . "SUM(total_mould) as total_mould,SUM(total_pouring) as total_pouring,SUM(total_fettling) as total_fettling,SUM(total_pattern) as total_pattern,SUM(fr_cut) as fr_cut,"        
        . "CASE WHEN c_date IS NULL THEN $second_year[$j] ELSE $second_year[$j] END AS c_month,"
        . "CASE WHEN c_date IS NULL THEN $split_year[1] ELSE $split_year[1] END AS c_year,SUM(met_rej) as met_rej,"
        . "CASE WHEN id IS NULL THEN 0 ELSE GROUP_CONCAT(CONCAT('''', id, '''' )) END AS ids FROM `tbl_camshaft_scrap` "
        . "WHERE `item`='$item' and Month(c_date)='$second_year[$j]' and Year(c_date)='$split_year[1]' and dept='$dept' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
        
        $first_result[]=$sec_ids[]=$this->db->query($query2)->row();
        
        $qq1="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_moulding_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$second_year[$j]' and Year(cs.c_date)='$split_year[1]' and cs.dept='$dept' and cs.item='$item' and md.defect_name='LOOSE SAND' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould1[]=$this->db->query($qq1)->row();
          $qq2="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_moulding_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$second_year[$j]' and Year(cs.c_date)='$split_year[1]' and cs.dept='$dept' and cs.item='$item' and md.defect_name='PIN HOLE' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould2[]=$this->db->query($qq2)->row();
          $qq3="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_moulding_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$second_year[$j]' and Year(cs.c_date)='$split_year[1]' and cs.dept='$dept' and cs.item='$item' and md.defect_name='GAS HOLE' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould3[]=$this->db->query($qq3)->row();
          $qq4="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_moulding_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$second_year[$j]' and Year(cs.c_date)='$split_year[1]' and cs.dept='$dept' and cs.item='$item' and md.defect_name='GLUE' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould4[]=$this->db->query($qq4)->row();
          $qq5="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_moulding_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$second_year[$j]' and Year(cs.c_date)='$split_year[1]' and cs.dept='$dept' and cs.item='$item' and md.defect_name='MOULD CRACK' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould5[]=$this->db->query($qq5)->row();
          $qq6="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_moulding_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$second_year[$j]' and Year(cs.c_date)='$split_year[1]' and cs.dept='$dept' and cs.item='$item' and md.defect_name='CORE BROKEN' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould6[]=$this->db->query($qq6)->row();
          $qq7="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_moulding_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$second_year[$j]' and Year(cs.c_date)='$split_year[1]' and cs.dept='$dept' and cs.item='$item' and md.defect_name='TIR REJECT' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould7[]=$this->db->query($qq7)->row();
          $qq8="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_moulding_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$second_year[$j]' and Year(cs.c_date)='$split_year[1]' and cs.dept='$dept' and cs.item='$item' and md.defect_name='CHILL DEFECT' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould8[]=$this->db->query($qq8)->row();
          $qq9="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_moulding_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$second_year[$j]' and Year(cs.c_date)='$split_year[1]' and cs.dept='$dept' and cs.item='$item' and md.defect_name='THICK FLASH' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould9[]=$this->db->query($qq9)->row();
          $qq10="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_moulding_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$second_year[$j]' and Year(cs.c_date)='$split_year[1]' and cs.dept='$dept' and cs.item='$item' and md.defect_name='MOULD LEAK' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould10[]=$this->db->query($qq10)->row();
          $qq11="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_moulding_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$second_year[$j]' and Year(cs.c_date)='$split_year[1]' and cs.dept='$dept' and cs.item='$item' and md.defect_name='EXTRA METAL' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould11[]=$this->db->query($qq11)->row();
          $qq12="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_moulding_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$second_year[$j]' and Year(cs.c_date)='$split_year[1]' and cs.dept='$dept' and cs.item='$item' and md.defect_name='OTHERS(MOULDING)' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould12[]=$this->db->query($qq12)->row();
          
          $qq13="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_melting_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$second_year[$j]' and Year(cs.c_date)='$split_year[1]' and cs.dept='$dept' and cs.item='$item' and md.defect_name='HOT TEAR' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould13[]=$this->db->query($qq13)->row();
          $qq14="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_melting_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$second_year[$j]' and Year(cs.c_date)='$split_year[1]' and cs.dept='$dept' and cs.item='$item' and md.defect_name='COLD METAL' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould14[]=$this->db->query($qq14)->row();
          $qq15="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_melting_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$second_year[$j]' and Year(cs.c_date)='$split_year[1]' and cs.dept='$dept' and cs.item='$item' and md.defect_name='LC' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould15[]=$this->db->query($qq15)->row();
          $qq16="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_melting_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$second_year[$j]' and Year(cs.c_date)='$split_year[1]' and cs.dept='$dept' and cs.item='$item' and md.defect_name='SLAG' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould16[]=$this->db->query($qq16)->row();
          $qq17="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_melting_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$second_year[$j]' and Year(cs.c_date)='$split_year[1]' and cs.dept='$dept' and cs.item='$item' and md.defect_name='RIPPLE' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould17[]=$this->db->query($qq17)->row();
          $qq18="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_melting_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$second_year[$j]' and Year(cs.c_date)='$split_year[1]' and cs.dept='$dept' and cs.item='$item' and md.defect_name='SHORT POURED' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould18[]=$this->db->query($qq18)->row();
          $qq19="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_melting_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$second_year[$j]' and Year(cs.c_date)='$split_year[1]' and cs.dept='$dept' and cs.item='$item' and md.defect_name='PELTCH' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould19[]=$this->db->query($qq19)->row();
          $qq20="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_melting_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$second_year[$j]' and Year(cs.c_date)='$split_year[1]' and cs.dept='$dept' and cs.item='$item' and md.defect_name='OTHERS(MELTING & POURING)' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould20[]=$this->db->query($qq20)->row();
          
          $qq21="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_fettling_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$second_year[$j]' and Year(cs.c_date)='$split_year[1]' and cs.dept='$dept' and cs.item='$item' and md.defect_name='CHIP COLD' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould21[]=$this->db->query($qq21)->row();
          $qq22="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_fettling_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$second_year[$j]' and Year(cs.c_date)='$split_year[1]' and cs.dept='$dept' and cs.item='$item' and md.defect_name='CHIP HOT' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould22[]=$this->db->query($qq22)->row();
          $qq23="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_fettling_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$second_year[$j]' and Year(cs.c_date)='$split_year[1]' and cs.dept='$dept' and cs.item='$item' and md.defect_name='EXCESS FET.' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould23[]=$this->db->query($qq23)->row();
          $qq24="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_fettling_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$second_year[$j]' and Year(cs.c_date)='$split_year[1]' and cs.dept='$dept' and cs.item='$item' and md.defect_name='OTHERS(KNOCKOUT & FETTLING)' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould24[]=$this->db->query($qq24)->row();
          
          $qq25="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_pattern_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$second_year[$j]' and Year(cs.c_date)='$split_year[1]' and cs.dept='$dept' and cs.item='$item' and md.defect_name='X-JOINT' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould25[]=$this->db->query($qq25)->row();
          $qq26="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_pattern_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$second_year[$j]' and Year(cs.c_date)='$split_year[1]' and cs.dept='$dept' and cs.item='$item' and md.defect_name='DIGGING IN' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould26[]=$this->db->query($qq26)->row();
          $qq27="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_pattern_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$second_year[$j]' and Year(cs.c_date)='$split_year[1]' and cs.dept='$dept' and cs.item='$item' and md.defect_name='LINEAR GAUGE REJ.' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould27[]=$this->db->query($qq27)->row();
          $qq28="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_pattern_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$second_year[$j]' and Year(cs.c_date)='$split_year[1]' and cs.dept='$dept' and cs.item='$item' and md.defect_name='OTHER(SIM GAP,P/M ETC.)' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould28[]=$this->db->query($qq28)->row();
          $qq29="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_pattern_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$second_year[$j]' and Year(cs.c_date)='$split_year[1]' and cs.dept='$dept' and cs.item='$item' and md.defect_name='PPAP' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould29[]=$this->db->query($qq29)->row();
        
        }
        
        
        
        
        $result['data']=$first_result;
        $result['mould1']=$mould1;
        $result['mould2']=$mould2;
        $result['mould3']=$mould3;
        $result['mould4']=$mould4;
        $result['mould5']=$mould5;
        $result['mould6']=$mould6;
        $result['mould7']=$mould7;
        $result['mould8']=$mould8;
        $result['mould9']=$mould9;
        $result['mould10']=$mould10;
        $result['mould11']=$mould11;
        $result['mould12']=$mould12;
        
        $result['mould13']=$mould13;
        $result['mould14']=$mould14;
        $result['mould15']=$mould15;
        $result['mould16']=$mould16;
        $result['mould17']=$mould17;
        $result['mould18']=$mould18;
        $result['mould19']=$mould19;
        $result['mould20']=$mould20;
        
        $result['mould21']=$mould21;
        $result['mould22']=$mould22;
        $result['mould23']=$mould23;
        $result['mould24']=$mould24;
        
        $result['mould25']=$mould25;
        $result['mould26']=$mould26;
        $result['mould27']=$mould27;
        $result['mould28']=$mould28;
        $result['mould29']=$mould29;
        
        $this->load->view('pages/get_monthly_yearly_data', $result);
        }
        
        else if($item=="ALL_R")
        {
        $result['item_name']=$item;    
        $result['cust_name']="ALL REGULAR ITEM";
        $result['year_show']=$s_year;
        for($i=0;$i<9;$i++)
        {
          $query="SELECT SUM(total_checked) as total_checked,SUM(total_good) as total_good,SUM(total_rej) as total_rej,item,cust_name,"
        . "SUM(total_mould) as total_mould,SUM(total_pouring) as total_pouring,SUM(total_fettling) as total_fettling,SUM(total_pattern) as total_pattern,SUM(fr_cut) as fr_cut,"
        . "CASE WHEN c_date IS NULL THEN $first_year[$i] ELSE $first_year[$i] END AS c_month,"
        . "CASE WHEN c_date IS NULL THEN $split_year[0] ELSE $split_year[0] END AS c_year,SUM(met_rej) as met_rej,"
        . "CASE WHEN id IS NULL THEN 0 ELSE GROUP_CONCAT(CONCAT('''', id, '''' )) END AS ids FROM `tbl_camshaft_scrap` "
        . " WHERE Month(c_date)='$first_year[$i]' and Year(c_date)='$split_year[0]' and dept='$dept' and `item_type`='R' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
        $first_result[]=$this->db->query($query)->row();
        
        
          $q1="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_moulding_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$first_year[$i]' and Year(cs.c_date)='$split_year[0]' and cs.dept='$dept' and cs.item_type='R' and md.defect_name='LOOSE SAND' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould1[]=$this->db->query($q1)->row();
          $q2="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_moulding_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$first_year[$i]' and Year(cs.c_date)='$split_year[0]' and cs.dept='$dept' and cs.item_type='R' and md.defect_name='PIN HOLE' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould2[]=$this->db->query($q2)->row();
          $q3="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_moulding_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$first_year[$i]' and Year(cs.c_date)='$split_year[0]' and cs.dept='$dept' and cs.item_type='R' and md.defect_name='GAS HOLE' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould3[]=$this->db->query($q3)->row();
          $q4="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_moulding_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$first_year[$i]' and Year(cs.c_date)='$split_year[0]' and cs.dept='$dept' and cs.item_type='R' and md.defect_name='GLUE' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould4[]=$this->db->query($q4)->row();
          $q5="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_moulding_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$first_year[$i]' and Year(cs.c_date)='$split_year[0]' and cs.dept='$dept' and cs.item_type='R' and md.defect_name='MOULD CRACK' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould5[]=$this->db->query($q5)->row();
          $q6="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_moulding_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$first_year[$i]' and Year(cs.c_date)='$split_year[0]' and cs.dept='$dept' and cs.item_type='R' and md.defect_name='CORE BROKEN' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould6[]=$this->db->query($q6)->row();
          $q7="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_moulding_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$first_year[$i]' and Year(cs.c_date)='$split_year[0]' and cs.dept='$dept' and cs.item_type='R' and md.defect_name='TIR REJECT' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould7[]=$this->db->query($q7)->row();
          $q8="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_moulding_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$first_year[$i]' and Year(cs.c_date)='$split_year[0]' and cs.dept='$dept' and cs.item_type='R' and md.defect_name='CHILL DEFECT' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould8[]=$this->db->query($q8)->row();
          $q9="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_moulding_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$first_year[$i]' and Year(cs.c_date)='$split_year[0]' and cs.dept='$dept' and cs.item_type='R' and md.defect_name='THICK FLASH' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould9[]=$this->db->query($q9)->row();
          $q10="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_moulding_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$first_year[$i]' and Year(cs.c_date)='$split_year[0]' and cs.dept='$dept' and cs.item_type='R' and md.defect_name='MOULD LEAK' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould10[]=$this->db->query($q10)->row();
          $q11="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_moulding_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$first_year[$i]' and Year(cs.c_date)='$split_year[0]' and cs.dept='$dept' and cs.item_type='R' and md.defect_name='EXTRA METAL' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould11[]=$this->db->query($q11)->row();
          $q12="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_moulding_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$first_year[$i]' and Year(cs.c_date)='$split_year[0]' and cs.dept='$dept' and cs.item_type='R' and md.defect_name='OTHERS(MOULDING)' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould12[]=$this->db->query($q12)->row();
          
          $q13="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_melting_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$first_year[$i]' and Year(cs.c_date)='$split_year[0]' and cs.dept='$dept' and cs.item_type='R' and md.defect_name='HOT TEAR' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould13[]=$this->db->query($q13)->row();
          $q14="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_melting_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$first_year[$i]' and Year(cs.c_date)='$split_year[0]' and cs.dept='$dept' and cs.item_type='R' and md.defect_name='COLD METAL' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould14[]=$this->db->query($q14)->row();
          $q15="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_melting_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$first_year[$i]' and Year(cs.c_date)='$split_year[0]' and cs.dept='$dept' and cs.item_type='R' and md.defect_name='LC' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould15[]=$this->db->query($q15)->row();
          $q16="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_melting_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$first_year[$i]' and Year(cs.c_date)='$split_year[0]' and cs.dept='$dept' and cs.item_type='R' and md.defect_name='SLAG' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould16[]=$this->db->query($q16)->row();
          $q17="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_melting_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$first_year[$i]' and Year(cs.c_date)='$split_year[0]' and cs.dept='$dept' and cs.item_type='R' and md.defect_name='RIPPLE' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould17[]=$this->db->query($q17)->row();
          $q18="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_melting_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$first_year[$i]' and Year(cs.c_date)='$split_year[0]' and cs.dept='$dept' and cs.item_type='R' and md.defect_name='SHORT POURED' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould18[]=$this->db->query($q18)->row();
          $q19="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_melting_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$first_year[$i]' and Year(cs.c_date)='$split_year[0]' and cs.dept='$dept' and cs.item_type='R' and md.defect_name='PELTCH' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould19[]=$this->db->query($q19)->row();
          $q20="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_melting_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$first_year[$i]' and Year(cs.c_date)='$split_year[0]' and cs.dept='$dept' and cs.item_type='R' and md.defect_name='OTHERS(MELTING & POURING)' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould20[]=$this->db->query($q20)->row();
          
          $q21="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_fettling_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$first_year[$i]' and Year(cs.c_date)='$split_year[0]' and cs.dept='$dept' and cs.item_type='R' and md.defect_name='CHIP COLD' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould21[]=$this->db->query($q21)->row();
          $q22="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_fettling_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$first_year[$i]' and Year(cs.c_date)='$split_year[0]' and cs.dept='$dept' and cs.item_type='R' and md.defect_name='CHIP HOT' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould22[]=$this->db->query($q22)->row();
          $q23="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_fettling_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$first_year[$i]' and Year(cs.c_date)='$split_year[0]' and cs.dept='$dept' and cs.item_type='R' and md.defect_name='EXCESS FET.' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould23[]=$this->db->query($q23)->row();
          $q24="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_fettling_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$first_year[$i]' and Year(cs.c_date)='$split_year[0]' and cs.dept='$dept' and cs.item_type='R' and md.defect_name='OTHERS(KNOCKOUT & FETTLING)' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould24[]=$this->db->query($q24)->row();
          
          $q25="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_pattern_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$first_year[$i]' and Year(cs.c_date)='$split_year[0]' and cs.dept='$dept' and cs.item_type='R' and md.defect_name='X-JOINT' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould25[]=$this->db->query($q25)->row();
          $q26="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_pattern_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$first_year[$i]' and Year(cs.c_date)='$split_year[0]' and cs.dept='$dept' and cs.item_type='R' and md.defect_name='DIGGING IN' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould26[]=$this->db->query($q26)->row();
          $q27="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_pattern_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$first_year[$i]' and Year(cs.c_date)='$split_year[0]' and cs.dept='$dept' and cs.item_type='R' and md.defect_name='LINEAR GAUGE REJ.' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould27[]=$this->db->query($q27)->row();
          $q28="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_pattern_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$first_year[$i]' and Year(cs.c_date)='$split_year[0]' and cs.dept='$dept' and cs.item_type='R' and md.defect_name='OTHER(SIM GAP,P/M ETC.)' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould28[]=$this->db->query($q28)->row();
          $q29="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_pattern_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$first_year[$i]' and Year(cs.c_date)='$split_year[0]' and cs.dept='$dept' and cs.item_type='R' and md.defect_name='PPAP' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould29[]=$this->db->query($q29)->row();
        
        
        }
        
        for($j=0;$j<3;$j++)
        {
         $query2="SELECT SUM(total_checked) as total_checked,SUM(total_good) as total_good,SUM(total_rej) as total_rej,item,cust_name,"
        . "SUM(total_mould) as total_mould,SUM(total_pouring) as total_pouring,SUM(total_fettling) as total_fettling,SUM(total_pattern) as total_pattern,SUM(fr_cut) as fr_cut,"        
        . "CASE WHEN c_date IS NULL THEN $second_year[$j] ELSE $second_year[$j] END AS c_month,"
        . "CASE WHEN c_date IS NULL THEN $split_year[1] ELSE $split_year[1] END AS c_year,SUM(met_rej) as met_rej,"
        . "CASE WHEN id IS NULL THEN 0 ELSE GROUP_CONCAT(CONCAT('''', id, '''' )) END AS ids FROM `tbl_camshaft_scrap` "
        . " WHERE Month(c_date)='$second_year[$j]' and Year(c_date)='$split_year[1]' and dept='$dept' and `item_type`='R' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
        $first_result[]=$this->db->query($query2)->row();
        
        $qq1="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_moulding_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$second_year[$j]' and Year(cs.c_date)='$split_year[1]' and cs.dept='$dept' and cs.item_type='R' and md.defect_name='LOOSE SAND' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould1[]=$this->db->query($qq1)->row();
          $qq2="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_moulding_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$second_year[$j]' and Year(cs.c_date)='$split_year[1]' and cs.dept='$dept' and cs.item_type='R' and md.defect_name='PIN HOLE' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould2[]=$this->db->query($qq2)->row();
          $qq3="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_moulding_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$second_year[$j]' and Year(cs.c_date)='$split_year[1]' and cs.dept='$dept' and cs.item_type='R' and md.defect_name='GAS HOLE' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould3[]=$this->db->query($qq3)->row();
          $qq4="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_moulding_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$second_year[$j]' and Year(cs.c_date)='$split_year[1]' and cs.dept='$dept' and cs.item_type='R' and md.defect_name='GLUE' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould4[]=$this->db->query($qq4)->row();
          $qq5="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_moulding_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$second_year[$j]' and Year(cs.c_date)='$split_year[1]' and cs.dept='$dept' and cs.item_type='R' and md.defect_name='MOULD CRACK' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould5[]=$this->db->query($qq5)->row();
          $qq6="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_moulding_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$second_year[$j]' and Year(cs.c_date)='$split_year[1]' and cs.dept='$dept' and cs.item_type='R' and md.defect_name='CORE BROKEN' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould6[]=$this->db->query($qq6)->row();
          $qq7="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_moulding_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$second_year[$j]' and Year(cs.c_date)='$split_year[1]' and cs.dept='$dept' and cs.item_type='R' and md.defect_name='TIR REJECT' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould7[]=$this->db->query($qq7)->row();
          $qq8="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_moulding_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$second_year[$j]' and Year(cs.c_date)='$split_year[1]' and cs.dept='$dept' and cs.item_type='R' and md.defect_name='CHILL DEFECT' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould8[]=$this->db->query($qq8)->row();
          $qq9="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_moulding_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$second_year[$j]' and Year(cs.c_date)='$split_year[1]' and cs.dept='$dept' and cs.item_type='R' and md.defect_name='THICK FLASH' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould9[]=$this->db->query($qq9)->row();
          $qq10="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_moulding_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$second_year[$j]' and Year(cs.c_date)='$split_year[1]' and cs.dept='$dept' and cs.item_type='R' and md.defect_name='MOULD LEAK' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould10[]=$this->db->query($qq10)->row();
          $qq11="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_moulding_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$second_year[$j]' and Year(cs.c_date)='$split_year[1]' and cs.dept='$dept' and cs.item_type='R' and md.defect_name='EXTRA METAL' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould11[]=$this->db->query($qq11)->row();
          $qq12="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_moulding_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$second_year[$j]' and Year(cs.c_date)='$split_year[1]' and cs.dept='$dept' and cs.item_type='R' and md.defect_name='OTHERS(MOULDING)' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould12[]=$this->db->query($qq12)->row();
          
          $qq13="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_melting_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$second_year[$j]' and Year(cs.c_date)='$split_year[1]' and cs.dept='$dept' and cs.item_type='R' and md.defect_name='HOT TEAR' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould13[]=$this->db->query($qq13)->row();
          $qq14="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_melting_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$second_year[$j]' and Year(cs.c_date)='$split_year[1]' and cs.dept='$dept' and cs.item_type='R' and md.defect_name='COLD METAL' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould14[]=$this->db->query($qq14)->row();
          $qq15="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_melting_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$second_year[$j]' and Year(cs.c_date)='$split_year[1]' and cs.dept='$dept' and cs.item_type='R' and md.defect_name='LC' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould15[]=$this->db->query($qq15)->row();
          $qq16="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_melting_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$second_year[$j]' and Year(cs.c_date)='$split_year[1]' and cs.dept='$dept' and cs.item_type='R' and md.defect_name='SLAG' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould16[]=$this->db->query($qq16)->row();
          $qq17="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_melting_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$second_year[$j]' and Year(cs.c_date)='$split_year[1]' and cs.dept='$dept' and cs.item_type='R' and md.defect_name='RIPPLE' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould17[]=$this->db->query($qq17)->row();
          $qq18="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_melting_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$second_year[$j]' and Year(cs.c_date)='$split_year[1]' and cs.dept='$dept' and cs.item_type='R' and md.defect_name='SHORT POURED' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould18[]=$this->db->query($qq18)->row();
          $qq19="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_melting_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$second_year[$j]' and Year(cs.c_date)='$split_year[1]' and cs.dept='$dept' and cs.item_type='R' and md.defect_name='PELTCH' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould19[]=$this->db->query($qq19)->row();
          $qq20="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_melting_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$second_year[$j]' and Year(cs.c_date)='$split_year[1]' and cs.dept='$dept' and cs.item_type='R' and md.defect_name='OTHERS(MELTING & POURING)' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould20[]=$this->db->query($qq20)->row();
          
          $qq21="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_fettling_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$second_year[$j]' and Year(cs.c_date)='$split_year[1]' and cs.dept='$dept' and cs.item_type='R' and md.defect_name='CHIP COLD' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould21[]=$this->db->query($qq21)->row();
          $qq22="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_fettling_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$second_year[$j]' and Year(cs.c_date)='$split_year[1]' and cs.dept='$dept' and cs.item_type='R' and md.defect_name='CHIP HOT' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould22[]=$this->db->query($qq22)->row();
          $qq23="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_fettling_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$second_year[$j]' and Year(cs.c_date)='$split_year[1]' and cs.dept='$dept' and cs.item_type='R' and md.defect_name='EXCESS FET.' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould23[]=$this->db->query($qq23)->row();
          $qq24="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_fettling_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$second_year[$j]' and Year(cs.c_date)='$split_year[1]' and cs.dept='$dept' and cs.item_type='R' and md.defect_name='OTHERS(KNOCKOUT & FETTLING)' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould24[]=$this->db->query($qq24)->row();
          
          $qq25="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_pattern_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$second_year[$j]' and Year(cs.c_date)='$split_year[1]' and cs.dept='$dept' and cs.item_type='R' and md.defect_name='X-JOINT' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould25[]=$this->db->query($qq25)->row();
          $qq26="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_pattern_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$second_year[$j]' and Year(cs.c_date)='$split_year[1]' and cs.dept='$dept' and cs.item_type='R' and md.defect_name='DIGGING IN' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould26[]=$this->db->query($qq26)->row();
          $qq27="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_pattern_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$second_year[$j]' and Year(cs.c_date)='$split_year[1]' and cs.dept='$dept' and cs.item_type='R' and md.defect_name='LINEAR GAUGE REJ.' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould27[]=$this->db->query($qq27)->row();
          $qq28="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_pattern_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$second_year[$j]' and Year(cs.c_date)='$split_year[1]' and cs.dept='$dept' and cs.item_type='R' and md.defect_name='OTHER(SIM GAP,P/M ETC.)' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould28[]=$this->db->query($qq28)->row();
          $qq29="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_pattern_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$second_year[$j]' and Year(cs.c_date)='$split_year[1]' and cs.dept='$dept' and cs.item_type='R' and md.defect_name='PPAP' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould29[]=$this->db->query($qq29)->row();
        }
        
        
        
        $result['data']=$first_result;
        $result['mould1']=$mould1;
        $result['mould2']=$mould2;
        $result['mould3']=$mould3;
        $result['mould4']=$mould4;
        $result['mould5']=$mould5;
        $result['mould6']=$mould6;
        $result['mould7']=$mould7;
        $result['mould8']=$mould8;
        $result['mould9']=$mould9;
        $result['mould10']=$mould10;
        $result['mould11']=$mould11;
        $result['mould12']=$mould12;
        
        $result['mould13']=$mould13;
        $result['mould14']=$mould14;
        $result['mould15']=$mould15;
        $result['mould16']=$mould16;
        $result['mould17']=$mould17;
        $result['mould18']=$mould18;
        $result['mould19']=$mould19;
        $result['mould20']=$mould20;
        
        $result['mould21']=$mould21;
        $result['mould22']=$mould22;
        $result['mould23']=$mould23;
        $result['mould24']=$mould24;
        
        $result['mould25']=$mould25;
        $result['mould26']=$mould26;
        $result['mould27']=$mould27;
        $result['mould28']=$mould28;
        $result['mould29']=$mould29;
        
        $this->load->view('pages/get_monthly_yearly_data', $result);
        }
        
        
        else if($item=="ALL_D")
        {
        $result['item_name']=$item;        
        $result['cust_name']="ALL DEVELOPMENT ITEM";
        $result['year_show']=$s_year;
        for($i=0;$i<9;$i++)
        {
          $query="SELECT SUM(total_checked) as total_checked,SUM(total_good) as total_good,SUM(total_rej) as total_rej,item,cust_name,"
        . "SUM(total_mould) as total_mould,SUM(total_pouring) as total_pouring,SUM(total_fettling) as total_fettling,SUM(total_pattern) as total_pattern,SUM(fr_cut) as fr_cut,"
        . "CASE WHEN c_date IS NULL THEN $first_year[$i] ELSE $first_year[$i] END AS c_month,"
        . "CASE WHEN c_date IS NULL THEN $split_year[0] ELSE $split_year[0] END AS c_year,SUM(met_rej) as met_rej,"
        . "CASE WHEN id IS NULL THEN 0 ELSE GROUP_CONCAT(CONCAT('''', id, '''' )) END AS ids FROM `tbl_camshaft_scrap` "
        . " WHERE Month(c_date)='$first_year[$i]' and Year(c_date)='$split_year[0]' and dept='$dept' and `item_type`='D' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
        $first_result[]=$this->db->query($query)->row();
        
        
          $q1="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_moulding_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$first_year[$i]' and Year(cs.c_date)='$split_year[0]' and cs.dept='$dept' and cs.item_type='D' and md.defect_name='LOOSE SAND' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould1[]=$this->db->query($q1)->row();
          $q2="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_moulding_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$first_year[$i]' and Year(cs.c_date)='$split_year[0]' and cs.dept='$dept' and cs.item_type='D' and md.defect_name='PIN HOLE' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould2[]=$this->db->query($q2)->row();
          $q3="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_moulding_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$first_year[$i]' and Year(cs.c_date)='$split_year[0]' and cs.dept='$dept' and cs.item_type='D' and md.defect_name='GAS HOLE' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould3[]=$this->db->query($q3)->row();
          $q4="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_moulding_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$first_year[$i]' and Year(cs.c_date)='$split_year[0]' and cs.dept='$dept' and cs.item_type='D' and md.defect_name='GLUE' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould4[]=$this->db->query($q4)->row();
          $q5="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_moulding_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$first_year[$i]' and Year(cs.c_date)='$split_year[0]' and cs.dept='$dept' and cs.item_type='D' and md.defect_name='MOULD CRACK' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould5[]=$this->db->query($q5)->row();
          $q6="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_moulding_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$first_year[$i]' and Year(cs.c_date)='$split_year[0]' and cs.dept='$dept' and cs.item_type='D' and md.defect_name='CORE BROKEN' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould6[]=$this->db->query($q6)->row();
          $q7="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_moulding_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$first_year[$i]' and Year(cs.c_date)='$split_year[0]' and cs.dept='$dept' and cs.item_type='D' and md.defect_name='TIR REJECT' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould7[]=$this->db->query($q7)->row();
          $q8="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_moulding_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$first_year[$i]' and Year(cs.c_date)='$split_year[0]' and cs.dept='$dept' and cs.item_type='D' and md.defect_name='CHILL DEFECT' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould8[]=$this->db->query($q8)->row();
          $q9="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_moulding_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$first_year[$i]' and Year(cs.c_date)='$split_year[0]' and cs.dept='$dept' and cs.item_type='D' and md.defect_name='THICK FLASH' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould9[]=$this->db->query($q9)->row();
          $q10="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_moulding_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$first_year[$i]' and Year(cs.c_date)='$split_year[0]' and cs.dept='$dept' and cs.item_type='D' and md.defect_name='MOULD LEAK' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould10[]=$this->db->query($q10)->row();
          $q11="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_moulding_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$first_year[$i]' and Year(cs.c_date)='$split_year[0]' and cs.dept='$dept' and cs.item_type='D' and md.defect_name='EXTRA METAL' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould11[]=$this->db->query($q11)->row();
          $q12="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_moulding_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$first_year[$i]' and Year(cs.c_date)='$split_year[0]' and cs.dept='$dept' and cs.item_type='D' and md.defect_name='OTHERS(MOULDING)' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould12[]=$this->db->query($q12)->row();
          
          $q13="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_melting_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$first_year[$i]' and Year(cs.c_date)='$split_year[0]' and cs.dept='$dept' and cs.item_type='D' and md.defect_name='HOT TEAR' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould13[]=$this->db->query($q13)->row();
          $q14="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_melting_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$first_year[$i]' and Year(cs.c_date)='$split_year[0]' and cs.dept='$dept' and cs.item_type='D' and md.defect_name='COLD METAL' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould14[]=$this->db->query($q14)->row();
          $q15="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_melting_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$first_year[$i]' and Year(cs.c_date)='$split_year[0]' and cs.dept='$dept' and cs.item_type='D' and md.defect_name='LC' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould15[]=$this->db->query($q15)->row();
          $q16="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_melting_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$first_year[$i]' and Year(cs.c_date)='$split_year[0]' and cs.dept='$dept' and cs.item_type='D' and md.defect_name='SLAG' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould16[]=$this->db->query($q16)->row();
          $q17="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_melting_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$first_year[$i]' and Year(cs.c_date)='$split_year[0]' and cs.dept='$dept' and cs.item_type='D' and md.defect_name='RIPPLE' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould17[]=$this->db->query($q17)->row();
          $q18="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_melting_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$first_year[$i]' and Year(cs.c_date)='$split_year[0]' and cs.dept='$dept' and cs.item_type='D' and md.defect_name='SHORT POURED' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould18[]=$this->db->query($q18)->row();
          $q19="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_melting_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$first_year[$i]' and Year(cs.c_date)='$split_year[0]' and cs.dept='$dept' and cs.item_type='D' and md.defect_name='PELTCH' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould19[]=$this->db->query($q19)->row();
          $q20="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_melting_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$first_year[$i]' and Year(cs.c_date)='$split_year[0]' and cs.dept='$dept' and cs.item_type='D' and md.defect_name='OTHERS(MELTING & POURING)' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould20[]=$this->db->query($q20)->row();
          
          $q21="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_fettling_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$first_year[$i]' and Year(cs.c_date)='$split_year[0]' and cs.dept='$dept' and cs.item_type='D' and md.defect_name='CHIP COLD' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould21[]=$this->db->query($q21)->row();
          $q22="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_fettling_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$first_year[$i]' and Year(cs.c_date)='$split_year[0]' and cs.dept='$dept' and cs.item_type='D' and md.defect_name='CHIP HOT' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould22[]=$this->db->query($q22)->row();
          $q23="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_fettling_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$first_year[$i]' and Year(cs.c_date)='$split_year[0]' and cs.dept='$dept' and cs.item_type='D' and md.defect_name='EXCESS FET.' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould23[]=$this->db->query($q23)->row();
          $q24="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_fettling_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$first_year[$i]' and Year(cs.c_date)='$split_year[0]' and cs.dept='$dept' and cs.item_type='D' and md.defect_name='OTHERS(KNOCKOUT & FETTLING)' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould24[]=$this->db->query($q24)->row();
          
          $q25="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_pattern_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$first_year[$i]' and Year(cs.c_date)='$split_year[0]' and cs.dept='$dept' and cs.item_type='D' and md.defect_name='X-JOINT' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould25[]=$this->db->query($q25)->row();
          $q26="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_pattern_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$first_year[$i]' and Year(cs.c_date)='$split_year[0]' and cs.dept='$dept' and cs.item_type='D' and md.defect_name='DIGGING IN' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould26[]=$this->db->query($q26)->row();
          $q27="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_pattern_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$first_year[$i]' and Year(cs.c_date)='$split_year[0]' and cs.dept='$dept' and cs.item_type='D' and md.defect_name='LINEAR GAUGE REJ.' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould27[]=$this->db->query($q27)->row();
          $q28="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_pattern_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$first_year[$i]' and Year(cs.c_date)='$split_year[0]' and cs.dept='$dept' and cs.item_type='D' and md.defect_name='OTHER(SIM GAP,P/M ETC.)' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould28[]=$this->db->query($q28)->row();
          $q29="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_pattern_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$first_year[$i]' and Year(cs.c_date)='$split_year[0]' and cs.dept='$dept' and cs.item_type='D' and md.defect_name='PPAP' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould29[]=$this->db->query($q29)->row();
        
        
        }
        
        for($j=0;$j<3;$j++)
        {
         $query2="SELECT SUM(total_checked) as total_checked,SUM(total_good) as total_good,SUM(total_rej) as total_rej,item,cust_name,"
        . "SUM(total_mould) as total_mould,SUM(total_pouring) as total_pouring,SUM(total_fettling) as total_fettling,SUM(total_pattern) as total_pattern,SUM(fr_cut) as fr_cut,"        
        . "CASE WHEN c_date IS NULL THEN $second_year[$j] ELSE $second_year[$j] END AS c_month,"
        . "CASE WHEN c_date IS NULL THEN $split_year[1] ELSE $split_year[1] END AS c_year,SUM(met_rej) as met_rej,"
        . "CASE WHEN id IS NULL THEN 0 ELSE GROUP_CONCAT(CONCAT('''', id, '''' )) END AS ids FROM `tbl_camshaft_scrap` "
        . " WHERE Month(c_date)='$second_year[$j]' and Year(c_date)='$split_year[1]' and dept='$dept' and `item_type`='D' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
        $first_result[]=$this->db->query($query2)->row();
        
        $qq1="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_moulding_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$second_year[$j]' and Year(cs.c_date)='$split_year[1]' and cs.dept='$dept' and cs.item_type='D' and md.defect_name='LOOSE SAND' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould1[]=$this->db->query($qq1)->row();
          $qq2="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_moulding_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$second_year[$j]' and Year(cs.c_date)='$split_year[1]' and cs.dept='$dept' and cs.item_type='D' and md.defect_name='PIN HOLE' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould2[]=$this->db->query($qq2)->row();
          $qq3="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_moulding_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$second_year[$j]' and Year(cs.c_date)='$split_year[1]' and cs.dept='$dept' and cs.item_type='D' and md.defect_name='GAS HOLE' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould3[]=$this->db->query($qq3)->row();
          $qq4="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_moulding_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$second_year[$j]' and Year(cs.c_date)='$split_year[1]' and cs.dept='$dept' and cs.item_type='D' and md.defect_name='GLUE' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould4[]=$this->db->query($qq4)->row();
          $qq5="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_moulding_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$second_year[$j]' and Year(cs.c_date)='$split_year[1]' and cs.dept='$dept' and cs.item_type='D' and md.defect_name='MOULD CRACK' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould5[]=$this->db->query($qq5)->row();
          $qq6="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_moulding_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$second_year[$j]' and Year(cs.c_date)='$split_year[1]' and cs.dept='$dept' and cs.item_type='D' and md.defect_name='CORE BROKEN' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould6[]=$this->db->query($qq6)->row();
          $qq7="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_moulding_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$second_year[$j]' and Year(cs.c_date)='$split_year[1]' and cs.dept='$dept' and cs.item_type='D' and md.defect_name='TIR REJECT' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould7[]=$this->db->query($qq7)->row();
          $qq8="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_moulding_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$second_year[$j]' and Year(cs.c_date)='$split_year[1]' and cs.dept='$dept' and cs.item_type='D' and md.defect_name='CHILL DEFECT' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould8[]=$this->db->query($qq8)->row();
          $qq9="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_moulding_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$second_year[$j]' and Year(cs.c_date)='$split_year[1]' and cs.dept='$dept' and cs.item_type='D' and md.defect_name='THICK FLASH' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould9[]=$this->db->query($qq9)->row();
          $qq10="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_moulding_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$second_year[$j]' and Year(cs.c_date)='$split_year[1]' and cs.dept='$dept' and cs.item_type='D' and md.defect_name='MOULD LEAK' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould10[]=$this->db->query($qq10)->row();
          $qq11="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_moulding_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$second_year[$j]' and Year(cs.c_date)='$split_year[1]' and cs.dept='$dept' and cs.item_type='D' and md.defect_name='EXTRA METAL' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould11[]=$this->db->query($qq11)->row();
          $qq12="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_moulding_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$second_year[$j]' and Year(cs.c_date)='$split_year[1]' and cs.dept='$dept' and cs.item_type='D' and md.defect_name='OTHERS(MOULDING)' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould12[]=$this->db->query($qq12)->row();
          
          $qq13="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_melting_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$second_year[$j]' and Year(cs.c_date)='$split_year[1]' and cs.dept='$dept' and cs.item_type='D' and md.defect_name='HOT TEAR' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould13[]=$this->db->query($qq13)->row();
          $qq14="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_melting_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$second_year[$j]' and Year(cs.c_date)='$split_year[1]' and cs.dept='$dept' and cs.item_type='D' and md.defect_name='COLD METAL' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould14[]=$this->db->query($qq14)->row();
          $qq15="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_melting_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$second_year[$j]' and Year(cs.c_date)='$split_year[1]' and cs.dept='$dept' and cs.item_type='D' and md.defect_name='LC' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould15[]=$this->db->query($qq15)->row();
          $qq16="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_melting_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$second_year[$j]' and Year(cs.c_date)='$split_year[1]' and cs.dept='$dept' and cs.item_type='D' and md.defect_name='SLAG' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould16[]=$this->db->query($qq16)->row();
          $qq17="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_melting_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$second_year[$j]' and Year(cs.c_date)='$split_year[1]' and cs.dept='$dept' and cs.item_type='D' and md.defect_name='RIPPLE' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould17[]=$this->db->query($qq17)->row();
          $qq18="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_melting_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$second_year[$j]' and Year(cs.c_date)='$split_year[1]' and cs.dept='$dept' and cs.item_type='D' and md.defect_name='SHORT POURED' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould18[]=$this->db->query($qq18)->row();
          $qq19="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_melting_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$second_year[$j]' and Year(cs.c_date)='$split_year[1]' and cs.dept='$dept' and cs.item_type='D' and md.defect_name='PELTCH' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould19[]=$this->db->query($qq19)->row();
          $qq20="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_melting_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$second_year[$j]' and Year(cs.c_date)='$split_year[1]' and cs.dept='$dept' and cs.item_type='D' and md.defect_name='OTHERS(MELTING & POURING)' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould20[]=$this->db->query($qq20)->row();
          
          $qq21="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_fettling_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$second_year[$j]' and Year(cs.c_date)='$split_year[1]' and cs.dept='$dept' and cs.item_type='D' and md.defect_name='CHIP COLD' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould21[]=$this->db->query($qq21)->row();
          $qq22="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_fettling_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$second_year[$j]' and Year(cs.c_date)='$split_year[1]' and cs.dept='$dept' and cs.item_type='D' and md.defect_name='CHIP HOT' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould22[]=$this->db->query($qq22)->row();
          $qq23="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_fettling_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$second_year[$j]' and Year(cs.c_date)='$split_year[1]' and cs.dept='$dept' and cs.item_type='D' and md.defect_name='EXCESS FET.' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould23[]=$this->db->query($qq23)->row();
          $qq24="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_fettling_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$second_year[$j]' and Year(cs.c_date)='$split_year[1]' and cs.dept='$dept' and cs.item_type='D' and md.defect_name='OTHERS(KNOCKOUT & FETTLING)' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould24[]=$this->db->query($qq24)->row();
          
          $qq25="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_pattern_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$second_year[$j]' and Year(cs.c_date)='$split_year[1]' and cs.dept='$dept' and cs.item_type='D' and md.defect_name='X-JOINT' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould25[]=$this->db->query($qq25)->row();
          $qq26="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_pattern_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$second_year[$j]' and Year(cs.c_date)='$split_year[1]' and cs.dept='$dept' and cs.item_type='D' and md.defect_name='DIGGING IN' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould26[]=$this->db->query($qq26)->row();
          $qq27="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_pattern_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$second_year[$j]' and Year(cs.c_date)='$split_year[1]' and cs.dept='$dept' and cs.item_type='D' and md.defect_name='LINEAR GAUGE REJ.' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould27[]=$this->db->query($qq27)->row();
          $qq28="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_pattern_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$second_year[$j]' and Year(cs.c_date)='$split_year[1]' and cs.dept='$dept' and cs.item_type='D' and md.defect_name='OTHER(SIM GAP,P/M ETC.)' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould28[]=$this->db->query($qq28)->row();
          $qq29="SELECT SUM(md.defects_sum) as defects_sum FROM `tbl_cam_pattern_deffects` md LEFT JOIN tbl_camshaft_scrap cs ON md.camshaft_scrap_id=cs.id WHERE Month(cs.c_date)='$second_year[$j]' and Year(cs.c_date)='$split_year[1]' and cs.dept='$dept' and cs.item_type='D' and md.defect_name='PPAP' and c_date NOT BETWEEN '$c_year-$c_months-01' and '$c_year-$c_months-31'";
          $mould29[]=$this->db->query($qq29)->row();
        }
        
        
        
        $result['data']=$first_result;
        $result['mould1']=$mould1;
        $result['mould2']=$mould2;
        $result['mould3']=$mould3;
        $result['mould4']=$mould4;
        $result['mould5']=$mould5;
        $result['mould6']=$mould6;
        $result['mould7']=$mould7;
        $result['mould8']=$mould8;
        $result['mould9']=$mould9;
        $result['mould10']=$mould10;
        $result['mould11']=$mould11;
        $result['mould12']=$mould12;
        
        $result['mould13']=$mould13;
        $result['mould14']=$mould14;
        $result['mould15']=$mould15;
        $result['mould16']=$mould16;
        $result['mould17']=$mould17;
        $result['mould18']=$mould18;
        $result['mould19']=$mould19;
        $result['mould20']=$mould20;
        
        $result['mould21']=$mould21;
        $result['mould22']=$mould22;
        $result['mould23']=$mould23;
        $result['mould24']=$mould24;
        
        $result['mould25']=$mould25;
        $result['mould26']=$mould26;
        $result['mould27']=$mould27;
        $result['mould28']=$mould28;
        $result['mould29']=$mould29;
        
        $this->load->view('pages/get_monthly_yearly_data', $result);
        }
        
        
//        echo '<pre>';
//        print_r($first_result);
    }
    
    public function camshaft_scrap_dash_page()
    {
      $result['s_date']=$this->input->post('select_date');
      $this->load->view('pages/camshaft_scrap_dash_page', $result);  
    }
    
    
    
    
    
    
    public function shaft_cell_rej_defects_report() {
        $data['template'] = 'shaft_cell_rej_defects_report';
        $data['title'] = 'Shaft Cell Defects Report';
        $this->layout_admin($data);
    }
    
    public function get_shaft_cell_data()
    {
      $s_date=$this->input->post('s_date');
      $query="select * from tbl_defects_main where c_date='$s_date'";
      $data['main_data']=$this->db->query($query)->result();
      
      $query2="select * from tbl_shaft_cell_rejection_defect where created_on='$s_date'";
      $data['defects_data']=$this->db->query($query2)->result();
      
      $query3="select defects_name,group_concat(`defects_val` separator ',') as `defects_val` from tbl_shaft_cell_rejection_defect where created_on='$s_date' group by defects_name order by id ASC";
      $data['def_data']=$this->db->query($query3)->result();
      
     
      
      
      
      $this->load->view('pages/get_shaft_cell_data', $data);  
    }
    
    
    public function mail_recieve_fun()
    {
        /* connect to gmail */
$hostname = '{imap.gmail.com:993/imap/ssl}INBOX';
$username = 'abhishekvaidya021@gmail.com';
$password = 'abhi@gmail@2008';

/* try to connect */
$inbox = imap_open($hostname,$username,$password) or die('Cannot connect to Gmail: ' . imap_last_error());

/* grab emails */
$emails = imap_search($inbox,'ALL');

/* if emails are returned, cycle through each... */
if($emails) {
	
	/* begin output var */
	$output = '';
	
	/* put the newest emails on top */
	rsort($emails);
	
	/* for every email... */
	foreach($emails as $email_number) {
		
		/* get information specific to this email */
		$overview = imap_fetch_overview($inbox,$email_number,0);
		$message = imap_fetchbody($inbox,$email_number,2);
		
		/* output the email header information */
		$output.= '<div class="toggler '.($overview[0]->seen ? 'read' : 'unread').'">';
		$output.= '<span class="subject">'.$overview[0]->subject.'</span> ';
		$output.= '<span class="from">'.$overview[0]->from.'</span>';
		$output.= '<span class="date">on '.$overview[0]->date.'</span>';
		$output.= '</div>';
		
		/* output the email body */
		$output.= '<div class="body">'.$message.'</div>';
	}
	
	echo $output;
} 

/* close the connection */
imap_close($inbox);

    }
    
    public function get_date_wise_calibration()
    {
    $date = new DateTime('now', new DateTimeZone('Asia/Kolkata'));
    $date1 = $date->format('Y-m-d');    
    $select_val=$this->input->post('this_val');
    $next_date=date('Y-m-d', strtotime(' +'.$select_val.' day'));   
        
    $query="select * from tbl_calibration where CALIBRATION_DUE_DATE>='$date1' and CALIBRATION_DUE_DATE<='$next_date' and Calibration_Status=''";
    $data['main_data']=$this->db->query($query)->result();
    $this->load->view('pages/get_date_wise_calibration', $data);
    
    }
    
    
    public function send_mail()
    {
        $date = new DateTime('now', new DateTimeZone('Asia/Kolkata'));
        $date1 = $date->format('Y-m-d');
        $two_days=date('Y-m-d', strtotime(' +2 day'));   
        $ten_days=date('Y-m-d', strtotime(' +10 day'));   
        $fifteen_days=date('Y-m-d', strtotime(' +15 day'));   
        
        $query="select * from tbl_calibration where CALIBRATION_DUE_DATE>='$date1' and CALIBRATION_DUE_DATE<='$two_days' and Calibration_Status=''";
        $result=$this->db->query($query)->result();
        
        $query1="select * from tbl_calibration where CALIBRATION_DUE_DATE>='$date1' and CALIBRATION_DUE_DATE<='$ten_days' and Calibration_Status=''";
        $result1=$this->db->query($query1)->result();
        
        $query2="select * from tbl_calibration where CALIBRATION_DUE_DATE>='$date1' and CALIBRATION_DUE_DATE<='$fifteen_days' and Calibration_Status=''";
        $result2=$this->db->query($query2)->result();


        $ci = get_instance();
        $ci->load->library('email');
        $config['smtp_timeout'] = "30";
        $config['protocol'] = "smtp";
        $config['smtp_host'] = "ssl://smtp.gmail.com";
        $config['smtp_port'] = "465";
        $config['smtp_user'] = "abhishek.vaidya@iping.in";
        $config['smtp_pass'] = "abhi@iping@2015";
        $config['charset'] = "utf-8";
        $config['mailtype'] = "html";
        $config['newline'] = "\r\n";

        $ci->email->initialize($config);

        $ci->email->from('abhishek.vaidya@iping.in', '');
        $list = array('sdsalunkhe@pfggears.com','mskhandait@pfggears.com','drmule@pfggears.com','amruta.nilawar@iping.in','abhishek.vaidya@iping.in');
        //$list = array('abhishek.vaidya@iping.in');

        $ci->email->to($list);
        //$ci->email->cc(); 
        $this->email->reply_to('');
        $ci->email->subject('GUAGE CALIBRATION STATUS - '.$date1);
        $msg1 = '<html><body>';
        $msg1 = '<h3>Hello All,<br>

Please find below the category wise Gauge Calibration Status as of '.$date1.'</h3>';
        
        $msg1 .= '<table width="100%"; rules="all" style="" cellpadding="10"><tr>
            
                        <th style=" border-top-color:  white; text-align:  center;" colspan="6"><img src="https://pfggears.com/images/PFG%20Logo.png" width="200" height="100"></th>
                            </tr></table>';
        $msg1 .= "<div style='box-shadow: 5px 5px 5px 5px gray;'>";
        $msg1 .= "<h3 style='color: red;'>List of Calibration due in 2 days:</h3><br>
            <table width='100%'; rules='all' style='border:1px solid #3A5896;' cellpadding='10'>
                    <tr style='background-color: lightcoral;'>
                        <th>#</th>
                        <th>GAUGE TYPE</th>
                        <th>MAKE</th>
                        <th>SIZE/RANGE</th>
                        <th>IDENTIFICATION NO</th>
                        <th>RCL ID</th>
                        <th>CALIBRATION FREQUENCY</th>
                        <th>CALIBRATION DATE</th>
                        <th>CALIBRATION DUE DATE</th>
                        <th>CELL</th>
                    </tr>";
        $i = 1;
        foreach ($result as $row) {
            
            $msg1 .= "<tr>
                    <td style='text-align:  center;'>$i</td>
                    <td style='text-align:  center;'>$row->GAUGE_TYPE</td>
                    <td style='text-align:  center;'>$row->MAKE</td>
                    <td style='text-align:  center;'>$row->SIZE_RANGE</td>
                    <td style='text-align:  center;'>$row->ID_NO</td>
                    <td style='text-align:  center;'>$row->RCL_ID</td>
                    <td style='text-align:  center;'>$row->CALIBRATION_FREQUENCY</td>
                    <td style='text-align:  center;'>$row->CALIBRATION_DATE</td>
                    <td style='text-align:  center;'>$row->CALIBRATION_DUE_DATE</td>
                    <td style='text-align:  center;'>$row->CELL</td>
                    </tr>";
            $i++;
        }
        $msg1 .= "</table>";
        
        // END Two
        
        
        // Ten
        
        $msg1 .= "<br><br><h3 style='color: blue;'>List of Calibration due in 10 days:</h3><br>
            <table width='100%'; rules='all' style='border:1px solid #3A5896;' cellpadding='10'>
                    <tr style='background-color: lightblue;'>
                        <th>#</th>
                       <th>GAUGE TYPE</th>
                        <th>MAKE</th>
                        <th>SIZE/RANGE</th>
                        <th>IDENTIFICATION NO</th>
                        <th>RCL ID</th>
                        <th>CALIBRATION FREQUENCY</th>
                        <th>CALIBRATION DATE</th>
                        <th>CALIBRATION DUE DATE</th>
                        <th>CELL</th>
                    </tr>";
        $i = 1;
        foreach ($result1 as $row) {
            
            $msg1 .= "<tr>
                    <td style='text-align:  center;'>$i</td>
                    <td style='text-align:  center;'>$row->GAUGE_TYPE</td>
                    <td style='text-align:  center;'>$row->MAKE</td>
                    <td style='text-align:  center;'>$row->SIZE_RANGE</td>
                    <td style='text-align:  center;'>$row->ID_NO</td>
                    <td style='text-align:  center;'>$row->RCL_ID</td>
                    <td style='text-align:  center;'>$row->CALIBRATION_FREQUENCY</td>
                    <td style='text-align:  center;'>$row->CALIBRATION_DATE</td>
                    <td style='text-align:  center;'>$row->CALIBRATION_DUE_DATE</td>
                    <td style='text-align:  center;'>$row->CELL</td>
                    </tr>";
            $i++;
        }
        $msg1 .= "</table>";
        
        // END Ten
        
        
        // fifteen
        
        $msg1 .= "<br><br><h3 style='color: green;'>List of Calibration due in 15 days:</h3><br>
            <table width='100%'; rules='all' style='border:1px solid #3A5896;' cellpadding='10'>
                    <tr style='background-color: lightgreen;'>
                        <th>#</th>
                       <th>GAUGE TYPE</th>
                        <th>MAKE</th>
                        <th>SIZE/RANGE</th>
                        <th>IDENTIFICATION NO</th>
                        <th>RCL ID</th>
                        <th>CALIBRATION FREQUENCY</th>
                        <th>CALIBRATION DATE</th>
                        <th>CALIBRATION DUE DATE</th>
                        <th>CELL</th>
                    </tr>";
        $i = 1;
        foreach ($result2 as $row) {
            
            $msg1 .= "<tr>
                    <td style='text-align:  center;'>$i</td>
                    <td style='text-align:  center;'>$row->GAUGE_TYPE</td>
                    <td style='text-align:  center;'>$row->MAKE</td>
                    <td style='text-align:  center;'>$row->SIZE_RANGE</td>
                    <td style='text-align:  center;'>$row->ID_NO</td>
                    <td style='text-align:  center;'>$row->RCL_ID</td>
                    <td style='text-align:  center;'>$row->CALIBRATION_FREQUENCY</td>
                    <td style='text-align:  center;'>$row->CALIBRATION_DATE</td>
                    <td style='text-align:  center;'>$row->CALIBRATION_DUE_DATE</td>
                    <td style='text-align:  center;'>$row->CELL</td>
                    </tr>";
            $i++;
        }
        $msg1 .= "</table>";
        
        // END fifteen
        $msg1 .= "</div><br><br>";
        $msg1 .= "<h4>Thanks & Regards,<br> 
PreciForge & Gears</h4>";
       
        
        
        
        $msg1 .= "</body></html>";
        $ci->email->message($msg1);
        $ci->email->send();
    }
    




}
