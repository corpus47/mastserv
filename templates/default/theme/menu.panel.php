<?php 
//var_dump($_SESSION);
//$user = $this->user->load_user($_SESSION['HDT_uid']);
//var_dump($user);
//exit;?>
<div class="col-md-3 left_col">
	<div class="left_col scroll-view">
            <div class="navbar nav_title" style="border: 0;">
              <a href="http://<?php echo ROOT_URL?>" class="site_title"><span></span></a>
            </div>
            <div class="clearfix"></div>

            <!-- menu profile quick info -->
			<?php
				//if(!isset($_SESSION['HDT_parcel_user'])) {
					$user = $this->user->load_user($_SESSION['HDT_uid']);
				//} elseif(isset($_SESSION['HDT_parcel_user'])) {
				//	$user = $this->parcel->load_user($_SESSION['HDT_uid']);
				//}
				
			?>
            <div class="profile clearfix">
              <div class="profile_pic">
                <!--<img src="images/img.jpg" alt="..." class="img-circle profile_img">-->
              </div>
              <div class="profile_info">
			 
                <!--<a href="<?php echo $this->create_url('edituser');?>&id=<?php echo $_SESSION['HDT_uid'];?>" ><h2><strong><?php echo $user['FullName'];?> ( <?php echo $user["Login"];?> ) </strong></h2></a>
				<a class="logout-link" href="http://<?php echo ROOT_URL .'?logout';?>">Kilépés</a>-->
              </div>
            </div>
            <!-- /menu profile quick info -->

            <br />

            <!-- sidebar menu -->
            <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
				<div class="menu_section">
					<!--<h3>General</h3>-->
					<ul class="nav side-menu">
						<li><a href="javascript:void(0)"><i class="fa fa-home"></i> Kezdőlap </a></li>
						<li class="<?php echo $this->action == 'addmandate' ? 'active' : '';?><?php echo $this->action == 'listmandates' ? 'active' : '';?><?php echo $this->action == 'editmandate' ? 'active' : '';?>"><a><i class="fa fa-wrench"></i> Megbízások <span class="fa fa-chevron-down"></span></a>
							<ul class="nav child_menu">
								<li class="<?php echo $this->action == 'addmandate' ? 'current-page' : '';?>"><a href="<?php echo $this->create_url('addmandate');?>">Új megbízás</a></li>
								<li class="<?php echo $this->action == 'listmandates&mode=unconfirmed' ? 'active' : '';?>"><a href="<?php echo $this->create_url('listmandates&mode=unconfirmed');?>">Várakozó megbízások</a></li>
								<li class="<?php echo $this->action == 'listmandates&mode=confirmed' ? 'active' : '';?>"><a href="<?php echo $this->create_url('listmandates&mode=confirmed');?>">Kiosztott megbízások</a></li>
								<li class="<?php echo $this->action == 'listmandates&mode=billable' ? 'active' : '';?>"><a href="<?php echo $this->create_url('listmandates&mode=billable');?>">Lezárt megbízások</a></li>
							</ul>
						</li>
						
						<?php echo $this->subcontactor->DrawMenu();?>
						<?php echo $this->master->DrawMenu();?>
						<?php echo $this->installations->DrawMenu();?>
						<?php echo $this->reports->DrawMenu();?>
						
						<?php echo $this->mandates_options->DrawMenu();?>
						<!--<li class="<?php echo $this->action == 'adduser' ? 'active' : '';?><?php echo $this->action == 'listusers' ? 'active' : '';?><?php echo $this->action == 'edituser' ? 'active' : '';?>"><a><i class="fa fa-user"></i> Felhasználók <span class="fa fa-chevron-down"></span></a>
							<ul class="nav child_menu">
								<li class="<?php echo $this->action == 'adduser' ? 'current-page' : '';?>"><a href="<?php echo $this->create_url('adduser');?>">Új felhasználó</a></li>
								<li class="<?php echo $this->action == 'listusers' ? 'active' : '';?>"><a href="<?php echo $this->create_url('listusers');?>">Felhasználók</a></li>
							</ul>
						</li>-->
						<?php echo $this->user->DrawMenu();?>
						<!--<li class="<?php echo $this->action == 'addmandates_option' ? 'active' : '';?><?php echo $this->action == 'listmandates_options' ? 'active' : '';?><?php echo $this->action == 'editmandates_option' ? 'active' : '';?>"><a><i class="fa fa-road"></i> Kiszállási opciók <span class="fa fa-chevron-down"></span></a>
							<ul class="nav child_menu">
								<li class="<?php echo $this->action == 'addmandates_options' ? 'current-page' : '';?>"><a href="<?php echo $this->create_url('addmandates_option');?>">Opció hozzáadása</a></li>
								<li class="<?php echo $this->action == 'listmandates_options' ? 'active' : '';?>"><a href="<?php echo $this->create_url('listmandates_options');?>">Opciók</a></li>
							</ul>
						</li>-->
						<?php echo $this->clients->DrawMenu();?>
						<?php echo $this->subclients->DrawMenu();?>
						<?php echo $this->days->DrawMenu();?>
						<li class="<?php echo $this->action == 'debug' ? 'active' : '';?>"><a href="<?php echo $this->create_url('debug');?>"><i class="fa fa-bug"></i> Debug </a></li>
					</ul>
				</div>
            </div>
     </div>
</div>

