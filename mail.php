<?php
    $headers = 'From: noreply@tutormeet.pl' . "\r\n" .
            'Reply-To: noreply@tutormeet.pl' . "\r\n" .
            'X-Mailer: PHP/' . phpversion();

    mail("marek@websfera.pl", "Test", "Wiadomość testowa", $headers);
