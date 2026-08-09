package com.bahuchar.bikecare.staff.data

import com.bahuchar.bikecare.core.data.api.StaffApi
import com.bahuchar.bikecare.core.data.local.TokenStore
import com.bahuchar.bikecare.core.data.model.*
import javax.inject.Inject
import javax.inject.Singleton

@Singleton
class StaffRepository @Inject constructor(
    private val api: StaffApi,
    private val tokenStore: TokenStore,
) {
    suspend fun login(email: String, password: String): UserDto {
        val response = api.login(LoginRequest(email, password))
        tokenStore.saveToken(response.token)
        return response.user
    }

    suspend fun logout() {
        runCatching { api.logout() }
        tokenStore.clearToken()
    }

    suspend fun dashboard(): DashboardResponse = api.dashboard()

    suspend fun customers(search: String? = null, page: Int = 1): PaginatedResponse<CustomerDto> =
        api.customers(search, page)

    suspend fun customer(id: Long): CustomerDto = api.customer(id)

    suspend fun createCustomer(request: CreateCustomerRequest): CreateCustomerResponse = api.createCustomer(request)

    suspend fun services(search: String? = null, status: String? = null, page: Int = 1): PaginatedResponse<ServiceRecordDto> =
        api.services(search, status, page)

    suspend fun service(id: Long): ServiceRecordDto = api.service(id)

    suspend fun serviceOptions(): ServiceOptionsResponse = api.serviceOptions()

    suspend fun createService(request: CreateServiceRequest): CreateServiceResponse = api.createService(request)

    suspend fun completeService(id: Long, paymentStatus: String, paymentMethod: String) =
        api.completeService(id, CompleteServiceRequest(paymentStatus, paymentMethod))

    suspend fun bills(search: String? = null, payment: String? = null, page: Int = 1): BillsIndexResponse =
        api.bills(search, payment, page)

    suspend fun bill(id: Long): BillDto = api.bill(id)

    suspend fun updatePayment(id: Long, request: UpdatePaymentRequest): BillDto =
        api.updatePayment(id, request)
}
