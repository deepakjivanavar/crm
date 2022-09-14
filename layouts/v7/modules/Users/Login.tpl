{*+**********************************************************************************
* The contents of this file are subject to the vtiger CRM Public License Version 1.1
* ("License"); You may not use this file except in compliance with the License
* The Original Code is: vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
************************************************************************************}
{* modules/Users/views/Login.php *}

{strip}
	<style>
		body {
		    
/* UI Properties */
background-image:url('layouts/v7/resources/Images/Group 1373.png');
  background-position: right;
 opacity: 0.89;
 
			/*background: url();*/
			/*background-position: center;*/
			/*background-size: cover;*/
			/*width: 100%;*/
			/*background-repeat: no-repeat;*/
		}
		
		.loginside{

background: transparent url('layouts/v7/resources/Images/oline.png') 0% 0% no-repeat padding-box;
opacity: 1;
padding-top:86px;
		}
		
			.loginrightside{
			    margin-top:18px;
background:#f7f4f28a;
/*background: transparent url('layouts/v7/resources/Images/Rectangle 250.png') 0% 0% no-repeat padding-box;*/
box-shadow: 12px 12px 20px #00000029;
opacity: 1;
backdrop-filter: blur(50px);
-webkit-backdrop-filter: blur(50px);
		}
		
	 
		.loginimg{
		    
		    padding-left:27px;
		    
		    padding-right:27px;
		    width: 100%;
 		}
		
		.loginp{
		    padding:50px;
		    text-align: center;
letter-spacing: 0px;
color: #202020;
  

		}
		
		.login-head{
		    
		margin: 20px 20px 40px;
 text-align:center; font-size:21px; color:#202020; border-bottom:2px solid #7D86A9;    
		}
		
		hr {
			margin-top: 15px;
			background-color: #7C7C7C;
			height: 2px;
			border-width: 0;
		}
		h3, h4 {
			margin-top: 0px;
		}
		hgroup {
			text-align:center;
			margin-top: 4em;
		}
		input {
		    margin-top:-10px;
		    background:transparent;
			font-size: 16px;
			padding: 10px 10px 10px 0px;
			-webkit-appearance: none;
			display: block;
			color: #636363;
			width: 100%;
			border: none;
			border-radius: 0;
			border-bottom: 1px solid #757575;
		}
		input:focus {
			outline: none;
		}
		label {
		    		    margin-top:-10px;

			font-size: 16px;
			font-weight: normal;
			position: absolute;
			pointer-events: none;
			left: 0px;
			top: 10px;
			transition: all 0.2s ease;
		}
		input:focus ~ label, input.used ~ label {
			top: -20px;
			transform: scale(.75);
			left: -12px;
			font-size: 18px;
		}
		input:focus ~ .bar:before, input:focus ~ .bar:after {
			width: 50%;
		}
		#page {
			padding-top: 86px;
		}
		.widgetHeight {
			height: 409px;
			margin-top: 111px !important;
		}
		.loginDiv {
			max-width: 380px;
			margin: 0 auto;
			border-radius: 4px;
			box-shadow: 0 0 10px gray;
			background-color: #FFFFFF;
		}
		.marketingDiv {
			color: #303030;
                        height: 510px !important;
		}
		.separatorDiv {
			background-color: #7C7C7C;
			width: 2px;
			height: 460px;
			margin-left: 20px;
		}
		.user-logo {
			height: 90px;
			margin: 0 auto;
			padding-top: 0px;
			padding-bottom: 20px;
		}
		.blockLink {
			border: 1px solid #303030;
			padding: 3px 5px;
		}
		.group {
			position: relative;
			margin: 20px 20px 40px;
		}
		.failureMessage {
			color: red;
			display: block;
			text-align: center;
			padding: 0px 0px 10px;
		}
		.successMessage {
			color: green;
			display: block;
			text-align: center;
			padding: 0px 0px 10px;
		}
		.inActiveImgDiv {
			padding: 5px;
			text-align: center;
			margin: 30px 0px;
		}
		.app-footer p {
			margin-top: 0px;
		}
		.footer {
			background-color: #fbfbfb;
			height:26px;
		}
		.bar {
			position: relative;
			display: block;
			width: 100%;
		}
		.bar:before, .bar:after {
			content: '';
			width: 0;
			bottom: 1px;
			position: absolute;
			height: 1px;
			background: #202020;
			transition: all 0.2s ease;
		}
		.bar:before {
			left: 50%;
		}
		.bar:after {
			right: 50%;
		}
		.button {
			position: relative;
			display: inline-block;
			padding: 9px;
			margin: .3em 0 1em 0;
			width: 100%;
			vertical-align: middle;
			color: #202020;
			font-size: 16px;
			line-height: 20px;
			-webkit-font-smoothing: antialiased;
			text-align: center;
			letter-spacing: 1px;
			background: transparent;
			border:0;
			border-radius:5px;
			cursor: pointer;
			transition: all 0.15s ease;
			font-weight:bold;
			
			 background: linear-gradient(to left, transparent 50%, #f05c22 50%) right;
    background-size: 200%;
    transition: .5s ease-out;
		}
		
		.button:hover{
		    color:#fff;
		        background-position: left;
   
		}
		
		.button:focus {
			outline: 0;
		}
		.buttonBlue {
			background-image:transparent;
		}
		.ripples {
			position: absolute;
			top: 0;
			left: 0;
			width: 100%;
			height: 100%;
			overflow: hidden;
			background: transparent;
		}
                .mCSB_container{
                    height: inherit;
                }

		//Animations
		@keyframes inputHighlighter {
			from {
				background: #4a89dc;
			}
			to 	{
				width: 0;
				background: transparent;
			}
		}
		@keyframes ripples {
			0% {
				opacity: 0;
			}
			25% {
				opacity: 1;
			}
			100% {
				width: 200%;
				padding-bottom: 200%;
				opacity: 0;
			}
		}
	</style>

	<span class="app-nav"></span>
	<div class="container" style="margin-top:-20px;">
	    <div class="col-md-1"></div>
	    		<div class="col-md-5 hidden-xs hidden-sm hidden-md loginside">
	    		    	<!--<img align="left" class="logocrm img-responsive user-logo">-->
			<img align="center" class="loginimg" src="layouts/v7/resources/Images/loginimg.jpg">
			<p class="loginp">Improve customer relationships by incorporating automated workflow into your business processes. 
			Increase your sales with the best CRM Software Solutions- Nectar CRM.</p>
		</div>

		<div class="col-md-5 col-sm-12 col-xs-12 loginrightside"  >
			<div class=" widgetHeight">
							    	    		    	<img align="center" class=" img-responsive user-logo" src="layouts/v7/resources/Images/nectarcrm logo.png"  >

				<div>
					<span class="{if !$ERROR}hide{/if} failureMessage" id="validationMessage">{$MESSAGE}</span>
					<span class="{if !$MAIL_STATUS}hide{/if} successMessage">{$MESSAGE}</span>
				</div>

				<div id="loginFormDiv">

					<form class="form-horizontal" method="POST" action="index.php">

						<input type="hidden" name="module" value="Users"/>
						<input type="hidden" name="action" value="Login"/>
						<div class="group">
							<input id="username" type="text" name="username" placeholder="Username">
							<span class="bar"></span>
							<label>Username</label>
						</div>
						<div class="group">
							<input id="password" type="password" name="password" placeholder="Password">
							<span class="bar"></span>
							<label>Password</label>
						</div>
						<div align="center" class="group">
							<button type="submit" class="button buttonBlue">LOGIN</button><br>
							<a  class="forgotPasswordLink" style="color: #545454;">Forgot your password? click here</a>
						</div>
					</form>
				</div>

				<div id="forgotPasswordDiv" class="hide">
					<form class="form-horizontal" action="forgotPassword.php" method="POST">
						<div class="group">
							<input id="fusername" type="text" name="username" placeholder="Username" >
							<span class="bar"></span>
							<label>Username</label>
						</div>
						<div class="group">
							<input id="email" type="email" name="emailId" placeholder="Email" >
							<span class="bar"></span>
							<label>Email</label>
						</div>
						<div class="group">
							<button type="submit" class="button buttonBlue forgot-submit-btn">Submit</button><br>
							<span>Please enter details and submit<a class="forgotPasswordLink pull-right" style="color: #15c;">Back</a></span>
						</div>
					</form>
				</div>
			</div>
		</div>
<div class="col-md-1"></div>
		<!--<div class="col-lg-1 hidden-xs hidden-sm hidden-md">-->
			<!--<div class="separatorDiv"></div>-->
		<!--</div>-->


		<script>
			jQuery(document).ready(function () {
				var validationMessage = jQuery('#validationMessage');
				var forgotPasswordDiv = jQuery('#forgotPasswordDiv');

				var loginFormDiv = jQuery('#loginFormDiv');
				loginFormDiv.find('#password').focus();

				loginFormDiv.find('a').click(function () {
					loginFormDiv.toggleClass('hide');
					forgotPasswordDiv.toggleClass('hide');
					validationMessage.addClass('hide');
				});

				forgotPasswordDiv.find('a').click(function () {
					loginFormDiv.toggleClass('hide');
					forgotPasswordDiv.toggleClass('hide');
					validationMessage.addClass('hide');
				});

				loginFormDiv.find('button').on('click', function () {
					var username = loginFormDiv.find('#username').val();
					var password = jQuery('#password').val();
					var result = true;
					var errorMessage = '';
					if (username === '') {
						errorMessage = 'Please enter valid username';
						result = false;
					} else if (password === '') {
						errorMessage = 'Please enter valid password';
						result = false;
					}
					if (errorMessage) {
						validationMessage.removeClass('hide').text(errorMessage);
					}
					return result;
				});

				forgotPasswordDiv.find('button').on('click', function () {
					var username = jQuery('#forgotPasswordDiv #fusername').val();
					var email = jQuery('#email').val();

					var email1 = email.replace(/^\s+/, '').replace(/\s+$/, '');
					var emailFilter = /^[^@]+@[^@.]+\.[^@]*\w\w$/;
					var illegalChars = /[\(\)\<\>\,\;\:\\\"\[\]]/;

					var result = true;
					var errorMessage = '';
					if (username === '') {
						errorMessage = 'Please enter valid username';
						result = false;
					} else if (!emailFilter.test(email1) || email == '') {
						errorMessage = 'Please enter valid email address';
						result = false;
					} else if (email.match(illegalChars)) {
						errorMessage = 'The email address contains illegal characters.';
						result = false;
					}
					if (errorMessage) {
						validationMessage.removeClass('hide').text(errorMessage);
					}
					return result;
				});
				jQuery('input').blur(function (e) {
					var currentElement = jQuery(e.currentTarget);
					if (currentElement.val()) {
						currentElement.addClass('used');
					} else {
						currentElement.removeClass('used');
					}
				});

				var ripples = jQuery('.ripples');
				ripples.on('click.Ripples', function (e) {
					jQuery(e.currentTarget).addClass('is-active');
				});

				ripples.on('animationend webkitAnimationEnd mozAnimationEnd oanimationend MSAnimationEnd', function (e) {
					jQuery(e.currentTarget).removeClass('is-active');
				});
				loginFormDiv.find('#username').focus();

				var slider = jQuery('.bxslider').bxSlider({
					auto: true,
					pause: 4000,
					nextText: "",
					prevText: "",
					autoHover: true
				});
				jQuery('.bx-prev, .bx-next, .bx-pager-item').live('click',function(){ slider.startAuto(); });
				jQuery('.bx-wrapper .bx-viewport').css('background-color', 'transparent');
				jQuery('.bx-wrapper .bxslider li').css('text-align', 'left');
				jQuery('.bx-wrapper .bx-pager').css('bottom', '-40px');

				var params = {
					theme		: 'dark-thick',
					setHeight	: '100%',
					advanced	:	{
										autoExpandHorizontalScroll:true,
										setTop: 0
									}
				};
				jQuery('.scrollContainer').mCustomScrollbar(params);
			});
		</script>
		</div>
	{/strip}