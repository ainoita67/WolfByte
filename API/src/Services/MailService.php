<?php
declare(strict_types=1);

namespace Services;

use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use Validation\Validator;
use Validation\ValidationException;
use Throwable;

class MailService
{

    public function __construct()
    {

    }

    public function createMailer(string $email): PHPMailer
    {
        $mail=new PHPMailer(true);

        $mail->isSMTP();
        $mail->Host      ='smtp.gmail.com';
        $mail->SMTPAuth  =true;
        $mail->Username  ='vocabulariodaw@gmail.com';
        $mail->Password  ='xxxxxxxxxxxxxxxx';
        $mail->SMTPSecure='tls';
        $mail->Port      =587;

        $mail->setFrom('vocabulariodaw@gmail.com', 'Vocabulario');
        $mail->addAddress($email);

        //$mail->ContentLanguage='es';
        $mail->CharSet='UTF-8';
        $mail->Encoding='base64';

        $mail->isHTML(true);
        return $mail;
    }

    /**
     * Crear un correo
     */
    public function createMail(string $correo, string $accion, string $titulo)
    {
        try {
            $mail = $this->createMailer($correo);
            $fechahora=date('Y-m-d H:i:s');
            $fecha = date('d-m-Y', strtotime($fechahora));
            $hora = date('H:i', strtotime($fechahora));
            $mail->Body = 'Se ha detectado '.$accion.' de '.$correo.' en la aplicación el día '.$fecha.' a las '.$hora.'.';
            $mail->Subject = ucfirst($titulo);
            $mail->send();
        } catch (Throwable $e) {
            throw new \Exception("Error interno en la base de datos: " . $e->getMessage(), 500);
        }
    }
}