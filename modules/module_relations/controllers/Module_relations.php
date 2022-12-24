<?php
class Module_relations extends Trongate {

    function _draw_summary_panel($alt_module_name, $token) {
        $calling_module_name = segment(1);
        $relation_settings = $this->_get_relation_settings($calling_module_name, $alt_module_name);
        if ($relation_settings == false) {
            echo '<p style="color: red;">Could not find module relation with '.$alt_module_name.' module!</p>';
        } else {
            $associated_module = $this->_est_associated_module($relation_settings, $calling_module_name);
            $data['update_id'] = segment(3);
            $data['token'] = $token;
            $data['associated_singular'] = $associated_module->record_name_singular;
            $data['associated_plural'] = $associated_module->record_name_plural;
            $data['relation_name'] = $this->_build_relation_name($relation_settings);
            $this->view('summary_panel', $data);
        }

    }

    function _est_associated_module($relation_settings, $calling_module_name) {
        $first_module = $relation_settings[0];
        $second_module = $relation_settings[1];

        if ($first_module->module_name == $calling_module_name) {
            $associated_module = $second_module;
        } else {
            $associated_module = $first_module;
        }

        return $associated_module;
    }

    function _get_relation_settings($calling_module_name, $alt_module_name) {
        $settings_file_path = '';
        $relation_names[] = $calling_module_name.'_and_'.$alt_module_name.'.json';
        $relation_names[] = $alt_module_name.'_and_'.$calling_module_name.'.json';

        $dirpath = APPPATH.'modules/module_relations/assets/module_relations';

        if (is_dir($dirpath)) {
            $files = scandir($dirpath);
            foreach($files as $filename) {
                if (($filename == $relation_names[0]) || ($filename == $relation_names[1])) {
                    $settings_file_path = $dirpath.'/'.$filename;
                }
            }
        }

        if ($settings_file_path == '') {
            return false;
        } else {
            $relation_settings = json_decode(file_get_contents($settings_file_path));
            return $relation_settings;
        }

    }

    function _build_relation_name($relation_settings) {
        $first_module_name = $relation_settings[0]->module_name;
        $second_module_name = $relation_settings[1]->module_name;
        $relation_name = 'associated_'.$first_module_name.'_and_'.$second_module_name;
        return $relation_name;
    }

    function _add_associated_records_to_array($rows, $data) {
        $relation_settings = $this->_get_relation_settings($data['calling_module'], $data['alt_module']);
        $table_name = $this->_build_relation_name($relation_settings);

        //get all of the rows a new property with an empty array - representing assigned options
        $target_column_1 = $data['alt_module'];
        foreach($rows as $key => $value) {
            $rows[$key]->$target_column_1 = [];
        }

        //rebuild $rows but with keys that match ids
        $all_rows = [];
        foreach($rows as $row) {
            $all_rows[$row->id] = $row;
        }

        //loop through the assigned records
        $target_column_2 = $data['alt_module'].'_id';
        $target_column_3 = $data['calling_module'].'_id';
        $assoc_records = $this->model->get('id', $table_name);
        foreach($assoc_records as $assoc_record) {
            if (isset($all_rows[$assoc_record->$target_column_3])) {
                $assigned_records_array = $all_rows[$assoc_record->$target_column_3]->$target_column_1;
                $assigned_records_array[] = $assoc_record->$target_column_2;
                $all_rows[$assoc_record->$target_column_3]->$target_column_1 = $assigned_records_array;
            }
        }

        //get full and meaningful possible associated options
        $all_associated_options = [];
        $rows_alt = $this->model->get('id', $data['alt_module']);
        foreach($rows_alt as $row) {
            $all_associated_options[$row->id] = $row;
        }

        //add more meaningful options to 'all_rows'
        foreach($all_rows as $key => $value) {
            if (isset($value->$target_column_1)) {
                $new_array = [];
                $old_array = $value->$target_column_1;
                foreach($old_array as $array_row) {
                    $new_array[] = $all_associated_options[$array_row];
                }
                $all_rows[$key]->$target_column_1 = $new_array;
            }
        }

        $rows = (isset($all_rows)) ? $all_rows : $rows;
        return $rows;
    }

