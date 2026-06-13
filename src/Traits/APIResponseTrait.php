<?php

namespace Vlancy\LaravelApiResponse\Traits;

use Generator;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Contracts\Validation\Factory;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Vlancy\LaravelApiResponse\Enums\ErrorCodesEnum;
use Symfony\Component\HttpFoundation\StreamedJsonResponse;
use UnitEnum;

trait APIResponseTrait
{
    /**
     * The ok response
     * @param $data mixed|null
     * @param string|null $message
     * @param array $headers
     * @return JsonResponse
     */
    public function apiCreated(mixed $data = null, ?string $message = null, array $headers = []): JsonResponse
    {
        return $this->apiResponse([
            'status_code' => Response::HTTP_CREATED,
            'data' => $data,
            'message' => $message,
            'response_headers' => $headers,
        ]);
    }

    /**
     * The ok response
     * @param $data mixed|null
     * @param string|null $message
     * @param array $headers
     * @return JsonResponse
     */
    public function apiOk(mixed $data = null, ?string $message = null, array $headers = []): JsonResponse
    {
        return $this->apiResponse([
            'data' => $data,
            'message' => $message,
            'response_headers' => $headers,
        ]);
    }

    /**
     * The deleted response (HTTP 204 No Content).
     * Signals that a resource was successfully deleted.
     *
     * @param mixed|null $data
     * @param string|null $message
     * @param array $headers
     * @return JsonResponse
     */
    public function apiDeleted(mixed $data = null, ?string $message = null, array $headers = []): JsonResponse
    {
        return $this->apiResponse([
            'status_code' => Response::HTTP_NO_CONTENT,
            'data' => $data,
            'message' => $message,
            'response_headers' => $headers,
        ]);
    }

    /**
     * The not found response
     * @param array|string $errors
     * @param string|null $message
     * @param bool $throw_exception
     * @param string|int|UnitEnum|null $errorCode
     * @param array $headers
     * @return JsonResponse
     */
    public function apiNotFound(
        array|string             $errors = [],
        ?string                  $message = null,
        bool                     $throw_exception = true,
        string|int|UnitEnum|null $errorCode = null,
        array                    $headers = []
    ): JsonResponse
    {
        // Set errors
        $errorsCollection = collect($errors)
            ->filter(function ($value, $key) {
                return !empty($value);
            });

        // Set errors collection
        if ($errorsCollection->isNotEmpty()) {
            $errorsCollection = collect([
                'errors' => $errorsCollection->toArray(),
            ]);
        }

        // Set a default value if error code not sent
        if (!$errorCode && (bool)config('api-response.returnDefaultErrorCodes', true)) {
            $errorCode = $this->getErrorCode(config('api-response.errorCodesDefaults.apiNotFound', 'RESOURCE_NOT_FOUND'));
        }

        return $this->apiResponse([
            'type' => 'notfound',
            'throw_exception' => $throw_exception,
            'message' => $message,
            'data' => null,
            'errors' => $errorsCollection->toArray(),
            'errorCode' => $errorCode,
            'response_headers' => $headers,
        ]);
    }

    /**
     * The bad request response
     * @param array|string $errors
     * @param string|null $message
     * @param array $data
     * @param bool $throw_exception
     * @param string|int|null|UnitEnum $errorCode
     * @param array $headers
     * @return JsonResponse
     */
    public function apiConflict(
        array|string             $errors = [],
        ?string                  $message = null,
        array                    $data = [],
        bool                     $throw_exception = true,
        string|int|null|UnitEnum $errorCode = null,
        array                    $headers = []
    ): JsonResponse
    {
        // Set errors
        $errorsCollection = collect($errors)
            ->filter(function ($value, $key) {
                return !empty($value);
            });

        // Set errors collection
        if ($errorsCollection->isNotEmpty()) {
            $errorsCollection = collect([
                'errors' => $errorsCollection->toArray(),
            ]);
        }

        // Set a default value if error code not sent
        if (!$errorCode && (bool)config('api-response.returnDefaultErrorCodes', true)) {
            $errorCode = $this->getErrorCode(config('api-response.errorCodesDefaults.apiConflict', 'CONFLICT_ERROR'));
        }

        return $this->apiResponse([
            'type' => 'conflict',
            'throw_exception' => $throw_exception,
            'message' => $message,
            'data' => $data,
            'errors' => $errorsCollection->toArray(),
            'errorCode' => $errorCode,
            'response_headers' => $headers,
        ]);
    }

