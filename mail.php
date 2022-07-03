<?php 

require_once('phpmailer/PHPMailerAutoload.php');
$mail = new PHPMailer;
$mail->CharSet = 'utf-8';

$name = $_POST['user_name'];
$phone = $_POST['user_phone'];
$product = $_POST['user_product'];

$mail->isSMTP();                                      
$mail->Host = 'smtp.mail.ru';  																							// Specify main and backup SMTP servers
$mail->SMTPAuth = true;                               
$mail->Username = '1_2_3_4_5mark@mail.ru'; 
$mail->Password = 'fdafbdbgsdgsgsgsgh'; 
$mail->SMTPSecure = 'ssl';                          
$mail->Port = 465; 

$mail->setFrom('1_2_3_4_5mark@mail.ru'); 
$mail->addAddress('k956646@gmail.com');     
$mail->isHTML(true);                                 

$mail->Subject = 'Поступил новый заказ!))';
$mail->Body    = '' .$name . ' оставил(а) заявку, телефон для связи: ' .$phone. '<br>Наименование товара: ' .$product;
$mail->AltBody = '';

if(!$mail->send()) {
    echo 'Error';
} else {
    header('location: thank-you.html');
}
?>
