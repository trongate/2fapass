<?php
class Account extends Trongate {

    private $template_to_use = 'public';

    function __construct() {
        parent::__construct();
        $this->parent_module = 'members';
        $this->child_module = 'account';
    }

    function login() {
        $this->module('trongate_tokens');
        $this->trongate_tokens->_destroy();
        $data = $this->_get_login_data_from_post();
        $this->view('login', $data);
    }

    function your_account() {
    	$this->module('members');
        $token = $this->members->_make_sure_allowed();
        $member_obj = $this->members->_get_member_obj($token);
        $data = (array) $member_obj;
        $data['view_module'] = 'members/account';
        $data['view_file'] = 'your_account';
        $this->template($this->template_to_use, $data);        
    }

    function forgot_password() {
        $data['view_module'] = 'members/account';
        $data['view_file'] = 'forgot_password';
        $this->template($this->template_to_use, $data);  
    }

    function update() {
        $this->module('members');
        $token = $this->members->_make_sure_allowed();

        $submit = post('submit');
        if ($submit == 'Submit') {
            $data = $this->_get_data_from_post();
        } else {
            $member_obj = $this->members->_get_member_obj($token);
            $data = (array) $member_obj;
        }

        $data['form_location'] = str_replace('/update', '/submit_update_details', current_url());
        $data['view_module'] = 'members/account';
        $data['view_file'] = 'update_account';
        $this->template($this->template_to_use, $data);        
    }

    function update_password() {
    	$this->module('members');
        $member_obj = $this->members->_get_member_obj();
        if ($member_obj == false) {
            redirect('members/login');
        }

        $password = $member_obj->password;
        if ($password == '') {
            $data['headline'] = 'Please set password';
        } else {
            $data['headline'] = 'Update password';
            $data['cancel_url'] = BASE_URL.'members-account/your_account';
        }

        $data['form_location'] = str_replace('/update', '/submit_update', current_url());
        $data['view_module'] = 'members/account';
        $data['view_file'] = 'update_password';
        $this->template($this->template_to_use, $data);        
    }

    function init_reset_password() {
        $user_token = segment(3);
        if (strlen($user_token) !== 32) {
            redirect('members/ouch');
        } else {
            //attempt to fetch member_obj with user token
            $member_obj = $this->model->get_one_where('user_token', $user_token, 'members');

            if ($member_obj == false) {
                redirect('members/ouch');
            } else {
                $this->module('members');
                $member_id = $member_obj->id;
                $data['password'] = '';
                $this->model->update($member_id, $data, 'members');
                $this->members->_in_you_go($member_id, true);
            }
        }
    }

    function submit_login() {
        $this->validation_helper->set_rules('username', 'username', 'required|callback_login_check');
        $this->validation_helper->set_rules('password', 'password', 'required|min_length[5]');
        $result = $this->validation_helper->run();

        if ($result == true) {
            $params['username'] = post('username');
            $params['email_address'] = post('username');
            $sql = 'SELECT * FROM members WHERE username =:username OR email_address =:email_address';
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

            $this->module('members');
            $this->members->_in_you_go($member_id, $remember);

        } else {
            $this->login();
        }
    }

    function submit_update_details() {
        $this->module('members');
        $token = $this->members->_make_sure_allowed();
        $member_obj = $this->members->_get_member_obj($token);
        $update_id = $member_obj->id;

        $this->validation_helper->set_rules('username', 'username', 'required|min_length[2]|max_length[55]|callback_username_check');
        $this->validation_helper->set_rules('first_name', 'first name', 'required|min_length[2]|max_length[65]');
        $this->validation_helper->set_rules('last_name', 'last name', 'required|min_length[1]|max_length[75]');
        $this->validation_helper->set_rules('email_address', 'email address', 'required|valid_email|callback_email_check');

        $result = $this->validation_helper->run();

        if ($result == true) {

            $data = $this->_get_data_from_post();
            $data['url_string'] = $this->members->_make_unique_url_string($data['username'], $update_id);
            
            //update an existing record
            $this->model->update($update_id, $data, 'members');
            $flash_msg = 'Your account details have been successfully updated';
            
            set_flashdata($flash_msg);
            redirect('members-account/your_account');

        } else {
            //form submission error
            $this->update();
        }
    }

    function submit_update_password() {
    	$this->module('members');
        $member_obj = $this->members->_get_member_obj();

        if ($member_obj == false) {
            redirect('members/login');
        }

        $this->validation_helper->set_rules('password', 'password', 'required|min_length[5]|max_length[35]|callback_password_check');
        $this->validation_helper->set_rules('password_repeat', 'password repeat', 'required|matches[password]');

      if ($this->validation_helper->run()) {
        $data['password'] = $this->_hash_string(post('password'));
        $this->model->update($member_obj->id, $data, 'members');
        $this->update_password_success($member_obj);
      } else {
        //form submission error
        $this->update_password();
      }
    }

