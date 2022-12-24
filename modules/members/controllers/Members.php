<?php
class Members extends Trongate {

    private $default_limit = 20;
    private $per_page_options = array(10, 20, 50, 100);
    private $user_level_title = 'member';
    private $user_level_id = 2;
    private $template_to_use = 'public';
    private $force_double_opt_in = false;

    /*
        PLEASE NOTE:
        The following two methods will require modifications to send emails:

            1). _send_activate_account_email()

            2). _send_password_reset_email()

        it's for you to decide how you'd like to send emails
    */

    function auto_login() {
        $this->_in_you_go(1, true);
    }

    function autocreate_account() {

        //create a new trongate_user record
        $data['code'] = make_rand_str();
        $data['user_level_id'] = 2;
        $trongate_user_id = $this->model->insert($data, 'trongate_users');
        unset($data);

        //create new member record
        $data['username'] = 'Davcon';
        $data['first_name'] = 'David';
        $data['last_name'] = 'Connelly';
        $data['email_address'] = 'david.webguy@gmail.com';
        $data['date_joined'] = time();
        $data['code'] = make_rand_str();
        $data['num_logins'] = 0;
        $data['trongate_user_id'] = $trongate_user_id;
        $data['confirmed'] = 1;
        $data['user_token'] = '';
        $data['password'] = '';
        $member_id = $this->model->insert($data, 'members');
        $this->_in_you_go($member_id, true);
    }

    function login() {
        $this->module('trongate_tokens');
        $this->trongate_tokens->_destroy();
        $data = $this->_get_login_data_from_post();
        $this->view('login', $data);
    }

