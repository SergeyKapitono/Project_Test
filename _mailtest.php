<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
?>

<!DOCTYPE html>
<html>
<head>
  <title>Тестовый скрипт отправки почты</title>
  <meta charset="utf-8" />
</head>
<body>
  <p>Отправка почты функцией mail()</p>
  <form action="" method="post">
    <input type="email" name="mail_sender" placeholder="отправитель@example.com"><br>
    <input type="email" name="mail_recipient" placeholder="получатель@example.com"><br>
    <input type="submit" value="Отправить"><br>
  </form>

<?php

  function isValidEmail($email){ 
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
  }
  $mailSender = filter_input(INPUT_POST, 'mail_sender', FILTER_DEFAULT, FILTER_VALIDATE_EMAIL);
  $mailRecipient = filter_input(INPUT_POST, 'mail_recipient', FILTER_DEFAULT, FILTER_VALIDATE_EMAIL);
  $boundary = uniqid('np');
  $headers = 'From: ' . $mailSender  . PHP_EOL .
             'Reply-To: ' . $mailSender  . PHP_EOL .
             'MIME-Version: 1.0' . PHP_EOL .
             "Content-Type: multipart/alternative;boundary=" . $boundary . PHP_EOL;
  //here is the content body
$message = "This is a MIME encoded message.";
$message .= "\r\n\r\n--" . $boundary . "\r\n";
$message .= "Content-type: text/plain;charset=utf-8\r\n\r\n";

//Plain text body
$message .= "Hello,\nThis is a text email, the text/plain version.
\n\nRegards,\nYour Name";
$message .= "\r\n\r\n--" . $boundary . "\r\n";
$message .= "Content-type: text/html;charset=utf-8\r\n\r\n";

//Html body
$message .= "
 Hello,
This is a text email, the html version.

Regards,
Your Name";
$message .= "\r\n\r\n--" . $boundary . "--";

  if (!empty($mailRecipient) && isValidEmail($mailRecipient)) {
    if (mail($mailRecipient, "TEST mail", $message, $headers, '-f '.$mailSender)) {
      echo "php_mail: Отправлено, получатель ",$mailRecipient;
    } else {
      echo "php_mail: Ошибка, проверьте правильность введенных данных";
    }
  } 
?>
  <hr>
  <p>Отправка почты smtp с авторизацией</p>
  <form action="" method="post">
    <input type="text" name="smtp_host" placeholder="domain.com">
    <select name="smtp_port">
		<option>25</option>
		<option>465</option>
		<option>578</option>
	</select><br>
    <input type="email" name="smtp_sender" placeholder="отправитель@example.com"><br>
    <input type="password" name="smtp_pass" placeholder="пароль почты отправителя"><br>
    <input type="email" name="smtp_recipient" placeholder="получатель@example.com"><br>
    <input type="submit" value="Отправить"><br>
  </form>

<?php

$err = '';
$smtpSender = filter_input(INPUT_POST, 'smtp_sender', FILTER_DEFAULT, FILTER_VALIDATE_EMAIL);
$smtpPass = filter_input(INPUT_POST, 'smtp_pass', FILTER_DEFAULT, FILTER_VALIDATE_EMAIL);
$smtpRecipient = filter_input(INPUT_POST, 'smtp_recipient', FILTER_DEFAULT, FILTER_VALIDATE_EMAIL);
$smtpHost = filter_input(INPUT_POST, 'smtp_host', FILTER_DEFAULT);
$smtpPort = filter_input(INPUT_POST, 'smtp_port', FILTER_DEFAULT);

