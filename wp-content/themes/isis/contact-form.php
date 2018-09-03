<?php
include '/home/intercom/ngn.tj/public_html//smtpmail/library.php'; // include the library file
include "/home/intercom/ngn.tj/public_html//smtpmail/classes/class.phpmailer.php"; // include the class name

if($_POST['result']!="11"){
    header('Location: http://intercom.tj/?mess=ERROR#win2');die;
}
if($_POST) {
    $reEmail = ($_POST['email']) ? $_POST['email'] : "manager@tjinter.com";
    $mail = new PHPMailer; // call the class
    $mail->IsSMTP();
    $mail->SMTPDebug = 1;
    $mail->SMTPAuth = true; //Whether to use SMTP authentication
    $mail->SMTPSecure = 'ssl';
    $mail->Host = "smtp.gmail.com"; //Hostname of the mail server
    $mail->Port = 465; //Port of the SMTP like to be 25, 80, 465 or 587
    $mail->IsHTML(true);
    $mail->Username = "ngnintercom@gmail.com"; //Username for SMTP authentication any valid email created in your domain
    $mail->Password = "Soper780%";
    $mail->AddReplyTo($reEmail, $reEmail); //reply-to address
    $mail->SetFrom($reEmail, $reEmail); //From address of the mail// put your while loop here like below,

    $mail->CharSet = 'UTF-8';

    $mail->Subject = "Заявка на подключение к интернету через сайт"; //Subject od your mail
    $recipients = array('manager@tjinter.com' => 'INTERCOM','ngnintercom@gmail.com'=>'NGNINTERCOM');

    foreach ($recipients as $email => $name) {
        // it will display the emails of all users in their Mailbox 'To' area. Simple multiple mail.
        $mail->AddAddress($email, $name); //To address who will receive this email
        $mail->MsgHTML("<b>Здравствуйте, Запрос на подключение интернета.<br> <br>Абонент ".$_POST['name']." <br>
 Адресс : ".$_POST['address']."<br> Номер телефона: ".$_POST['tel']."<br>Желаемое время подключения: ".$_POST['datetime']."<br>
  Коментарий :".$_POST['message']." <br> Почта : ".$_POST['email']."<br><br/><br/> Всего наилучшего!.. <br/><br/> </br>"); //Put your body of the message you can place html code here
        //$mail->AddAttachment("images/asif18-logo.png"); //Attach a file here if any or comment this line (usimg absolute path),
        $send = $mail->Send(); //Send the mails
        // if you want to does not show other users email addresses like newsletter, daily, weekly, subscription emails means use the below line to clear previous email address
        $mail->ClearAddresses();
    }

    if ($send) {
        header('Location: http://intercom.tj/newfolder/?mess=OK#win2');die;
    } else {
        header('Location: http://intercom.tj/newfolder/?mess=ERROR#win2');die;
    }

}
?>