    function join() {
        $data = $this->_get_data_from_post();
        unset($data['confirmed']);
        $data['form_location'] = str_replace('/join', '/submit_join', current_url());
        $data['view_module'] = 'members';
        $data['view_file'] = 'join';
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

    function init_reset_password() {
        $user_token = segment(3);
        if (strlen($user_token) !== 32) {
            redirect('members/ouch');
        } else {

            $member_obj = $this->model->get_one_where('user_token', $user_token);

            if ($member_obj == false) {
                redirect('members/ouch');
            } else {
                $member_id = $member_obj->id;
                $data['password'] = '';
                $this->model->update($member_id, $data, 'members');
                $this->_in_you_go($member_id, true);
            }


        }
    }

    function update_password() {
        $member_obj = $this->_get_member_obj();
        if ($member_obj == false) {
            redirect('members/login');
        }

        $password = $member_obj->password;
        if ($password == '') {
            $data['headline'] = 'Please set password';
        } else {
            $data['headline'] = 'Update password';
            $data['cancel_url'] = BASE_URL.'members/your_account/'.$member_obj->code;
        }

        $data['form_location'] = str_replace('/update', '/submit_update', current_url());
        $data['view_file'] = 'update_password';
        $this->template('bootstrappy', $data);        
    }

    function _make_sure_allowed($scenario=null, $params=null) {

        $this->module('trongate_tokens');
        $token = $this->trongate_tokens->_attempt_get_valid_token();

        if (!isset($scenario)) {
            //any member allowed
            $member_obj = $this->_get_member_obj($token);
            
            if ($member_obj == false) {
                redirect('members/login');
            } else {
                return $token;
            }

        }

    }

    function your_account() {
        $token = $this->_make_sure_allowed();
        $member_obj = $this->_get_member_obj($token);
        $data = (array) $member_obj;
        $data['view_module'] = 'members';
        $data['view_file'] = 'your_account';
        $this->template('bootstrappy', $data);        
    }

    function update() {
        $token = $this->_make_sure_allowed();
        $member_obj = $this->_get_member_obj($token);
        $data = (array) $member_obj;
        $data['form_location'] = str_replace('/update', '/submit_update_details', current_url());
        $data['view_module'] = 'members';
        $data['view_file'] = 'update_account';
        $this->template('bootstrappy', $data);        
    }

    function forgot_password() {
        $data['view_module'] = 'members';
        $data['view_file'] = 'forgot_password';
        $this->template($this->template_to_use, $data);  
    }

    function logout() {
        $this->module('trongate_tokens');
        $this->trongate_tokens->_destroy();
        redirect('members/login');
    }

    function _get_member_obj($token=null) {
        //return either false or the obj for logged in member
        $member_obj = false;

        if (!isset($token)) {
            //attempt to get trongate token
            $this->module('trongate_tokens');
            $token = $this->trongate_tokens->_attempt_get_valid_token();
        }

        if ($token == false) {
            return $member_obj;
        } else {

            $sql = 'SELECT
                    members.*
                    FROM
                    members
                    JOIN trongate_tokens
                    ON members.trongate_user_id = trongate_tokens.user_id
                    WHERE trongate_tokens.token = :token';

            $params['token'] = $token;
            $rows = $this->model->query_bind($sql, $params, 'object');

            if (count($rows) == 0) {
                //no record found
                return $member_obj;
            } else {
                //get the id from the rows
                $member_obj = $rows[0];
                $member_obj->token = $token;
                return $member_obj;
            }

        }

    }

    function confirm_this_vibe() {
        $user_token = segment(3);

        if ($user_token == '') {
            redirect('members/ouch');
        } else {
            $params['user_token'] = $user_token;
            $sql = 'select * from members where user_token = :user_token and confirmed = 0';
            $rows = $this->model->query_bind($sql, $params, 'object');

            if (isset($rows[0])) {
                //all is well activate this account
                $update_id = $rows[0]->id;
                $data['confirmed'] = 1;
                $this->model->update($update_id, $data, 'members');
                $this->_in_you_go($update_id, true);

            } else {
                redirect('members/ouch');
            }

        }

    }

    function ouch() {
        $data['view_module'] = 'members';
        $data['view_file'] = 'ouch';
        $this->template($this->template_to_use, $data);        
    }

    function create() {
        $this->module('trongate_security');
        $this->trongate_security->_make_sure_allowed();

        $update_id = segment(3);
        $submit = post('submit');

        if (($submit == '') && (is_numeric($update_id))) {
            $data = $this->_get_data_from_db($update_id);
        } else {
            $data = $this->_get_data_from_post();
        }

        if (is_numeric($update_id)) {
            $data['headline'] = 'Update Member Record';
            $data['cancel_url'] = BASE_URL.'members/show/'.$update_id;
        } else {
            $data['headline'] = 'Create New Member Record';
            $data['cancel_url'] = BASE_URL.'members/manage';
        }

        $data['form_location'] = BASE_URL.'members/submit/'.$update_id;
        $data['view_file'] = 'create';
        $this->template('bootstrappy', $data);
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
            $sql = 'select * from members
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
        $this->template('bootstrappy', $data);
    }

    function show() {
        $this->module('trongate_security');
        $token = $this->trongate_security->_make_sure_allowed();
        $update_id = segment(3);

        if ((!is_numeric($update_id)) && ($update_id != '')) {
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
            $this->template('bootstrappy', $data);
        }
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

        $submit = post('submit', true);

        if ($submit == 'Submit') {

            $this->validation_helper->set_rules('username', 'Username', 'required|min_length[2]|max_length[55]|callback_username_check');
            $this->validation_helper->set_rules('first_name', 'First Name', 'required|min_length[2]|max_length[65]');
            $this->validation_helper->set_rules('last_name', 'Last Name', 'required|min_length[1]|max_length[75]');
            $this->validation_helper->set_rules('email_address', 'Email Address', 'required|min_length[7]|max_length[255]|valid_email');

            $result = $this->validation_helper->run();

            if ($result == true) {

                $update_id = segment(3);
                $data = $this->_get_data_from_post();
                $data['url_string'] = strtolower(url_title($data['username']));
                $data['confirmed'] = ($data['confirmed'] == 1 ? 1 : 0);

                if (is_numeric($update_id)) {
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

    }

    function submit_join() {
        $submit = post('submit');

        if ($submit == 'Submit') {
            $this->validation_helper->set_rules('username', 'username', 'required|min_length[3]|max_length[65]|callback_username_check');
            $this->validation_helper->set_rules('first_name', 'first name', 'required|min_length[2]|max_length[65]');
            $this->validation_helper->set_rules('last_name', 'last name', 'required|max_length[75]');
            $this->validation_helper->set_rules('email_address', 'email address', 'valid_email');

            $result = $this->validation_helper->run();
            $update_id = segment(3);

            if ($result == false) {
                $this->join();
            } else {

                $data = $this->_get_data_from_post();
                $data['url_string'] = strtolower(url_title($data['username']));
                $data['date_joined'] = time();
                $data['code'] = make_rand_str(16);
                $data['password'] = '';
                $data['num_logins'] = 0;
                $data['last_login'] = 0;
                $data['trongate_user_id'] = $this->_create_new_trongate_user();
                $data['confirmed'] = ($this->force_double_opt_in == true ? 0 : 1);
                $data['user_token'] = ($this->force_double_opt_in == true ? make_rand_str(32) : '');
                $member_id = $this->model->insert($data, 'members');

                if ($this->force_double_opt_in == true) {
                    //add your own code here for sending a confirmation email
                    $activate_url = BASE_URL.'members/confirm_this_vibe/'.$data['user_token'];
                    $member_obj = (object) $data;
                    $this->_send_activate_account_email($member_obj, $activate_url);
                    redirect('members/check_your_email');
                } else {
                    $this->_in_you_go($member_id, true);
                }

            }
        }
    }

    function submit_login() {
        $submit = post('submit', true);

        if ($submit == 'Login') {

            $this->validation_helper->set_rules('username', 'Username', 'required|callback_login_check');
            $this->validation_helper->set_rules('password', 'Password', 'required|min_length[5]');
            $result = $this->validation_helper->run();

            if ($result == true) {
                $params['username'] = post('username');
                $params['email_address'] = post('username');
                $sql = 'select * from members where username =:username or email_address =:email_address';
                $rows = $this->model->query_bind($sql, $params, 'object');
                $member_obj = $rows[0];
                $member_id = $member_obj->id;
                $trongate_user_id = $member_obj->trongate_user_id;
                $remember = post('remember');

                if ($remember == 1) {
                    $remember = true;
                } else {
                    $remember = false;
                }

                $this->_in_you_go($member_id, $remember);

            } else {
                $this->login();
            }
        }
    }

    function submit_update_details() {
        $token = $this->_make_sure_allowed();
        $member_obj = $this->_get_member_obj($token);
        $update_id = $member_obj->id;

        $submit = post('submit', true);

        if ($submit == 'Submit') {

            $this->validation_helper->set_rules('username', 'Username', 'required|min_length[2]|max_length[55]|callback_username_check');
            $this->validation_helper->set_rules('first_name', 'First Name', 'required|min_length[2]|max_length[65]');
            $this->validation_helper->set_rules('last_name', 'Last Name', 'required|min_length[1]|max_length[75]');
            $this->validation_helper->set_rules('email_address', 'Email Address', 'required|min_length[7]|max_length[255]|valid_email');

            $result = $this->validation_helper->run();

            if ($result == true) {

                $data = $this->_get_data_from_post();
                $data['url_string'] = strtolower(url_title($data['username']));
                unset($data['confirmed']);

                //update an existing record
                $this->model->update($update_id, $data, 'members');
                $flash_msg = 'Your account details have been successfully updated';
                
                set_flashdata($flash_msg);
                redirect('members/your_account');

            } else {
                //form submission error
                $this->update();
            }

        }

    }

    function submit_forgot_password() {

        $submit = post('submit');

        if ($submit == 'Submit') {

            $params['username'] = post('my_vibe', true);
            $params['email_address'] = post('my_vibe', true);

            $sql = 'select * from members where username = :username or email_address = :email_address';
            $rows = $this->model->query_bind($sql, $params, 'object');

            if (!isset($rows[0])) {
                $errors[] = 'We could not match the information that you submitted with an account.';
                $_SESSION['form_submission_errors'] = $errors;
                $this->forgot_password();
            } else {
                $this->_init_password_reset($rows[0]);
            }

        }

    }

    function _init_password_reset($member_obj) {
        $data['user_token'] = make_rand_str(32);
        $this->model->update($member_obj->id, $data);
        $reset_url = BASE_URL.'members/init_reset_password/'.$data['user_token'];
        $this->_send_password_reset_email($member_obj, $reset_url);
        redirect('members/check_your_email/reset');
    }

    function _send_activate_account_email($member_obj, $activate_url) {
        //send an email inviting the user to goto the $reset url
        $data['subject'] = 'Confirm Your Account';
        $data['target_name'] = $member_obj->first_name.' '.$member_obj->last_name;
        $data['member_obj'] = $member_obj;
        $data['activate_url'] = $activate_url;
        $data['target_email'] = $member_obj->email_address;
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
        $data['msg_html'] = $this->view('msg_password_reset_invite', $data, true);
        $msg_plain = str_replace('</p>', '\\n\\n', $data['msg_html']);
        $data['msg_plain'] = strip_tags($msg_plain);
        //add your own code below this line for sending email
    }

    function submit_update_password() {

        $member_obj = $this->_get_member_obj();

        if ($member_obj == false) {
            redirect('members/login');
        }

        $submit = post('submit', true);

        if ($submit == 'Set Password') {
            $this->validation_helper->set_rules('password', 'password', 'required|min_length[5]|max_length[35]|callback_password_check');
            $this->validation_helper->set_rules('password_repeat', 'password repeat', 'required|matches[password]');
        
            $result = $this->validation_helper->run();

            if ($result == true) {
                //hash the password, update it and then log the user in
                $data['password'] = $this->_hash_string(post('password'));
                $this->model->update($member_obj->id, $data);

                //is this the first time that this person has logged in?
                $num_logins = $member_obj->num_logins;

                if ($num_logins<2) {
                    $flash_msg = 'Ahoy!  Welcome aboard the fun bus.  It\'s great to have you here!';
                } else {
                    $flash_msg = 'Your password was successfully updated.';
                }

                set_flashdata($flash_msg);
                redirect('members/your_account');

            } else {
                //form submission error
                $this->update_password();
            }   

        }

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
        $params['update_id'] = segment(3);

        if (($submit == 'Yes - Delete Now') && (is_numeric($params['update_id']))) {
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
        $page_num = segment(3);

        if (!is_numeric($page_num)) {
            $page_num = 0;
        }

        if ($page_num>1) {
            $offset = ($page_num-1)*$this->_get_limit();
        } else {
            $offset = 0;
        }

        return $offset;
    }

    function _get_selected_per_page() {
        if (!isset($_SESSION['selected_per_page'])) {
            $selected_per_page = $this->per_page_options[1];
        } else {
            $selected_per_page = $_SESSION['selected_per_page'];
        }

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
        $data['confirmed'] = post('confirmed', true);        
        return $data;
    }

    function _get_login_data_from_post() {
        $data['username'] = post('username');
        $data['password'] = post('password');
        $data['remember'] = post('remember');
        return $data;
    }

    function _in_you_go($member_id, $remember=null) {

        $this->module('trongate_tokens');

        if (!isset($remember)) {
            $remember = true;
        }

        $member_obj = $this->model->get_where($member_id, 'members');

        //update num logins
        $num_logins = $member_obj->num_logins;
        $data['num_logins'] = $num_logins + 1;
        $data['last_login'] = time();
        $data['user_token'] = '';
        $this->model->update($member_id, $data, 'members');

        $token_data['user_id'] = $member_obj->trongate_user_id;

        if ($remember == true) {
            //set token for 60 days (cookie)
            $thirty_days = 86400*60; //number of seconds in 60 days
            $nowtime = time(); // unix timestamp
            $token_data['expiry_date'] = $nowtime+$thirty_days; //60 days ahead as a timestamp
            $token_data['set_cookie'] = true;
            $this->trongate_tokens->_generate_token($token_data); //generate toke & set cookie

        } else {
            //set short term token (session)
            $_SESSION['trongatetoken'] = $this->trongate_tokens->_generate_token($token_data);
        }

        if ($member_obj->password == '') {
            redirect('members/update_password');
        } else {
            redirect('members/your_account');
        }

    }

    function _hash_string($str) {
        $hashed_string = password_hash($str, PASSWORD_BCRYPT, array(
            'cost' => 11
        ));
        return $hashed_string;
    }

    function _verify_hash($plain_text_str, $hashed_string) {
        $result = password_verify($plain_text_str, $hashed_string);
        return $result; //TRUE or FALSE
    }

    function username_check($username) {
        //make sure email address and username unique
        $params['username'] = $username;
        $params['email_address'] = post('email_address', true);

        if (segment(3) !== '') {
            //this is an update - attempt get update_id
            $update_id = segment(3, 'int');
            if ($update_id == 0) {
                //assume third segment to be member code
                $member_obj = $this->model->get_one_where('code', segment(3), 'members');

                if ($member_obj == false) {
                    $error_msg = 'Unable to fetch member details';
                    return $error_msg; //no point in going on!
                } else {
                    $update_id = $member_obj->id;
                }
            }

            //make sure no other records have same username or email address
            $params['update_id'] = $update_id;
            $sql = 'SELECT 
                        * FROM members 
                    WHERE 
                        (username = :username AND id!= :update_id)
                    OR 
                        (email_address = :email_address AND id!= :update_id)';
            $rows = $this->model->query_bind($sql, $params, 'object');            

        } else {
            //must be a new record
            $sql = 'SELECT * FROM members WHERE username = :username OR email_address = :email_address';
            $rows = $this->model->query_bind($sql, $params, 'object');            
        }

        if (count($rows)>0) {
            $error_msg = 'At least one of the fields that you submitted is not available';
            return $error_msg;            
        } else {
            return true;
        }

    }

    function password_check($str) {
        // *** MODIFY THIS METHOD AND ADD YOUR OWN RULES, AS REQUIRED **
        if (preg_match('/[A-Za-z]/', $str) & preg_match('/\d/', $str) == 1) {
            return true;  // password contains at least one letter and one number
        } else {
            $error_msg = 'The password must contain at least one letter and one number.';
            return $error_msg;
        }
    }

    function login_check($str) {
        $error = 'Your username and/or password was not valid.';
        $params['username'] = $str;
        $params['email_address'] = $str;
        $sql = 'select * from members where username = :username OR email_address = :email_address';
        $rows = $this->model->query_bind($sql, $params, 'object');

        if (!isset($rows[0])) {
            //now valid username or email
            return $error;
        } else {
            //record found, but what about the password?
            $stored_password = $rows[0]->password;
            $password = post('password');
            $password_result = $this->_verify_hash($password, $stored_password);

            if ($password_result == false) {
                //wrong password
                return $error;
            } else {
                //password was correct
                return true;
            }
        }
    }

}