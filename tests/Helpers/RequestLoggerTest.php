<?php

namespace Tests\Helpers;

use Mockery;
use Packback\Lti1p3\Helpers\RequestLogger;
use Packback\Lti1p3\Interfaces\IServiceRequest;
use Tests\TestCase;

class RequestLoggerTest extends TestCase
{
    /**
     * @var Mockery\MockInterface
     */
    private $request;
    /**
     * @var array
     */
    private $responseHeaders;
    /**
     * @var array
     */
    private $responseBody;

    public function setUp(): void
    {
        $this->request = Mockery::mock(IServiceRequest::class);
        $this->responseHeaders = [
            'Content-Type' => ['application/json'],
            'Server' => ['nginx'],
        ];
        $this->responseBody = ['some' => 'response'];
        $this->requestLogger = Mockery::mock(RequestLogger::class)->makePartial();
    }

    public function testItLogsRequests()
    {
        $this->request->shouldReceive('getPayload')
            ->once()
            ->andReturn([
                'body' => json_encode(['userId' => 'id']),
            ]);
        $this->request->shouldReceive('getMethod')
            ->once()
            ->andReturn('GET');
        $this->request->shouldReceive('getUrl')
            ->once()
            ->andReturn('/test.com/test');
        $this->requestLogger->shouldReceive('errorLog')
            ->once();

        $result = $this->requestLogger->logRequest(
            RequestLogger::UNSUPPORTED_REQUEST,
            $this->request,
            $this->responseHeaders,
            $this->responseBody
        );

        $this->assertNull($result);
    }
}
