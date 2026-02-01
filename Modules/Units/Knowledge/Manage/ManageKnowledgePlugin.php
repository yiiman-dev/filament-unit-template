<?php

namespace Units\Knowledge\Manage;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Guava\FilamentKnowledgeBase\Plugins\KnowledgeBaseCompanionPlugin;
use Guava\FilamentKnowledgeBase\Plugins\KnowledgeBasePlugin;

class ManageKnowledgePlugin implements Plugin
{
    public static function make()
    {
        return new static;
    }
    public function getId(): string
    {
        return 'manage-knowledge';
    }

    public function register(Panel $panel): void
    {
       $panel->plugins([
           KnowledgeBasePlugin::make(),
           KnowledgeBaseCompanionPlugin::make()
               ->knowledgeBasePanelId('knowledge-base')
               ->slideOverPreviews()
           ->modalPreviews(),
       ]);
    }

    public function boot(Panel $panel): void
    {
        // TODO: Implement boot() method.
    }
}
