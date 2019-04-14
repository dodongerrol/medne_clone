<!doctype html>
<html lang="en">
<head>
	<meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no, width=device-width">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta charset="UTF-8">
	<meta http-equiv='cache-control' content='no-cache'>
	<meta http-equiv='expires' content='-1'>
	<meta http-equiv='pragma' content='no-cache'>
	<title><?php echo $title;?></title>
	<link rel="shortcut icon" href="{{ asset('assets/images/Medicloud-Favicon_16x16px.ico') }}" type="image/ico">
	{{ HTML::style('assets/css/jquery-confirm.css') }}
	{{ HTML::style('assets/css/bootstrap/css/bootstrap.css') }}
	{{ HTML::style('assets/css/bootstrap/css/bootstrap-theme.css') }}
	<link rel="stylesheet" href="<?php echo $server; ?>/assets/css/medicloud.css?_={{ $date->format('U') }}">
	<link rel="stylesheet" href="<?php echo $server; ?>/assets/css/mob.css?_={{ $date->format('U') }}">
	{{ HTML::script('assets/js/jquery-1.11.1.js') }}
	{{ HTML::script('assets/js/doctor-form-validation.js') }}
	{{ HTML::script('assets/js/user-update-account-validate.js') }}
	{{ HTML::script('assets/css/bootstrap/js/bootstrap.min.js') }}
	<script type="text/javascript" src="<?php echo $server; ?>/assets/js/form-validate.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/js/common-ajax.js?_={{ $date->format('U') }}"></script>
	{{ HTML::script('assets/js/jquery-blockUI.js') }}
	<script type="text/javascript" src="<?php echo $server; ?>/assets/dashboard/country_code.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/js/jquery-confirm.js?_={{ $date->format('U') }}"></script>
	{{ HTML::style('assets/common/sinkin-sans-fontfacekit/web fonts/sinkinsans_300light_macroman/stylesheet.css') }}
	{{ HTML::style('assets/css/offline-theme-default.css') }}
	{{ HTML::style('assets/css/offline-language-english.css') }}
	<style type="text/css">
	body,td,th,input,textarea {font-family: 'sinkin_sans300_light';}
	</style>
	<link href='https://fonts.googleapis.com/css?family=Montserrat' rel='stylesheet' type='text/css'>
	<link href='https://fonts.googleapis.com/css?family=Oxygen' rel='stylesheet' type='text/css'>
	<link href='https://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>
	{{ HTML::script('assets/js/offline.min.js') }}
</head>

<body style="font-family:'Open Sans', sans-serif;">
<div class="mc-container">
