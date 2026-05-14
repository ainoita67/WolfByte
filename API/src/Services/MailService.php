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
        $mail->Host      ='mail.cpifpbajoaragon.info';
        $mail->SMTPAuth  =true;
        $mail->Username  ='reservasies@cpifpbajoaragon.info';
        $mail->Password  ='xxxxxxxxxxxxxxxx';
        $mail->SMTPSecure='tls';
        $mail->Port      =587;

        $mail->setFrom('reservasies@cpifpbajoaragon.info', 'Reservas IES Bajo Aragón');
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
    public function createMail(string $correo, string $accion)
    {
        try {
            $mail = $this->createMailer($correo);
            $fechahora=date('Y-m-d H:i:s');
            $fecha = date('d-m-Y', strtotime($fechahora));
            $hora = date('H:i', strtotime($fechahora));
            $reservaBody='Se ha creado una nueva reserva de '.$correo.' en la aplicación el día '.$fecha.' a las '.$hora.'.';
            $acciones=[
                'login' => [
                    'titulo' => 'Inicio de sesión',
                    'body' => "Se ha detectado un inicio de sesión de $correo en la aplicación el día $fecha a las $hora."
                ],
                'createreserva' => [
                    'titulo' => 'Creación de reserva',
                    'body' => $reservaBody
                ],
                'createreservaespacio' => [
                    'titulo' => 'Creación de reserva de espacio',
                    'body' => $reservaBody
                ],
                'createreservaportatil' => [
                    'titulo' => 'Creación de reserva de portátiles',
                    'body' => $reservaBody
                ],
                'createreservapermanente' => [
                    'titulo' => 'Creación de reserva permanente',
                    'body' => 'Se ha creado una nueva reserva permanente de '.$correo.' en la aplicación el día '.$fecha.' a las '.$hora.'.'
                ],
                'solicitudreserva' => [
                    'titulo' => 'Solicitud de reserva',
                    'body' => "Se ha solicitado una reserva de $correo en la aplicación el día $fecha a las $hora."
                ],
                'editarreserva' => [
                    'titulo' => 'Modificación de reserva',
                    'body' => "Se ha modificado una reserva de $correo en la aplicación el día $fecha a las $hora."
                ],
                'editarreservapermanente' => [
                    'titulo' => 'Modificación de reserva permanente',
                    'body' => "Desde la cuenta $correo se ha modificado una reserva permanente en la aplicación el día $fecha a las $hora."
                ],
                'autorizacion' => [
                    'titulo' => 'Autorización de reserva',
                    'body' => "Se ha autorizado una reserva de $correo en la aplicación el día $fecha a las $hora."
                ],
                'cancelacion' => [
                    'titulo' => 'Cancelación de reserva',
                    'body' => "Se ha cancelado una reserva de $correo en la aplicación el día $fecha a las $hora."
                ],
                'importar' => [
                    'titulo' => 'Importación de reservas permanentes',
                    'body' => "Se han importado reservas permanentes con la cuenta de $correo en la aplicación el día $fecha a las $hora."
                ],
                'usuarios' => [
                    'titulo' => 'Importación de usuarios',
                    'body' => "Se han importado usuarios con la cuenta de $correo en la aplicación el día $fecha a las $hora."
                ]
            ];
            
            $html='
                <a href="https://reservasies.cpifpbajoaragon.info/frontend/vistas/menu.html">
                    <button style="font-size: 1.4em; background-color: #274C9C; color: white; padding: 2rem; border: 0; border-radius: 25px">
                        Acceder a la aplicación
                    </button>
                </a>
            ';
            if (isset($acciones[$accion])) {
                $titulo = $acciones[$accion]['titulo'];
                $mail->Body = '<div style="text-align: center; padding: 5%;"><p style="font-size: 1.2em;">'.$acciones[$accion]['body']."</p><br>".$html."</div>";
                $mail->Subject = ucfirst($titulo);
                $mail->send();
            }
        } catch (Throwable $e) {
            throw new \Exception("Error interno al autenticar el correo electrónico", 500);
        }
    }
}