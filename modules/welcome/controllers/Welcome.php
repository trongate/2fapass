<?php
class Welcome extends Trongate {
 
    function index() {
        $data['view_module'] = 'welcome';
        $data['view_file'] = 'homepage_content';
        $this->template('public', $data);
    }

}