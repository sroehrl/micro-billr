<?php

namespace App\Mailing;

interface MailProviderInterface
{

    public function setAuth(string $privateKey, string $publicKey);
    public function addSender(string $email, string $name): void;

    public function addReceiver(string $email, string $name): void;

    public function setSubject(string $subject): void;

    public function setText(string $textContent): void;

    public function setHtmlContent(string $htmlString): void;

    public function addAttachment(string $filePath, string $fileName): void;

    public function send(): bool;

}