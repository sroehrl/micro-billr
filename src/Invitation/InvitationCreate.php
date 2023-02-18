<?php

namespace App\Invitation;

use App\Auth\Auth;
use App\Auth\BehindLogin;
use App\Helper\FeedbackWrapper;
use Neoan\Routing\Attributes\Post;
use Neoan\Routing\Interfaces\Routable;

#[Post('/invitation', BehindLogin::class)]
class InvitationCreate implements Routable
{
    public function __invoke(Auth $auth, InvitationRequest $request): void
    {
        $feedback = 'invitation created';
        $invitation = new InvitationModel([
            'inviterUserId' => $auth->user->id,
            'companyId' => $auth->user->companyId
        ]);
        $invitation->privilege = $request->privilege;
        $invitation->email = $request->email;
        try{
            $invitation->store();
        } catch (\Exception $e){
            $feedback = 'error creating invite';
        }
        FeedbackWrapper::redirectBack($feedback);
    }
}