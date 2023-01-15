<?php

namespace Config;

use Neoan\Database\SqLiteAdapter;
use Neoan\Enums\GenericEvent;
use Neoan\Enums\ResponseOutput;
use Neoan\Event\Event;
use Neoan\Helper\Env;
use Neoan\Helper\Setup;
use Neoan\NeoanApp;
use Neoan\Response\Response;
use Neoan\Store\Store;
use Neoan3\Apps\Session;
use Neoan3\Apps\Template\Constants;
use Neoan3\Apps\Template\Template;
use NeoanIo\MarketPlace\DatabaseAdapter;

class Config
{
    private Setup $setup;
    public function __construct(NeoanApp $app)
    {
        new Session();
        Response::setDefaultRenderer(CustomRenderer::class);
        $skeletonVariables = [
            'pageTitle' => Store::dynamic('pageTitle'),
            'webPath' => $app->webPath,
            'loggedIn' => Session::isLoggedIn(),
            'currency' => Env::get('CURRENCY', 'USD')
        ];

        $this->typeSetup($app->injectionProvider->get(Setup::class))
            ->setUseSkeleton(true)
            ->setDefaultOutput(ResponseOutput::HTML)
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
        Constants::addCustomAttribute('partial', fn(\DOMAttr &$attr, $context) => $this->renderPartial($attr, $context));
        Constants::addCustomAttribute('gender-title', fn(\DOMAttr &$attr, $context) => $this->gender($attr, $context));
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
            'assumes_uuid' => Env::get('DB_UUID', false)
        ]);
    }

    private function renderPartial(\DOMAttr &$attr, $contextData = []): void
    {
        if(!$attr->parentNode->hasChildNodes()){
            $htmlString = Template::embraceFromFile($this->setup->get('templatePath') . $attr->nodeValue, $contextData);
            $fresh = new \DOMDocument();
            @$fresh->loadHTML( $htmlString, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
            $imported = $attr->ownerDocument->importNode($fresh->documentElement, true);
            $attr->parentNode->appendChild($imported);
        }
    }
    private function gender(\DOMAttr &$attr, $contextData = []):void
    {
        $attr->parentNode->nodeValue = match ((int) $attr->nodeValue){
            0 => 'Mrs.',
            1 => 'Mr.',
            2 => 'Mx.'
        };
    }
}