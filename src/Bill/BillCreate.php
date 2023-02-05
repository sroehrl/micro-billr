<?php

namespace App\Bill;

use App\Auth\Auth;
use App\Auth\BehindLogin;
use App\Company\CompanyModel;
use App\Customer\CustomerModel;
use App\Helper\FeedbackWrapper;
use App\Milestone\MilestoneModel;
use App\Project\ProjectModel;
use App\Settings\InvoiceSettingModel;
use App\Timesheet\TimesheetModel;
use App\User\UserModel;
use Neoan\Errors\SystemError;
use Neoan\Model\Collection;
use Neoan\Request\Request;
use Neoan\Response\Response;
use Neoan\Routing\Attributes\Post;
use Neoan\Routing\Attributes\Web;
use Neoan\Routing\Interfaces\Routable;

#[Web('/new-bill/:projectId', 'Bill/views/create.html', BehindLogin::class)]
#[Post('/new-bill/:projectId', BehindLogin::class)]
class BillCreate implements Routable
{
    private Collection $milestones;
    private UserModel $user;
    private ?InvoiceSettingModel $invoiceSettings;

    public function __invoke(Auth $auth): array
    {
        $this->user = $auth->user;
        try{
            $this->invoiceSettings = InvoiceSettingModel::retrieveOne(['companyId' => $auth->user->companyId]);
        } catch (\Exception $e) {
            new SystemError('Invoice settings are not set up');
        }

        // call get() or post()
        return $this->{strtolower(Request::getRequestMethod()->name)}(Request::getParameter('projectId'));
    }
    public function get(int $projectId): array
    {
        $this->milestones = new Collection();
        MilestoneModel::retrieve(['projectId' => $projectId, '^deletedAt'])->each(function (MilestoneModel $milestone){
            if(CostCalculator::unbilledHoursOnMilestone($milestone->id) > 0) {
                $this->milestones->add($milestone);
            }
        });
        $project = ProjectModel::get($projectId)->withPerson();
        return [
            'project' => $project->toArray(),
            'milestones' => $this->milestones->toArray(),
            'company' => $this->user->company()->toArray(),
            'customer' => CustomerModel::get($project->customerId)->withAddress()->toArray(),
            'billNumber' => CostCalculator::createInvoiceNumber($this->invoiceSettings)
        ];
    }
    public function post(int $projectId): array
    {
        $bill = new BillModel();
        [
            'customerId' => $bill->customerId,
            'billNumber' => $bill->billNumber,
        ] = Request::getInputs();
        $bill->projectId = $projectId;
        $timeSheets = new Collection();
        foreach (Request::getInput('timesheets') as $id => $status) {
            if($status === 'on'){
                $timeSheets->add(TimesheetModel::get($id));
            }
        }
        $bill->totalNet = CostCalculator::calculateTimesheetNet($timeSheets);
        $bill->store();
        $timeSheets->each(function (TimesheetModel $timesheet) use($bill){
            $timesheet->billId = $bill->id;
        })->store();
        // update runner
        $this->invoiceSettings->invoiceNumber++;
        $this->invoiceSettings->store();

        Response::redirect(FeedbackWrapper::appendFeedback('/bill/'. $bill->id, 'Bill created'));
        return [];
    }
}