    /**
     * The bad request response
     * @param array|string $errors
     * @param string|null $message
     * @param bool $throw_exception
     * @param string|int|null|UnitEnum $errorCode
     * @param array $headers
     * @return JsonResponse
     */
    public function apiBadRequest(
        array|string             $errors = [],
        ?string                  $message = null,
        bool                     $throw_exception = true,
        string|int|null|UnitEnum $errorCode = null,
        array                    $headers = []
    ): JsonResponse
    {
        // Set errors
        $errorsCollection = collect($errors)
            ->filter(function ($value, $key) {
                return !empty($value);
            });

        // Set errors collection
        if ($errorsCollection->isNotEmpty()) {
            $errorsCollection = collect([
                'errors' => $errorsCollection->toArray(),
            ]);
        }

        // Set a default value if error code not sent
        if (!$errorCode && (bool)config('api-response.returnDefaultErrorCodes', true)) {
            $errorCode = $this->getErrorCode(config('api-response.errorCodesDefaults.apiBadRequest', 'BAD_REQUEST'));
        }

        return $this->apiResponse([
            'type' => 'Bad Request',
            'throw_exception' => $throw_exception,
            'message' => $message,
            'data' => null,
            'errors' => $errorsCollection->toArray(),
            'errorCode' => $errorCode,
            'response_headers' => $headers,
        ]);
    }

    /**
     * The exception response
     * @param array|string $errors
     * @param string|null $message
     * @param bool $throw_exception
     * @param string|int|UnitEnum|null $errorCode
     * @param array $headers
     * @return JsonResponse
     */
    public function apiException(
        array|string             $errors = [],
        ?string                  $message = null,
        bool                     $throw_exception = true,
        string|int|UnitEnum|null $errorCode = null,
        array                    $headers = []
    ): JsonResponse
    {
        // Set errors
        $errorsCollection = collect($errors)
            ->filter(function ($value, $key) {
                return !empty($value);
            });

        // Set errors collection
        if ($errorsCollection->isNotEmpty()) {
            $errorsCollection = collect([
                'errors' => $errorsCollection->toArray(),
            ]);
        }

        // Set a default value if error code not sent
        if (!$errorCode && (bool)config('api-response.returnDefaultErrorCodes', true)) {
            $errorCode = $this->getErrorCode(config('api-response.errorCodesDefaults.apiException', 'SERVER_ERROR'));
        }

        return $this->apiResponse([
            'type' => 'Exception',
            'throw_exception' => $throw_exception,
            'data' => null,
            'message' => $message,
            'errors' => $errorsCollection->toArray(),
            'errorCode' => $errorCode,
            'response_headers' => $headers,
        ]);
    }

    /**
     * The exception response
     * @param null|string $message
     * @param array|string $errors
     * @param string|int|UnitEnum|null $errorCode
     * @param array $headers
     * @return JsonResponse
     */
    public function apiUnauthenticated(
        ?string                  $message = null,
        array|string             $errors = [],
        string|int|UnitEnum|null $errorCode = null,
        array                    $headers = []
    ): JsonResponse
    {
        // Set errors
        $errorsCollection = collect($errors)
            ->filter(function ($value, $key) {
                return !empty($value);
            });

        // Set errors collection
        if ($errorsCollection->isNotEmpty()) {
            $errorsCollection = collect([
                'errors' => $errorsCollection->toArray(),
            ]);
        }

        // Set a default value if error code not sent
        if (!$errorCode && (bool)config('api-response.returnDefaultErrorCodes', true)) {
            $errorCode = $this->getErrorCode(config('api-response.errorCodesDefaults.apiUnauthenticated', 'UNAUTHORIZED_ACCESS'));
        }

        return $this->apiResponse([
            'type' => 'unauthenticated',
            'throw_exception' => true,
            'message' => $message,
            'data' => null,
            'errors' => $errorsCollection->toArray(),
            'errorCode' => $errorCode,
            'response_headers' => $headers,
        ]);
    }

