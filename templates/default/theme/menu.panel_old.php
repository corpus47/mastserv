<?php $now_url = $_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];?>
          <div class="page-head">
            <ul class="page-head-elements">
                    <li><a href="#" class="page-navigation-toggle"><span class="fa fa-bars"></span></a></li>
                    <li <?php ?>><a href="javascript:void(0);"  onclick="system_change(1)"> 
                        <?php echo $this->lang['app._navigation.hazhozszallitas']; ?>
                        </a>
                    </li>
                    <li <?php ?>><a href="javascript:void(0);" onclick="system_change(2)">
                        <?php echo $this->lang['app._navigation.fuvarok_a_bol_b_be']; ?>
                        </a>
                    </li>
                    <li <?php ?>><a href="javascript:void(0);" onclick="system_change(5)">
                        <?php echo $this->lang['app._navigation.mesterszerviz']; ?>
                        </a>
                    </li>
                    <li <?php ?>><a href="javascript:void(0);" onclick="system_change(4)">
                        <?php echo $this->lang['app._navigation.garanciakovetes']; ?>
                        </a>
                    </li>
                    <li><?php ?><a href="javascript:void(0);" onclick="system_change(3)">
                        <?php echo $this->lang['app._navigation.raktar']; ?>
                        </a>
                    </li>
                </ul>
                
            </div>
            
            <div class="page-navigation">
                <div class="page-navigation-info">
                    <a href="home">Home Delivery Team</a>
                </div>
                <div class="profile">
					<?php 
						$user = $this->user->load_user($_SESSION['HDT_uid']);
					?>
                    <div class="profile-info">
						<!-- Theme change -->
						<!--<select name="login-type" class="form-control theme-select">
							<option value="">Váltson témát - jelenleg ( <?php echo $_SESSION['HDT_theme']?> )</option>
                            <option value="default">Régi (MISI)</option>
							<option value="new">Új téma</option>
						</select>-->
						<!-- #Theme Change -->
                        <a href="<?php echo $this->create_url('profil');?>" class="profile-title" style="text-transform: uppercase;"><?php echo $user['FullName'];?></a>
                        <a href="http://<?php echo ROOT_URL?>?logout"><span class="profile-subtitle"><?php echo $this->lang['app._sidebar.kilepes']; ?></span></a>
                    </div>                 
                </div>

                <ul class="navigation">
                    <?php $menu_order = array('inactiveOrders', 'activeOrders', 'completteOrder'); ?>
					<li <?php if (in_array($now_url, $menu_order)){ echo "class='active open'";}?> ><a href="#"><i class="fa fa-share"></i> <?php echo $this->lang['app._menuOrder.fuvarok']; ?></a>
                        <ul>
                            <li <?php if($now_url == "inactiveOrders"){ echo "class='active'"; }?> ><a href="inactiveOrders"><?php echo $this->lang['app.mesterservice.menuOrder.inaktiv']; ?></a></li>
                            <li <?php if($now_url == "activeOrders"){ echo "class='active'"; }?> ><a href="activeOrders"><?php echo $this->lang['app.mesterservice.menuOrder.aktiv']; ?></a></li>
                            <li <?php if($now_url == "completteOrder"){ echo "class='active'"; }?> ><a href="completteOrder"><?php echo $this->lang['app.mesterservice.menuOrder.complette']; ?></a></li>
                        </ul>
                    </li>
                    <?php $menu_deviceGroup = array('deviceadd','devicelist','devicecategory'); ?>
                    <li <?php if (in_array($now_url, $menu_deviceGroup)){ echo "class='active open'";}?> ><a href="#"><i class="fa fa-share"></i> <?php echo $this->lang['app.mesterservice.menuOrder.device']; ?></a>
                        <ul>
                            <li <?php if($now_url == "devicelist"){ echo "class='active'"; }?> ><a href="devicelist"><?php echo $this->lang['app.mesterservice.menuOrder.devicelist']; ?></a></li>
                            <li <?php if($now_url == "devicecategory"){ echo "class='active'"; }?> ><a href="devicecategory"><?php echo $this->lang['app.mesterservice.menuOrder.devicecategory']; ?></a></li>
                        </ul>
                    </li>
                    <?php $menu_subcontractor = array('mastersubcontractoradd','mastersubcontractorlist'); ?>
                    <li <?php if (in_array($now_url, $menu_subcontractor)){ echo "class='active open'";}?> ><a href="#"><i class="fa fa-share"></i> <?php echo $this->lang['app._menuPartnerHandling.alvallalkozok']; ?></a>
                        <ul>
                            <li <?php if($now_url == "mastersubcontractoradd"){ echo "class='active'"; }?> ><a href="mastersubcontractoradd"><?php echo $this->lang['app._menuPartnerHandling.uj_alvallalkozo_felvetele']; ?></a></li>
                            <li <?php if($now_url == "mastersubcontractorlist"){ echo "class='active'"; }?> ><a href="mastersubcontractorlist"><?php echo $this->lang['app._menuPartnerHandling.alvallalkozo_kezelese']; ?></a></li>
                        </ul>
                    </li>
                     
					<!-- Új -->

					<li class="<?php echo $this->action == 'home' ? 'active' : ''; ?>">
						<a href="<?php echo $this->create_url('home');?>">
							<i class="fa fa-home"></i>Kezdőlap
						</a>
					</li>
					<li class="">
						<a href="javascript:void(0);">
							<i class="fa fa-users"></i>Felhasználók
						</a>
						<ul>
							<li class="<?php echo $this->action == 'adduser' ? 'active' : ''; ?>">
								<a href="<?php echo $this->create_url('adduser');?>">
									<i class="fa fa-user"></i>Új felhasználó
								</a>
							</li>
						</ul>
					</li>
					<li class="<?php echo $this->action == 'debug' ? 'active' : ''; ?>">
						<a href="<?php echo $this->create_url('debug');?>">
							<i class="fa fa-bug"></i>Debug
						</a>
					</li>
					 
                </ul>                
            </div>

<script>
  
</script>

            
            <div class="page-content">
            <div class="container">
