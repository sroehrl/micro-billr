<?php

namespace Config;

use Neoan\Database\SqLiteAdapter;
use Neoan\Enums\GenericEvent;
use Neoan\Event\Event;
use Neoan\Helper\Env;
use Neoan\Helper\Setup;
use Neoan\NeoanApp;
use Neoan\Response\Response;
use Neoan\Store\Store;
use Neoan3\Apps\Session;
use Neoan3\Apps\Template\Constants;
use NeoanIo\MarketPlace\DatabaseAdapter;

class Config
{
    private Setup $setup;
    public function __construct(NeoanApp $app)
    {
        new Session('mircrobillr', 3600);
        Response::setDefaultRenderer(CustomRenderer::class);
        $skeletonVariables = [
            'pageTitle' => Store::dynamic('pageTitle'),
            'webPath' => $app->webPath,
            'loggedIn' => Session::isLoggedIn(),
            'privilege' => Session::getUserSession() ? Session::getUserSession()['scope'][0]->name : 'GUEST',
            'currency' => Env::get('CURRENCY', 'USD')
        ];

        $this->typeSetup($app->injectionProvider->get(Setup::class))
            ->setUseSkeleton(true)
            ->setSkeletonHTML('config/skeleton.html')
            ->setSkeletonComponentPlacement('content')
            ->setSkeletonVariables($skeletonVariables)
            ->setTemplatePath('src/')
            ->setDatabaseAdapter($this->MySQL());
        Event::on(GenericEvent::BEFORE_RENDERING, function($ev)use ($skeletonVariables){
            if($skeletonVariables['pageTitle']->get() === null){
                Store::write('pageTitle', 'Billr');
            }
        });
        Constants::addCustomFunction('pureDate', function ($input){
            return preg_replace('/\s\d{2}:\d{2}:\d{2}/', '', $input);
        });
        Constants::addCustomAttribute('partial', fn(\DOMAttr &$attr, $context) => CustomAttributes::renderPartial($attr, $this->setup->get('templatePath'), $context));
        Constants::addCustomAttribute('gender-title', fn(\DOMAttr &$attr, $context) => CustomAttributes::gender($attr, $context));
        Constants::addCustomAttribute('decimal', fn(\DOMAttr &$attr, $context) => CustomAttributes::renderDecimal($attr, $context));
        Constants::addCustomAttribute('timeline-activity', fn(\DOMAttr &$attr, $context) => CustomAttributes::timelineIcon($attr, $context));
    }

    private function typeSetup(Setup $setup): Setup
    {
        $this->setup = $setup;
        return $setup;
    }
    private function SQLite(): SqLiteAdapter
    {
        return new SqLiteAdapter([
            'location' => __DIR__ . '/database.db'
        ]);

    }
    private function MySQL(): DatabaseAdapter
    {
        return new DatabaseAdapter([
            'host' => Env::get('DB_HOST', 'localhost'),
            'name' => Env::get('DB_NAME', 'billr'),
            'port' => Env::get('DB_PORT', 3306),
            'user' => Env::get('DB_USER', 'root'),
            'password' => Env::get('DB_PASSWORD', ''),
            'charset' => Env::get('DB_CHARSET', 'utf8mb4'),
            'casing' => Env::get('DB_CASING', 'camel'),
            'assumes_uuid' => Env::get('DB_UUID', false),
            'dev_errors' => Env::get('DB_DEV_ERRORS', false)
        ]);
    }

}