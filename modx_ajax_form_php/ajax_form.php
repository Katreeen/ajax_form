<?php
define('MODX_API_MODE', true);
require 'index.php';

if (!$_POST) {
	die('=(');
}

$json_response = [
	'error' => []
];

$name = isset($_POST['name']) ? $_POST['name'] : false;
$email = isset($_POST['email']) ? $_POST['email'] : false;
$tel = isset($_POST['tel']) ? $_POST['tel'] : false;
$agree = isset($_POST['agree']) ? $_POST['agree'] : false;

if (!$name || !$email || !$tel) {
	$json_response['error'][] = 'Все текстовые поля обязательны для заполнения';
}

if (!$agree) {
	$json_response['error'][] = 'Необходимо согласиться с условиями';
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
	$json_response['error'][] = 'Не верный формат email';
}

if ( !isset($_FILES['file']) || !isset($_FILES['file']['tmp_name']) ) {
	$json_response['error'][] = 'Файл обязателен';
} else {

	if ($_FILES["file"]["error"]) {
		$json_response['error'][] = 'Ошибки загрузки файла';
	}

	$allowedExt = ['doc','docx','txt','pdf'];
	$maxFileSize = 512 * 1024 * 6;
	$fileName = basename( $_FILES['file']['name'] );
	$fileSize = filesize( $_FILES['file']['tmp_name'] );
	$fileExt = mb_strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

	if(!in_array($fileExt, $allowedExt)) {
		$json_response['error'][] = 'Файл имеет не разрешённый тип';
	}

	if($fileSize > $maxFileSize) {
		$json_response['error'][] = 'Размер файла превышает 512 Кбайт';
	}
}

if ($json_response['error']) {
	echo json_encode($json_response);
	die();
}

$filename = "uploads/".uniqid().'.'.$fileExt;
move_uploaded_file($_FILES['file']['tmp_name'], $filename);

$message = "<p>Имя: $name</p>" .
"<p>Телефон: $tel</p>" .
"<p>Email: $email</p>" .
"<p>Файл: http://stak63.com/$filename</p>";

$modx->getService('mail', 'mail.modPHPMailer');
$modx->mail->set(modMail::MAIL_BODY, $message);
$modx->mail->set(modMail::MAIL_FROM,'robot@stak63.com');
$modx->mail->set(modMail::MAIL_FROM_NAME,'Сайт stak63.com');
$modx->mail->set(modMail::MAIL_SUBJECT,'Новая заявка');
$modx->mail->address('to','saxap@bk.ru');
//$modx->mail->address('reply-to','me@xexample.org');
$modx->mail->setHTML(true);
if (!$modx->mail->send()) {
    $modx->log(modX::LOG_LEVEL_ERROR,'An error occurred while trying to send the email: '.$modx->mail->mailer->ErrorInfo);
}
$modx->mail->reset();

echo json_encode($json_response);
die();