<?php

function getServerIP()
{
	$output = shell_exec("ipconfig");
	preg_match('/IPv4 Address[. ]*: (\d+\.\d+\.\d+\.\d+)/', $output, $matches);
	$localIp = $matches[1] ?? 'IP not found';
	
	return $localIp . ':8000';
}

