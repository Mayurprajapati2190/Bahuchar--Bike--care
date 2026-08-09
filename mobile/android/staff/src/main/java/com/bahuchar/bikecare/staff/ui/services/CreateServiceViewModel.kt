package com.bahuchar.bikecare.staff.ui.services

import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.bahuchar.bikecare.core.data.model.CreateServiceRequest
import com.bahuchar.bikecare.core.data.model.CustomerOptionDto
import com.bahuchar.bikecare.core.data.model.ServiceItemInput
import com.bahuchar.bikecare.core.data.network.ApiErrorParser
import com.bahuchar.bikecare.core.data.network.ApiException
import com.bahuchar.bikecare.staff.data.StaffRepository
import dagger.hilt.android.lifecycle.HiltViewModel
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.asStateFlow
import kotlinx.coroutines.flow.update
import kotlinx.coroutines.launch
import java.time.LocalDate
import java.time.format.DateTimeFormatter
import javax.inject.Inject

data class CreateServiceUiState(
    val isLoadingOptions: Boolean = true,
    val isSubmitting: Boolean = false,
    val customers: List<CustomerOptionDto> = emptyList(),
    val customerId: Long? = null,
    val bikeId: Long? = null,
    val serviceDate: String = LocalDate.now().format(DateTimeFormatter.ISO_LOCAL_DATE),
    val workDone: String = "",
    val items: List<ServiceItemInput> = listOf(
        ServiceItemInput(description = "General service", quantity = 1.0, unitPrice = 0.0),
    ),
    val fieldErrors: Map<String, String> = emptyMap(),
    val generalError: String? = null,
    val loadError: String? = null,
)

sealed class CreateServiceResult {
    data class Success(val serviceId: Long) : CreateServiceResult()
}

@HiltViewModel
class CreateServiceViewModel @Inject constructor(
    private val repository: StaffRepository,
) : ViewModel() {
    private val _state = MutableStateFlow(CreateServiceUiState())
    val state = _state.asStateFlow()

    fun load(preselectedCustomerId: Long?) {
        viewModelScope.launch {
            _state.update { it.copy(isLoadingOptions = true, loadError = null) }
            runCatching { repository.serviceOptions() }
                .onSuccess { options ->
                    val customerId = preselectedCustomerId?.takeIf { id ->
                        options.customers.any { it.id == id }
                    }
                    val customer = options.customers.find { it.id == customerId }
                    val bikeId = customer?.bikes?.singleOrNull()?.id
                    _state.update {
                        it.copy(
                            isLoadingOptions = false,
                            customers = options.customers,
                            customerId = customerId,
                            bikeId = bikeId,
                        )
                    }
                }
                .onFailure { error ->
                    _state.update {
                        it.copy(
                            isLoadingOptions = false,
                            loadError = ApiErrorParser.parse(error).message,
                        )
                    }
                }
        }
    }

    fun selectCustomer(customerId: Long) {
        val customer = _state.value.customers.find { it.id == customerId }
        val bikeId = customer?.bikes?.singleOrNull()?.id
        _state.update {
            it.copy(
                customerId = customerId,
                bikeId = bikeId,
                fieldErrors = emptyMap(),
                generalError = null,
            )
        }
    }

    fun selectBike(bikeId: Long) {
        _state.update { it.copy(bikeId = bikeId, fieldErrors = emptyMap(), generalError = null) }
    }

    fun updateServiceDate(value: String) = updateField { copy(serviceDate = value) }
    fun updateWorkDone(value: String) = updateField { copy(workDone = value) }
    fun updateItems(items: List<ServiceItemInput>) = updateField { copy(items = items) }

    private inline fun updateField(block: CreateServiceUiState.() -> CreateServiceUiState) {
        _state.update { it.block().copy(fieldErrors = emptyMap(), generalError = null) }
    }

    val selectedCustomer: CustomerOptionDto?
        get() = _state.value.customers.find { it.id == _state.value.customerId }

    fun submit(onSuccess: (CreateServiceResult.Success) -> Unit) {
        val current = _state.value
        val clientErrors = validate(current)
        if (clientErrors.isNotEmpty()) {
            _state.update { it.copy(fieldErrors = clientErrors) }
            return
        }

        viewModelScope.launch {
            _state.update { it.copy(isSubmitting = true, fieldErrors = emptyMap(), generalError = null) }
            runCatching {
                repository.createService(
                    CreateServiceRequest(
                        customerId = current.customerId!!,
                        bikeId = current.bikeId!!,
                        serviceDate = current.serviceDate,
                        workDone = current.workDone.trim().ifBlank { null },
                        items = current.items,
                    ),
                )
            }.onSuccess { response ->
                _state.update { it.copy(isSubmitting = false) }
                onSuccess(CreateServiceResult.Success(response.service.id))
            }.onFailure { error ->
                val apiError = ApiErrorParser.parse(error)
                _state.update {
                    it.copy(
                        isSubmitting = false,
                        fieldErrors = apiError.fieldErrors,
                        generalError = if (apiError.fieldErrors.isEmpty()) apiError.message else null,
                    )
                }
            }
        }
    }

    private fun validate(state: CreateServiceUiState): Map<String, String> {
        val errors = mutableMapOf<String, String>()
        if (state.customerId == null) errors["customer_id"] = "Select a customer"
        if (state.bikeId == null) errors["bike_id"] = "Select a bike"
        if (state.serviceDate.isBlank()) errors["service_date"] = "Service date is required"
        if (state.items.isEmpty()) errors["items"] = "Add at least one bill item"
        state.items.forEachIndexed { index, item ->
            if (item.description.isBlank()) errors["items.$index.description"] = "Description required"
            if (item.quantity <= 0) errors["items.$index.quantity"] = "Invalid quantity"
            if (item.unitPrice < 0) errors["items.$index.unit_price"] = "Invalid price"
        }
        return errors
    }
}
