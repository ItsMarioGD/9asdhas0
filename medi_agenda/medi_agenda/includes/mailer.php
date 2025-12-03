<?php
class Mailer {
    private $logFile;

    public function __construct() {
        $this->logFile = __DIR__ . '/../emails.log';
    }

    public function sendAppointmentConfirmation($toEmail, $patientName, $dateTime, $doctorName) {
        $subject = "Confirmación de Cita - Medi-Agenda AI";
        $message = "
        Hola $patientName,

        Tu cita ha sido confirmada exitosamente.

        📅 Fecha y Hora: $dateTime
        👨‍⚕️ Doctor: $doctorName
        
        Por favor llega 10 minutos antes.
        
        Saludos,
        Equipo Medi-Agenda AI
        ";

        // Intentar enviar correo real (requiere configuración SMTP en php.ini o PHPMailer)
        // Por defecto en XAMPP esto suele fallar sin config, así que usaremos un log.
        $headers = 'From: no-reply@mediagenda.com' . "\r\n" .
                   'Reply-To: contacto@mediagenda.com' . "\r\n" .
                   'X-Mailer: PHP/' . phpversion();

        $sent = @mail($toEmail, $subject, $message, $headers);

        // Guardar en log siempre (para demostración)
        $logEntry = "--- EMAIL SENT [" . date('Y-m-d H:i:s') . "] ---\n";
        $logEntry .= "To: $toEmail\n";
        $logEntry .= "Subject: $subject\n";
        $logEntry .= "Message:\n$message\n";
        $logEntry .= "Status: " . ($sent ? "SENT" : "LOGGED ONLY (SMTP not configured)") . "\n";
        $logEntry .= "----------------------------------------\n\n";

        file_put_contents($this->logFile, $logEntry, FILE_APPEND);

        return true;
    }
}
?>
