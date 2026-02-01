<?php

namespace Modules\Basic\Enums;

enum DocumentLabelsEnum: string
{
    case FINANCIAL_STATEMENT = 'financial_statement';
    case PROOF_OF_BALANCE = 'proof_of_balance';
    case TAX_CERTIFICATE_AND_VALUE_ADDED_TAX = 'tax_certificate_and_value_added_tax';
    case SPECIFIC_AND_MONTHLY_REPORTS = 'specific_and_monthly_reports';
    case BUDGET_AND_PRODUCTION_COMMERCIAL_REPORTS = 'budget_and_production_commercial_reports';
    case REGISTRATION_AND_CHANGE_DOCUMENTS = 'registration_and_change_documents';
    case IDENTITY_DOCUMENTS_OF_NATURAL_PERSONS = 'identity_documents_of_natural_persons';
    case LICENSES_CERTIFICATES_AND_BUSINESS_PERMITS = 'licenses_certificates_and_business_permits';
    case ASSETS_AND_ACTIVITY_LOCATION_DOCUMENTS = 'assets_and_activity_location_documents';
    case INSURANCE_AND_TAX_PAYMENTS = 'insurance_and_tax_payments';
    case BANK_LETTER_WITH_COMPANY_SEAL = 'bank_letter_with_company_seal';
    case COMPLETED_BANK_FORM_WITH_COMPANY_SEAL = 'completed_bank_form_with_company_seal';
    case COMMERCIAL_CONTRACTS_AND_INVOICES = 'commercial_contracts_and_invoices';

    public static function getOptions(): array
    {
        return array_combine(
            array_map(fn(self $documentType) => $documentType->value, self::cases()),
            array_map(fn(self $documentType) => $documentType->getLabel(), self::cases())
        );
    }

    public function getLabel(): string
    {
        return match ($this) {
            self::FINANCIAL_STATEMENT => 'صورت مالی',
            self::PROOF_OF_BALANCE => 'تراز آزمایشی',
            self::TAX_CERTIFICATE_AND_VALUE_ADDED_TAX => 'اظهارنامه مالیاتی و ارزش افزوده',
            self::SPECIFIC_AND_MONTHLY_REPORTS => 'گزارشات معین و فصلی و ارزش افزوده',
            self::BUDGET_AND_PRODUCTION_COMMERCIAL_REPORTS => 'گزارشات بودجه و تولید و تجارت',
            self::REGISTRATION_AND_CHANGE_DOCUMENTS => 'مستندات ثبت و تغییرات شرکت',
            self::IDENTITY_DOCUMENTS_OF_NATURAL_PERSONS => 'اسناد هویتی اشخاص حقیقی',
            self::LICENSES_CERTIFICATES_AND_BUSINESS_PERMITS => 'مجوزها و گواهی نامه ها و کارت بازرگانی',
            self::ASSETS_AND_ACTIVITY_LOCATION_DOCUMENTS => 'اسناد و اطلاعات دارایی ها و محل فعالیت',
            self::INSURANCE_AND_TAX_PAYMENTS => 'پرداخت بیمه و مالیات',
            self::BANK_LETTER_WITH_COMPANY_SEAL => 'نامه در سربرگ با مهر شرکت',
            self::COMPLETED_BANK_FORM_WITH_COMPANY_SEAL => 'فرم تکمیل شده با مهر شرکت',
            self::COMMERCIAL_CONTRACTS_AND_INVOICES => 'قرارداد و فاکتور معاملاتی',
        };
    }
}
