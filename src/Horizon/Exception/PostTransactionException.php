<?php


namespace ZuluCrypto\StellarSdk\Horizon\Exception;


use GuzzleHttp\Exception\ClientException;
use ZuluCrypto\StellarSdk\Xdr\XdrBuffer;
use ZuluCrypto\StellarSdk\XdrModel\TransactionResult;

class PostTransactionException extends HorizonException
{
    /**
     * @var TransactionResult
     */
    protected $result;

    public static function fromRawResponse($requestedUrl, $httpMethod, $raw, ClientException $clientException)
    {
        $horizonEx = parent::fromRawResponse($requestedUrl, $httpMethod, $raw, $clientException);

        $postTransactionEx = static::fromHorizonException($horizonEx);

        // Add in the PostTransactionResponse
        if (!empty($raw['extras']['result_xdr'])) {
            $xdr = new XdrBuffer(base64_decode($raw['extras']['result_xdr']));
            $postTransactionEx->result = TransactionResult::fromXdr($xdr);
        }

        return $postTransactionEx;
    }

    /**
     * @param HorizonException $horizonException
     * @return PostTransactionException
     */
    public static function fromHorizonException(HorizonException $horizonException)
    {
        $ex = new PostTransactionException($horizonException->getTitle(), $horizonException->getPrevious());

        $ex->requestedUrl = $horizonException->getRequestedUrl();
        $ex->httpMethod = $horizonException->getHttpMethod();
        $ex->type = $horizonException->getType();
        $ex->httpStatusCode = $horizonException->getHttpStatusCode();
        $ex->detail = $horizonException->getDetail();
        $ex->operationResultCodes = $horizonException->getOperationResultCodes();
        $ex->transactionResultCode = $horizonException->getTransactionResultCode();
        $ex->message = $horizonException->getMessage();
        $ex->raw = $horizonException->getRaw();
        $ex->clientException = $horizonException->getClientException();

        return $ex;
    }

    /**
     * @return TransactionResult
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @param TransactionResult $result
     */
    public function setResult($result)
    {
        $this->result = $result;
    }
}