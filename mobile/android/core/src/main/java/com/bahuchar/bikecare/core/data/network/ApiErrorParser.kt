package com.bahuchar.bikecare.core.data.network

import com.bahuchar.bikecare.core.data.model.ApiErrorResponse
import kotlinx.serialization.json.Json
import retrofit2.HttpException

class ApiException(
    message: String,
    val fieldErrors: Map<String, String> = emptyMap(),
    val statusCode: Int? = null,
) : Exception(message)

object ApiErrorParser {
    private val json = Json {
        ignoreUnknownKeys = true
        isLenient = true
    }

    fun parse(throwable: Throwable): ApiException {
        if (throwable is ApiException) return throwable

        if (throwable is HttpException) {
            val body = throwable.response()?.errorBody()?.string()
            if (!body.isNullOrBlank()) {
                runCatching {
                    val error = json.decodeFromString<ApiErrorResponse>(body)
                    val fieldErrors = error.errors?.mapValues { (_, messages) ->
                        messages.firstOrNull().orEmpty()
                    }?.filterValues { it.isNotBlank() }.orEmpty()

                    return ApiException(
                        message = error.message ?: fieldErrors.values.firstOrNull() ?: "Request failed",
                        fieldErrors = fieldErrors,
                        statusCode = throwable.code(),
                    )
                }
            }
            return ApiException(
                message = when (throwable.code()) {
                    401 -> "Invalid email or password"
                    403 -> "Access denied"
                    404 -> "Not found"
                    422 -> "Validation failed"
                    else -> "Request failed (${throwable.code()})"
                },
                statusCode = throwable.code(),
            )
        }

        return ApiException(message = throwable.message ?: "Something went wrong")
    }

    fun fieldError(fieldErrors: Map<String, String>, vararg keys: String): String? {
        keys.forEach { key ->
            fieldErrors[key]?.let { return it }
        }
        return null
    }
}
