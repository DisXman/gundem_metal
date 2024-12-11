<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

header('Content-Type: application/json');

function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function check_data() {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (empty($_POST["email"]) || empty($_POST["message"])) {
            return false;
        }
    
        $email = test_input($_POST["email"]);
    
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }
    
        return true;    
    } else {
        return false;
    }
}

function send_mail() {
    if (!check_data()) {
        return ['success' => false, 'error' => 'Invalid input data'];
    }

    $name = test_input($_POST["name"]);
    $email = test_input($_POST["email"]);
    $phone = test_input($_POST["phone"]);
    $firmaismi = test_input($_POST["firmaismi"]);
    $material = test_input($_POST["material"]);
    $etKalinligi = test_input($_POST["etKalinligi"]);
    $message = test_input($_POST["message"]);
    $subject = "Yeni Teklif Talebi";

    $mail = new PHPMailer;
    // Set PHPMailer to use SMTP.
    $mail->isSMTP();
    // Set SMTP host name                      
    $mail->Host = "smtp.gmail.com";
    // Set this to true if SMTP host requires authentication to send email
    $mail->SMTPAuth = true;                      
    // Provide username and password
    $mail->Username = "batuhandonmezweb@gmail.com";             
    $mail->Password = "wtxt mcko carz mlbo";                       
    // If SMTP requires TLS encryption then set it
    $mail->SMTPSecure = "tls";   
    $mail->CharSet = "UTF-8";                    
    // Set TCP port to connect to
    $mail->Port = 587;                    
    $mail->From = $email;
    $mail->FromName = $name;
    $mail->addAddress("batuhandonmez17@gmail.com", "neta");
    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body = "Gönderen e-mail: " . $email . "<br>" . 
                  "Mesaj: " . nl2br($message) . "<br>" . 
                  "Telefon: " . $phone . "<br>" . 
                  "Firma İsmi: " . $firmaismi . "<br>" . 
                  "Materyal: " . $material . "<br>" . 
                  "Et Kalınlığı: " . $etKalinligi;

    // Dosya yüklemesi başarılı mı kontrol et
    if (isset($_FILES["file"]) && $_FILES["file"]["error"] == UPLOAD_ERR_OK) {
        $mail->addAttachment($_FILES["file"]["tmp_name"], $_FILES["file"]["name"]); // Dosyayı ekle
    } else {
        // Dosya yükleme hatası kodunu kontrol et
        switch ($_FILES["file"]["error"]) {
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                return ['success' => false, 'error' => 'Yüklemeye çalıştığınız dosya çok büyük.'];
            case UPLOAD_ERR_PARTIAL:
                return ['success' => false, 'error' => 'Dosya kısmen yüklendi.'];
            case UPLOAD_ERR_NO_FILE:
                return ['success' => false, 'error' => 'Hiçbir dosya yüklü değil.'];
            default:
                return ['success' => false, 'error' => 'Dosya yüklenirken bir hata oluştu.'];
        }
    }

    if (!$mail->send()) {
        return ['success' => false, 'error' => $mail->ErrorInfo];
    } else {
        return ['success' => true];
    }
}

echo json_encode(send_mail());
?>
