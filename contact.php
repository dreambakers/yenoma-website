<?php
	// ========== Enter your email address here ========== //
	$to = "info@yenoma.com";

	// Clean up the input values
	foreach($_POST as $key => $value) {
		if(ini_get('magic_quotes_gpc'))
			$_POST[$key] = stripslashes($_POST[$key]);

		$_POST[$key] = htmlspecialchars(strip_tags($_POST[$key]));
	}

	// Assign the input values to variables for easy reference
	$name = $_POST["name"];
	$email = $_POST["email"];
	$subject = $_POST["subject"];
	$message = $_POST["message"];

	// Check input values for errors
	$errors = array();
	if(strlen($name) < 2) {
		if(!$name) {
			$errors[] = "msg-enter-name";
		} else {
			$errors[] = "msg-name-characters";
		}
	}
	if(!$email) {
		$errors[] = "msg-enter-email";
	} else if(!validEmail($email)) {
		$errors[] = "msg-invalid-email";
	}
	if(strlen($message) < 10) {
		if(!$message) {
			$errors[] = "msg-enter-msg";
		} else {
			$errors[] = "msg-chacters-lenght";
		}
	}

	// Output error message(s)
	if($errors) {
		$response = (object) [
			'status' => 0,
			'errors' => $errors
		];
		die(json_encode($response));
	}

	// Send the email
	if($subject!=""){
		$subject = "Contact Form: $subject";
	}
	else {
		$subject = "Contact Form: $name";
	}
	$message = "$message";
	$headers = "From: ".$name." <".$email.">" . "\r\n" . "Reply-To: " . $email . "\r\n" . "Content-Type: text/plain; charset=UTF-8";

	if (mail($to, $subject, $message, $headers)) {
		$response = (object) [
			'status' => 1,
		];

		die(json_encode($response));
	} else {
		$errors[] = "error-msg-unsuccessful";
		$response = (object) [
			'status' => 0,
			'errors' => $errors
		];
		die(json_encode($response));
	}

	// Check if email is valid
	function validEmail($email) {
		$isValid = true;
		$atIndex = strrpos($email, "@");
		if (is_bool($atIndex) && !$atIndex)	{
			$isValid = false;
		}
		else {
			$domain = substr($email, $atIndex+1);
			$local = substr($email, 0, $atIndex);
			$localLen = strlen($local);
			$domainLen = strlen($domain);
			if ($localLen < 1 || $localLen > 64) {
				// Local part length exceeded
				$isValid = false;
			}
			else if ($domainLen < 1 || $domainLen > 255) {
				// Domain part length exceeded
				$isValid = false;
			}
			else if ($local[0] == '.' || $local[$localLen-1] == '.') {
				// Local part starts or ends with '.'
				$isValid = false;
			}
			else if (preg_match('/\\.\\./', $local)) {
				// Local part has two consecutive dots
				$isValid = false;
			}
			else if (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain)) {
				// Character not valid in domain part
				$isValid = false;
			}
			else if (preg_match('/\\.\\./', $domain)) {
				// Domain part has two consecutive dots
				$isValid = false;
			}
			else if(!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/',
			str_replace("\\\\","",$local))) {
				// Character not valid in local part unless local part is quoted
				if (!preg_match('/^"(\\\\"|[^"])+"$/',
				str_replace("\\\\","",$local))) {
					$isValid = false;
				}
			}
			if ($isValid && !(checkdnsrr($domain,"MX") || checkdnsrr($domain,"A"))) {
				// Domain not found in DNS
				$isValid = false;
			}
		}
		return $isValid;
	}
?>