<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
	define('USER', '');
	define('PASS', '');
	define('IPP_SERVER', 'localhost');
	
	function initializePrinting() {
		require_once('phpprintipp/CupsPrintIPP.php');
		
		$unix = true;
		$ipp = new CupsPrintIPP();
		
		//UNIX-Sockets
		if ($unix) {                 
		  $ipp->setUnix(IPP_SERVER);
		}
		
		$ipp->setHost(IPP_SERVER);
		$ipp->setAuthentication(USER, PASS);
		$ipp->setUserName('GiegSports');
		$ipp->setCharset('utf-8');
		
		return $ipp;
	}
	
	function printFile($filepath, $file_name = 'GiegSports | Data', $landscape = false) {
		$ipp = initializePrinting();
		if (!$ipp) {
			return false;
		} 
		
		$ipp->getPrinters();                       
		$uri = $ipp->available_printers[0];
		
		$uri = $ipp->cupsGetDefaults(array('printer-uri-supported'));
		
		$ipp->setPrinterUri($uri);
		
		$ipp->setAttribute('scaling', 100);
		$ipp->setAttribute('print-quality', 'high');
		if ($landscape) { 
			$ipp->setAttribute('orientation-requested', 'reverse-landscape'); 
			//$ipp->setAttribute('page-right', -4000);
			$ipp->setAttribute('page-top', 0);
		}
		
		$ipp->setData($filepath);
		$ipp->setDocumentName('GiegSports | ' . $file_name);
		
		$ipp->printJob();
		
		$job = $ipp->last_job;
		$jobattributes = $ipp->getJobAttributes($job);
		
		return $jobattributes;
	}
	
	function getPrinters() {
		$ipp = initializePrinting();
		if (!$ipp) {
			return false;
		} 
		
		$ipp->getPrinters();
		$printers = $ipp->printers_attributes;
		
		foreach ($printers as $printer) {
			$available_printers[]['printer_name'] = $printer->printer_info;
		}
		if (isset($available_printers)) {
			return $available_printers;
		}
		return false;
	} 
	
	function pausePrinter() {
		$ipp = initializePrinting();
		if (!$ipp) {
			return false;
		} 
	
		$ipp->pausePrinter();
	}
	
	function getUncompletePrintingJobs() {
		$ipp = initializePrinting();
		if (!$ipp) {
			return false;
		}
		
		$printer = 'http://127.0.0.1:631/printers/Kyocera_FS_1030D';
		$ipp->setPrinterURI($printer);
		
		$jobs = $ipp->getJobs(true,0,"");
		
		return $ipp->jobs_attributes;
	}
?>