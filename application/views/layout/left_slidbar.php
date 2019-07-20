<body style="font-family: Calibri; font-size: 15px;">
<div class="page-sidebar">
                <!-- START X-NAVIGATION -->
                <ul class="x-navigation">
                    <li class="xn-logo">
                        <a href="#"><?php echo $username = $this->session->userdata('user_name'); ?></a>
                        <a href="#" class="x-navigation-control"></a>
                    </li>
                    <li class="xn-profile">
                        <a href="#" class="profile-mini">
                            <img src="assets/images/users/avatar.png" alt="John Doe"/>
                        </a>
                        <div class="profile">
                            <div class="profile-image">
                                <img src="assets/images/users/avatar.png" alt="John Doe"/>
                            </div>
                            <div class="profile-data">
                                <div class="profile-data-name"><?php echo $username = $this->session->userdata('user_name'); ?></div>
<!--                                <div class="profile-data-title">Web Developer/Designer</div>-->
                            </div>
<!--                            <div class="profile-controls">
                                <a href="pages-profile.html" class="profile-control-left"><span class="fa fa-info"></span></a>
                                <a href="pages-messages.html" class="profile-control-right"><span class="fa fa-envelope"></span></a>
                            </div>-->
                        </div>                                                                        
                    </li>
                    <li class="xn-title">Form</li>
<!--                    <li class="">
                        <a href="<?php echo base_url();?>hardness_chilldept"><span class="fa fa-envelope"></span> <span class="xn-text">Hardness & Chilldepth</span></a>                        
                    </li>-->
<?php 
if($this->session->userdata('access')=="5" || $this->session->userdata('access')=="4")
{
?>
                    
                    <li <?php if($this->uri->segment(1)=="logsheet"){echo 'class="active"';}?>>
                        <a href="<?php echo base_url();?>logsheet"><span class="fa fa-file-text-o"></span> <span class="xn-text">Extrusion LogSheet</span></a>                        
                    </li>
                    <li <?php if($this->uri->segment(1)=="CaCo3_report"){echo 'class="active"';}?>>
                        <a href="<?php echo base_url();?>CaCo3_report"><span class="fa fa-file-text-o"></span> <span class="xn-text">CaCo3 Report</span></a>                        
                    </li>
                    <li <?php if($this->uri->segment(1)=="logsheet_report"){echo 'class="active"';}?>>
                        <a href="<?php echo base_url();?>logsheet_report"><span class="fa fa-file-text-o"></span> <span class="xn-text">Log sheet Report</span></a>                        
                    </li>
                    <li <?php if($this->uri->segment(1)=="dashboard"){echo 'class="active"';}?>>
                        <a href="<?php echo base_url();?>dashboard"><span class="fa fa-file-text-o"></span> <span class="xn-text">Dashboard</span></a>                        
                    </li>
                    
<?php }?>                
                    
                </ul>
                <!-- END X-NAVIGATION -->
            </div>