<?php

namespace Units\FinnoTech\Common\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Client\ConnectionException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Units\FinnoTech\Common\Services\dto\FinnoTechAuthorizedSignatoriesDto;
use Units\FinnoTech\Common\Services\dto\FinnoTechAuthorizedSignatoriesHolderItemDto;
use Units\FinnoTech\Common\Services\dto\FinnoTechBackChequeDto;
use Units\FinnoTech\Common\Services\dto\FinnoTechCorporateJournalDto;
use Units\FinnoTech\Common\Services\dto\FinnoTechErrorDto;
use Units\FinnoTech\Common\Services\dto\FinnoTechFacilityInquiryDto;
use Units\FinnoTech\Common\Services\dto\FinnoTechMobileAndNationalCodeVerifyDto;
use Units\FinnoTech\Common\Services\exceptions\FinnoTechServiceCreateTokenException;

class FinnoTechService
{
    protected string $apiUrl;
    protected string $clientId;


    public function __construct()
    {
        $this->apiUrl = config('finnotech.api_url');
        $this->clientId = config('finnotech.client_id');
    }


    /**
     * @throws FinnoTechServiceCreateTokenException
     */
    public function createClientCredentials(): FinnoTechErrorDto|array
    {
        try {
            $response = \Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Basic ' . base64_encode($this->clientId . ':' . config('finnotech.client_secret')),
            ])
                ->contentType('application/json')
                ->accept('application/json')
                ->post($this->apiUrl . '/dev/v2/oauth2/token', [
                    "nid" => config('finnotech.client_credentials.nid'),
                    "grant_type" => "client_credentials",
                    "scopes" => config('finnotech.client_credentials.scopes'),
                ]);

