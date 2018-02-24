<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @file
 * helper sent mail using smpt or direct sent
 * @author Faisal Latada
 * @param $conf
 *   An associative array containing:
 *   - subject: judul email.
 *   - content: isi email.
 *   - to: email to.
 *   - to_name: (optional) nama kepada nya ex latada@jayadata.co.id, faisal latada.
 *	  - from: (optional) default from config.
 *	  - from_name:(optional) default from config.
 *	  @example
 *	   $this->load->helper('mail');
		$conf['subject'] 	= 'test sent mail from helper using smtp';
		$conf['content'] 	= '<b> isi email</b><br><p>lorem ipsum dolor sit amet</p>';
		$conf['to'] 		= 'latada@msn.com';
		$conf['from'] 		= 'latada@msn.com';
		$conf['from_name']= 'agg';
		sent_mail($conf);
 */
function sent_mail($conf){
	 $CI = & get_instance();
	 $CI->load->helper('email');
	 $error = 'sent mail configuration error : ';
	 if(!$conf['subject']){
		  die("$error subject is empty!");
	 }
	 else if(!$conf['content']){
		  die("$error content is empty!");
	 }
	 else if(!$conf['to']){
		  die("$error to is empty!");
	 }	 
	 else if (!valid_email($conf['to'])){
		  die("$error $conf[to] is not valid email!");
	 }
	 
	 $path = str_replace("system/","application/helpers/",BASEPATH);
	 require_once $path.'mail/class.phpmailer.php';
	 $config				= $CI->db->get("ref_mail_config")->row();
	 //$ssl				= ($config['ssl'] == 'y') ? 'ssl://' : '';
	 // $nama_pengirim 	= $config->name;
	 
	 // $nama_pengirim 	= (isset($conf['from_name']) && $conf['from_name']) ? $conf['from_name'] : $config->name;
	 $nama_pengirim 	= (isset($conf['from_name']) && $conf['from_name']) ? $conf['from_name'] : '-';
	 $email_pengirim 	= $config->email;
	 
	 if($config->type=='SMTP'){
		  date_default_timezone_set('Asia/Jakarta');
		  $mail             = new PHPMailer();
		  $mail->IsSMTP();
		  $mail->Host       = $config->smtp_server;
		  $mail->SMTPDebug  = 0;
		  $mail->SMTPAuth   = true;
		  $mail->Port       = 587;
		  $mail->Username   = $config->email;
		  $mail->Password   = $config->password;
		  //$mail->SMTPSecure = ($config->ssl == 'y') ? "tls" : '';
		  $mail->SMTPSecure = "tls";
		  
		  $mail->SetFrom($email_pengirim,$config->name); 
		  if(isset($conf['repto']) && isset($conf['from_name'])){
			$mail->AddReplyTo($conf['repto'],$conf['repto']);
		  }
		  $mail->Subject    = $conf['subject'];
		  $mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test
		  $mail->MsgHTML($conf['content']);
		  $mail->AddAddress($conf['to'], $nama_pengirim);
		  
		  if(!$mail->Send()) {
				//echo "Error|" . $mail->ErrorInfo;
				exit;
		  }
	 }
	 else {
		  die('under construction');
	  }
}
