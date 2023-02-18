<?php

namespace Config;

use App\Bill\BillModel;
use App\Customer\CustomerModel;
use App\Product\ProductModel;
use App\Project\ProjectModel;
use App\Timeline\TimelineActivity;
use App\Timeline\TimelineModel;
use Neoan\Helper\Str;
use Neoan\NeoanApp;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand('update', 'Runs deployment updates')]
class UpdateCommand extends Command
{
    protected static $defaultName = 'update';
    protected static $defaultDescription = 'Runs deployment updates';
    private NeoanApp $neoanApp;

    public function __construct(NeoanApp $neoanApp, string $name = null)
    {
        $this->neoanApp = $neoanApp;
        parent::__construct($name);
    }

    protected function configure()
    {
        $this
            ->setHelp('Run this to "migrate" data fillers between versions');

    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // generate hashes & companyId for projects
        ProjectModel::retrieve([])->each(function (ProjectModel $project) use($output){
            $project->companyId = 1;
            $project->slug = Str::randomAlphaNumeric(12);
            $project->store();
            // timeline minimum
            if(TimelineModel::retrieve(['projectId' => $project->id])->count() < 1) {
                $tl = new TimelineModel();
                $tl->projectId = $project->id;
                $tl->activity = TimelineActivity::PROJECT_CREATED;
                $tl->store();

                $output->writeln('Project created: ' . $project->id);
            }
        });
        $output->writeln('Projects updated');

        //product
        ProductModel::retrieve()->each(function (ProductModel $product){
            $product->companyId = 1;
            $product->store();

        });
        $output->writeln('Products updated');
        // customer
        CustomerModel::retrieve()->each(function (CustomerModel $c){
            $c->companyId = 1;
        });
        $output->writeln('Customers updated');
        // bill
        BillModel::retrieve()->each(function (BillModel $c){
            $c->companyId = 1;
        });
        $output->writeln('Bills updated');

        return Command::SUCCESS;
    }
}