            if ($response->failed()) {
                throw new FinnoTechServiceCreateTokenException(
                    $response->body()
                );
            }
            if ($response->successful()) {
                $result = $response->json();
                if ($result['status'] === 'DONE') {
                    cache()->put(
                        'finnotech-client-credentials',
                        $result,
                        now()->addMilliseconds($result['result']['lifeTime'])
                    );
                    return $result;
                } else {
                    return FinnoTechErrorDto::fromArray($result);
                }
            }
        } catch (ConnectionException $e) {
            throw new FinnoTechServiceCreateTokenException($e->getMessage());
        }
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws FinnoTechServiceCreateTokenException
     */
    public function getClientCredentials(): FinnoTechErrorDto|array
    {
        $cachedToken = null;
        if (cache()->has('finnotech-client-credentials')) {
            $cachedToken = cache()->get('finnotech-client-credentials');
        }
        if (is_null($cachedToken)) {
            $cachedToken = $this->createClientCredentials();
        }

        return $cachedToken;
    }


    public function mobileAndNationalCodeVerify(
        string $mobileNumber,
        string $nationalCode
    ): FinnoTechMobileAndNationalCodeVerifyDto|FinnoTechErrorDto {
        $response = \Http::withToken($this->getClientCredentials()['result']['value'])
            ->get($this->apiUrl . '/mpg/v2/clients/' . config('finnotech.client_id') . '/shahkar/verify', [
                'trackId' => \Str::uuid(),
                'mobile' => $mobileNumber,
                'nationalCode' => $nationalCode,
            ]);

        if ($response->successful()) {
            $json = $response->json();
            if ($json['status'] === 'DONE') {
                return FinnoTechMobileAndNationalCodeVerifyDto::fromArray($json['result']);
            } else {
                return FinnoTechErrorDto::fromArray($json);
            }
        }

        if ($response->failed()) {
            \Log::error('خطا در فینوتک: ', ['response' => $response->body(), 'code' => $response->status()]);
        }
        if ($response->getStatusCode() == 403) {
            return FinnoTechErrorDto::fromArray([
                'responseCode' => 403,
                'status' => 'FAILED',
                'error'=>[
                    'code' => 403,
                    'message' => 'دسترسی به سرویس فینوتک محدود شده است'
                ],
            ]);
        }
        return FinnoTechErrorDto::fromArray($response->json());
    }

    public function authorizedSignatories(string $companyId,string $trackId=null): FinnoTechErrorDto|FinnoTechAuthorizedSignatoriesDto|null
    {
        $response = \Http::withToken($this->getClientCredentials()['result']['value'])
            ->accept('application/json')
            ->get($this->apiUrl . '/kyb/v2/clients/' . $this->clientId . '/authorizedSignatories', [
                'trackId' => !empty($trackId)?$trackId:\Str::uuid(),
                'companyId' => $companyId,
            ]);
        if ($response->getStatusCode() == 403) {
            return FinnoTechErrorDto::fromArray([
                'responseCode' => 403,
                'status' => 'FAILED',
                'error'=>[
                    'code' => 403,
                    'message' => 'دسترسی به سرویس فینوتک محدود شده است'
                ],
            ]);
        }
        if ($response->successful()) {
            $json = $response->json();
            if ($json['status'] === 'DONE') {
                return FinnoTechAuthorizedSignatoriesDto::fromArray($json['result']);
            } else {
                return FinnoTechErrorDto::fromArray($json);
            }
        }

        \Log::error('خطا در فینوتک: ', ['response' => $response->body(), 'code' => $response->status()]);
        return FinnoTechErrorDto::fromArray($response->json());
    }

    public function facilityInquiry(string $user): FinnoTechFacilityInquiryDto|FinnoTechErrorDto|null
    {
        $response = \Http::withToken($this->getClientCredentials()['result']['value'])
            ->accept('application/json')
            ->get($this->apiUrl . '/credit/v2/clients/' . $this->clientId . '/users/' . $user . '/facilityInquiry', [
                'trackId' => \Str::uuid(),
            ]);
        if ($response->getStatusCode() == 403) {
            return FinnoTechErrorDto::fromArray([
                'responseCode' => 403,
                'status' => 'FAILED',
                'error'=>[
                    'code' => 403,
                    'message' => 'دسترسی به سرویس فینوتک محدود شده است'
                ],
            ]);
        }
        if ($response->successful()) {
            $json = $response->json();
            if ($json['status'] == 'DONE') {
                return FinnoTechFacilityInquiryDto::fromArray($json->json()['result']);
            } else {
                return FinnoTechErrorDto::fromArray($json);
            }
        }
        \Log::error('خطا در فینوتک: ', ['response' => $response->body(), 'code' => $response->status()]);

        return FinnoTechErrorDto::fromArray($response->json());
    }

    /**
     * استعلام چک برگشتی
     * @param string $user
     * @return FinnoTechBackChequeDto|null
     * @throws ConnectionException
     * @throws ContainerExceptionInterface
     * @throws FinnoTechServiceCreateTokenException
     * @throws NotFoundExceptionInterface
     */
    public function backCheque(string $user): FinnoTechErrorDto|FinnoTechBackChequeDto|null
    {
        $response = \Http::withToken($this->getClientCredentials()['result']['value'])
            ->accept('application/json')
            ->get($this->apiUrl . '/credit/v2/clients/' . $this->clientId . '/users/' . $user . '/backCheques', [
                'trackId' => \Str::uuid(),
            ]);
        if ($response->getStatusCode() == 403) {
            return FinnoTechErrorDto::fromArray([
                'responseCode' => 403,
                'status' => 'FAILED',
                'error'=>[
                    'code' => 403,
                    'message' => 'دسترسی به سرویس فینوتک محدود شده است'
                ],
            ]);
        }
        if ($response->successful()) {
            $json = $response->json();
            if ($json['status'] == 'DONE') {
                return FinnoTechBackChequeDto::fromArray($json['result']);
            } else {
                return FinnoTechErrorDto::fromArray($json);
            }
        }

        \Log::error('خطا در فینوتک: ', ['response' => $response->body(), 'code' => $response->status()]);

        return FinnoTechErrorDto::fromArray($response->json());
    }

    /**
     * استعلام آگهی های روزنامه رسمی شرکت
     * @param string $companyId شناسه ملی شرکت
     * @param string|null $trackId کد پیگیری (اختیاری)
     * @return FinnoTechCorporateJournalDto|null
     * @throws ConnectionException
     * @throws ContainerExceptionInterface
     * @throws FinnoTechServiceCreateTokenException
     * @throws NotFoundExceptionInterface
     */
    public function corporateJournalInquiry(
        string $companyId,
        ?string $trackId = null
    ): FinnoTechCorporateJournalDto|FinnoTechErrorDto|null {
        $params = [
            'companyId' => $companyId,
        ];

        if ($trackId) {
            $params['trackId'] = $trackId;
        } else {
            $params['trackId'] = \Str::uuid();
        }

        $response = \Http::withToken($this->getClientCredentials()['result']['value'])
            ->accept('application/json')
            ->get($this->apiUrl . '/kyb/v2/clients/' . $this->clientId . '/corporateJournal', $params);
        if ($response->getStatusCode() == 403) {
            return FinnoTechErrorDto::fromArray([
                'responseCode' => 403,
                'status' => 'FAILED',
                'error'=>[
                    'code' => 403,
                    'message' => 'دسترسی به سرویس فینوتک محدود شده است'
                ],
            ]);
        }
        if ($response->successful()) {
            $json = $response->json();
            if ($json['status'] == 'DONE') {
                return FinnoTechCorporateJournalDto::fromArray($json);
            } else {
                return FinnoTechErrorDto::fromArray($json);
            }
        }

        \Log::error('خطا در فینوتک corporate journal: ', [
            'response' => $response->body(),
            'code' => $response->status(),
            'companyId' => $companyId,
            'trackId' => $trackId
        ]);
        \Log::error('خطا در فینوتک: ', ['response' => $response->body(), 'code' => $response->status()]);

        return FinnoTechErrorDto::fromArray($response->json());
    }
}