    /**
     * The exception response
     * @param string|null $message
     * @param array|string $errors
     * @param string|int|UnitEnum|null $errorCode
     * @param array $headers
     * @return JsonResponse
     */
    public function apiForbidden(
        ?string                  $message = null,
        array|string             $errors = [],
        string|int|UnitEnum|null $errorCode = null,
        array                    $headers = []
    ): JsonResponse
    {
        // Set errors
        $errorsCollection = collect($errors)
            ->filter(function ($value, $key) {
                return !empty($value);
            });

        // Set errors collection
        if ($errorsCollection->isNotEmpty()) {
            $errorsCollection = collect([
                'errors' => $errorsCollection->toArray(),
            ]);
        }

        // Set a default value if error code not sent
        if (!$errorCode && (bool)config('api-response.returnDefaultErrorCodes', true)) {
            $errorCode = $this->getErrorCode(config('api-response.errorCodesDefaults.apiForbidden', 'FORBIDDEN'));
        }

        return $this->apiResponse([
            'type' => 'forbidden',
            'throw_exception' => true,
            'message' => $message,
            'data' => null,
            'errors' => $errorsCollection->toArray(),
            'errorCode' => $errorCode,
            'response_headers' => $headers,
        ]);
    }

    /**
     * Paginate data
     * @param LengthAwarePaginator|ResourceCollection|CursorPaginator $pagination
     * @param array $appends Extra data to append to the response
     * @param bool $reverse_data Reverse data
     * @param array $headers
     * @param int $total
     * @return JsonResponse
     */
    public function apiPaginate(
        LengthAwarePaginator|ResourceCollection|CursorPaginator $pagination,
        array                                                   $appends = [],
        bool                                                    $reverse_data = false,
        array                                                   $headers = [],
        int                                                     $total = 0
    ): JsonResponse
    {
        // Common meta
        $isFirst = $pagination->onFirstPage();
        $isLast = $pagination->onLastPage();
        $isNext = $pagination->hasMorePages();
        $perPage = $pagination->perPage();
        $count = $pagination->count();

        // Set is cursor paginate as false
        $isCursorPaginate = false;

        // Set pagination data
        if (
            ($pagination instanceof ResourceCollection && $pagination->resource instanceof CursorPaginator) ||
            $pagination instanceof CursorPaginator
        ) {
            // Meta
            $isPrevious = !is_null($pagination->previousCursor());
            $current = null;
            $next = $pagination->nextCursor()?->encode();
            $previous = $pagination->previousCursor()?->encode();
            $first = null;
            $last = null;
            $from = null;
            $to = null;
            $isCursorPaginate = true;
        } else {
            // Meta
            $isPrevious = (($pagination->currentPage() - 1) > 0);
            $current = $pagination->currentPage();
            $next = ($isNext ? $current + 1 : null);
            $previous = ($isPrevious ? $current - 1 : null);
            $first = 1;
            $last = $pagination->lastpage();
            $from = $pagination->firstItem();
            $to = $pagination->lastItem();
            $total = $pagination->total();
        }

        $data = $pagination->items();

        // Reverse data
        if ($reverse_data) {
            $data = array_reverse($data);
        }

        // If no page found
        if ($current > $last && config('api-response.returnNotFoundOnEmptyPagination', true)) {
            return $this->apiResponse(['type' => 'not found']);
        }

        $pagination = collect([
            'meta' => [
                'page' => [
                    'current' => $current,
                    'first' => $first,
                    'last' => $last,
                    'next' => $next,
                    'previous' => $previous,

                    'per' => $perPage,
                    'from' => $from,
                    'to' => $to,

                    'count' => $count,
                    'total' => $total,

                    'isFirst' => $isFirst,
                    'isLast' => $isLast,
                    'isNext' => $isNext,
                    'isPrevious' => $isPrevious,
                    'isCursorPaginate' => $isCursorPaginate,
                ],
            ]
        ])
            ->when(!config('api-response.hideMetaPaginationLinks', true), function (Collection $collection) use ($pagination, $isNext, $next, $isPrevious, $previous, $last) {
                // Common urls
                $pathUrl = $pagination->path();

                if ($pagination instanceof ResourceCollection && $pagination->resource instanceof CursorPaginator) {
                    // Urls
                    $firstUrl = null;
                    $nextUrl = $pagination->nextPageUrl();
                    $previousUrl = $pagination->previousPageUrl();
                    $lastPageUrl = null;
                } else {
                    // Urls
                    $firstUrl = $pagination->url(1);
                    $nextUrl = ($isNext ? $pagination->url($next) : null);
                    $previousUrl = ($isPrevious ? $pagination->url($previous) : null);
                    $lastPageUrl = $pagination->url($last);
                }
                return $collection->put('links', [
                    'path' => $pathUrl,
                    'first' => $firstUrl,
                    'next' => $nextUrl,
                    'previous' => $previousUrl,
                    'last' => $lastPageUrl
                ]);
            })
            ->toArray();

        // Set extra
        $extra = collect($appends)
            ->merge([
                'pagination' => $pagination,
            ])
            ->toArray();

        return $this->apiRawResponse(data: $data, extra: $extra, responseHeaders: $headers);
    }

