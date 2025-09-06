<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Assets extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        
        $this->load->library('Customlib');
        $this->config->load('app-config');
        $this->load->helper('form');
        $this->load->model('assets_model');
    }

    public function index()
    {
        if (!$this->rbac->hasPrivilege('assets', 'can_view')) {
            access_denied();
        }
        
        $this->session->set_userdata('top_menu', 'Administration Setup');
        $this->session->set_userdata('sub_menu', 'admin/assets');
        $data['title'] = 'Add Assets';
        $data['title_list'] = 'Assets List';

        $this->form_validation->set_rules('name', $this->lang->line('name'), 'trim|required|xss_clean');
        // $this->form_validation->set_rules('description', $this->lang->line('description'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('purchase_date', $this->lang->line('purchase_date'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('price', $this->lang->line('price'), 'trim|required|xss_clean');

        if ($this->form_validation->run() == false) {
            $data['assetslist'] = $this->assets_model->get_with_accumulated_depreciation();
            $this->load->view('layout/header');
            $this->load->view('admin/assets/assetsList', $data);
            $this->load->view('layout/footer');
        } else {
            // Get form data
            $price = $this->input->post('price') ? $this->input->post('price') : 0;
            $residu = $this->input->post('residu') ? $this->input->post('residu') : 0;
            $aging = $this->input->post('aging') ? $this->input->post('aging') : 0;
            $metode = $this->input->post('metode');
            
            // Calculate depreciation based on metode
            $depresiasi = 0;
            // If metode is "Garis Lurus" (value = 1) and aging > 0, calculate monthly depreciation
            if ($metode == '1' && !empty($aging) && is_numeric($aging) && $aging > 0) {
                // Calculate annual depreciation first, then divide by 12 for monthly depreciation
                $annual_depreciation = ($price - $residu) / $aging;
                $depresiasi = $annual_depreciation; // Store annual depreciation in database
            }
            
            // Handle purchase date conversion
            $purchase_date = $this->input->post('purchase_date');
            $purchase_date_formatted = '';
            if (!empty($purchase_date)) {
                $timestamp = $this->customlib->datetostrtotime($purchase_date);
                if ($timestamp !== false && $timestamp > 0) {
                    $purchase_date_formatted = date('Y-m-d', $timestamp);
                } else {
                    $purchase_date_formatted = date('Y-m-d'); // Use current date if conversion fails
                }
            } else {
                $purchase_date_formatted = date('Y-m-d'); // Use current date if empty
            }
            
            $data = array(
                'name' => $this->input->post('name'),
                'description' => $this->input->post('description'),
                'purchase_date' => $purchase_date_formatted,
                'category' => $this->input->post('category'),
                'price' => $price,
                'residu' => $residu ? $residu : 0,
                'depresiasi' => round($depresiasi, 2),
                'aging' => $aging,
                'metode' => $metode,
            );
            
            $this->assets_model->add($data);
            $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">'.$this->lang->line('success_message').'</div>');
            redirect('admin/assets/index');
        }
    }

    public function download($file)
    {
        $this->load->helper('download');
        $filepath = "./uploads/inventory_items/" . $this->uri->segment(6);
        $data     = file_get_contents($filepath);
        $name     = $this->uri->segment();
        force_download($name, $data);
    }

    public function delete($id)
    {
        if (!$this->rbac->hasPrivilege('assets', 'can_delete')) {
            access_denied();
        }
        
        $this->assets_model->remove($id);
        $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">'.$this->lang->line('delete_message').'</div>');
        redirect('admin/assets/index');
    }

    public function getAvailQuantity()
    {
        $item_id = $this->input->get('item_id');
        $data    = $this->item_model->getItemAvailable($item_id);

        $available = ($data['added_stock'] - $data['issued']);
        if ($available >= 0) {
            echo json_encode(array('available' => $available));
        } else {
            echo json_encode(array('available' => 0));
        }
    }

    public function handle_upload()
    {
        if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
            $allowedExts = array('jpg', 'jpeg', 'png');
            $temp        = explode(".", $_FILES["file"]["name"]);
            $extension   = end($temp);
            if ($_FILES["file"]["error"] > 0) {
                $error .= "Error opening the file<br />";
            }
            if ($_FILES["file"]["type"] != 'image/gif' &&
                $_FILES["file"]["type"] != 'image/jpeg' &&
                $_FILES["file"]["type"] != 'image/png') {

                $this->form_validation->set_message('handle_upload', $this->lang->line('file_type_not_allowed'));
                return false;
            }
            if (!in_array($extension, $allowedExts)) {

                $this->form_validation->set_message('handle_upload', $this->lang->line('extension_not_allowed'));
                return false;
            }
            if ($_FILES["file"]["size"] > 10240000) {

                $this->form_validation->set_message('handle_upload', $this->lang->line('file_size_shoud_be_less_than'));
                return false;
            }
            if ($error == "") {
                return true;
            }
        } else {
            return true;
        }
    }

    public function edit($id)
    {
        if (!$this->rbac->hasPrivilege('assets', 'can_edit')) {
            access_denied();
        }
        
        $data['title'] = 'Edit Assets';
        $data['id'] = $id;
        $asset = $this->assets_model->get_with_accumulated_depreciation($id);
        $data['asset'] = $asset;
        
        $this->form_validation->set_rules('name', $this->lang->line('name'), 'trim|required|xss_clean');
        // $this->form_validation->set_rules('description', $this->lang->line('description'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('purchase_date', $this->lang->line('purchase_date'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('price', $this->lang->line('price'), 'trim|required|xss_clean');

        if ($this->form_validation->run() == false) {
            $this->load->view('layout/header');
            $this->load->view('admin/assets/assetsEdit', $data);
            $this->load->view('layout/footer');
        } else {
            // Get form data
            $price = $this->input->post('price');
            $residu = $this->input->post('residu');
            $aging = $this->input->post('aging');
            $metode = $this->input->post('metode');
            
            // Calculate depreciation based on metode
            $depresiasi = 0;
            // If metode is "Garis Lurus" (value = 1) and aging > 0, calculate monthly depreciation
            if ($metode == '1' && !empty($aging) && is_numeric($aging) && $aging > 0) {
                // Calculate annual depreciation first, then divide by 12 for monthly depreciation
                $annual_depreciation = ($price - $residu) / $aging;
                $depresiasi = $annual_depreciation; // Store annual depreciation in database
            }
            
            // Handle purchase date conversion
            $purchase_date = $this->input->post('purchase_date');
            $purchase_date_formatted = '';
            if (!empty($purchase_date)) {
                $timestamp = $this->customlib->datetostrtotime($purchase_date);
                if ($timestamp !== false && $timestamp > 0) {
                    $purchase_date_formatted = date('Y-m-d', $timestamp);
                } else {
                    $purchase_date_formatted = date('Y-m-d'); // Use current date if conversion fails
                }
            } else {
                $purchase_date_formatted = date('Y-m-d'); // Use current date if empty
            }
            
            $data = array(
                'id' => $id,
                'name' => $this->input->post('name'),
                'description' => $this->input->post('description'),
                'purchase_date' => $purchase_date_formatted,
                'category' => $this->input->post('category'),
                'price' => $price,
                'residu' => $residu ? $residu : 0,
                'depresiasi' => round($depresiasi, 2),
                'aging' => $aging,
                'metode' => $metode,
            );
            
            $this->assets_model->add($data);
            $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">'.$this->lang->line('update_message').'</div>');
            redirect('admin/assets/index');
        }
    }
}