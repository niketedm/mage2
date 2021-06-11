<?php

namespace Synchrony\DigitalBuy\Gateway\Command;

use Magento\Payment\Gateway\CommandInterface;
use Magento\Payment\Gateway\Http\ClientInterface;
use Magento\Payment\Gateway\Command\ResultInterface;
use Magento\Payment\Gateway\Command\CommandException;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Payment\Gateway\Validator\ValidatorInterface;
use Magento\Payment\Gateway\Response\HandlerInterface;
use Magento\Payment\Gateway\Http\TransferFactoryInterface;
use Psr\Log\LoggerInterface;

abstract class AbstractGatewayCommand implements CommandInterface
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
     * @var HandlerInterface
     */
    private $handler;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * AdjustCommand constructor.
     *
     * @param BuilderInterface $requestBuilder
     * @param TransferFactoryInterface $transferFactory
     * @param ClientInterface $client
     * @param ValidatorInterface $validator
     * @param HandlerInterface $handler
     * @param LoggerInterface $logger
     */
    public function __construct(
        BuilderInterface $requestBuilder,
        TransferFactoryInterface $transferFactory,
        ClientInterface $client,
        ValidatorInterface $validator,
        HandlerInterface $handler,
        LoggerInterface $logger
    ) {
        $this->requestBuilder = $requestBuilder;
        $this->transferFactory = $transferFactory;
        $this->client = $client;
        $this->validator = $validator;
        $this->handler = $handler;
        $this->logger = $logger;
    }

    /**
     * @param array $commandSubject
     * @return void
     */
    public function execute(array $commandSubject)
    {
        $transfer = $this->transferFactory->create($this->requestBuilder->build($commandSubject));

        $response = $this->client->placeRequest($transfer);
        $result = $this->validator->validate(array_merge($commandSubject, ['response' => $response]));

        if (!$result->isValid()) {
            $this->processInvalidResult($result, $response);
        }

        $this->handler->handle($commandSubject, $response);
    }

    /**
     * Retrieve API endpoint
     *
     * @return string
     */
    protected function getClientApiEndpoint()
    {
        return $this->client->getApiEndpoint();
    }

    /**
     * Throws an exception if error.
     *
     * @param ResultInterface $result
     * @param array $response
     * @throws CommandException
     */
    abstract protected function processInvalidResult($result, $response);
}
