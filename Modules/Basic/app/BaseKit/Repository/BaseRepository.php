<?php
/*
 * Copyright (C) Saman beheshtian, Inc - All Rights Reserved
 * 2025.
 *
 * Author        Saman beheshtian
 * Position      Developer
 * Email         amintado@gmail.com
 * Phone         +989353466620
 * Date          8/6/25, 3:59 AM
 */

namespace Modules\Basic\BaseKit\Repository;
use Illuminate\Support\Facades\DB;
use Modules\Basic\BaseKit\BaseService;
use MongoDB\Client as MongoClient;
use MongoDB\Driver\Session;
use Exception;

/**
 * Class BaseDistributedRepository
 *
 * مدیریت تراکنش توزیع شده بین MySQL و MongoDB
 */
abstract class BaseRepository extends BaseService
{
    /**
     * دیتابیس‌هایی که تحت تراکنش هستند
     */
    protected static array $dbTransactions = [];
    protected static ?Session $mongoSession = null;
    protected static bool $transactionStarted = false;
    abstract public static function make():self;
    /**
     * شروع تراکنش برای دیتابیس‌های مختلف
     *
     * @throws Exception
     */
    public static function beginTransactions(): void
    {
        if (self::$transactionStarted) {
            return;
        }

        try {
            // Start MySQL transaction (default connection)
            DB::beginTransaction();
            self::$dbTransactions[] = 'mysql';

            // Start MongoDB transaction
            $client = app(MongoClient::class);
            $session = $client->startSession();
            $session->startTransaction();

            self::$mongoSession = $session;
            self::$transactionStarted = true;
        } catch (Exception $e) {
            throw new Exception('خطا در شروع تراکنش: ' . $e->getMessage());
        }
    }

    /**
     * تایید تراکنش‌ها
     *
     * @throws Exception
     */
    public static function commitTransactions(): void
    {
        try {
            if (in_array('mysql', self::$dbTransactions)) {
                DB::commit();
            }

            if (self::$mongoSession) {
                self::$mongoSession->commitTransaction();
            }

            self::resetTransactionState();
        } catch (Exception $e) {
            self::rollbackTransactions();
            throw new Exception('خطا در commit تراکنش: ' . $e->getMessage());
        }
    }

    /**
     * لغو تراکنش‌ها
     */
    public static function rollbackTransactions(): void
    {
        if (in_array('mysql', self::$dbTransactions)) {
            DB::rollBack();
        }

        if (self::$mongoSession) {
            self::$mongoSession->abortTransaction();
        }

        self::resetTransactionState();
    }

    /**
     * گرفتن سشن MongoDB برای استفاده در عملیات
     *
     * @return Session|null
     */
    public static function getMongoSession(): ?Session
    {
        return self::$mongoSession;
    }

    /**
     * پاک‌سازی وضعیت تراکنش‌ها
     */
    protected static function resetTransactionState(): void
    {
        self::$mongoSession = null;
        self::$dbTransactions = [];
        self::$transactionStarted = false;
    }
}
