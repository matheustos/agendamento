<?php

namespace Controller;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Model\Agendamento;
use Validators\AgendamentoValidators;

class EmailController{

    private static function configurar(PHPMailer $mail)
    {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'matheustos123456@gmail.com';
        $mail->Password   = 'dvts ltjd ssso yezb';
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        $mail->setFrom('matheustos123456@gmail.com.com', 'Sistema de Agendamento');
        $mail->isHTML(true);
    }

    public static function enviar($email, $data, $hora, $nome, $servico)
    {
        $mail = new PHPMailer(true);
        $mensagemTexto = "Olá, ".$nome."!\n".
        "Seu agendamento para o serviço ".$servico." foi confirmado.\n".
        "Data: ".$data." Hora: ".$hora."\n".
        "Obrigado por escolher nosso serviço!";
        $mensagemHtml = "<h2>Olá, $nome!</h2>
                <p>Seu agendamento para o serviço <b>$servico</b> foi confirmado.</p>
                <p><strong>Data:</strong> $data<br>
                   <strong>Hora:</strong> $hora</p>
                <p>Obrigado por escolher nosso serviço!</p>";

        try {
            self::configurar($mail);

            $mail->addAddress($email);
            $mail->Subject = "Confirmação de Agendamento";
            $mail->CharSet = 'UTF-8';
            $mail->Body    = $mensagemHtml;
            $mail->AltBody = $mensagemTexto ?: strip_tags($mensagemHtml);

            $mail->send();
            return [
                "status" => "success",
                "message" => "E-mail enviado com sucesso!"
            ];
        } catch (Exception $e) {
            return [
                "status" => "error",
                "message" => $mail->ErrorInfo
            ];
        }
    }

    public static function cancelamento($email, $nome)
    {
        $mail = new PHPMailer(true);
        $mensagemTexto = "Olá, ".$nome."!\n".
        "Seu agendamento foi cancelado com sucesso!";
        $mensagemHtml = "<h2>Olá, $nome!</h2>
                <p>Seu agendamento foi cancelado com sucesso!</p>";

        try {
            self::configurar($mail);

            $mail->addAddress($email);
            $mail->Subject = "Confirmação de Cancelamento";
            $mail->CharSet = 'UTF-8';
            $mail->Body    = $mensagemHtml;
            $mail->AltBody = $mensagemTexto ?: strip_tags($mensagemHtml);

            $mail->send();
            return [
                "status" => "success",
                "message" => "E-mail enviado com sucesso!"
            ];
        } catch (Exception $e) {
            return [
                "status" => "error",
                "message" => $mail->ErrorInfo
            ];
        }
    }
}