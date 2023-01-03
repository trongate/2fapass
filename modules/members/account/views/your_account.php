<section class="container">
	<h1>Your Account</h1>
	<?= flashdata() ?>
	<div class="card">
	    <div class="card-heading">
	        Options
	    </div>
	    <div class="card-body">
	        <?php 
	        echo anchor('members-account/update/'.$code, 'Update Your Details', array("class" => "button alt"));
	        echo anchor('members-account/update_password', 'Update Password', array("class" => "button alt"));
	        echo anchor('members/logout', 'Sign Out', array("class" => "button"));
	        ?>
	    </div>
	</div>
	<div class="two-col">
	    <div class="card record-details">
	        <div class="card-heading">
	            Your Details
	        </div>
	        <div class="card-body">
	            <div><span>Username</span><span><?= $username ?></span></div>
	            <div><span>First Name</span><span><?= $first_name ?></span></div>
	            <div><span>Last Name</span><span><?= $last_name ?></span></div>
	            <div><span>Email Address</span><span><?= $email_address ?></span></div>
	            <div><span>Date Joined</span><span><?= date('jS F Y', $date_joined) ?></span></div>
	        </div>
	    </div>
	</div>
</section>