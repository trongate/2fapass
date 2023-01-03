<?php
class Members extends Trongate {

    private $default_limit = 20;
    private $per_page_options = array(10, 20, 50, 100);
    private $user_level_title = 'member';
    private $user_level_id = 2;
    private $template_to_use = 'public';

    function create() {
        $this->module('trongate_security');
        $this->trongate_security->_make_sure_allowed();

        $update_id = (int) segment(3);
        $submit = post('submit');

        if (($submit == '') && ($update_id>0)) {
            $data = $this->_get_data_from_db($update_id);
        } else {
            $data = $this->_get_data_from_post();
        }

        if ($update_id>0) {
            $data['headline'] = 'Update Member Record';
            $data['cancel_url'] = BASE_URL.'members/show/'.$update_id;
        } else {
            $data['headline'] = 'Create New Member Record';
            $data['cancel_url'] = BASE_URL.'members/manage';
        }

        $data['form_location'] = BASE_URL.'members/submit/'.$update_id;
        $data['view_file'] = 'create';
        $this->template('admin', $data);
    }

    function manage() {
        $this->module('trongate_security');
        $this->trongate_security->_make_sure_allowed();

        if (segment(4) !== '') {
            $data['headline'] = 'Search Results';
            $searchphrase = trim($_GET['searchphrase']);
            $params['username'] = '%'.$searchphrase.'%';
            $params['first_name'] = '%'.$searchphrase.'%';
            $params['last_name'] = '%'.$searchphrase.'%';
            $params['email_address'] = '%'.$searchphrase.'%';
            $sql = 'SELECT * FROM members
            WHERE username LIKE :username
            OR first_name LIKE :first_name
            OR last_name LIKE :last_name
            OR email_address LIKE :email_address
            ORDER BY username';
            $all_rows = $this->model->query_bind($sql, $params, 'object');
        } else {
            $data['headline'] = 'Manage Members';
            $all_rows = $this->model->get('username');
        }

        $pagination_data['total_rows'] = count($all_rows);
        $pagination_data['page_num_segment'] = 3;
        $pagination_data['limit'] = $this->_get_limit();
        $pagination_data['pagination_root'] = 'members/manage';
        $pagination_data['record_name_plural'] = 'members';
        $pagination_data['include_showing_statement'] = true;
        $data['pagination_data'] = $pagination_data;

        $data['rows'] = $this->_reduce_rows($all_rows);
        $data['selected_per_page'] = $this->_get_selected_per_page();
        $data['per_page_options'] = $this->per_page_options;
        $data['view_module'] = 'members';
        $data['view_file'] = 'manage';
        $this->template('admin', $data);
    }

    function show() {
        $this->module('trongate_security');
        $token = $this->trongate_security->_make_sure_allowed();
        $update_id = (int) segment(3);

        if ($update_id == 0) {
            redirect('members/manage');
        }

        $data = $this->_get_data_from_db($update_id);
        $data['confirmed'] = ($data['confirmed'] == 1 ? 'yes' : 'no');
        $data['token'] = $token;

        if ($data == false) {
            redirect('members/manage');
        } else {
            $data['update_id'] = $update_id;
            $data['headline'] = 'Member Information';
            $data['view_file'] = 'show';
            $this->template('admin', $data);
        }
    }

    function ouch() {
        // Confirmation link has expired or user token valid
        $data['view_module'] = 'members';
        $data['view_file'] = 'ouch';
        $this->template($this->template_to_use, $data);        
    }

    function check_your_email() {
        if (segment(3) == 'reset') {
            $data['info'] = 'An email has been sent to you with a confirmation link to reset your password.';
        } else {
            $data['info'] = 'An email has been sent to you with a confirmation link to activate your account.';
        }

        $data['view_module'] = 'members';
        $data['view_file'] = 'check_your_email';
        $this->template($this->template_to_use, $data);
    }

    function join() {
        redirect('members-join');
    }

    function forgot_password() {
        redirect('members-account/forgot_password'); 
    }

    function login() {
        redirect('members-account/login');
    }

    function _make_sure_allowed() {
      // Check if the user is logged in
      $this->module('trongate_tokens');
      $token = $this->trongate_tokens->_attempt_get_valid_token();
      $member_obj = $this->_get_member_obj($token);

      if (!$member_obj) {
        // If the user is not logged in, redirect to login page
        redirect('members/login');
      } else {
        // Return the token if any member is allowed
        return $token;
      }
    }

    function logout() {
        $this->module('trongate_tokens');
        $this->trongate_tokens->_destroy();
        redirect('members/login');
    }

