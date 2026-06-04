<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public $token;
    public $email;
    public $name;

    public function __construct($token, $email, $name)
    {
        $this->token = $token;
        $this->email = $email;
        $this->name = $name;
    }

    public function build()
    {
        // Menyusun URL lengkap untuk diarahkan ke Next.js
        $resetUrl = env('FRONTEND_URL') . '/reset-password?token=' . $this->token . '&email=' . urlencode($this->email);

        return $this->subject('Permintaan Pemulihan Kata Sandi - Smart Room')
                    ->view('emails.reset-password') // Kita akan buat file view-nya di bawah
                    ->with([
                        'resetUrl' => $resetUrl,
                        'name' => $this->name
                    ]);
    }
}