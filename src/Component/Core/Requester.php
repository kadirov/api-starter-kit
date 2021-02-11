<?php
declare(strict_types=1);

namespace App\Component\Core;

use App\Controller\Base\Constants\ResponseFormat;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Throwable;

class Requester
{
    private LoggerInterface $logger;
    private KernelInterface $kernel;
    private HttpClientInterface $httpClient;
    private SerializerInterface $serializer;

    public function __construct(
        LoggerInterface $logger,
        KernelInterface $kernel,
        HttpClientInterface $httpClient,
        SerializerInterface $serializer
    ) {
        $this->logger = $logger;
        $this->kernel = $kernel;
        $this->httpClient = $httpClient;
        $this->serializer = $serializer;
    }

    public function request(
        string $url,
        string $type,
        array $options = [],
        bool $isUrlEnvVar = true,
        array $urlContext = []
    ): ResponseInterface {
        $rawUrl = $isUrlEnvVar ? $this->getEnv($url) : $url;

        if (!empty($urlContext)) {
            $rawUrl = $this->replaceUrlContext($rawUrl, $urlContext);
        }

        try {
            $this->getLogger()->info(
                'request to url: {url}, with options: {options}',
                [
                    'url'     => $rawUrl,
                    'options' => $this->recursiveImplode(', ', $options),
                ]
            );

            $response = $this->getHttpClient()->request($type, $rawUrl, $options);
        } catch (TransportExceptionInterface $e) {
            $this->getLogger()->error($e->getMessage());
            throw new RuntimeException($e->getMessage());
        }

        return $response;
    }

    public function isResponseOk(ResponseInterface $response): bool
    {
        try {
            $code = $response->getStatusCode();

            if ($code >= Response::HTTP_OK && $code < Response::HTTP_MULTIPLE_CHOICES) {
                return true;
            }

            if ($code === Response::HTTP_NOT_FOUND) {
                return false;
            }

            $this->getLogger()->info('HTTP Status: {code}', ['code' => $response->getStatusCode()]);
            return false;
        } catch (TransportExceptionInterface $e) {
            throw new RuntimeException($e->getMessage());
        }
    }

    public function getContent(ResponseInterface $response): string
    {
        try {
            return $response->getContent(true);
        } catch (Throwable $e) {
            throw new RuntimeException($e->getMessage());
        }
    }

    public function responseToDto(
        ResponseInterface $response,
        string $dtoClass,
        string $format = ResponseFormat::JSONLD
    ): object {
        return $this->getSerializer()->deserialize($this->getContent($response), $dtoClass, $format);
    }

    private function getEnv(string $env): string
    {
        return $this->kernel->getContainer()->getParameter($env);
    }

    private function getLogger(): LoggerInterface
    {
        return $this->logger;
    }

    private function getKernel(): KernelInterface
    {
        return $this->kernel;
    }

    private function getHttpClient(): HttpClientInterface
    {
        return $this->httpClient;
    }

    private function getSerializer(): SerializerInterface
    {
        return $this->serializer;
    }

    private function replaceUrlContext(string $url, array $context): string
    {
        $res = str_replace(array_keys($context), $context, $url);
        return str_replace(['{', '}'], '', $res);
    }

    private function recursiveImplode(string $glue, array $array): string
    {
        $result = '';

        foreach ($array as $item) {
            if (is_array($item)) {
                $result .= $this->recursiveImplode($glue, $item) . $glue;
            } elseif (is_object($item)) {
                $result .= 'object' . $glue;
            } else {
                $result .= $item . $glue;
            }
        }

        return substr($result, 0, 0 - strlen($glue));
    }
}