    function _get_member_obj($token=null) {
      $this->module('trongate_tokens');
      $token = $token ?: $this->trongate_tokens->_attempt_get_valid_token();

      if (!$token) return false;

      $params['token'] = $token;
      $sql = 'SELECT members.* 
                   FROM members 
                   JOIN trongate_tokens ON members.trongate_user_id = trongate_tokens.user_id 
                   WHERE trongate_tokens.token = :token';
      $rows = $this->model->query_bind($sql, $params, 'object');
      return count($rows) ? $rows[0] : false;
    }

    function _reduce_rows($all_rows) {
        $rows = [];
        $start_index = $this->_get_offset();
        $limit = $this->_get_limit();
        $end_index = $start_index + $limit;

        $count = -1;
        foreach ($all_rows as $row) {
            $count++;
            if (($count>=$start_index) && ($count<$end_index)) {
                $row->confirmed = ($row->confirmed == 1 ? 'yes' : 'no');
                $rows[] = $row;
            }
        }

        return $rows;
    }

    function submit() {
        $this->module('trongate_security');
        $this->trongate_security->_make_sure_allowed();

        $this->validation_helper->set_rules('username', 'username', 'required|min_length[3]|max_length[50]|callback_username_check');
        $this->validation_helper->set_rules('first_name', 'first name', 'required|min_length[2]|max_length[65]');
        $this->validation_helper->set_rules('last_name', 'last name', 'required|max_length[75]');
        $this->validation_helper->set_rules('email_address', 'email address', 'required|valid_email|callback_email_check');

        $result = $this->validation_helper->run();

        if ($result == true) {
            $update_id = (int) segment(3);
            $data = $this->_get_data_from_post();
            $data['url_string'] = $this->_make_unique_url_string($data['username'], $update_id);
            $data['confirmed'] = ($data['confirmed'] == 1 ? 1 : 0);
            
            if ($update_id>0) {
                //update an existing record
                $this->model->update($update_id, $data, 'members');
                $flash_msg = 'The record was successfully updated';
            } else {
                //insert the new record
                $data['date_joined'] = time();
                $data['code'] = make_rand_str(16);
                $data['password'] = '';
                $data['num_logins'] = 0;
                $data['last_login'] = 0;
                $data['trongate_user_id'] = $this->_create_new_trongate_user();
                $data['user_token'] = '';
                $update_id = $this->model->insert($data, 'members');
                $flash_msg = 'The record was successfully created';
            }

            set_flashdata($flash_msg);
            redirect('members/show/'.$update_id);

        } else {
            //form submission error
            $this->create();
        }

    }

    function _in_you_go($member_id, $remember=true) {
      $this->module('trongate_tokens');

      $member_obj = $this->model->get_where($member_id, 'members');

      //update num logins
      $num_logins = $member_obj->num_logins;
      $data = [
        'num_logins' => $num_logins + 1,
        'last_login' => time(),
        'user_token' => '',
      ];
      $this->model->update($member_id, $data, 'members');

      $token_data = [
        'user_id' => $member_obj->trongate_user_id,
      ];

      if ($remember) {
        //set token for 60 days (cookie)
        $thirty_days = 86400 * 60;
        $nowtime = time();
        $token_data['expiry_date'] = $nowtime + $thirty_days;
        $token_data['set_cookie'] = true;
        $this->trongate_tokens->_generate_token($token_data);
      } else {
        //set short term token (session)
        $_SESSION['trongatetoken'] = $this->trongate_tokens->_generate_token($token_data);
      }

      $update_password_url = 'members-account/update_password';
      $account_url = 'members-account/your_account';
      redirect($member_obj->password == '' ? $update_password_url : $account_url);
    }

    function _send_activate_account_email($member_obj, $activate_url) {
        //send an email inviting the user to goto the $reset url
        $data['subject'] = 'Confirm Your Account';
        $data['target_name'] = $member_obj->first_name.' '.$member_obj->last_name;
        $data['member_obj'] = $member_obj;
        $data['activate_url'] = $activate_url;
        $data['target_email'] = $member_obj->email_address;
        $data['view_module'] = 'members';
        $data['msg_html'] = $this->view('msg_confirm_account', $data, true);
        $msg_plain = str_replace('</p>', '\\n\\n', $data['msg_html']);
        $data['msg_plain'] = strip_tags($msg_plain);
        //add your own code below this line for sending email
    }

