<?php

namespace ScaleXY\Whatsapp;

use ScaleXY\Whatsapp\Commands\WhatsappCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class WhatsappServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('whatsapp')
            ->hasConfigFile();
        // ->hasViews()
        // ->hasMigration('create_whatsapp_table')
        // ->hasCommand(WhatsappCommand::class);
    }
}
