<h1><?= $headline ?></h1>
<?php
flashdata();
echo '<p>'.anchor('item_pictures/create', 'Create New Item Picture Record', array("class" => "button")).'</p>'; 
echo Pagination::display($pagination_data);
if (count($rows)>0) { ?>
    <table id="results-tbl">
        <thead>
            <tr>
                <th colspan="3">
                    <div>
                        <div><?php
                        echo form_open('item_pictures/manage/1/', array("method" => "get"));
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
                <th>Entity Name</th>
                <th>URL Identifier String</th>
                <th style="width: 20px;">Action</th>            
            </tr>
        </thead>
        <tbody>
            <?php 
            $attr['class'] = 'button alt';
            foreach($rows as $row) { ?>
            <tr>
                <td>
                    <div>
                        <div><img src="<?= $row->pic_path ?>" alt="<?= $row->entity_name ?>"></div>
                        <div><?= $row->entity_name ?></div>
                    </div>
                </td>
                <td><?= $row->url_identifier_string ?></td>
                <td><?= anchor('item_pictures/show/'.$row->id, 'View', $attr) ?></td>        
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

<style>
td img {
    max-height: 42px;
    border: 0;
    margin-right: 1em;
}

#results-tbl > tbody > tr > td:nth-child(1) > div {
    display: flex;
    flex-direction: row;
    align-items: center;
    margin: 0;
    padding: 0;
    justify-content: flex-start;
    font-weight: bold;
}
</style>