    function _send_password_reset_email($member_obj, $reset_url) {
        //send an email inviting the user to goto the $reset url
        $data['subject'] = 'Password Reset';
        $data['target_name'] = $member_obj->first_name.' '.$member_obj->last_name;
        $data['member_obj'] = $member_obj;
        $data['reset_url'] = $reset_url;
        $data['target_email'] = $member_obj->email_address;
        $data['view_module'] = 'members';
        $data['msg_html'] = $this->view('msg_password_reset_invite', $data, true);
        $msg_plain = str_replace('</p>', '\\n\\n', $data['msg_html']);
        $data['msg_plain'] = strip_tags($msg_plain);
        //add your own code below this line for sending email
    }

    function _create_new_trongate_user() {
        $data['user_level_id'] = $this->user_level_id;
        $data['code'] = make_rand_str(32);
        $trongate_user_id = $this->model->insert($data, 'trongate_users');
        return $trongate_user_id;
    }

    function submit_delete() {
        $this->module('trongate_security');
        $this->trongate_security->_make_sure_allowed();

        $submit = post('submit');
        $params['update_id'] = (int) segment(3);

        if (($submit == 'Yes - Delete Now') && ($params['update_id']>0)) {
            //delete all of the comments associated with this record
            $sql = 'delete from trongate_comments where target_table = :module and update_id = :update_id';
            $params['module'] = 'members';
            $this->model->query_bind($sql, $params);

            //delete the record
            $this->model->delete($params['update_id'], 'members');

            //set the flashdata
            $flash_msg = 'The record was successfully deleted';
            set_flashdata($flash_msg);

            //redirect to the manage page
            redirect('members/manage');
        }
    }

    function _get_limit() {
        if (isset($_SESSION['selected_per_page'])) {
            $limit = $this->per_page_options[$_SESSION['selected_per_page']];
        } else {
            $limit = $this->default_limit;
        }

        return $limit;
    }

    function _get_offset() {
        $page_num = (int) segment(3);

        if ($page_num>1) {
            $offset = ($page_num-1)*$this->_get_limit();
        } else {
            $offset = 0;
        }

        return $offset;
    }

    function _get_selected_per_page() {
        $selected_per_page = (isset($_SESSION['selected_per_page'])) ? $_SESSION['selected_per_page'] : 1;
        return $selected_per_page;
    }

    function set_per_page($selected_index) {
        $this->module('trongate_security');
        $this->trongate_security->_make_sure_allowed();

        if (!is_numeric($selected_index)) {
            $selected_index = $this->per_page_options[1];
        }

        $_SESSION['selected_per_page'] = $selected_index;
        redirect('members/manage');
    }

    function _make_unique_url_string($username, $update_id=0) {
        $url_string = url_title($username);

        //is this a unique url_string?
        $params['url_string'] = $url_string;
        $sql = 'SELECT COUNT(*) as count FROM members WHERE url_string =:url_string';

        if ($update_id>0) {
            $params['update_id'] = $update_id;
            $sql.= ' AND id !=:update_id';
        }

        $rows = $this->model->query_bind($sql, $params, 'object');
        $count = $rows[0]->count ?? 0;

        if($count>0) {
            $url_string.= '-'.make_rand_str(5);
        }

        $url_string = strtolower($url_string);
        return $url_string;
    }

    function _get_data_from_db($update_id) {
        $record_obj = $this->model->get_where($update_id, 'members');

        if ($record_obj == false) {
            $this->template('error_404');
            die();
        } else {
            $data = (array) $record_obj;
            return $data;        
        }
    }

    function _get_data_from_post() {
        $data['username'] = post('username', true);
        $data['first_name'] = post('first_name', true);
        $data['last_name'] = post('last_name', true);
        $data['email_address'] = post('email_address', true);
        $data['confirmed'] = (int) post('confirmed', true);
        return $data;
    }

    function username_check($username) {
        // Check if the username is formatted correctly
        $filtered_username = filter_name($username);

        if ($filtered_username !== $username) {
            $error_msg = 'The username contains characters that are not allowed.';
            return $error_msg;
        }

        // Make sure submitted username is available
        $update_id = (int) segment(3);
        $params['username'] = $username;
        $sql = 'SELECT COUNT(*) as count FROM members WHERE username =:username';

        if ($update_id>0) {
            $params['update_id'] = $update_id;
            $sql.= ' AND id !=:update_id';
        }

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
        $update_id = (int) segment(3);
        $params['email_address'] = $email_address;
        $sql = 'SELECT COUNT(*) as count FROM members WHERE email_address =:email_address';

        if ($update_id>0) {
            $params['update_id'] = $update_id;
            $sql.= ' AND id !=:update_id';
        }

        $rows = $this->model->query_bind($sql, $params, 'object');
        $count = $rows[0]->count ?? 0;

        if($count>0) {
            $error_msg = 'The submitted email address is not available.';
            return $error_msg;        
        }

        return true;
    }

}