<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Itemissue_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->current_session = $this->setting_model->getCurrentSession();
    }

    public function get($id = null)
    {
        $sql   = "SELECT item_issue.*,item.name as `item_name`,item.item_category_id,item_category.item_category ,staff.employee_id,staff.name as staff_name,staff.surname,roles.name FROM `item_issue` INNER JOIN item on item.id=item_issue.item_id INNER JOIN item_category on item_category.id=item.item_category_id INNER JOIN staff on staff.id=item_issue.issue_to INNER JOIN staff_roles on staff_roles.staff_id =staff.id INNER JOIN roles on roles.id= staff_roles.role_id";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    /**
     * This function is used to get issue item list
     * @param $id
     */
    public function getitemlist()
    {
        $class_section = $this->customlib->get_myClassSection();

        $this->datatables->select('item_issue.*, item.name as item_name, item.item_category_id, item_category.item_category, staff.employee_id, staff.name as staff_name, staff.surname, issueby.employee_id as issueby_employee_id, issueby.name as issueby_staff_name, issueby.surname as issueby_surname, roles.name, classes.class as class')
            ->from('item_issue')
            ->join('item', 'item.id = item_issue.item_id')
            ->join('item_category', 'item_category.id = item.item_category_id')
            ->join('staff', 'staff.id = item_issue.issue_to')
            ->join('staff as issueby', 'issueby.id = item_issue.issue_by')
            ->join('staff_roles', 'staff_roles.staff_id = staff.id')
            ->join('classes', 'classes.id = item_issue.class_id')
            ->join('roles', 'roles.id = staff_roles.role_id')
            ->orderable('item.id,item.name,item_category,issue_date,staff.name,issue_by,quantity,null')
            ->searchable('item.id,item.name,item_category,issue_date,staff.name,issue_by,item_issue.quantity,null');
        if ($class_section) {
            $class_ids = array_keys($class_section);
            $this->datatables->where_in('item_issue.class_id', $class_ids);
        }
        return $this->datatables->generate('json');
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
        $this->db->delete('item_issue');
        $message   = DELETE_RECORD_CONSTANT . " On item issue id " . $id;
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
            $this->db->where('id', $data['id']);
            $this->db->update('item_issue', $data);
            $message   = UPDATE_RECORD_CONSTANT . " On  item issue id " . $data['id'];
            $action    = "Update";
            $record_id = $data['id'];
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
        } else {
            $this->db->insert('item_issue', $data);
            $insert_id = $this->db->insert_id();
            $message   = INSERT_RECORD_CONSTANT . " On item issue id " . $insert_id;
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

    public function get_IssueInventoryReport($start_date, $end_date)
    {
        $condition = " and date_format(item_issue.issue_date,'%Y-%m-%d') between '" . $start_date . "' and '" . $end_date . "'";

        $sql = "SELECT item_issue.*,item.name as `item_name`,item.item_category_id,item_category.item_category ,staff.employee_id,staff.name as staff_name,staff.surname
        ,issued_by.employee_id as issued_by_employee_id,issued_by.name as issued_by_name,issued_by.surname as issued_by_surname ,roles.name FROM `item_issue` INNER JOIN item on item.id=item_issue.item_id 
        INNER JOIN item_category on item_category.id=item.item_category_id INNER JOIN staff on staff.id=item_issue.issue_to  INNER JOIN staff as issued_by on issued_by.id=item_issue.issue_by INNER JOIN staff_roles on staff_roles.staff_id =staff.id 
        INNER JOIN roles on roles.id= staff_roles.role_id where 1 " . $condition;
        $this->datatables->query($sql)
            ->orderable('item.name,item_category,issue_date,staff_name,issue_by,item_issue.quantity')
            ->searchable('item.name,item_category,issue_date,staff.name,issue_by,item_issue.quantity')
            ->query_where_enable(true);
        return $this->datatables->generate('json');
    }
}
