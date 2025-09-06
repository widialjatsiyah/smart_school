<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Assets_model extends MY_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->current_session = $this->setting_model->getCurrentSession();
    }

    public function get($id = null)
    {   
        $this->db->select('*');
        $this->db->from('assets');
        
        if ($id != null) {
            $this->db->where('id', $id);
            $query = $this->db->get();
            return $query->row_array();
        } else {
            $this->db->order_by('id');
            $query = $this->db->get();
            return $query->result_array();
        }
    }

    /**
     * This function will delete the record based on the id
     * @param $id
     */
    public function remove($id)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where('id', $id);
        $this->db->delete('assets');
        $message   = DELETE_RECORD_CONSTANT . " On assets id " . $id;
        $action    = "Delete";
        $record_id = $id;
        $this->log($message, $record_id, $action);
        //======================Code End==============================
        $this->db->trans_complete(); # Completing transaction
        /* Optional */
        if ($this->db->trans_status() === false) {
            # Something went wrong.
            $this->db->trans_rollback();
            return false;
        } else {
            //return $return_value;
        }
    }

    /**
     * This function will take the post data passed from the controller
     * If id is present, then it will do an update
     * else an insert. One function doing both add and edit.
     * @param $data
     */
    public function add($data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        if (isset($data['id'])) {
            $id = $data['id'];
            unset($data['id']); // Remove id from data to update
            $this->db->where('id', $id);
            $this->db->update('assets', $data);
            $message   = UPDATE_RECORD_CONSTANT . " On  assets id " . $id;
            $action    = "Update";
            $record_id = $id;
            $this->log($message, $record_id, $action);
            //======================Code End==============================

            $this->db->trans_complete(); # Completing transaction
            /* Optional */

            if ($this->db->trans_status() === false) {
                # Something went wrong.
                $this->db->trans_rollback();
                return false;
            } else {
                //return $return_value;
            }
            return $id; // Return the id of updated record
        } else {
            $this->db->insert('assets', $data);
            $insert_id = $this->db->insert_id();
            $message   = INSERT_RECORD_CONSTANT . " On assets id " . $insert_id;
            $action    = "Insert";
            $record_id = $insert_id;
            $this->log($message, $record_id, $action);
            //======================Code End==============================

            $this->db->trans_complete(); # Completing transaction
            /* Optional */

            if ($this->db->trans_status() === false) {
                # Something went wrong.
                $this->db->trans_rollback();
                return false;
            } else {
                //return $return_value;
            }
            return $insert_id;
        }
    }

    /**
     * Calculate accumulated depreciation for an asset (monthly calculation)
     * @param $asset Asset data array
     * @param $months Number of months since purchase (optional)
     * @return float Accumulated depreciation value
     */
    public function calculate_accumulated_depreciation($asset, $months = null)
    {
        // Check if purchase_date is valid
        if (empty($asset['purchase_date']) || $asset['purchase_date'] == '0000-00-00' || $asset['purchase_date'] == '1970-01-01') {
            return 0;
        }
        
        // Only calculate for straight line method (metode = 1)
        if ($asset['metode'] == 1) {
            // Annual depreciation
            $annual_depreciation = $asset['depresiasi'];
            
            // If no specific months provided, calculate based on purchase date to current date
            if ($months === null) {
                $purchase_date = new DateTime($asset['purchase_date']);
                $current_date = new DateTime();

                // If purchase_date is in the future, depreciation is 0
                if ($purchase_date > $current_date) {
                    $total_months = 0;
                    
                } else {
                    $interval = $purchase_date->diff($current_date);
                    $total_months = ($interval->y * 12) + $interval->m;
                    // Make sure we don't have negative months
                    // var_dump($purchase_date, $purchase_date->diff($current_date)->m);
                    if ($total_months < 0) {
                        $total_months = 0;
                    }
                }
            } else {
                $total_months = $months;
            }
           
            // die();
            // Monthly depreciation
            $monthly_depreciation = $annual_depreciation / 12;
            // Calculate accumulated depreciation
            $accumulated_depreciation = $monthly_depreciation * $total_months;
            
            // Ensure accumulated depreciation doesn't exceed (price - residu)
            $max_depreciation = $asset['price'] - $asset['residu'];
            if ($accumulated_depreciation > $max_depreciation) {
                $accumulated_depreciation = $max_depreciation;
            }
            
            // Ensure we don't return negative values
            if ($accumulated_depreciation < 0) {
                $accumulated_depreciation = 0;
            }
            
            return round($accumulated_depreciation, 2);
        }
        
        // For no depreciation method (metode = 0), accumulated depreciation is 0
        return 0;
    }

    /**
     * Get assets with accumulated depreciation
     * @param $id (optional) specific asset id
     * @return array Assets with accumulated depreciation
     */
    public function get_with_accumulated_depreciation($id = null)
    {
        $assets = $this->get($id);
        
        if ($id != null) {
            // Single asset
            if (!empty($assets)) {
                $assets['accumulated_depreciation'] = $this->calculate_accumulated_depreciation($assets);
                $assets['current_book_value'] = $assets['price'] - $assets['accumulated_depreciation'];
            }
            return $assets;
        } else {
            // Multiple assets
            if (!empty($assets)) {
                foreach ($assets as &$asset) {
                    $asset['accumulated_depreciation'] = $this->calculate_accumulated_depreciation($asset);
                    $asset['current_book_value'] = $asset['price'] - $asset['accumulated_depreciation'];
                }
            }
            return $assets;
        }
    }
}