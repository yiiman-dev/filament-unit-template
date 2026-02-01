<?php
/*
 * Copyright (C) Saman beheshtian, Inc - All Rights Reserved
 * 2025.
 *
 * Author        Saman beheshtian
 * Position      Developer
 * Email         amintado@gmail.com
 * Phone         +989353466620
 * Date          5/3/25, 5:00 PM
 */

namespace Units\Auth\My\Filament\Pages\Auth;

use App\Filament\Forms\Components\Heading;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Http\Middleware\Authenticate;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Support\Colors\Color;
use Modules\Basic\BaseKit\Filament\BasePage;
use Units\Auth\My\Services\AuthService;
use Units\Corporates\Users\Common\Services\CorporateUserService;

/**
 * Page for selecting a corporate after successful authentication
 *
 * @property Form $form
 * @see \Units\Auth\My\Filament\Pages\Auth\VerifyPage
 * @see \Units\Corporates\Users\Common\Services\CorporateUserService
 */
class CorporateSelectPage extends BasePage implements HasForms
{
    use InteractsWithFormActions;

    /**
     * @var array<string, mixed> | null
     */
    public ?array $data = [];

    protected static string $route_path = 'auth/select-corporate';
    protected static string $view = 'my_auth::filament.pages.auth.select-corporate';
    protected static string $layout = 'my_auth::filament.layout.auth';
    protected static bool $shouldRegisterNavigation = false;
    protected static string|array $withoutRouteMiddleware = [
        Authenticate::class
    ];

    protected AuthService $_authService;

    protected CorporateUserService $_corporateUserService;

    public function __construct()
    {
        parent::__construct();
        $this->_authService = app(AuthService::class);
        $this->_corporateUserService = app(CorporateUserService::class);

        // Redirect to login if not authenticated
        if (empty($this->_authService->getMobileNumber())) {
            $this->redirect(Filament::getPanel('my')->getUrl(), true);
        }
    }

    public static function getRoutePath(): string
    {
        return self::$route_path;
    }

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        $corporateOptions = $this->_getCorporateOptions();

        // If no corporates available, show a message and redirect
        if (empty($corporateOptions)) {
            $this->redirect(Filament::getPanel('my')->getUrl('dashboard'), true);
            return $form;
        }

        return $form
            ->schema([
                Heading::make('title')
                    ->content('لطفاً شرکت مورد نظر خود را انتخاب نمایید')
                    ->columnSpanFull(),

                Select::make('corporate_id')
                    ->label('شرکت')
                    ->options($corporateOptions)
                    ->required()
                    ->searchable()
            ])
            ->statePath('data');
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('select')
                ->label('انتخاب شرکت')
                ->submit('select'),

            Action::make('skip')
                ->label('ورود بدون انتخاب شرکت')
                ->color(Color::Gray)
                ->url(Filament::getPanel('my')->getUrl('dashboard'))
        ];
    }

    public function select(): void
    {
        $data = $this->form->getState();

        if (empty($data['corporate_id'])) {
            $this->redirect(Filament::getPanel('my')->getUrl('dashboard'), true);
            return;
        }

        // Set the selected corporate in session
        session(['selected_corporate_id' => $data['corporate_id']]);

        // Redirect to dashboard
        $this->redirect(Filament::getPanel('my')->getUrl('dashboard'), true);
    }

    protected function getLayoutData(): array
    {
        return [
            'title' => 'انتخاب شرکت',
        ];
    }

    protected function hasFullWidthFormActions(): bool
    {
        return true;
    }

    protected function getHeaderWidgets(): array
    {
        return [];
    }

    protected function getFooterWidgets(): array
    {
        return [];
    }

    /**
     * Get the corporate options for the select field
     *
     * @return array<string, string>
     */
    private function _getCorporateOptions(): array
    {
        $this->_corporateUserService->actGetUserCorporates();

        if ($this->_corporateUserService->hasErrors()) {
            return [];
        }

        $response = $this->_corporateUserService->getSuccessResponse();
        if (!$response) {
            return [];
        }

        $corporates = $response->getData()['corporates'] ?? [];
        $options = [];

        foreach ($corporates as $corporate) {
            $options[$corporate->id] = "{$corporate->corporate_national_code} ({$corporate->rule_of_user})";
        }

        return $options;
    }
}
