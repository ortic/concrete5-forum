<?php

namespace Concrete\Package\OrticForum;

use Concrete\Core\Backup\ContentImporter;
use Concrete\Package\OrticForum\Src\Repository\Forum;
use Package;
use Core;
use Concrete\Core\Asset\AssetList;

class Controller extends Package
{
    protected $pkgHandle = 'ortic_forum';
    protected $appVersionRequired = '5.8';
    protected $pkgVersion = '0.0.3';

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
        $pkg = Package::getByHandle($this->pkgHandle);
        $ci = new ContentImporter();
        $ci->importContentFile($pkg->getPackagePath() . '/install.xml');
    }

    protected function registerRepositories()
    {
        Core::bind('ortic/forum', function () {
            return new Forum();
        });
    }

    protected function registerAssets()
    {
        $al = AssetList::getInstance();
        $pkg = Package::getByHandle($this->pkgHandle);

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