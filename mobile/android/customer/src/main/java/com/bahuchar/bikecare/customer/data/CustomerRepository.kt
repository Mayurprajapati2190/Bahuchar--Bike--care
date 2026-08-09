package com.bahuchar.bikecare.customer.data

import com.bahuchar.bikecare.core.data.api.CustomerApi
import com.bahuchar.bikecare.core.data.local.TokenStore
import com.bahuchar.bikecare.core.data.model.*
import javax.inject.Inject
import javax.inject.Singleton

@Singleton
class CustomerRepository @Inject constructor(
    private val api: CustomerApi,
    private val tokenStore: TokenStore,
) {
    suspend fun requestOtp(phone: String) {
        api.requestOtp(OtpRequest(phone))
    }

    suspend fun verifyOtp(phone: String, code: String): CustomerDto {
        val response = api.verifyOtp(VerifyOtpRequest(phone, code))
        tokenStore.saveToken(response.token)
        return response.customer
    }

    suspend fun logout() {
        runCatching { api.logout() }
        tokenStore.clearToken()
    }

    suspend fun profile(): CustomerDto = api.profile()

    suspend fun bikes(): List<BikeDto> = api.bikes().data

    suspend fun services(): List<ServiceRecordDto> = api.services().data

    suspend fun bills(): List<BillDto> = api.bills().data

    suspend fun nextServiceDue(): NextServiceResponse = api.nextServiceDue()
}
