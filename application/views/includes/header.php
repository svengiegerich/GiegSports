<!doctype html>
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> 
<html class="no-js" lang="en"> <!--<![endif]-->
	<head>
		<meta charset="utf-8">

		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

		<title>GiegLabs <?php if (isset($title)) { echo '| ' . $title;} ?></title>
		<meta name="description" content="">
		<meta name="author" content="Sven Giegerich">

		<meta name="viewport" content="width=device-width,initial-scale=1">
  
		<link rel="stylesheet" href="<?php echo base_url();?>css/bootstrap.css">
		<link rel="stylesheet" href="<?php echo base_url();?>css/bootstrap-responsive.css">
		<link rel="stylesheet" href="<?php echo base_url();?>css/style.css">
  
		<script src="<?php echo base_url();?>js/libs/jquery.js" type="text/javascript"></script>
	</head>

	<body>
		<header>
			<div class="navbar navbar-fixed-top">
	  			<div class="navbar-inner">
	    			<div class="container">
	      				
	      				<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
	      					<span class="icon-bar"></span>
	      				 	<span class="icon-bar"></span>
	      				 	<span class="icon-bar"></span>
	      				 </a>
	      				
	      				<a class="brand" href="<?php echo base_url(); ?>"><?php echo (isset($contest_name)) ? ($contest_name) : ('GiegSports'); ?></a>
	      				
	      				<div class="nav-collapse" style="height: 0px;">
	      					<ul class="nav">
	       						<li><a href="<?php echo  base_url() . 'index.php/site' ?>">Home</a></li>
	        					<li><a href="<?php echo base_url() . 'index.php/add_results'; ?>">Ergebnisse eintragen</a></li>
	        					<li><a href="<?php echo base_url() . 'index.php/results'; ?>">Ergebnisse</a></li>
	      					</ul>
	      
	      					<ul class="nav pull-right">
	     						<li id="fat-menu" class="dropdown">
	      							<a href="#" class="dropdown-toggle" data-toggle="dropdown">Mehr <b class="caret"></b></a>
	      							 <ul class="dropdown-menu">
	      	       						<li><a href="<?php echo base_url() . 'index.php/competitors'; ?>">Teilnehmer</a></li>
	      	       						<li><a href="<?php echo base_url() . 'index.php/input_tables'; ?>">Wettkampfkarten</a></li>
	      	       						<li><a href="<?php echo base_url() . 'index.php/printing'; ?>">Drucken</a></li>
	      	            				<li class="divider"></li>
	      	            				<li><a href="<?php echo base_url() . 'index.php/preferences'; ?>">Wettbewerbe verwalten</a></li>
	      	         				</ul>
	      	         			</li>
	      	         		</ul>
	      	         	</div>
	      	        </div>
	      		</div>        
			</div>
		</header>
  
<div role="main" class="container">

	<noscript>
		<div class="modal-backdrop fade in"></div>
		<div class="modal" style="display: block;">
		            <div class="modal-header">
		              <h3>Kein JavaSkript aktiviert!</h3>
		            </div>
		            <div class="modal-body">
		              <p>Bitte aktivieren sie JavaSkript in ihrem Browser, denn diese Komponente wird für den Einsatz der gesamten Seite benötig.</p>
		            </div>
		          </div>
	</noscript>