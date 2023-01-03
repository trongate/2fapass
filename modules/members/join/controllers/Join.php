<?php
class Join extends Trongate {

    private $template_to_use = 'public';
    private $force_double_opt_in = false;

    function __construct() {
        parent::__construct();
        $this->parent_module = 'members';
        $this->child_module = 'join';
    }

    function index() {
        $data = $this->_get_data_from_post();
        $data['form_location'] = BASE_URL.'members-join/submit_join';
        $data['view_module'] = 'members/join';
        $data['view_file'] = 'join';
        $this->template($this->template_to_use, $data);
    }

    function confirm_this_vibe() {
        $user_token = segment(3);

        if ($user_token == '') {
            redirect('members/ouch');
        } else {
            $params['user_token'] = $user_token;
            $sql = 'SELECT * FROM members WHERE user_token = :user_token AND confirmed = 0';
            $rows = $this->model->query_bind($sql, $params, 'object');

            if (isset($rows[0])) {
                //all is well activate this account
                $this->module('members');
                $update_id = $rows[0]->id;
                $data['confirmed'] = 1;
                $this->model->update($update_id, $data, 'members');
                $this->members->_in_you_go($update_id, true);

            } else {
                redirect('members/ouch');
            }

        }

    }

    function submit_join() {
        $this->validation_helper->set_rules('username', 'username', 'required|min_length[3]|max_length[50]|callback_username_check');
        $this->validation_helper->set_rules('first_name', 'first name', 'required|min_length[2]|max_length[65]');
        $this->validation_helper->set_rules('last_name', 'last name', 'required|max_length[75]');
        $this->validation_helper->set_rules('email_address', 'email address', 'required|valid_email|callback_email_check');
        
        $result = $this->validation_helper->run();

        if ($result == false) {
            $this->index();
        } else {
            $this->module('members');
            $data = $this->_get_data_from_post();
            $data['url_string'] = $this->members->_make_unique_url_string($data['username']);
            $data['date_joined'] = time();
            $data['code'] = make_rand_str(16);
            $data['password'] = '';
            $data['num_logins'] = 0;
            $data['last_login'] = 0;
            $data['trongate_user_id'] = $this->members->_create_new_trongate_user();
            $data['confirmed'] = ($this->force_double_opt_in == true ? 0 : 1);
            $data['user_token'] = ($this->force_double_opt_in == true ? make_rand_str(32) : '');
            $member_id = $this->model->insert($data, 'members');

            if ($this->force_double_opt_in == true) {
                //add your own code here for sending a confirmation email
                $activate_url = BASE_URL.'members-join/confirm_this_vibe/'.$data['user_token'];
                $member_obj = (object) $data;
                $this->members->_send_activate_account_email($member_obj, $activate_url);
                redirect('members/check_your_email');
            } else {
                $this->members->_in_you_go($member_id, true);
            }

        }
    }

    function username_check($username) {
        // Check if the username is formatted correctly
        $filtered_username = filter_name($username);

        if ($filtered_username !== $username) {
            $error_msg = 'The username contains characters that are not allowed.';
            return $error_msg;
        }

        // Make sure submitted username is available
        $params['username'] = $username;
        $sql = 'SELECT COUNT(*) as count FROM members WHERE username =:username';
        $rows = $this->model->query_bind($sql, $params, 'object');
        $count = $rows[0]->count ?? 0;

        if($count>0) {
            $error_msg = 'The submitted username is not available.';
            return $error_msg;        
        }

        return true;
    }

    function email_check($email_address) {
        // Make sure submitted email_address is available
        $params['email_address'] = $email_address;
        $sql = 'SELECT COUNT(*) as count FROM members WHERE email_address =:email_address';
        $rows = $this->model->query_bind($sql, $params, 'object');
        $count = $rows[0]->count ?? 0;

        if($count>0) {
            $error_msg = 'The submitted email address is not available.';
            return $error_msg;        
        }

        return true;
    }

    function _get_data_from_post() {
        $data['username'] = post('username', true);
        $data['first_name'] = post('first_name', true);
        $data['last_name'] = post('last_name', true);
        $data['email_address'] = post('email_address', true);    
        return $data;
    }

    function __destruct() {
        $this->parent_module = '';
        $this->child_module = '';
    }

}