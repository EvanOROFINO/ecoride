<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;

final class ContactController extends Controller
{
    public function show(): void
    {
        $this->view('home/contact', ['pageTitle' => 'Contact']);
    }

    public function send(): void
    {
        $this->verifyCsrf();

        $nom     = $this->input('nom')     ?? '';
        $email   = $this->input('email')   ?? '';
        $sujet   = $this->input('sujet')   ?? '';
        $message = $this->input('message') ?? '';

        if ($nom === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $sujet === '' || strlen($message) < 10) {
            $this->flash('error', 'Merci de remplir tous les champs correctement.');
            $this->redirect('/contact');
        }

        // En production : envoyer un mail via PHPMailer.
        // Pour l'évaluation, on stocke le message en logs MongoDB.
        \App\Core\Mongo::getInstance()->logs->insertOne([
            'date'    => new \MongoDB\BSON\UTCDateTime(),
            'niveau'  => 'info',
            'message' => 'Message de contact reçu',
            'context' => compact('nom', 'email', 'sujet', 'message'),
        ]);

        $this->flash('success', 'Votre message a bien été envoyé. Nous vous répondrons sous 48h.');
        $this->redirect('/contact');
    }
}
