<?php


namespace App\Helpers;


use Illuminate\Support\Facades\Response;

class ResponseHelper
{
    /**
     * @var int
     */
    private $statusCode = 200;

    public function getStatusCode()
    {
        return $this->statusCode;
    }

    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;
        return $this;
    }

    public function respond($data)
    {
        return Response::json($data, $this->getStatusCode(), [], JSON_INVALID_UTF8_IGNORE);
    }

    public function respondWithError(\Throwable $throwable)
    {
        $this->setStatusCode($throwable->getCode());

        if (
            $this->statusCode > 700 ||
            $this->statusCode == 0 ||
            $this->statusCode == 42 ||
            $this->statusCode == 22 ||
            $this->statusCode == -1
        ) {
            // Likely an SQL error (for instance: foreign key dependency, not respecting default value...), let's tell them this is not authorized
            $this->setStatusCode(401);
        }

        report($throwable);

        $response = [
            'displayAlert' => 'error',
            'message' => 'There was an error in the operation.',
            'error' => [
                'status_code' => $this->getStatusCode()
            ],
        ];

        if (
            is_a($throwable, 'Symfony\Component\HttpKernel\Exception\HttpException') &&
            array_key_exists('explicit_message', $throwable->getHeaders()) && $throwable->getHeaders()['explicit_message']
        )
            $response['message'] = $throwable->getMessage();

        if (env('APP_DEBUG')) {
            $response = array_merge($response, [
                'message' => $throwable->getMessage(),
                'file' => $throwable->getFile(),
                'line' => $throwable->getLine(),
                'trace' => $throwable->getTraceAsString()
            ]);
        }

        return $this->respond($response);
    }

    public function respondWithSuccessMessage($statusCode, $response)
    {
        // $response is usually a string but could be an array
        switch (gettype($response)) {
            case "array":
                return $this->setStatusCode($statusCode)->respond(array_merge([
                    'displayAlert' => 'success'
                ], $response));
            default:
                return $this->setStatusCode($statusCode)->respond([
                    'displayAlert' => 'success',
                    'message' => $response,
                ]);
        }
    }
}