if (!empty($smtpSender) && !empty($smtpPass) && !empty($smtpHost)) {
    
  $login = $smtpSender; 
  $password = $smtpPass; 
  $to = $smtpRecipient;
  $text="Проверка отправки почты SMTP. Не отвечайте на это письмо.";

  function get_data($smtp_conn)
  {
  $data="";
  while($str = fgets($smtp_conn,515)) 
  {
  $data .= $str;
  if(substr($str,3,1) == " ") { break; }
  }
  return $data;
  }

  $header="Date: ".date("D, j M Y G:i:s")." +0700\r\n"; 
  $header.="From: =?UTF-8?Q?".str_replace("+","_",str_replace("%","=",urlencode('Тестовый скрипт')))."?= <$login>\r\n"; 
  $header.="X-Mailer: Test script shneider-host \r\n"; 
  $header.="Reply-To: =?UTF-8?Q?".str_replace("+","_",str_replace("%","=",urlencode('Тестовый скрипт')))."?= <$login>\r\n";
  $header.="X-Priority: 3 (Normal)\r\n";
  $header.="Message-ID: <12345654321.".date("YmjHis")."@" . $smtpHost . ">\r\n";
  $header.="To: =?UTF-8?Q?".str_replace("+","_",str_replace("%","=",urlencode('Получателю тестового письма')))."?= <$to\r\n";
  $header.="Subject: =?UTF-8?Q?".str_replace("+","_",str_replace("%","=",urlencode('Тестовое письмо smtp')))."?=\r\n";
  $header.="MIME-Version: 1.0\r\n";
  $header.="Content-Type: text/plain; charset=UTF-8\r\n";
  $header.="Content-Transfer-Encoding: 8bit\r\n";
  $smtp_conn = fsockopen($smtpHost, $smtpPort, $errno, $errstr, 10);
  if(!$smtp_conn) {print "соединение с серверов не прошло"; fclose($smtp_conn); exit;}  
  $data = get_data($smtp_conn);
  fputs($smtp_conn,"EHLO $smtpHost\r\n"); // начинаем приветствие.
  $code = substr(get_data($smtp_conn),0,3); // проверяем, не возвратил ли сервер ошибку.
  if($code != 250) {print "ошибка приветсвия EHLO"; fclose($smtp_conn); exit;}
  fputs($smtp_conn,"AUTH LOGIN\r\n"); // начинаем процедуру авторизации.
  $code = substr(get_data($smtp_conn),0,3);
  if($code != 334) {print "сервер не разрешил начать авторизацию"; fclose($smtp_conn); exit;}

  fputs($smtp_conn,base64_encode("$login")."\r\n"); 
  $code = substr(get_data($smtp_conn),0,3);
  if($code != 334) {print "ошибка доступа к такому юзеру"; fclose($smtp_conn); exit;}

  fputs($smtp_conn,base64_encode("$password")."\r\n");       // отправляем серверу пароль.
  $code = substr(get_data($smtp_conn),0,3);                 
  if($code != 235) {print "неправильный пароль"; fclose($smtp_conn); exit;}

  fputs($smtp_conn,"MAIL FROM:$login\r\n"); // отправляем серверу значение MAIL FROM.
  $code = substr(get_data($smtp_conn),0,3);
  if($code != 250) {print "сервер отказал в команде MAIL FROM"; fclose($smtp_conn); exit;}

  fputs($smtp_conn,"RCPT TO:$to\r\n"); // отправляем серверу адрес получателя.
  $code = substr(get_data($smtp_conn),0,3);
  if($code != 250 AND $code != 251) {print "Сервер не принял команду RCPT TO"; fclose($smtp_conn); exit;}

  fputs($smtp_conn,"DATA\r\n"); // отправляем команду DATA.
  $code = substr(get_data($smtp_conn),0,3);
  if($code != 354) {print "сервер не принял DATA"; fclose($smtp_conn); exit;}

  fputs($smtp_conn,$header."\r\n".$text."\r\n.\r\n"); // отправляем тело письма.
  $code = substr(get_data($smtp_conn),0,3);
  if($code != 250) {print "ошибка отправки письма"; fclose($smtp_conn); exit;}

  if($code == 250) {print "Письмо отправлено успешно. Ответ сервера $code"  ;}

  fputs($smtp_conn,"QUIT\r\n");   // завершаем отправку командой QUIT.
  fclose($smtp_conn); // закрываем соединение.
}
?>

</body>
</html>
