<h1><?= $headline ?></h1>
<?php
flashdata();
echo '<p>'.anchor('members/create', 'Create New Member Record', array("class" => "button")).'</p>'; 
echo Pagination::display($pagination_data);
if (count($rows)>0) { ?>
    <table id="results-tbl">
        <thead>
            <tr>
                <th colspan="8">
                    <div>
                        <div><?php
                        echo form_open('members/manage/1/', array("method" => "get"));
                        echo form_input('searchphrase', '', array("placeholder" => "Search records..."));
                        echo form_submit('submit', 'Search', array("class" => "alt"));
                        echo form_close();
                        ?></div>
                        <div>Records Per Page: <?php
                        $dropdown_attr['onchange'] = 'setPerPage()';
                        echo form_dropdown('per_page', $per_page_options, $selected_per_page, $dropdown_attr); 
                        ?></div>

                    </div>                    
                </th>
            </tr>
            <tr>
                <th>Username</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Email Address</th>
                <th>Date Joined</th>
                <th>Num Logins</th>
                <th>Confirmed</th>
                <th style="width: 20px;">Action</th>            
            </tr>
        </thead>
        <tbody>
            <?php 
            $attr['class'] = 'button alt';
            foreach($rows as $row) { ?>
            <tr>
                <td><?= $row->username ?></td>
                <td><?= $row->first_name ?></td>
                <td><?= $row->last_name ?></td>
                <td><?= $row->email_address ?></td>
                <td><?= date('l, jS F Y', $row->date_joined) ?></td>
                <td><?= $row->num_logins ?></td>
                <td><?= $row->confirmed ?></td>
                <td><?= anchor('members/show/'.$row->id, 'View', $attr) ?></td>        
            </tr>
            <?php
            }
            ?>
        </tbody>
    </table>
<?php 
    if(count($rows)>9) {
        unset($pagination_data['include_showing_statement']);
        echo Pagination::display($pagination_data);
    }
}
?>