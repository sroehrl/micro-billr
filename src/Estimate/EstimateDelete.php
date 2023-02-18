<?php

namespace App\Estimate;

use App\Auth\BehindLogin;
use App\Timeline\TimelineActivity;
use App\Timeline\TimelineModel;
use Neoan\Database\Database;
use Neoan\Routing\Attributes\Delete;
use Neoan\Routing\Interfaces\Routable;

#[Delete('/api/estimate/:id', BehindLogin::class)]
class EstimateDelete implements Routable
{
    public function __invoke(EstimateDeleteRequest $request, TimelineModel $timeline): array
    {
        $estimate = EstimateModel::get($request->id);
        $estimate->deletedAt->set('now');
        Database::raw('DELETE FROM estimate_item_model WHERE estimateId = {{id}}', (array) $request);
        $timeline->activity = TimelineActivity::ESTIMATE_DELETED;
        $timeline->projectId = $estimate->projectId;
        $timeline->store();
        $estimate->store();
        return ['name' => 'EstimateDelete'];
    }
}