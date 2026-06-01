<?php

namespace MA\LaravelApiResponse\Facades;

use Generator;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Facade;
use Symfony\Component\HttpFoundation\StreamedJsonResponse;
use UnitEnum;

/**
 * @method static JsonResponse apiOk(mixed $data = null, string|null $message = null, array $headers = [])
 *   Returns a 200 OK JSON response.
 *
 * @method static JsonResponse apiCreated(mixed $data = null, string|null $message = null, array $headers = [])
 *   Returns a 201 Created JSON response.
 *
 * @method static JsonResponse apiDeleted(mixed $data = null, string|null $message = null, array $headers = [])
 *   Returns a 204 No Content JSON response indicating a resource was successfully deleted.
 *
 * @method static JsonResponse apiNotFound(array|string $errors = [], string|null $message = null, bool $throw_exception = true, string|int|UnitEnum|null $errorCode = null, array $headers = [])
 *   Returns a 404 Not Found JSON response, optionally throwing an HttpResponseException.
 *
 * @method static JsonResponse apiConflict(array|string $errors = [], string|null $message = null, array $data = [], bool $throw_exception = true, string|int|UnitEnum|null $errorCode = null, array $headers = [])
 *   Returns a 409 Conflict JSON response, optionally throwing an HttpResponseException.
 *
 * @method static JsonResponse apiBadRequest(array|string $errors = [], string|null $message = null, bool $throw_exception = true, string|int|UnitEnum|null $errorCode = null, array $headers = [])
 *   Returns a 400 Bad Request JSON response, optionally throwing an HttpResponseException.
 *
 * @method static JsonResponse apiException(array|string $errors = [], string|null $message = null, bool $throw_exception = true, string|int|UnitEnum|null $errorCode = null, array $headers = [])
 *   Returns a 422 Unprocessable Entity JSON response, optionally throwing an HttpResponseException.
 *
 * @method static JsonResponse apiUnauthenticated(string|null $message = null, array|string $errors = [], string|int|UnitEnum|null $errorCode = null, array $headers = [])
 *   Returns a 401 Unauthorized JSON response.
 *
 * @method static JsonResponse apiForbidden(string|null $message = null, array|string $errors = [], string|int|UnitEnum|null $errorCode = null, array $headers = [])
 *   Returns a 403 Forbidden JSON response.
 *
 * @method static JsonResponse apiPaginate(LengthAwarePaginator|ResourceCollection|CursorPaginator $pagination, array $appends = [], bool $reverse_data = false, array $headers = [], int $total = 0)
 *   Returns a paginated JSON response with meta and optional links.
 *
 * @method static array|JsonResponse apiValidate(array|Request $request, array $rules, array $messages = [], array $attributes = [])
 *   Validates input against the given rules; returns validated data or a 400 Bad Request response on failure.
 *
 * @method static JsonResponse apiDD(mixed $data)
 *   Returns a debug "die and dump" JSON response (422) — use only during development.
 *
 * @method static StreamedJsonResponse apiStreamResponse(Generator $generator, string|null $message = null, int $statusCode = 200, array $headers = [])
 *   Returns a streamed JSON response built from the given Generator.
 *
 * @method static JsonResponse|StreamedJsonResponse apiResponse(array|string|null $arg = null, mixed $data = null, array $guards = [])
 *   Low-level response builder; accepts a type/arg array or a type string and optional data.
 *
 * @see \MA\LaravelApiResponse\Traits\APIResponseTrait
 */
class ApiResponse extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'lapi-response';
    }
}