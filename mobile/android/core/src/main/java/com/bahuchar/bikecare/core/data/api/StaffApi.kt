package com.bahuchar.bikecare.core.data.api

import com.bahuchar.bikecare.core.data.model.*
import retrofit2.http.*

interface StaffApi {
    @POST("auth/login")
    suspend fun login(@Body request: LoginRequest): LoginResponse

    @POST("auth/logout")
    suspend fun logout()

    @GET("auth/user")
    suspend fun currentUser(): UserDto

    @GET("dashboard")
    suspend fun dashboard(): DashboardResponse

    @GET("customers")
    suspend fun customers(
        @Query("search") search: String? = null,
        @Query("page") page: Int = 1,
    ): PaginatedResponse<CustomerDto>

    @GET("customers/{id}")
    suspend fun customer(@Path("id") id: Long): CustomerDto

    @POST("customers")
    suspend fun createCustomer(@Body request: CreateCustomerRequest): CreateCustomerResponse

    @GET("services")
    suspend fun services(
        @Query("search") search: String? = null,
        @Query("status") status: String? = null,
        @Query("page") page: Int = 1,
    ): PaginatedResponse<ServiceRecordDto>

    @GET("services/{id}")
    suspend fun service(@Path("id") id: Long): ServiceRecordDto

    @GET("services/options")
    suspend fun serviceOptions(): ServiceOptionsResponse

    @POST("services")
    suspend fun createService(@Body request: CreateServiceRequest): CreateServiceResponse

    @POST("services/{id}/complete")
    suspend fun completeService(
        @Path("id") id: Long,
        @Body request: CompleteServiceRequest,
    ): Map<String, kotlinx.serialization.json.JsonElement>

    @GET("bills")
    suspend fun bills(
        @Query("search") search: String? = null,
        @Query("payment") payment: String? = null,
        @Query("page") page: Int = 1,
    ): BillsIndexResponse

    @GET("bills/{id}")
    suspend fun bill(@Path("id") id: Long): BillDto

    @PATCH("bills/{id}/payment")
    suspend fun updatePayment(
        @Path("id") id: Long,
        @Body request: UpdatePaymentRequest,
    ): BillDto
}

interface CustomerApi {
    @POST("customer/auth/request-otp")
    suspend fun requestOtp(@Body request: OtpRequest): MessageResponse

    @POST("customer/auth/verify-otp")
    suspend fun verifyOtp(@Body request: VerifyOtpRequest): CustomerLoginResponse

    @POST("customer/auth/logout")
    suspend fun logout()

    @GET("customer/profile")
    suspend fun profile(): CustomerDto

    @GET("customer/bikes")
    suspend fun bikes(): PaginatedResponse<BikeDto>

    @GET("customer/services")
    suspend fun services(@Query("page") page: Int = 1): PaginatedResponse<ServiceRecordDto>

    @GET("customer/bills")
    suspend fun bills(@Query("page") page: Int = 1): PaginatedResponse<BillDto>

    @GET("customer/next-service-due")
    suspend fun nextServiceDue(): NextServiceResponse
}
