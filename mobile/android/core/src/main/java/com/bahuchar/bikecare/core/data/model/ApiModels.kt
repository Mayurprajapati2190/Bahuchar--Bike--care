package com.bahuchar.bikecare.core.data.model

import kotlinx.serialization.SerialName
import kotlinx.serialization.Serializable

@Serializable
data class UserDto(
    val id: Long,
    val name: String,
    val email: String,
    val role: String,
)

@Serializable
data class LoginRequest(
    val email: String,
    val password: String,
    @SerialName("device_name") val deviceName: String = "android",
)

@Serializable
data class LoginResponse(
    val token: String,
    val user: UserDto,
)

@Serializable
data class CustomerDto(
    val id: Long,
    val name: String,
    val phone: String,
    val email: String? = null,
    val address: String? = null,
    @SerialName("bikes_count") val bikesCount: Int? = null,
    @SerialName("service_records_count") val serviceRecordsCount: Int? = null,
    val bikes: List<BikeDto>? = null,
    @SerialName("service_records") val serviceRecords: List<ServiceRecordDto>? = null,
)

@Serializable
data class BikeDto(
    val id: Long,
    @SerialName("customer_id") val customerId: Long? = null,
    val brand: String,
    val model: String? = null,
    @SerialName("registration_number") val registrationNumber: String? = null,
    @SerialName("display_name") val displayName: String? = null,
)

@Serializable
data class ServiceItemDto(
    val id: Long? = null,
    val description: String,
    val quantity: Double,
    @SerialName("unit_price") val unitPrice: Double,
    val amount: Double? = null,
)

@Serializable
data class ServiceRecordDto(
    val id: Long,
    @SerialName("customer_id") val customerId: Long? = null,
    @SerialName("bike_id") val bikeId: Long? = null,
    @SerialName("service_date") val serviceDate: String,
    @SerialName("completed_at") val completedAt: String? = null,
    @SerialName("next_service_due_at") val nextServiceDueAt: String? = null,
    @SerialName("total_amount") val totalAmount: Double,
    @SerialName("work_done") val workDone: String? = null,
    val status: String,
    val customer: CustomerDto? = null,
    val bike: BikeDto? = null,
    val items: List<ServiceItemDto>? = null,
    val bill: BillDto? = null,
)

@Serializable
data class BillDto(
    val id: Long,
    @SerialName("bill_number") val billNumber: String,
    @SerialName("bill_date") val billDate: String? = null,
    @SerialName("total_amount") val totalAmount: Double,
    @SerialName("amount_paid") val amountPaid: Double? = null,
    @SerialName("balance_due") val balanceDue: Double? = null,
    @SerialName("payment_status") val paymentStatus: String,
    @SerialName("payment_method") val paymentMethod: String? = null,
    @SerialName("service_record") val serviceRecord: ServiceRecordDto? = null,
    @SerialName("print_url") val printUrl: String? = null,
)

@Serializable
data class DashboardStatsDto(
    @SerialName("total_customers") val totalCustomers: Int,
    @SerialName("services_this_month") val servicesThisMonth: Int,
    @SerialName("in_progress") val inProgress: Int,
    @SerialName("due_reminders") val dueReminders: Int,
    @SerialName("pending_payments") val pendingPayments: Int,
    @SerialName("pending_amount") val pendingAmount: Double,
)

@Serializable
data class ShopDto(
    val name: String,
    val address: String? = null,
    val phone: String? = null,
    val tagline: String? = null,
    val hours: String? = null,
)

@Serializable
data class DashboardResponse(
    val stats: DashboardStatsDto,
    @SerialName("completed_today") val completedToday: List<ServiceRecordDto> = emptyList(),
    @SerialName("upcoming_reminders") val upcomingReminders: List<ServiceRecordDto> = emptyList(),
    @SerialName("pending_payments") val pendingPayments: List<BillDto> = emptyList(),
    val shop: ShopDto? = null,
)

@Serializable
data class PaginatedResponse<T>(
    val data: List<T>,
    val meta: PaginationMeta? = null,
    val links: PaginationLinks? = null,
)

@Serializable
data class PaginationMeta(
    @SerialName("current_page") val currentPage: Int? = null,
    @SerialName("last_page") val lastPage: Int? = null,
    @SerialName("per_page") val perPage: Int? = null,
    val total: Int? = null,
)

@Serializable
data class PaginationLinks(
    val next: String? = null,
    val prev: String? = null,
)

@Serializable
data class MessageResponse(
    val message: String,
)

@Serializable
data class CreateCustomerRequest(
    val name: String,
    val phone: String,
    val email: String? = null,
    val address: String? = null,
    val bike: BikeInput,
    @SerialName("add_service") val addService: Boolean = false,
    @SerialName("service_date") val serviceDate: String? = null,
    @SerialName("work_done") val workDone: String? = null,
    val items: List<ServiceItemInput>? = null,
)

@Serializable
data class BikeInput(
    val brand: String,
    val model: String? = null,
    @SerialName("registration_number") val registrationNumber: String? = null,
)

@Serializable
data class ServiceItemInput(
    val description: String,
    val quantity: Double,
    @SerialName("unit_price") val unitPrice: Double,
)

@Serializable
data class CreateServiceRequest(
    @SerialName("customer_id") val customerId: Long,
    @SerialName("bike_id") val bikeId: Long,
    @SerialName("service_date") val serviceDate: String,
    @SerialName("work_done") val workDone: String? = null,
    val items: List<ServiceItemInput>,
)

@Serializable
data class CompleteServiceRequest(
    @SerialName("payment_status") val paymentStatus: String,
    @SerialName("payment_method") val paymentMethod: String? = "cash",
)

@Serializable
data class UpdatePaymentRequest(
    @SerialName("payment_status") val paymentStatus: String,
    @SerialName("amount_paid") val amountPaid: Double? = null,
    @SerialName("payment_method") val paymentMethod: String? = null,
)

@Serializable
data class OtpRequest(
    val phone: String,
)

@Serializable
data class VerifyOtpRequest(
    val phone: String,
    val code: String,
    @SerialName("device_name") val deviceName: String = "customer-android",
)

@Serializable
data class CustomerLoginResponse(
    val token: String,
    val customer: CustomerDto,
)

@Serializable
data class NextServiceResponse(
    @SerialName("next_service") val nextService: ServiceRecordDto? = null,
    val shop: ShopDto? = null,
)

@Serializable
data class BillsIndexResponse(
    val data: List<BillDto>,
    val meta: PaginationMeta? = null,
    val summary: BillsSummary? = null,
)

@Serializable
data class BillsSummary(
    @SerialName("pending_count") val pendingCount: Int? = null,
    @SerialName("pending_amount") val pendingAmount: Double? = null,
)

@Serializable
data class ServiceOptionsResponse(
    val customers: List<CustomerOptionDto>,
)

@Serializable
data class CustomerOptionDto(
    val id: Long,
    val name: String,
    val phone: String,
    val bikes: List<BikeOptionDto>,
)

@Serializable
data class BikeOptionDto(
    val id: Long,
    @SerialName("display_name") val displayName: String,
)

@Serializable
data class ApiErrorResponse(
    val message: String? = null,
    val errors: Map<String, List<String>>? = null,
)

@Serializable
data class CreateCustomerResponse(
    val customer: CustomerDto,
    val service: ServiceRecordDto? = null,
)

@Serializable
data class CreateServiceResponse(
    val service: ServiceRecordDto,
)
