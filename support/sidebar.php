<?php
if(isset($_SESSION['HDT_uid'])){
	$user = $support->get_user($_SESSION['HDT_uid']);
	$_SESSION['HDT_supuid'] = $_SESSION['HDT_uid'];
} elseif(isset($_SESSION['HDT_supuid'])) {
	$user = $support->get_user($_SESSION['HDT_supuid']);
} else {
	//$user = NULL;
	$user = $support->check_login();
}
?>
	<div id="sidebar-collapse" class="col-sm-3 col-lg-2 sidebar">
		<div class="profile-sidebar">
			<div class="profile-userpic">
				<img src="http://placehold.it/50/30a5ff/fff" class="img-responsive" alt="">
			</div>
			<?php if($support->check_logged() !== false):?>
			<div class="profile-usertitle">
				<div class="profile-usertitle-name"><?php echo $user['FullName'];?></div>
				<div class="profile-usertitle-status"><span class="indicator label-success"></span>Online</div>
			</div>
			<?php else:?>
			<div class="profile-usertitle">
				<div class="profile-usertitle-name">Nincs felhasználó bejelentkezve</div>
			</div>
			<?php endif;?>
			<div class="clear"></div>
			<?php
				//var_dump($_SESSION);
			?>
		</div>
		<div class="divider"></div>
	</div><!--/.sidebar-->
