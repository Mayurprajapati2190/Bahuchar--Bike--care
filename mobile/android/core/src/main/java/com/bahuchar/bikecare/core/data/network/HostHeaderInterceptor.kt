package com.bahuchar.bikecare.core.data.network

import okhttp3.Interceptor
import okhttp3.Response

/**
 * Laravel Herd routes by hostname. When the app calls http://10.0.2.2/ from the emulator,
 * this interceptor sets the Host header so Herd serves bahuchar-bike-care.test.
 */
class HostHeaderInterceptor(
    private val hostHeader: String?,
) : Interceptor {
    override fun intercept(chain: Interceptor.Chain): Response {
        val host = hostHeader?.trim().orEmpty()
        val request = if (host.isNotEmpty()) {
            chain.request().newBuilder()
                .header("Host", host)
                .build()
        } else {
            chain.request()
        }
        return chain.proceed(request)
    }
}
