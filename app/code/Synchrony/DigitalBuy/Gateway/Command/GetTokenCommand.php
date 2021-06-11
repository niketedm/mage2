<?php

namespace Synchrony\DigitalBuy\Gateway\Command;

use Magento\Payment\Gateway\CommandInterface;
use Magento\Payment\Gateway\Http\ClientInterface;
use Magento\Payment\Gateway\Command\ResultInterface;
use Magento\Payment\Gateway\Command\CommandException;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Payment\Gateway\Validator\ValidatorInterface;
use Magento\Payment\Gateway\Http\TransferFactoryInterface;
use Magento\Payment\Gateway\Command\Result\ArrayResultFactory;
use Synchrony\DigitalBuy\Gateway\Response\AuthenticationReader;
use Psr\Log\LoggerInterface;

class GetTokenCommand implements CommandInterface
{
    /**
     * @var BuilderInterface
     */
    private $requestBuilder;

    /**
     * @var TransferFactoryInterface
     */
    private $transferFactory;

    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @var ArrayResultFactory
     */
    private $resultFactory;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var AuthenticationReader
     */
    private $responseReader;

    /**
     * GetTokenCommand constructor.
     * @param BuilderInterface $requestBuilder
     * @param TransferFactoryInterface $transferFactory
     * @param ClientInterface $client
     * @param ArrayResultFactory $resultFactory
     * @param ValidatorInterface $validator
     * @param AuthenticationReader $responseReader
     * @param LoggerInterface $logger
     */
    public function __construct(
        BuilderInterface $requestBuilder,
        TransferFactoryInterface $transferFactory,
        ClientInterface $client,
        ArrayResultFactory $resultFactory,
        ValidatorInterface $validator,
        AuthenticationReader $responseReader,
        LoggerInterface $logger
    ) {
        $this->requestBuilder = $requestBuilder;
        $this->transferFactory = $transferFactory;
        $this->client = $client;
        $this->resultFactory = $resultFactory;
        $this->validator = $validator;
        $this->responseReader = $responseReader;
        $this->logger = $logger;
    }

    /**
     * @param array $commandSubject
     * @return \Magento\Payment\Gateway\Command\ResultInterface|null|void
     */
    public function execute(array $commandSubject)
    {
        $transfer = $this->transferFactory->create($this->requestBuilder->build($commandSubject));

        $response = $this->client->placeRequest($transfer);
        $result = $this->validator->validate(array_merge($commandSubject, ['response' => $response]));

        if (!$result->isValid()) {
            $errorMessage = implode("\n", $result->getFailsDescription());
            $this->logger->critical(
                'Got invalid authentication response: ' . $errorMessage
                . '. API Endpoint was: ' . $this->client->getApiEndpoint()
                . '. Response was: ' . var_export($response, true)
            );
            throw new CommandException(__('Something went wrong, please try again later'));
        }

        $this->responseReader->setData($response);
        return $this->resultFactory->create(
            [
                'array' => [AuthenticationReader::CLIENT_TOKEN_KEY => $this->responseReader->getClientToken()]
            ]
        );
    }
}