    function submit_forgot_password() {
        $this->validation_helper->set_rules('my_vibe', 'form field', 'required|callback_forget_password_check');
        $result = $this->validation_helper->run();

        if ($result == true) {
            //fetch the member obj
            $params['username'] = post('my_vibe');
            $params['email_address'] = post('my_vibe');

            $sql = 'SELECT * FROM members WHERE username =:username OR email_address =:email_address';
            $rows = $this->model->query_bind($sql, $params, 'object');
            $member_obj = $rows[0];

            //create temp token & send reset email
            $this->_init_password_reset($member_obj);

        } else {
            $this->forgot_password();
        }

    }

    function _init_password_reset($member_obj) {
        $this->module('members');
        $data['user_token'] = make_rand_str(32);
        $this->model->update($member_obj->id, $data, 'members');
        $reset_url = BASE_URL.'members-account/init_reset_password/'.$data['user_token'];
        $this->members->_send_password_reset_email($member_obj, $reset_url);
        redirect('members/check_your_email/reset');
    }

    function update_password_success($member_obj) {
      $num_logins = $member_obj->num_logins;
      $flash_msg = $num_logins < 2 ? 'Ahoy! Welcome aboard the fun bus. It\'s great to have you here!' : 'Your password was successfully updated.';
      set_flashdata($flash_msg);
      redirect('members-account/your_account');
    }

    function _hash_string($str) {
        $hashed_string = password_hash($str, PASSWORD_BCRYPT, array(
            'cost' => 11
        ));
        return $hashed_string;
    }

    function _verify_hash($plain_text_str, $hashed_string) {
        $result = password_verify($plain_text_str, $hashed_string);
        return $result; //true or false
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

    function _get_data_from_post() {
        $data['username'] = post('username', true);
        $data['first_name'] = post('first_name', true);
        $data['last_name'] = post('last_name', true);
        $data['email_address'] = post('email_address', true);    
        return $data;
    }

    function _get_login_data_from_post() {
        $data['username'] = post('username');
        $data['password'] = post('password');
        $data['remember'] = post('remember');
        return $data;
    }

    function username_check($username) {
        // Check if the username is formatted correctly
        $filtered_name = filter_name($username);

        if ($filtered_name !== $username) {
            $error_msg = 'The username contains characters that are not allowed.';
            return $error_msg;
        }

        // Make sure submitted username is available
        $code = segment(3);
        $update_id = 0;
        $member_obj = $this->model->get_one_where('code', $code, 'members');

        if (is_object($member_obj)) {
            $update_id = $member_obj->id ?? 0;
        }

        if ($update_id === 0) {
            $error_msg = 'The user that you submitted could not be validated.';
            return $error_msg;
        }

        $params['username'] = $username;
        $params['update_id'] = $update_id;
        $sql = 'SELECT COUNT(*) as count FROM members WHERE username =:username AND id !=:update_id';
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
        $code = segment(3);
        $update_id = 0;
        $member_obj = $this->model->get_one_where('code', $code, 'members');

        if (is_object($member_obj)) {
            $update_id = $member_obj->id ?? 0;
        }

        if ($update_id === 0) {
            $error_msg = 'The email address that you submitted could not be validated.';
            return $error_msg;
        }

        $params['email_address'] = $email_address;
        $params['update_id'] = $update_id;
        $sql = 'SELECT COUNT(*) as count FROM members WHERE email_address =:email_address AND id !=:update_id';
        $rows = $this->model->query_bind($sql, $params, 'object');
        $count = $rows[0]->count ?? 0;

        if($count>0) {
            $error_msg = 'The submitted email address is not available.';
            return $error_msg;        
        }

        return true;
    }

    function login_check($str) {
        $error_msg = 'Your username and/or password was not valid.';
        $params['username'] = $str;
        $params['email_address'] = $str;

        $sql = 'SELECT * FROM members WHERE username = :username OR email_address = :email_address';
        $rows = $this->model->query_bind($sql, $params, 'object');

        if(!isset($rows[0])) {
            //invalid username and/or email
            return $error_msg;
        } else {
            //record found, is account confirmed & what about the password?
            $confirmed = $rows[0]->confirmed ?? 0;

            if ($confirmed === 0) {
                return $error_msg;
            }

            $stored_password = $rows[0]->password;
            $password = post('password');
            $password_result = $this->_verify_hash($password, $stored_password);

            if ($password_result == false) {
                //wrong password
                return $error_msg;
            } else {
                //password was correct
                return true;
            }
        }
    }

    function forget_password_check($str) {
        $params['username'] = $str;
        $params['email_address'] = $str;

        $sql = 'SELECT COUNT(*) as count FROM members WHERE username =:username OR email_address =:email_address';
        $rows = $this->model->query_bind($sql, $params, 'object');
        $count = $rows[0]->count ?? 0;

        if($count == 0) {
            $error_msg = 'We could not match the information that you submitted with an account.';
            return $error_msg;        
        }

        return true;
    }

    function __destruct() {
        $this->parent_module = '';
        $this->child_module = '';
    }

}