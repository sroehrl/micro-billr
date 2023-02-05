<?php

namespace App\Mailing;

use App\Auth\Auth;
use App\Bill\BillModel;
use App\Company\CompanyModel;
use App\Estimate\EstimateModel;
use App\Person\PersonModel;
use App\Settings\MailSettingsModel;
use Neoan\Helper\Env;
use Neoan\Helper\Str;
use Neoan\NeoanApp;
use Neoan\Routing\Interfaces\Routable;
use Neoan3\Apps\Template\Template;

class Mail implements Routable
{
    private MailProviderInterface $provider;
    private CompanyModel $company;
    private NeoanApp $app;
    public function __invoke(Auth $auth, NeoanApp $app): static
    {
        // get settings
        $mailSettings = MailSettingsModel::retrieveOne(['^deletedAt', 'companyId' => $auth->user->companyId]);
        $this->company = $auth->user->company();
        $this->app = $app;
        // get provider
        $this->provider = $mailSettings->mailProvider->getProvider();
        $this->provider->setAuth($mailSettings->privateKey, $mailSettings->publicKey);
        $this->provider->addSender($mailSettings->senderEmail, $mailSettings->senderName);
        return $this;
    }
    public function sendEstimate(EstimateModel $estimate): bool
    {
        $project = $estimate->project();
        $receiver = $project->person();
        $customer = $project->customer();
        $attachmentPath = $this->app->publicPath . '/documents/'. $customer->id . '/' . $estimate->createdAt->stamp;
        $this->addReceiver($receiver);
        $this->provider->addAttachment($attachmentPath . '/estimate.pdf', 'estimate.pdf');
        $this->provider->setSubject('Your requested estimate');
        $html = Template::embraceFromFile('src/Mailing/documents/estimate.html', [
            'customer' => $customer->toArray(),
            'receiver' => $receiver->toArray(),
            'company' => $this->company->toArray(),
            'companyAddress' => $this->company->printableAddress()
        ]);
        $this->provider->setHtmlContent($html);
        return $this->provider->send();
    }
    public function sendBill(BillModel $bill): bool
    {
        $project = $bill->project();
        $receiver = $project->person();
        $customer = $project->customer();
        $attachmentPath = $this->app->publicPath . '/documents/'. $customer->id . '/' . $bill->createdAt->stamp;
        $this->addReceiver($receiver);
        $this->provider->addAttachment($attachmentPath . '/estimate.pdf', 'estimate.pdf');
        $this->provider->setSubject('Invoice ' . $bill->billNumber);
        $html = Template::embraceFromFile('src/Mailing/documents/invoice.html', [
            'customer' => $customer->toArray(),
            'receiver' => $receiver->toArray(),
            'company' => $this->company->toArray(),
            'companyAddress' => $this->company->printableAddress()
        ]);
        $this->provider->setHtmlContent($html);
        return $this->provider->send();
    }

    private function addReceiver(PersonModel $receiver){
        $this->provider->addReceiver($receiver->email, $receiver->firstName . ' ' . $receiver->lastName);
    }
}