<!DOCTYPE html>
<html>
<head>
<link href='http://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800' rel='stylesheet' type='text/css'>
<title>Google Calendar Authorization</title>
<style type="text/css">

body *{font-family: 'Open Sans', Arial, sans-serif !important}

div, p, a, li, td { -webkit-text-size-adjust:none; }

*{-webkit-font-smoothing: antialiased;-moz-osx-font-smoothing: grayscale;}
td{word-break: break-word;}
a{word-break: break-word; text-decoration: none; color: inherit;}

body .ReadMsgBody
{width: 100%; background-color: #ffffff;}
body .ExternalClass
{width: 100%; background-color: #ffffff;}
body{width: 100%; height: 100%; background-color: #ffffff; margin:0; padding:0; -webkit-font-smoothing: antialiased;}
html{ background-color:#ffffff; width: 100%;}

body p {padding: 0!important; margin-top: 0!important; margin-right: 0!important; margin-bottom: 0!important; margin-left: 0!important; }
body img {user-drag: none; -moz-user-select: none; -webkit-user-drag: none;}
body a.rotator img {-webkit-transition: all 1s ease-in-out;-moz-transition: all 1s ease-in-out; -o-transition: all 1s ease-in-out; -ms-transition: all 1s ease-in-out; }
body a.rotator img:hover {-webkit-transform: rotate(360deg); -moz-transform: rotate(360deg); -o-transform: rotate(360deg);-ms-transform: rotate(360deg); }
body .hover:hover {opacity:0.85;filter:alpha(opacity=85);}
body .jump:hover {opacity:0.75; filter:alpha(opacity=75); padding-top: 10px!important;}
body #opacity {opacity:0.90;filter:alpha(opacity=90);}

body #logo160 img {width: 160px; height: auto;}
body .icon22 img {width: 22px; height: auto;}
body .image190 img {width: 190px; height: auto;}
body .image274 img {width: 274px; height: auto;}
body .icon53 img {width: 53px; height: auto;}
body .icon27 img {width: 27px; height: auto;}
body .image125 img {width: 125px; height: auto;}
body .image226 img {width: 226px; height: auto;}
body .avatar89 img {width: 89px; height: auto;}
body .logo130 img {width: 130px; height: auto;}
body .logo150 img {width: 150px; height: auto;}


</style>

<style type="text/css">@media only screen and (max-width: 640px){
		body body{width:auto!important;}
		body table[class=full] {width: 100%!important; clear: both; }
		body table[class=mobile] {width: 100%!important; padding-left: 30px; padding-right: 30px; clear: both; }
		body table[class=fullCenter] {width: 100%!important; text-align: center!important; clear: both; }
		body td[class=fullCenter] {width: 100%!important; text-align: center!important; clear: both; }
		body .erase {display: none;}
		body .buttonScale {float: none!important; text-align: center!important; display: inline-block!important; clear: both;}
		body .buttonScale2 {float: none!important; text-align: center!important; vertical-align: bottom!important; height: 0px!important; padding-left: 10px!important; padding-right: 10px!important; display: inline-table!important; padding-bottom: 7px!important; clear: both;}
		body .image515 img {width: 100%!important;}
		body .break {display: block!important;}
		body td[class=pad20] {padding-left: 20px!important; padding-right: 20px!important; text-align: center!important; clear: both; }
		body *[class=h10] {width: 100%!important; height: 10px!important;}
		body *[class=h20] {width: 100%!important; height: 20px!important;}
		body *[class=h40] {width: 100%!important; height: 40px!important;}
		body *[class=h50] {width: 100%!important; height: 40px!important;}
		body *[class=h30] {width: 100%!important; height: 30px!important;}
		body table[class=sponsor] {text-align:center; float:none; width:360px;}
		body table[class=mcenter] {text-align:center; vertical-align:middle; clear:both!important; float:none; margin: 0px!important;}
		body table[class=table33] {width: 33%!important; text-align: center!important; }
		body .image197 img {width: 100%!important;}	
		body .image560 img {width: 100%!important;}	

}</style>

<style type="text/css">@media only screen and (max-width: 479px){ 
		body body{width:auto!important;}
		body table[class=full] {width: 100%!important; clear: both; }
		body table[class=mobile] {width: 100%!important; padding-left: 20px; padding-right: 20px; clear: both; }
		body table[class=fullCenter] {width: 100%!important; text-align: center!important; clear: both; }
		body td[class=fullCenter] {width: 100%!important; text-align: center!important; clear: both; }
		body .erase {display: none;}
		body .buttonScale {float: none!important; text-align: center!important; display: inline-block!important; clear: both;}
		body .buttonScale2 {width: 100%!important; text-align: center!important; vertical-align: middle!important; height: 0px!important; padding-left: 0px!important; padding-right: 0px!important; padding-bottom: 5px!important; padding-top: 5px!important; clear: both;}
		body .eraseMob {display: none!important;}
		body .font30 {font-size: 30px!important; line-height: 34px!important;}
		body .image310 img {width: 100%!important;}
		body .image515 img {width: 100%!important;}
		body .image275 img {width: 100%!important; text-align: center!important; clear: both; }
		body td[class=pad20] {padding-left: 20px!important; padding-right: 20px!important; text-align: center!important; clear: both;}
		body .break {display: block!important;}
		body table[class=mcenter] {text-align:center; vertical-align:middle; clear:both!important; float:none; margin: 0px!important;}
		body *[class=h10] {width: 100%!important; height: 10px!important;}
		body *[class=h20] {width: 100%!important; height: 20px!important;}
		body *[class=h30] {width: 100%!important; height: 30px!important;}
		body *[class=h40] {width: 100%!important; height: 40px!important;}
		body table[class=sponsor] {text-align:center; float:none; width:260px;}
		body table[class=mcenter2] {text-align:center; vertical-align:middle; clear:both!important; float:none; margin: 0px!important;}
		body table[class=table33] {width: 100%!important; text-align: center!important; clear: both; }
		body .image197 img {width: 100%!important;}
		body .image560 img {width: 100%!important;}	
		body .image226 img {width: 100%!important;}
		

}</style>

</head>
<body style='margin: 0; padding: 0;'>

<table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" class="full" bgcolor="#f0f0f0" style="background-color: #f0f0f0;">
	<tbody><tr>
		<td align="center" style="background-image: url('https://medicloud.sg/images/56a6119add3a9mc_header.png'); background-size: cover; background-position: 50% 50%; background-repeat: no-repeat;" background="https://medicloud.sg/images/header_bg.jpg">
		
			<table class="mobile" align="center" border="0" width="100%" cellpadding="0" cellspacing="0">
				<tbody><tr>
					<td align="center">
					
						<!-- Start Nav -->
						<table width="600" border="0" cellpadding="0" cellspacing="0" align="center" class="full">
							<tbody><tr>
								<td width="100%" height="20" class="h20" style="font-size: 1px; line-height: 1px;">&nbsp;</td>
							</tr>
							<tr>
								<td width="100%" valign="middle" align="center">
								
									<!-- Logo -->
									<table width="160" border="0" cellpadding="0" cellspacing="0" align="left" style="border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;" class="fullCenter">
										<tbody><tr>
											<td height="50" valign="middle" align="center" width="100%" class="fullCenter" style="padding-bottom:90px" id="logo160">
												<a href="#" style="text-decoration: none;"> 
													<img src="https://medicloud.sg/images/56a610674239bmc_logo.png" width="160" alt="" border="0" class="hover">
												</a>
											</td>
										</tr>
									</tbody></table>
								</td>
							</tr>
						</tbody></table>
						<!-- End Nav -->
					
						<table width="600" border="0" cellpadding="0" cellspacing="0" align="center" class="full">
							<tbody><tr>
								<td width="100%" align="center">
									
									<table style="border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;" class="full" align="center" border="0" width="100%" cellpadding="0" cellspacing="0">
										<tbody><tr>
											<td height="40" width="100%" style="font-size: 1px; line-height: 1px;" class="h20">&nbsp;</td>
										</tr>
									</tbody></table> 
												
									<table width="600" border="0" cellpadding="0" cellspacing="0" align="center" style="border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;" class="full">
										<tbody><tr>
											<td height="10" width="100%" style="font-size: 1px; line-height: 1px;">&nbsp;</td>
										</tr>
										<tr>
											<td width="100%" style="color: #4f4f4f; font-family: Helvetica, Arial, sans-serif, 'Open Sans'; line-height: 10px; font-weight: 400; vertical-align: top; font-size: 24px; text-align: left; padding-top: 3px; text-transform: uppercase; word-break: break-word;" class="fullCenter">
												<span style="font-family:arial,helvetica,sans-serif;"><span style="font-size:32px;"><strong><span style="color:#FFF0F5;">GOOGLE CALENDAR<span style="font-size:48px;"></strong></span></span>
											</td>
										</tr>

										<tr>
											<td width="100%" style="color: #959595; font-family: Helvetica, Arial, sans-serif, 'Open Sans'; line-height: 22px; font-weight: 600; vertical-align: top; font-size: 13px; text-align: left; padding-top: 10px;" class="fullCenter">
												<p><span style="font-family:arial,helvetica,sans-serif;"><span style="color:#FFF0F5;"><span style="font-size:18px;">connect with medicloud</span></span></span></p></td>
										</tr>

										
										<tr>
											<td height="120" width="100%" style="font-size: 1px; line-height: 1px;">&nbsp;</td>
										</tr>
									</tbody></table>
						
								</td>
							</tr>
						</tbody></table>
			
					</td>
				</tr>
			</tbody></table> 
			
		</td>
	</tr>
</table>







<table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" class="full" bgcolor="#ffffff" style="background-color: #ffffff;">
	<tbody><tr>
		<td width="100%" valign="top" align="center">
			
			<table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" class="mobile">
				<tbody><tr>
					<td align="center">
						
						<table width="600" border="0" cellpadding="0" cellspacing="0" align="center" class="full">
							<tbody><tr>
								<td width="100%" align="center">
								
									<!-- Text -->
									<table width="600" border="0" cellpadding="0" cellspacing="0" align="left" style="border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt; text-align: center;" class="fullCenter">
										<tbody>
											<tr>
												<td valign="middle" width="100%" style="text-align: left; font-family: Helvetica, Arial, sans-serif, 'Open Sans'; font-size: 14px; color: #9c9c9c; line-height: 24px; font-weight: 400;" class="fullCenter">
													<p><span style="color:#666666;"><strong>Hello There!</strong></span><span style="color:#000000;"><strong><br></strong></span></p><p><br></p><p>Thank you for using the Mednefits platform, please click on the Authorize button given below to connect your google calendar with Mednefits, upon successful authorization entries added in google calendar and Mednefits calendar will be in sync.<br></p>
												</td>
											</tr>
											<tr>
												<td width="100%" height="60" style="font-size: 1px; line-height: 1px;">&nbsp;</td>
											</tr>
											<tr>
											<td style="text-align: center;">
												<a  href="{{$link}}" style="text-decoration: none;">
														<img src="https://medicloud.sg/assets/css/images/authorize.png" alt="" border="0">
												</a>
											</td>
											</tr>
										</tbody>
									</table>
									
								</td>
							</tr>
						</tbody></table>
						
					</td>
				</tr>
			</tbody></table>
		
		</td>
	</tr>
</table>






<table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" class="full" bgcolor="#ffffff" style="background-color: #ffffff;">
	<tbody><tr>
		<td width="100%" valign="top" align="center">
			
			<table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" class="mobile">
				<tbody><tr>
					<td align="center">
						
						<table width="600" border="0" cellpadding="0" cellspacing="0" align="center" class="full">
							<tbody><tr>
								<td width="100%" align="center">
								
									<!-- Text -->
									<table width="600" border="0" cellpadding="0" cellspacing="0" align="left" style="border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt; text-align: center;" class="fullCenter">
										<tbody><tr>
											<td width="100%" height="15" style="font-size: 1px; line-height: 1px;">&nbsp;</td>
										</tr>
										<tr>
											<td width="100%" height="35" style="font-size: 1px; line-height: 1px;">&nbsp;</td>
										</tr>
										</td>
										</tr>

										<tr>
											<td valign="middle" width="100%" style="text-align: left; font-family: Helvetica, Arial, sans-serif, 'Open Sans'; font-size: 14px; color: #9c9c9c; line-height: 24px; font-weight: 400;" class="fullCenter">
												<p style="text-align: center;">+65 3163 5403 | <a href="mailto:happiness@mednefits.com" style="text-decoration: none;"> happiness@mednefits.com </a><br>​1 Temasek Boulevard #18-02, Suntec Tower One, Singapore 038987</p>
											</td>
										</tr>
										<tr>
											<td width="100%" height="10" style="font-size: 1px; line-height: 1px;">&nbsp;</td>
										</tr>
										<table width="200" border="0" cellpadding="0" cellspacing="0" align="center" style="border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;" class="fullCenter">
										<tbody>
										<tr>
											<td valign="middle" align="center" width="100%" class="fullCenter" style="line-height: 1px;">
												<a href="https://www.facebook.com/Medicloud-832802676807391/" style="text-decoration: none;"> 
													<img src="https://medicloud.sg/images/mc_fb.png" width="25px" alt="" border="0" class="hover">
												</a>
												<a href="https://www.youtube.com/watch?v=vKZsKQQ7kyw" style="text-decoration: none;"> 
													<img src="https://medicloud.sg/images/mc_tube.png" width="25px" alt="" border="0" class="hover">
												</a>

												<a href="https://twitter.com/medicloudsg" style="text-decoration: none;"> 
													<img src="https://medicloud.sg/images/mc_tweet.png" width="25px" alt="" border="0" class="hover">
												</a>

												<a href=" https://www.linkedin.com/company/13238401" style="text-decoration: none;"> 
													<img src="https://medicloud.sg/images/mc_in.png" width="25px" alt="" border="0" class="hover">
												</a>

											</td>
										</tr>
										<tr>
											<td width="100%" height="20" style="font-size: 1px; line-height: 1px;">&nbsp;</td>
										</tr>


									</tbody></table>

									</tbody></table>
									
								</td>
							</tr>
						</tbody></table>
						
					</td>
				</tr>
			</tbody></table>
		
		</td>
	</tr>
</table>







<table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" class="full" bgcolor="#f5f5f5" style="background-color: #f5f5f5;">
	<tbody>
		<tr>
		<td width="100%" valign="top" align="center">
		
			<table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" class="mobile">
				<tbody>
					<tr>
						<td align="center">
						
							<table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" style="border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;" class="full">
								<tbody>
									<tr>
										<td width="100%" height="30" style="font-size: 1px; line-height: 1px;">&nbsp;</td>
									</tr>
								</tbody>
							</table>
							
							<table width="600" border="0" cellpadding="0" cellspacing="0" align="center" class="fullCenter">
								<tbody>
									<tr>
										
										
									<table width="370" border="0" cellpadding="0" cellspacing="0" align="center" style="border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;" class="full">
										<tbody>
											<tr>
												<td valign="middle" width="100%" style="text-align: center; font-family: Helvetica, Arial, sans-serif, 'Open Sans'; font-size: 13px; color: #9d9d9d; line-height: 24px; font-weight: 400;" class="fullCenter">
													You can unsubscribe by clicking the link below
												</td>
											</tr>
											<tr>
												<td width="100%" style="color: #333333; font-family: Helvetica, Arial, sans-serif, 'Open Sans'; font-weight: 400; vertical-align: top; font-size: 13px; text-align: center; line-height: 24px;" class="fullCenter">
													<a href="mailto:info@medicloud.sg" style="text-decoration: none; color: #666666;">Unsubscribe</a>|
													<a href="https://medicloud.sg/?page_id=4234" style="text-decoration: none; color: #666666;">Terms of use</a>|
													<a href="https://medicloud.sg/?page_id=4261" style="text-decoration: none; color: #666666;">Privacy Policy</a>


												</td>
											</tr>
										</tbody>
									</table>
										
								</td>
							</tr>
						</tbody>
					</table>
												
						<table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" style="border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;" class="full">
							<tbody><tr>
								<td width="100%" height="30" style="font-size: 1px; line-height: 1px;">&nbsp;</td>
							</tr>
							<tr>
								<td width="100%" height="1" style="font-size: 1px; line-height: 1px;">&nbsp;</td>
							</tr>
						</tbody></table>
						
					</td>
				</tr>
			</tbody>
</table>

</body>
</html>