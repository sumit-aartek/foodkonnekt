<div id="container" class="container">
	<div class="main-content-holder">
		<div id="header" class="container">
			<a href="#" class="header-logo">
				<img src="<?php echo PJ_INSTALL_URL . PJ_THIRD_PARTY_PATH; ?>front/images/logo.png">
			</a>
		</div><!--#header-->
		
		<div id="main-content-conatiner" class="container">
			<!--<form>
				<div class="form-group">
					<label for="exampleInputEmail1">Email address *</label>
					<input type="email" class="form-control" id="exampleInputEmail1" placeholder="Email">
				</div>
				<div class="form-group">
					<label for="exampleInputPassword1">Password *</label>
					<input type="password" class="form-control" id="exampleInputPassword1" placeholder="Password">
				</div>
				<div class="col-md-6">
					Don't have an account yet?&nbsp;<a href="#" class="fdContinue">Continue</a>
				</div>
				<div class="col-md-6">
					<button type="button" class="btn btn-danger">Login</button>
				</div>
				<div class="clearfix"></div>
			</form>			
			<hr>-->
			<form action="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminOptions&action=pjActionPreview" method="post" id="foodkonnekt-form">
				<input type="hidden" name="o_user_id" value="<?php echo $tpl['user']; ?>" />
				<input type="hidden" name="o_user_name" value="<?php echo $tpl['name']; ?>" />
				<h3>1. Select Location</h3>
				<div class="col-sm-4">
					<select id="location_id" name="o_location_id" class="form-control">
						<option value="">Select Location</option>
						<?php
						$i = count($tpl['location']);
						foreach($tpl['location'] as $row) { ?>
						<option value="<?php echo $row['id']; ?>" <?php if($i==1){echo 'selected="selected"';} ?>><?php echo $row['name']; ?></option>
						<?php $i++; } ?>
					</select>
				</div>
				<div class="col-sm-8">
					<p>Address : <span id="fdPickupAddressLabel"></span></p>
					<input type="hidden" id="fdPickupAddressText" name="o_address" value="" readonly="readonly" />
				</div>
				<!--<div class="col-sm-8">
					<ul class="nav navbar-nav">
						<li class="dropdown">
							<a href="" class="dropdown-toggle" data-toggle="dropdown">Dropdown <b class="caret"></b></a>
							<ul class="dropdown-menu">
								<?php foreach($tpl['location'] as $row) { ?>
								<li><a href="<?php echo $row['id']; ?>"><?php echo $row['name']; ?></a></li>
								<?php } ?>
							</ul>
						</li>
					</ul>
				</div>-->
				<div class="clearfix"></div>
				<hr>
				<h3>2. Would you like Delivery or Take Out?</h3>
				<div class="col-sm-3">	
					<label class="radio checked">
					<span class="icons"><span class="first-icon fa fa-circle-o"></span><span class="second-icon fa fa-dot-circle-o"></span></span>
					<input type="radio" name="o_takeOut" data-toggle="radio" id="optionsRadios2" value="Delivery" />
					<i></i>Delivery
				  </label>
				</div>
				<div class="col-sm-3">
				<label class="radio">
					<span class="icons"><span class="first-icon fa fa-circle-o"></span><span class="second-icon fa fa-dot-circle-o"></span></span>
					<input type="radio" name="o_takeOut" data-toggle="radio" id="optionsRadios1" value="Take Out" />
					<i></i>Take Out
				  </label>
				</div> 
				<div class="clearfix"></div>
				<hr>
				<h3>3. When would you like your order? </h3>
				<div class="col-sm-3">	
					<label class="radio checked">
					<span class="icons"><span class="first-icon fa fa-circle-o"></span><span class="second-icon fa fa-dot-circle-o"></span></span>
					<input type="radio" name="o_order" data-toggle="radio" id="optionsRadios2" value="Now" checked />
					<i></i>Now
				  </label>
				</div>
				<!--<div class="col-sm-3">
				<label class="radio">
					<span class="icons"><span class="first-icon fa fa-circle-o"></span><span class="second-icon fa fa-dot-circle-o"></span></span>
					<input type="radio" name="o_order" data-toggle="radio" id="optionsRadios1" value="Later" checked />
					<i></i>Later
				  </label>
				</div> -->
				<div class="clearfix"></div>
				<hr>
				<div id="date-picker" class="col-sm-6">
					<div class="dateholder">
						<input type="text" name="o_date" id="datepicker" />
					</div>
				</div><!--#date-picker-->
				<div id="time-picker" class="col-sm-6">
				</div><!--#time-picker-->
				<div class="clearfix"></div>
				<hr>
				<div class="col-md-5">
					<input type="submit" name="submit" class="btn btn-block btn-lg btn-info btn-round" value="Start New Order" />
				</div>
			</form><!--#foodkonnekt-form-->
		</div><!--#main-content-conatiner-->
		
		<div id="footer" class="container">
			<a href="#"><img src="<?php echo PJ_INSTALL_URL . PJ_THIRD_PARTY_PATH; ?>front/images/logo.png" class="footer-logo"></a>
		</div><!--#footer-->
	</div><!--.main-content-holder-->
</div><!--.container-->