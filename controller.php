<?php

namespace Concrete\Package\OrticForum;

use Concrete\Core\Application\Application;
use Concrete\Core\Backup\ContentImporter;
use Concrete\Core\Package\PackageService;
use Concrete\Package\OrticForum\Src\Repository\Forum;
use Package;
use Core;
use Concrete\Core\Asset\AssetList;

class Controller extends Package
{
    protected $pkgHandle = 'ortic_forum';
    protected $appVersionRequired = '5.8';
    protected $pkgVersion = '0.0.5';

    public function getPackageName()
    {
        return t('Ortic Forum');
    }

    public function getPackageDescription()
    {
        return t('Simple forum solution');
    }

    public function install()
    {
        parent::install();
        $this->installXml();
    }

    public function upgrade()
    {
        $this->installXml();
        parent::upgrade();
    }

    protected function installXml()
    {
        $pkg = Core::make(PackageService::class)->getByHandle($this->pkgHandle);
        $ci = new ContentImporter();
        $ci->importContentFile($pkg->getPackagePath() . '/install.xml');
    }

    protected function registerRepositories()
    {
        $this->app->bind('ortic/forum', function () {
            return new Forum();
        });

        $this->app->singleton('ortic/forum/config', function (Application $app) {
            $pkg = Core::make(PackageService::class)->getByHandle('ortic_forum');
            return $pkg->getFileConfig();
        });
    }

    protected function registerAssets()
    {
        $al = AssetList::getInstance();
        $pkg = Core::make(PackageService::class)->getByHandle($this->pkgHandle);

        $al->register('javascript', 'ortic/forum', 'js/forum.js',
            ['minify' => true, 'combine' => true], $pkg
        );

        $al->registerGroup('ortic/forum', [
            ['javascript', 'jquery'],
            ['javascript', 'ortic/forum'],
        ]);
    }

    public function on_start()
    {
        $this->registerRepositories();
        $this->registerAssets();
    }
}