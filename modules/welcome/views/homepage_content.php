<h1>Welcome to <?= WEBSITE_NAME ?></h1>
<p>
    <?php
    $link_attr['class'] = 'button';
    echo anchor('members/join', 'Sign Up For Free', $link_attr);
    $link_attr['class'] = 'button alt';
    echo anchor('members/login', 'Login To Your Existing Account', $link_attr);
    ?>
</p>