    function fetch_associated_records() {
        api_auth();
        $posted_data = $this->_get_posted_data();

        $data['relation_name'] = $this->_make_safe($posted_data['relationName'], false);
        $data['calling_module'] = $this->_make_safe($posted_data['callingModule'], false);
        $data['alt_module'] = $this->_extract_alt_module_name($data['relation_name'], $data['calling_module']);
        $data['update_id'] = $posted_data['updateId'];
        settype($data['update_id'], 'integer');
        $relation_settings = $this->_get_relation_settings($data['calling_module'], $data['alt_module']);
        $associated_module = $this->_est_associated_module($relation_settings, $data['calling_module']);
        $data['identifier_column'] = $associated_module->identifier_column;
        $data['bits'] = explode(',', $data['identifier_column']);

        $relationship_type =  $relation_settings[2]->relationship_type;
        if ($relationship_type == 'one to many') {
            $associated_records = $this->_fetch_from_child_module($data);
        } else {
            $associated_records = $this->_fetch_from_assoc_tbl($data);
        }
        
        http_response_code(200);
        echo json_encode($associated_records);
    }

    function _fetch_from_child_module($data) {
        //fetch associated records from child module (this only gets used in one to many)
        $sql = 'SELECT
                    [child_module].* 
                FROM
                    [parent_module]
                INNER JOIN
                    [child_module]
                ON
                    [parent_module].id = [child_module].[parent_module]_id 
                WHERE [parent_module].id = [update_id] 
                ORDER BY [child_module].[order_by]';

        $parent_module = $data['calling_module'];
        $child_module = $data['alt_module'];
        $update_id = $data['update_id'];
        $bits = $data['bits'];
        $order_by = $this->_est_order_by($bits);

        $sql = str_replace('[parent_module]', $parent_module, $sql);
        $sql = str_replace('[child_module]', $child_module, $sql);
        $sql = str_replace('[update_id]', $update_id, $sql);
        $sql = str_replace('[order_by]', $order_by, $sql);

        $associated_records = [];
        $rows = $this->model->query($sql, 'object');
        foreach($rows as $row) {
            $row_data['id'] = $row->id;
            $value = '';
            foreach($bits as $bit) {
                $bit = trim($bit);
                $value.= $row->$bit.' ';
            }

            $row_data['value'] = trim($value);
            $associated_records[] = $row_data;
        }

        return $associated_records;
    }

