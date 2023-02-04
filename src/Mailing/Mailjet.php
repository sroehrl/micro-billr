<?php

namespace App\Mailing;

use Mailjet\Client;
use Mailjet\Resources;

class Mailjet implements MailProviderInterface
{
    private array $from;

    private array $to = [];

    private Client $mj;
    private string $subject;

    private string $textContent = 'Please activate HTML to view the content of this email';

    private string $htmlContent;

    private array $attachments = [];

    public function setAuth(string $privateKey, string $publicKey)
    {
        $this->mj = new Client($publicKey, $privateKey, true, ['version' => 'v3.1']);
    }
    public function addSender(string $email, string $name): void
    {
        $this->from = [
            'Email' => $email,
            'Name' => $name
        ];
    }

    public function addReceiver(string $email, string $name): void
    {
        $this->to[] = [
            'Email' => $email,
            'Name' => $name
        ];
    }

    public function setSubject(string $subject): void
    {
        $this->subject = $subject;
    }

    public function setText(string $textContent): void
    {
        $this->textContent = $textContent;
    }

    public function setHtmlContent(string $htmlString): void
    {
        $this->htmlContent = $htmlString;
    }

    public function addAttachment(string $filePath, string $fileName): void
    {
        $contents = file_get_contents($filePath);
        $this->attachments[] = [
            'ContentType' => "text/plain",
            'Filename' => $fileName,
            'Base64Content' => base64_encode($contents)
        ];
    }

    public function send(): bool
    {
        $body = [
            'Messages' => [[
                'From' => $this->from,
                'To' => $this->to,
                'Subject' => $this->subject,
                'TextPart' => $this->textContent,
                'HTMLPart' => $this->htmlContent,
                'Attachments' => $this->attachments
            ]],
        ];
        $response = $this->mj->post(Resources::$Email, ['body'=>$body]);
        return $response->success();
    }


}