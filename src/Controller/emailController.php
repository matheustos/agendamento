<?php

namespace Controller;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dotenv\Dotenv;

class EmailController{

    private static function configurar(PHPMailer $mail)
    {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'matheusstos123456@gmail.com';
        $mail->Password   = $_ENV['CHAVE_APP'];
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        $mail->setFrom('matheusstos123456@gmail.com.com', 'Sistema de Agendamento');
        $mail->isHTML(true);
    }

    public static function enviar($email, $data, $hora, $nome, $servico, $obs)
    {
        $data_format = date("d/m/Y", strtotime($data)); 
        $mail = new PHPMailer(true);
        $mensagemTexto = "Olá, ".$nome."!\n".
        "Seu agendamento para o serviço ".$servico." foi efetuado com sucesso.\n".
        "📅 Data: ".$data." ⏰ Hora: ".$hora."\n".
        "Obrigado por escolher nosso serviço!"."\n".
        "Aviso: Pedimos que chegue com alguns minutos de antecedência.\n
        Em caso de atraso maior que 15 minutos, não conseguimos garantir o atendimento
        e poderá ser necessário reagendar conforme disponibilidade de horário.";
        $mensagemHtml = "<h2>Olá, $nome!</h2>
                <p>Seu agendamento para o serviço <b>$servico</b> foi efetuado com sucesso.</p>
                <p><strong><span>📅</span> Data:</strong> $data_format<br>
                   <strong><span>⏰</span> Hora:</strong> $hora</p>
                <p><strong>Observações:</strong> $obs</p>
                <p><em><strong>Aviso:</strong> Pedimos que chegue com alguns minutos de antecedência. Em caso de atraso maior que 15 minutos, não conseguimos garantir o atendimento e poderá ser necessário reagendar conforme disponibilidade de horário.</em>"
                ;

        try {
            self::configurar($mail);

            $mail->addAddress($email);
            $mail->Subject = "Confirmação de Novo Agendamento";
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

    public static function confirmar($email, $data, $hora, $nome, $servico)
    {
        $data_format = date("d/m/Y", strtotime($data)); 
        $mail = new PHPMailer(true);
        $mensagemTexto = "Olá, ".$nome."!\n".
        "Seu agendamento para o serviço ".$servico." foi confirmado!\n".
        "📅 Data: ".$data_format." ⏰ Hora: ".$hora."\n".
        "Aviso: Pedimos que chegue com alguns minutos de antecedência.\n
        Em caso de atraso maior que 15 minutos, não conseguimos garantir o atendimento
        e poderá ser necessário reagendar conforme disponibilidade de horário.";
        $mensagemHtml = "<h2>Olá, $nome!</h2>
                <p>Seu agendamento para o serviço <b>$servico</b> foi confirmado!</p>
                <p><strong><span>📅</span> Data:</strong> $data_format<br>
                   <strong><span>⏰</span> Hora:</strong> $hora</p>
                <p><em><strong>Aviso:</strong> Pedimos que chegue com alguns minutos de antecedência. Em caso de atraso maior que 15 minutos, não conseguimos garantir o atendimento e poderá ser necessário reagendar conforme disponibilidade de horário.</em>"
                ;

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

    public static function cancelamento($email, $nome, $data, $hora)
    {
        $data_format = date("d/m/Y", strtotime($data)); 
        $mail = new PHPMailer(true);
        $mensagemTexto = "Olá, ".$nome."!\n".
        "Seu agendamento foi cancelado com sucesso!";
        $mensagemHtml = "<h2>Olá, $nome!</h2>
                <p>Seu agendamento foi cancelado com sucesso!</p>
                <p><span>📅</span> Data: $data_format</p>
                <p><span>⏰</span> Hora: $hora</p>
                <p>Status: Cancelado</p>";

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

    public static function atualizar($email, $data, $hora, $nome, $servico)
    {
        $data_format = date("d/m/Y", strtotime($data)); 
        $mail = new PHPMailer(true);
        $mensagemTexto = "Olá, ".$nome."!\n".
        "Seu agendamento foi atualizado com sucesso!\n".
        "📅Data: ".$data."\n"."⏰ Hora: ".$hora."\n"."Serviço: ".$servico."\nObrigado por escolher nosso serviço!";
        $mensagemHtml = "<h2>Olá, $nome!</h2>
                <p>Seu agendamento foi atualizado com sucesso!</p>
                <p><strong><span>📅</span> Data:</strong> $data_format<br>
                   <strong><span>⏰</span> Hora:</strong> $hora<br>
                   <strong>Serviço:</strong> $servico
                </p>
                <p><em><strong>Aviso:</strong> Pedimos que chegue com alguns minutos de antecedência. Em caso de atraso maior que 15 minutos, não conseguimos garantir o atendimento e poderá ser necessário reagendar conforme disponibilidade de horário.</em>";

        try {
            self::configurar($mail);

            $mail->addAddress($email);
            $mail->Subject = "Troca de agendamento";
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

    public static function resetSenha($email, $senha, $nome)
    {
        $mail = new PHPMailer(true);
        $mensagemTexto = "Olá".$nome."!\nSua senha foi alterada com sucesso!\nNova senha: ".$senha."\n".
        "Aviso: Caso queira criar uma senha personalizada, basta acessar: Perfil -> Atualizar Senha.";
        $mensagemHtml = "<h2>Olá, $nome!</h2>
                <p>Sua senha foi alterada com sucesso!</p>
                <p><strong>Nova senha:</strong> $senha</p>
                <p><em><strong>Aviso:</strong> Caso queira criar uma senha personalizada, basta acessar: Perfil -> Atualizar Senha.</em></p>";

        try {
            self::configurar($mail);

            $mail->addAddress($email);
            $mail->Subject = "Reset de Senha";
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

    public static function atualizarSenha($email, $nome)
    {
        $mail = new PHPMailer(true);
        $mensagemTexto = "Olá".$nome."!\nSua senha foi alterada com sucesso!\nFaça login e acesse seus agendamentos!";
        $mensagemHtml = "<h2>Olá, $nome!</h2>
                <p>Sua senha foi alterada com sucesso!</p>
                <p>Faça login e acesse seus agendamentos!</p>";

        try {
            self::configurar($mail);

            $mail->addAddress($email);
            $mail->Subject = "Atualização de Senha";
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