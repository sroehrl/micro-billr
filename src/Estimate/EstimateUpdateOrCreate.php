<?php

namespace App\Estimate;

use App\Auth\BehindLogin;
use App\DocumentCreator\Create;
use App\Helper\FeedbackWrapper;
use App\Mailing\Mail;
use Neoan\Helper\Setup;
use Neoan\Request\Request;
use Neoan\Routing\Attributes\Post;
use Neoan\Routing\Interfaces\Routable;

#[Post('/estimate/:projectId', BehindLogin::class)]
class EstimateUpdateOrCreate implements Routable
{
    public function __invoke(Create $documentCreator, Setup $setup, Mail $mail): void
    {
        $projectId = Request::getParameter('projectId');
        try{
            $estimate = EstimateModel::get(Request::getInput('id'));
        } catch (\Exception $e) {
            $estimate = new EstimateModel([
                'projectId' => $projectId
            ]);
            $estimate->store();
        }
        $feedback = 'Updated';


        if(Request::getInput('milestoneId')){
            $inputs = Request::getInputs();
            unset($inputs['id']);
            $item = new EstimateItemModel($inputs);
            $item->estimateId = $estimate->id;
            $item->store();
            $estimate->items->add($item);
            $feedback = 'Item added';
        }

        if(Request::getInput('lockIn')){
            $path = $setup->get('publicPath') . '/documents';
            foreach ([$estimate->project()->customerId, $estimate->createdAt->stamp] as $part) {
                $path .= '/' . $part;
                @mkdir($path);
            }
            $fileName = $path . '/estimate.pdf';
            $documentCreator->setTemplatePath('src/Estimate/documents/basic-offer.html');
//            $documentCreator->setOutputMode('I');
            $documentCreator->generateEstimate($estimate, $fileName);
            $estimate->lockedInAt->set('now');
            $estimate->store();
            $feedback = 'Estimate generated';
        }
        if(Request::getInput('sendOut')) {
            $feedback = 'There was a problem with sending out the email automatically.';
            if($mail->sendEstimate($estimate)){
                $estimate->sentAt->set('now');
                $estimate->store();
                $feedback = 'Email sent';
            }


        }

        FeedbackWrapper::redirectBack($feedback);
    }
}