    function _make_sure_table_exists($data) {
        $params['table_name'] = $data['relation_name'];
        $sql = 'SHOW TABLES LIKE :table_name';
        $rows = $this->model->query_bind($sql, $params, 'object');

        if(count($rows) == 0) {
            $filename = str_replace('associated_', '', $params['table_name']).'.json';
            $dirpath = APPPATH.'modules/module_relations/assets/module_relations';
            $settings_file_path = $dirpath.'/'.$filename;

            if (!file_exists($settings_file_path)) {
                http_response_code(401);
                die();
            }

            $relation_settings = json_decode(file_get_contents($settings_file_path));
            $column_name_a = $relation_settings[0]->module_name.'_id';
            $column_name_b = $relation_settings[1]->module_name.'_id';
            $table_name = $params['table_name'];

            $queries[] = 'CREATE TABLE `'.$table_name.'` (
              `id` int(11) NOT NULL,
              `'.$column_name_a.'` int(11) NOT NULL DEFAULT 0,
              `'.$column_name_b.'` int(11) NOT NULL DEFAULT 0
            )';

            $queries[] = 'ALTER TABLE `'.$table_name.'`
              ADD PRIMARY KEY (`id`)';

            $queries[] = 'ALTER TABLE `'.$table_name.'`
              MODIFY `id` int(11) NOT NULL AUTO_INCREMENT';

            $queries[] = 'COMMIT';

            foreach($queries as $query) {
                $this->model->query($query);
            }
        }
    }

    function _fetch_from_assoc_tbl($data) {
        //fetch associated records from assoc table
        $relation_name = $data['relation_name'];
        $calling_module = $data['calling_module'];
        $alt_module = $data['alt_module'];
        $update_id = $data['update_id']; //the update id for the calling module 
        $bits = $data['bits'];
        $order_by = $this->_est_order_by($bits);

        $this->_make_sure_table_exists($data);

        $sql = 'SELECT
                    [relation_name].id as __id,
                    [alt_module].* 
                FROM
                    [calling_module]
                INNER JOIN
                    [relation_name]
                ON
                    [calling_module].id = [relation_name].[calling_module]_id
                INNER JOIN
                    [alt_module]
                ON
                    [relation_name].[alt_module]_id = [alt_module].id 
                WHERE [calling_module].id = [update_id] 
                ORDER BY [alt_module].[order_by]';

        $sql = str_replace('[relation_name]', $relation_name, $sql);
        $sql = str_replace('[calling_module]', $calling_module, $sql);
        $sql = str_replace('[alt_module]', $alt_module, $sql);
        $sql = str_replace('[update_id]', $update_id, $sql);
        $sql = str_replace('[order_by]', $order_by, $sql);

        $associated_records = [];
        $rows = $this->model->query($sql, 'object');
        foreach($rows as $row) {
            $row_data['id'] = $row->__id;
            $value = '';
            foreach($bits as $bit) {
                $bit = trim($bit);
                $value.= $row->$bit.' ';
            }

            $row_data['value'] = trim($value);
            $associated_records[] = $row_data;
        }

        return $associated_records;
    }

    function _est_order_by($bits) {
        $order_by = $bits[count($bits)-1];
        return $order_by;
    }

    function _extract_alt_module_name($relation_name, $calling_module) {
        $bits = explode('_and_', $relation_name);
        $str_end = $bits[count($bits)-1];

        if ($str_end == $calling_module) {
            $target_str = $bits[count($bits)-2];
            $alt_module = substr($target_str, 11, strlen($target_str));
        } else {
            $alt_module = $str_end;
        }

        return $alt_module;
    }

    function _get_posted_data() {
        $post = file_get_contents('php://input');
        $params = json_decode($post, true);

        if ((gettype($params) == 'NULL') || (strlen($post) == 0)) {
            $params = [];
        }

        return $params;
    }

    function _make_safe($string, $allow_spaces) {
        $string = trim(strip_tags($string));
        $string = preg_replace('/\s+/', ' ', $string);
        $string = preg_replace("/[^A-Za-z0-9 _]/", '', $string);
        $string = rawurlencode(utf8_encode($string));

        if ($allow_spaces !== true) {
            $string = preg_replace('/-+/', '_', $string);
            $string = str_replace("%20", '_', $string);
        }

        return $string;
    }

    function fetch_available_options() {
        api_auth();
        $posted_data = $this->_get_posted_data();

        if (!is_numeric($posted_data['updateId'])) {
            http_response_code(422);
            echo 'Non numeric update_id.'; die();
        }

        if (isset($posted_data['results'])) {
            $data['results'] = (gettype($posted_data['results']) == 'array' ? $posted_data['results'] : []);
        } else {
            $data['results'] = [];   
        }

        if (isset($posted_data['relationName'])) {
            $data['relation_name'] = $this->_make_safe($posted_data['relationName'], false);
            $data['calling_module'] = $this->_make_safe($posted_data['callingModule'], false);
            $data['alt_module'] = $this->_extract_alt_module_name($data['relation_name'], $data['calling_module']);
            $data['update_id'] = $posted_data['updateId'];
            settype($data['update_id'], 'integer');
            $relation_settings = $this->_get_relation_settings($data['calling_module'], $data['alt_module']);
            $relationship_type = $relation_settings[2]->relationship_type;
            $data['associated_module'] = $this->_est_associated_module($relation_settings, $data['calling_module']);
        }

        switch($relationship_type) {
            case 'one to one':
                $options = $this->_fetch_available_options_one_to_one($data);
                break;
            case 'many to many':
                $options = $this->_fetch_available_options_many_to_many($data);
                break;
            case 'one to many':
                $options = $this->_fetch_available_options_one_to_many($data);
                break;
        }

        http_response_code(200);
        if (isset($options)) {
            echo json_encode($options);
        } else {
            die();
        }

    }

    function _fetch_available_options_one_to_one($data) {
        $num_results = count($data['results']);
        if ($num_results>0) {
            $options = []; //no options available since already assigned
        } else {
            //return records from the 'alt_module' that have not yet been assigned 
            $sql = 'SELECT
                        [alt_module].* 
                    FROM
                        [relation_name]
                    RIGHT JOIN
                        [alt_module]
                    ON
                        [relation_name].[alt_module]_id = [alt_module].id
                    WHERE
                        [relation_name].[alt_module]_id IS NULL';

            $options = $this->_fetch_available($sql, $data);
        }
        http_response_code(200);
        echo json_encode($options);
    }

    function _fetch_available_options_many_to_many($data) {
        //return records from the 'alt_module' that have not yet been assigned to THIS particular module
        $sql = 'SELECT *
                FROM [alt_module]
                ORDER BY [alt_module].[order_by]';        

        $alt_records = $this->_fetch_available($sql, $data);

        $exclude_array = [];
        foreach($data['results'] as $result) {
            $exclude_array[] = $result['value']; //alread assigned to this record
        }

        $options = [];
        foreach($alt_records as $record) {

            if (!in_array($record['value'], $exclude_array)) {
                $options[] = $record;
            }

        }

        http_response_code(200);
        echo json_encode($options);
    }

    function _fetch_available_options_one_to_many($data) {
        //select * from child_module where fk != update_id
        $parent_module = $data['calling_module'];
        $child_module = $data['alt_module'];
        $identifier_column = $data['associated_module']->identifier_column;
        $bits = explode(',', $identifier_column);
        $order_by = $this->_est_order_by($bits);
        $update_id = $data['update_id'];

        $sql = 'SELECT * from [child_module] 
                WHERE [parent_module]_id != [update_id] 
                ORDER BY [order_by]';

        $sql = str_replace('[parent_module]', $parent_module, $sql);
        $sql = str_replace('[child_module]', $child_module, $sql);
        $sql = str_replace('[order_by]', $order_by, $sql);
        $sql = str_replace('[update_id]', $update_id, $sql);

        $available_records = [];
        $rows = $this->model->query($sql, 'object');

        foreach($rows as $row) {
            $row_data['key'] = $row->id;
            $value = '';
            foreach($bits as $bit) {
                $bit = trim($bit);
                $value.= $row->$bit.' ';
            }

            $row_data['value'] = trim($value);
            $available_records[] = $row_data;
        }

        return $available_records;
    }

    function _fetch_available($sql, $data) {
        //fetch available option for 'with bt' relations
        $relation_name = $data['relation_name'];
        $calling_module = $data['calling_module'];
        $alt_module = $data['alt_module'];
        $identifier_column = $data['associated_module']->identifier_column;
        $bits = explode(',', $identifier_column);
        $order_by = $this->_est_order_by($bits);

        $sql = str_replace('[relation_name]', $relation_name, $sql);
        $sql = str_replace('[calling_module]', $calling_module, $sql);
        $sql = str_replace('[alt_module]', $alt_module, $sql);
        $sql = str_replace('[order_by]', $order_by, $sql);

        $available_records = [];
        $rows = $this->model->query($sql, 'object');

        foreach($rows as $row) {
            $row_data['key'] = $row->id;
            $value = '';
            foreach($bits as $bit) {
                $bit = trim($bit);
                $value.= $row->$bit.' ';
            }

            $row_data['value'] = trim($value);
            $available_records[] = $row_data;
        }

        return $available_records;
    }

    function submit() {
        api_auth();
        $posted_data = $this->_get_posted_data();
        $update_id = $posted_data['updateId'];

        if (!is_numeric($update_id)) {
            http_response_code(422);
            echo 'Non numeric update_id.'; die();
        }

        $relation_name = $posted_data['relationName'];
        $calling_module_name = $posted_data['callingModule'];

        $ditch = 'associated_'.$calling_module_name.'_and_';
        $strpos = strpos($relation_name, $ditch);

        if (is_numeric($strpos)) {
            $alt_module_name = str_replace($ditch, '', $relation_name);
        } else {
            //associated_books_sizes_and_books
            $relation_str = substr($relation_name, 11, strlen($relation_name));
            $end_pos = strlen($relation_str) - (strlen($calling_module_name) + 5);
            $alt_module_name = substr($relation_str, 0, $end_pos);
        }

        $relation_settings = $this->_get_relation_settings($calling_module_name, $alt_module_name);
        $relationship_type = $relation_settings[2]->relationship_type;
        $first_module = $relation_settings[0];
        $second_module = $relation_settings[1];

        if ($relationship_type == 'one to many') {
            $parent_module_name = $relation_settings[0]->module_name;
            $child_module = $relation_settings[1];
            $identifier_column = $child_module->identifier_column;
            $foreign_key = $parent_module_name.'_id';
            $data[$foreign_key] = $update_id;
            $this->model->update($posted_data['value'], $data, $child_module->module_name);
        } else {
            $data[$calling_module_name.'_id'] = $update_id;
            $data[$alt_module_name.'_id'] = $posted_data['value'];
            $this->model->insert($data, $relation_name);
        }

    }

    function disassociate() {
        api_auth();
        $posted_data = $this->_get_posted_data();
        $id = $posted_data['updateId'];
        $target_tbl = $posted_data['relationName'];

        //get the table names
        $str = str_replace('associated_', '', $target_tbl);
        $tables = explode('_and_', $str);

        //get the relationship type
        $relation_settings = $this->_get_relation_settings($tables[0], $tables[1]);
        $relationship_type = $relation_settings[2]->relationship_type;

        if ($relationship_type == 'one to many') {
            $child_module_table = $relation_settings[1]->module_name;
            $foreign_key = $relation_settings[0]->module_name.'_id';
            $params['update_id'] = $id;
            $sql = 'UPDATE '.$child_module_table.' SET '.$foreign_key.'=0 WHERE id=:update_id';
            $this->model->query_bind($sql, $params, 'object');
        } else {
            $this->model->delete($id, $target_tbl);
        }

    }

    function _fetch_options($selected_key, $calling_module_name, $alt_module_name) {
        $options = [];

        if (($selected_key == '') || ($selected_key == 0) || ($selected_key == '0')) {
            $options[''] = 'Select...';
        }

        $relation_settings = $this->_get_relation_settings($calling_module_name, $alt_module_name);

        //get the alt module idenfifier column
        if ($relation_settings[0]->module_name == $alt_module_name) {
            $identifier_column = $relation_settings[0]->identifier_column;
            $foreign_key = $relation_settings[1]->module_name.'_id';
        } else {
            $identifier_column = $relation_settings[1]->identifier_column;
            $foreign_key = $relation_settings[0]->module_name.'_id';
        }

        $relationship_type = $relation_settings[2]->relationship_type;

        $bits =  explode(',', $identifier_column);
        $order_by = $this->_est_order_by($bits);

        if ($relationship_type == 'one to many') {
            $options = $this->_get_parent_options($alt_module_name, $identifier_column, $selected_key); 
        } else {

            $sql = 'select * from '.$alt_module_name.' order by '.$order_by;
            $rows = $this->model->query($sql, 'object');

            foreach($rows as $row) {
                $row_data['key'] = $row->id;
                $value = '';
                foreach($bits as $bit) {
                    $bit = trim($bit);
                    $value.= $row->$bit.' ';
                }

                $row_desc = trim($value);
                $options[$row->id] = $row_desc;

                if ($selected_key == $row->id) {
                    $row_label = $value;
                    $options[0] = strtoupper('*** Disassociate with '.$row_label.' ***');
                } else {

                }

            }

        }

        return $options;
    }

    function _get_parent_options($parent_module_name, $identifier_column, $selected_key) {

        $options = [];
        $sql = 'select id, '.$identifier_column.' from '.$parent_module_name.' order by '.$identifier_column;
        $rows = $this->model->query($sql, 'object');

        if (($selected_key == '') || ($selected_key == 0) || ($selected_key == '0')) {
            $options[''] = 'Select...';
        }

        $bits =  explode(',', $identifier_column);

        foreach ($rows as $row) {
            $identifier_column_str = '';

            foreach($bits as $bit) {
                $bit = trim($bit);
                $identifier_column_str.= $row->$bit.' ';
            }

            $identifier_column_str = trim($identifier_column_str);

            if ($selected_key == $row->id) {
                $options[0] = strtoupper('*** Disassociate with '.$identifier_column_str.' ***');
                $options[$selected_key] = $identifier_column_str;
            } else {
                $options[$row->id] = $identifier_column_str;
            }
        }

        return $options;
    }

}