    /**
     * Validate
     * @param array|Request $request
     * @param array $rules
     * @param array $messages
     * @param array $attributes
     * @return array|JsonResponse
     */
    public function apiValidate(
        array|Request $request,
        array         $rules,
        array         $messages = [],
        array         $attributes = []
    ): array|JsonResponse
    {
        // Check if data is a request instance
        if ($request instanceof Request) {
            $request = $request->all();
        }

        $validator = app(Factory::class)->make(
            $request, $rules, $messages, $attributes
        );

        // If validation fails
        if ($validator->fails()) {

            // Set errors
            $errors = config('api-response.returnValidationErrorsKeys', true) ?
                $validator->errors()->toArray() :
                $validator->errors()->all();
            if ((bool)config('api-response.returnDefaultErrorCodes', true)) {
                $errorCode = $this->getErrorCode(config('api-response.errorCodesDefaults.apiValidate', 'VALIDATION_FAILED'));
            } else {
                $errorCode = null;
            }

            return $this->apiBadRequest($errors, null, true, $errorCode);
        }

        return $validator->validated();
    }

    /**
     * Die and debug
     * @param mixed $data
     * @return JsonResponse
     */
    public function apiDD(mixed $data): JsonResponse
    {
        return $this->apiResponse([
            'type' => 'Exception',
            'throw_exception' => true,
            'message' => 'Die and dump',
            'data' => $data,
        ]);
    }

    /**
     * @param Generator $generator
     * @param string|null $message
     * @param int $statusCode
     * @param array $headers
     * @return StreamedJsonResponse
     */
    public function apiStreamResponse(
        Generator $generator,
        ?string   $message = null,
        int       $statusCode = Response::HTTP_OK,
        array     $headers = []
    ): StreamedJsonResponse
    {
        return $this->apiResponse([
            'status_code' => $statusCode,
            'message' => $message,
            'data' => $generator,
            'isStream' => true,
            'response_headers' => $headers,
        ]);
    }

    /**
     * @param array|string|null $arg [type, filter_data, throw_exception, message, data]
     * @param mixed $data
     * @param array $guards
     * @return JsonResponse|StreamedJsonResponse
     */
    public function apiResponse(array|string|null $arg = null, mixed $data = null, array $guards = []): JsonResponse|StreamedJsonResponse
    {
        // Set attributes
        $type = isset($arg['type']) && !!$this->checkGetType($arg['type']) ? $arg['type'] : null;
        $filter_data = isset($arg['filter_data']) && (bool)$arg['filter_data'];
        $throw_exception = !isset($arg['throw_exception']) || (bool)$arg['throw_exception'];
        $message = $arg['message'] ?? null;
        $errorCode = $arg['errorCode'] ?? null;
        $isStream = $arg['isStream'] ?? false;
        $extra = $arg['extra'] ?? [];
        $responseHeaders = $arg['response_headers'] ?? [];
        if (array_key_exists('data', $extra)) {
            $extra['renamedDataAttributeInArray'] = $extra['data'];
            unset($extra['data']);
        }

        // Handle type
        if (is_null($type) && (!is_null($arg) && !is_array($arg) && !is_null($data))) {
            // Set type
            $type = $arg;
        }

        // Handle data
        if (is_null($data) && array_key_exists('data', $arg)) {
            $data = $arg['data'];
        } elseif (
            is_null($data) &&
            !is_null($arg) &&
            (!is_array($arg) ||
                !(
                    isset($arg['type']) ||
                    isset($arg['filter_data']) ||
                    isset($arg['message'])
                )
            )
        ) {
            $data = $arg;
        }

        // Set status code
        if (is_null($type)) {
            $status_code = $arg['status_code'] ?? Response::HTTP_OK;
        } else {
            $status_code = $this->setStatusCode($type);
        }

        // Filter data[]
        $data = ((is_array($data) && !!$filter_data) ? $this->removeNullArrayValues($data) : $data);

        // Set data if sent as array
        if (is_array($data) && array_key_exists('data', $data) && sizeof($data) === 1) {
            $data = $data['data'];
        }

        // Check if errors is sent
        $errors = collect($arg['errors'] ?? [])
            ->filter(fn($item) => !empty($item));

        // Merge extra with errors
        if ($errors->isEmpty()) {
            $errors->merge($extra);
        }

        // Check if errors
        if (isset($arg['errors'])) {
            $response = $this->apiRawResponse($data, $message, $arg['errors'], $status_code, $errorCode, $isStream, $responseHeaders);
        } else {
            $response = $this->apiRawResponse($data, $message, $extra, $status_code, $errorCode, $isStream, $responseHeaders);
        }

        // Throw exceptions
        if (in_array($status_code, config('api-response.apiExceptionCodes', [])) && $throw_exception) {
            throw new HttpResponseException($response);
        }

        return $response;
    }

