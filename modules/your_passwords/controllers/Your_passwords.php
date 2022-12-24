<?php
class Your_passwords extends Trongate {

    function index() {
        $this->module('trongate_security');
        $params['reject_url'] = 'members/login';
        $token = $this->trongate_security->_make_sure_allowed('members area', $params);

        $this->module('members');
        $member_obj = $this->members->_get_member_obj($token);
        $data['items'] = $this->_fetch_items($member_obj->id);
        $data['view_file'] = 'your_passwords';
        $this->template('bootstrappy', $data);
    }

    function _fetch_items($member_id) {
        $rows = $this->model->get_many_where('members_id', $member_id, 'member_passwords');
        $rows = $this->_add_full_pic_paths($rows);
// $more_rows = $rows;
// for ($i=0; $i < 4; $i++) { 
//     $rows = array_merge($rows, $more_rows);
// }

        return $rows;
    }

    function _add_full_pic_paths($rows) {
        foreach($rows as $key => $value) {

            if($value->picture !== '') {
                $rows[$key]->pic_path = BASE_URL.'member_passwords_module/member_passwords_pics/';
                $rows[$key]->pic_path.= $value->id.'/'.$value->picture;
            } else {
                $rows[$key]->pic_path = '';
                $rand_index = rand(0, count(MATCHING_COLORS)-1);
                $rows[$key]->cell_background = MATCHING_COLORS[$rand_index];
            }
            
        }

        return $rows;
    }

    function create() {
        $update_id = (int) segment(3);
        $data = $this->_get_data_from_post();
        $data['headline'] = ($update_id>0) ? 'Update Password Details' : 'Create Password Record';
        $data['form_location'] = 'your_passwords/submit';
        $data['cancel_url'] = 'your_passwords';
        $data['view_file'] = 'create_password';
        $this->template('bootstrappy', $data);
    }

    function submit() {

        $this->module('trongate_security');
        $params['reject_url'] = 'members/login';
        $token = $this->trongate_security->_make_sure_allowed('members area', $params);

        $this->module('members');
        $member_obj = $this->members->_get_member_obj($token);

        $this->validation_helper->set_rules('website_url', 'website URL', 'required|min_length[9]');
        $this->validation_helper->set_rules('website_name', 'website name', 'required|min_length[9]');
        $this->validation_helper->set_rules('username', 'username', 'required|min_length[2]');
        $this->validation_helper->set_rules('password', 'site password', 'required|min_length[9]');

        $result = $this->validation_helper->run(); //produce true or false

        if ($result == true) {
            $data = $this->_get_data_from_post();
            $data['members_id'] = $member_obj->id;
            $data['picture'] = '';
            $this->module('encryption');
            $data['password'] = $this->encryption->_encrypt($data['password']);
            $this->model->insert($data, 'member_passwords');
            set_flashdata('Your new site password record was successfully created');
            redirect('your_passwords');
        } else {
            $this->create();
        }

    }

    function _get_data_from_post() {
        $data['website_url'] = post('website_url', true);
        $data['website_name'] = post('website_name', true);
        $data['username'] = post('username', true);
        $data['password'] = post('password', true);
        return $data;
    }

}