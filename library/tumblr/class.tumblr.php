<?php

class Tumblr{
	
	/*
		Tumblr API PHP class by Evan Walsh
		http://code.evanwalsh.net/Projects/Tumblr
	*/
	
	function read($url,$json = false){
		$url = 'http://' . $url . '.tumblr.com/api/read';
		if($json){
			$url .= "/json";
		}
		if(ini_get("allow_url_fopen")){
			$output = file_get_contents($url);
		}
		elseif(function_exists("curl_version")){
			$c = curl_init($url);
			curl_setopt($c,CURLOPT_HEADER,1);
			curl_setopt($c,CURLOPT_RETURNTRANSFER,1);
			$output = curl_exec($c);
		}
		else{
			$output = "error: cannot fetch file";
		}
		return $output;
	}
	
	function init($email, $password, $generator = "Web"){
		$this->email = $email;
		$this->password = $password;
		$this->generator = $generator;
	}
	
	function post($data){
		if( function_exists("curl_version") ){
			$data["email"] = $this->email;
			$data["password"] = $this->password;
			$data["generator"] = $this->generator;
			$request = http_build_query($data);
			$c = curl_init('http://www.tumblr.com/api/write');
			curl_setopt($c,CURLOPT_POST,true);
			curl_setopt($c,CURLOPT_POSTFIELDS,$request);
			curl_setopt($c,CURLOPT_RETURNTRANSFER,true);
			$return = curl_exec($c);
			$status = curl_getinfo($c,CURLINFO_HTTP_CODE);
			curl_close($c);
			if($status == "201"){
			    return true;
			}
			elseif($status == "403"){
			    return false;
			}
			else{
			    return "error: $return";
			}
		}
		else{
			return "error: cURL not installed";
		}
	}
	
	function check($action){
		$accepted = array("authenticate","check-vimeo","check-audio");
		if(in_array($action,$accepted)){
			$data["email"] = $this->email;
			$data["password"] = $this->password;
			$data["generator"] = $this->generator;
			$data["action"] = $action;
			if(function_exists("curl_version")){
				$c = curl_init('http://www.tumblr.com/api/write');
				curl_setopt($c,CURLOPT_POST,true);
				curl_setopt($c,CURLOPT_POSTFIELDS,$data);
				curl_setopt($c,CURLOPT_RETURNTRANSFER,true);
				$result = curl_exec($c);
				$status = curl_getinfo($c,CURLINFO_HTTP_CODE);
				curl_close($c);
				if($status == "200"){
					$status = true;
				}
				elseif($status == "403" || $status == "400"){
					$status = false;
				}
				return $status;
			}
			else{
				return "error: cURL not installed";
			}
		}
	}
	
}

?>