    /**
     * The row response
     * @param mixed|null $data
     * @param string|null $message
     * @param array $extra
     * @param int $status_code
     * @param null|UnitEnum|int|string $errorCode
     * @param bool $isStream
     * @param array $responseHeaders
     * @return JsonResponse|StreamedJsonResponse
     */
    private function apiRawResponse(
        mixed                    $data = null,
        ?string                  $message = null,
        array                    $extra = [],
        int                      $status_code = Response::HTTP_OK,
        null|UnitEnum|int|string $errorCode = null,
        bool                     $isStream = false,
        array                    $responseHeaders = [],
    )
    {
        // Filter data[]
        $data = (is_array($data) && config('api-response.removeNullDataValues', false) ? $this->removeNullArrayValues($data) : $data);

        // Check if data is an empty array
        if (config('api-response.setNullEmptyData', false)) {
            if (
                (is_string($data) && !strlen($data)) ||
                (is_array($data) && !sizeof($data))
            ) {
                $data = null;
            }
        }

        // Set response
        $response = collect([
            'status' => $this->setStatus($status_code),
            'statusCode' => $status_code,
            'timestamp' => now()->timestamp,
            'message' => ($message == null ? $this->setMessage($status_code) : $message),
            'data' => $data,
        ]);

        // Check if error codes enabled
        if (config('api-response.enableErrorCodes', true) && $errorCode) {
            // Get error codes type
            $errorCodesType = config('api-response.errorCodesType', 'string');
            if (!in_array($errorCodesType, ['string', 'integer'])) {
                $errorCodesType = 'string';
            }

            // Get error code if not enum
            if (!$errorCode instanceof UnitEnum) {
                $errorCode = $this->getErrorCode($errorCode);
            }

            // Set error code
            $errorCode = $errorCodesType === 'string' ? $errorCode->name : $errorCode->value;

            $response->put('errorCode', $errorCode);
        }

        // Convert to array
        $response = $response->toArray();

        // Set extra response data
        if (!!sizeof($extra)) {
            $response = $this->arrayMergeRecursiveDistinct($response, $extra);
        }

        // Return data
        return $isStream ? response()->streamJson($response, $status_code, $responseHeaders) : response()->json($response, $status_code, $responseHeaders);
    }

    /**
     * Check and get the type
     * @param int|string $type
     * @return array|string|string[]|void
     */
    private function checkGetType(int|string $type = 'OK')
    {
        // If not string
        if (!is_numeric($type) && !is_string($type)) {
            return;
        }
        if (!is_numeric($type)) {
            $type = trim($type);
            $type = strtolower($type);
            $type = str_replace('-', '', $type);
            $type = str_replace('.', '', $type);
            $type = str_replace(' ', '', $type);
        }
        $types = [
            'ok',
            'created',
            'accepted',
            'deleted',
            'notfound',
            'conflict',
            'badrequest',
            'exception',
            'unauthenticated',
            'unauthorized',
            'forbidden',
            'servererror',
            'error',
        ];

        if (in_array($type, $types) || in_array($type, config('api-response.statusCodes', []))) {
            return $type;
        } else {
            return 'ok';
        }
    }

