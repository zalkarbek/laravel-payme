<?php
namespace App\Constants\Payments;

// Payme методы Merchant Api
class PaycomMerchantMethods {
    const CHECK_PERFORM_TRANSACTION = 'CheckPerformTransaction';
    const CREATE_TRANSACTION = 'CreateTransaction';
    const PERFORM_TRANSACTION = 'PerformTransaction';
    const CANCEL_TRANSACTION = 'CancelTransaction';
    const CHECK_TRANSACTION = 'CheckTransaction';
    const CHANGE_PASSWORD = 'ChangePassword';
    const GET_STATEMENT = 'GetStatement';

    const METHOD_LISTS = [
        self::CHECK_PERFORM_TRANSACTION,
        self::CREATE_TRANSACTION,
        self::PERFORM_TRANSACTION,
        self::CANCEL_TRANSACTION,
        self::CHECK_TRANSACTION,
        self::CHANGE_PASSWORD,
        self::GET_STATEMENT,
    ];
}