<!-- top navigation -->
        <div class="top_nav">
          <div class="nav_menu">
            <nav>
              <div class="nav toggle">
                <a id="menu_toggle"><i class="fa fa-bars"></i></a>
              </div>
				<!--<ul id="hdt_topnav" class="nav navbar-nav navbar-left">
					<li class="hazhoz"><a class="hdt-topnav-link" href="">HÁZHOZSZÁLLÍTÁS</a></li>
					<li><a class="hdt-topnav-link" href="">FUVAROK A-BÓL B-BE</a></li>
					<li><a class="hdt-topnav-link" href="">MESTERSZOLGÁLTATÁS</a></li>
					<li><a class="hdt-topnav-link" href="">GARANCIAKÖVETÉS</a></li>
					<li><a class="hdt-topnav-link" href="">RAKTÁR</a></li>
				</ul>
				<ul id="hdt_topnav_mobile" class="nav navbar-nav navbar-left">
					<li class="hazhoz"><a class="hdt-topnav-link" href="">HÁZ</a></li>
					<li><a class="hdt-topnav-link" href="">A-B</a></li>
					<li><a class="hdt-topnav-link" href="">MEST</a></li>
					<li><a class="hdt-topnav-link" href="">GAR</a></li>
					<li><a class="hdt-topnav-link" href="">RAKT</a></li>
				</ul>-->
              <ul class="nav navbar-nav navbar-right">
                <li class="">
                  <a href="javascript:;" class="user-profile dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                    <!--<img src="images/img.jpg" alt="">--><?php echo $user['FullName'];?>
                    <span class=" fa fa-angle-down"></span>
                  </a>
                  <!--<ul class="dropdown-menu dropdown-usermenu pull-right">
                    <li><a href="javascript:;"> Profile</a></li>
                    <li>
                      <a href="javascript:;">
                        <span class="badge bg-red pull-right">50%</span>
                        <span>Settings</span>
                      </a>
                    </li>
                    <li><a href="javascript:;">Help</a></li>
                    <li><a href="login.html"><i class="fa fa-sign-out pull-right"></i> Log Out</a></li>
                  </ul>-->
                  <!--<ul class="dropdown-menu dropdown-usermenu pull-right">
                  	<li>
                  	<span class="userinfo-box">
                  	<?php
					//if(isset($_SESSION['HDT_parcel_user'])) {
					//	echo $this->parcel->userinfo_content();
					//} else {
					//	echo $this->user->userinfo_content();
					//}
					?>
                  	</span>
                  	</li>
                  </ul>-->
                  <!--<a href="javascript:;" class="user-profile dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                    <img src="images/img.jpg" alt="">John Doe
                    <span class=" fa fa-angle-down"></span>
                  </a>-->
                  <ul class="dropdown-menu dropdown-usermenu pull-right">
                    <li><a href="<?php echo $this->create_url('profil');?>&id=<?php echo $_SESSION['HDT_uid'];?>"> Profil</a></li>
                    <!--<li>
                      <a href="javascript:;">
                        <span class="badge bg-red pull-right">50%</span>
                        <span>Settings</span>
                      </a>
                    </li>
                    <li><a href="javascript:;">Help</a></li>-->
					<!--<li><a href="<?php //echo $this->create_url('listemail_contents');?>"> E-mail-ek</a></li>-->
					<?php echo $this->email_contents->DrawMenu();?>
                    <li><a href="http://<?php echo ROOT_URL;?>?logout"><i class="fa fa-sign-out pull-right"></i> Kilépés</a></li>
                  </ul>
                </li>

                <!--<li role="presentation" class="dropdown">
                  <a href="javascript:;" class="dropdown-toggle info-number" data-toggle="dropdown" aria-expanded="false">
                    <i class="fa fa-envelope-o"></i>
                    <span class="badge bg-green">6</span>
                  </a>
                  <ul id="menu1" class="dropdown-menu list-unstyled msg_list" role="menu">
                    <li>
                      <a>
                        <span class="image"><img src="images/img.jpg" alt="Profile Image" /></span>
                        <span>
                          <span>John Smith</span>
                          <span class="time">3 mins ago</span>
                        </span>
                        <span class="message">
                          Film festivals used to be do-or-die moments for movie makers. They were where...
                        </span>
                      </a>
                    </li>
                    <li>
                      <a>
                        <span class="image"><img src="images/img.jpg" alt="Profile Image" /></span>
                        <span>
                          <span>John Smith</span>
                          <span class="time">3 mins ago</span>
                        </span>
                        <span class="message">
                          Film festivals used to be do-or-die moments for movie makers. They were where...
                        </span>
                      </a>
                    </li>
                    <li>
                      <a>
                        <span class="image"><img src="images/img.jpg" alt="Profile Image" /></span>
                        <span>
                          <span>John Smith</span>
                          <span class="time">3 mins ago</span>
                        </span>
                        <span class="message">
                          Film festivals used to be do-or-die moments for movie makers. They were where...
                        </span>
                      </a>
                    </li>
                    <li>
                      <a>
                        <span class="image"><img src="images/img.jpg" alt="Profile Image" /></span>
                        <span>
                          <span>John Smith</span>
                          <span class="time">3 mins ago</span>
                        </span>
                        <span class="message">
                          Film festivals used to be do-or-die moments for movie makers. They were where...
                        </span>
                      </a>
                    </li>
                    <li>
                      <div class="text-center">
                        <a>
                          <strong>See All Alerts</strong>
                          <i class="fa fa-angle-right"></i>
                        </a>
                      </div>
                    </li>
                  </ul>
                </li>-->
              </ul>
            </nav>
          </div>
        </div>
        <!-- /top navigation -->
