<?php

namespace Units\Approval\Manage\Docs;

use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Guava\FilamentKnowledgeBase\Enums\NodeType;
use Guava\FilamentKnowledgeBase\Models\FlatfileNode;
use Units\Knowledge\Package\src\Models\DocumentModel;

class ApprovalConceptDoc extends DocumentModel
{

    public function getId(): string
    {
        return 'approval';
    }

    public function getTitle(): ?string
    {
        return 'گردش کار';
    }

    public function isActive(): bool
    {
        return true;
    }

    public function getContent(): string
    {
        return  '### This is my title';
    }

    public function getOrder(): int
    {
        return  1;
    }

    public function getIcon(): ?string
    {
        return 'heroicons-o-eye';
    }

    public function getBreadcrumbs(): array
    {
        return [];
    }

    public function getType(): NodeType
    {
        return NodeType::Documentation;
    }

    public function getData(): array
    {
        return [];
    }

    public function getPanelId(): string
    {
        return 'manage';
    }


}