    /**
     * Set status from status_code
     * @param int|string $type
     * @return int
     */
    private function setStatusCode(int|string $type = 'OK'): int
    {
        // Get type
        $type = $this->checkGetType($type);
        if (is_numeric($type)) {
            $status_code = $type;
        } else {
            $status_code = match ($type) {
                'created' => Response::HTTP_CREATED,
                'accepted' => Response::HTTP_ACCEPTED,
                'deleted' => Response::HTTP_NO_CONTENT,
                'notfound' => Response::HTTP_NOT_FOUND,
                'conflict' => Response::HTTP_CONFLICT,
                'badrequest' => Response::HTTP_BAD_REQUEST,
                'exception' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'unauthenticated', 'unauthorized' => Response::HTTP_UNAUTHORIZED,
                'forbidden' => Response::HTTP_FORBIDDEN,
                'servererror', 'error' => Response::HTTP_INTERNAL_SERVER_ERROR,
                default => Response::HTTP_OK,
            };
        }
        return $status_code;
    }

    /**
     * Set status from status_code
     * @param int $status_code
     * @return bool
     */
    private function setStatus(int $status_code = Response::HTTP_OK): bool
    {
        return in_array($status_code, config('api-response.apiSuccessCodes', []));
    }

    /**
     * Set message from status_code
     * @param int $status_code
     * @return string
     */
    private function setMessage(int $status_code = Response::HTTP_OK): string
    {
        return match ($status_code) {
            Response::HTTP_OK => 'OK',
            Response::HTTP_CREATED => 'Created',
            Response::HTTP_ACCEPTED => 'Accepted',
            Response::HTTP_NOT_FOUND => 'Not found!',
            Response::HTTP_INTERNAL_SERVER_ERROR => 'Internal server error!',
            Response::HTTP_UNPROCESSABLE_ENTITY => 'Unprocessable entity!',
            Response::HTTP_UNAUTHORIZED => 'Unauthenticated!',
            Response::HTTP_FORBIDDEN => 'Unauthorized!',
            Response::HTTP_NO_CONTENT => 'No content!',
            Response::HTTP_BAD_REQUEST => 'Bad Request!',
            Response::HTTP_CONFLICT => 'Conflict!',
            default => 'Error',
        };
    }

    /**
     * Remove null values from array
     * @param array $array
     * @param string $callback
     * @return array
     */
    private function removeNullArrayValues(array $array, string $callback = ''): array
    {
        foreach ($array as $key => & $value) {
            if (is_array($value)) {
                $value = $this->removeNullArrayValues($value, $callback);
            } else {
                if (!empty($callback)) {
                    if (!$callback($value)) {
                        unset($array[$key]);
                    }
                } else {
                    if ((is_string($value) and !(bool)$value) or is_null($value)) {
                        unset($array[$key]);
                    }
                }
            }
        }
        unset($value);

        return $array;
    }

    /**
     * @param array<int|string, mixed> $array1
     * @param array<int|string, mixed> $array2
     *
     * @return array<int|string, mixed>
     */
    private function arrayMergeRecursiveDistinct(array &$array1, array &$array2): array
    {
        $merged = $array1;
        foreach ($array2 as $key => &$value) {
            if (is_array($value) && isset($merged[$key]) && is_array($merged[$key])) {
                $merged[$key] = $this->arrayMergeRecursiveDistinct($merged[$key], $value);
            } else {
                $merged[$key] = $value;
            }
        }

        return $merged;
    }

    /**
     * Get error code
     * @param string|int $errorCode
     * @return UnitEnum
     */
    private function getErrorCode(string|int $errorCode): UnitEnum
    {
        // Set a default value if error code not sent
        $errorCodesEnum = config('api-response.errorCodes', ErrorCodesEnum::class);

        // Set error code enum
        if (!$errorCodesEnum instanceof UnitEnum) {
            $errorCodesEnum = ErrorCodesEnum::class;
        }

        return call_user_func([$errorCodesEnum, 'getProperty'], $errorCode);
    }
}
