package com.bahuchar.bikecare.staff.ui.customers

import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.bahuchar.bikecare.core.data.model.BikeInput
import com.bahuchar.bikecare.core.data.model.CreateCustomerRequest
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

data class CreateCustomerUiState(
    val name: String = "",
    val phone: String = "",
    val email: String = "",
    val address: String = "",
    val bikeBrand: String = "",
    val bikeModel: String = "",
    val bikeRegistration: String = "",
    val addService: Boolean = true,
    val serviceDate: String = LocalDate.now().format(DateTimeFormatter.ISO_LOCAL_DATE),
    val workDone: String = "",
    val items: List<ServiceItemInput> = listOf(
        ServiceItemInput(description = "General service", quantity = 1.0, unitPrice = 0.0),
    ),
    val isLoading: Boolean = false,
    val fieldErrors: Map<String, String> = emptyMap(),
    val generalError: String? = null,
)

sealed class CreateCustomerResult {
    data class Success(val customerId: Long, val serviceId: Long?) : CreateCustomerResult()
}

@HiltViewModel
class CreateCustomerViewModel @Inject constructor(
    private val repository: StaffRepository,
) : ViewModel() {
    private val _state = MutableStateFlow(CreateCustomerUiState())
    val state = _state.asStateFlow()

    fun updateName(value: String) = updateField { copy(name = value) }
    fun updatePhone(value: String) = updateField { copy(phone = value.filter { it.isDigit() }.take(10)) }
    fun updateEmail(value: String) = updateField { copy(email = value) }
    fun updateAddress(value: String) = updateField { copy(address = value) }
    fun updateBikeBrand(value: String) = updateField { copy(bikeBrand = value) }
    fun updateBikeModel(value: String) = updateField { copy(bikeModel = value) }
    fun updateBikeRegistration(value: String) = updateField { copy(bikeRegistration = value.uppercase()) }
    fun updateAddService(value: Boolean) = updateField { copy(addService = value) }
    fun updateServiceDate(value: String) = updateField { copy(serviceDate = value) }
    fun updateWorkDone(value: String) = updateField { copy(workDone = value) }
    fun updateItems(items: List<ServiceItemInput>) = updateField { copy(items = items) }

    private inline fun updateField(block: CreateCustomerUiState.() -> CreateCustomerUiState) {
        _state.update { it.block().copy(fieldErrors = emptyMap(), generalError = null) }
    }

    fun submit(onSuccess: (CreateCustomerResult.Success) -> Unit) {
        val current = _state.value
        val clientErrors = validate(current)
        if (clientErrors.isNotEmpty()) {
            _state.update { it.copy(fieldErrors = clientErrors) }
            return
        }

        viewModelScope.launch {
            _state.update { it.copy(isLoading = true, fieldErrors = emptyMap(), generalError = null) }
            runCatching {
                repository.createCustomer(
                    CreateCustomerRequest(
                        name = current.name.trim(),
                        phone = current.phone.trim(),
                        email = current.email.trim().ifBlank { null },
                        address = current.address.trim().ifBlank { null },
                        bike = BikeInput(
                            brand = current.bikeBrand.trim(),
                            model = current.bikeModel.trim().ifBlank { null },
                            registrationNumber = current.bikeRegistration.trim(),
                        ),
                        addService = current.addService,
                        serviceDate = if (current.addService) current.serviceDate else null,
                        workDone = current.workDone.trim().ifBlank { null },
                        items = if (current.addService) current.items else null,
                    ),
                )
            }.onSuccess { response ->
                _state.update { it.copy(isLoading = false) }
                onSuccess(
                    CreateCustomerResult.Success(
                        customerId = response.customer.id,
                        serviceId = response.service?.id,
                    ),
                )
            }.onFailure { error ->
                val apiError = ApiErrorParser.parse(error)
                _state.update {
                    it.copy(
                        isLoading = false,
                        fieldErrors = mapApiErrors(apiError),
                        generalError = if (apiError.fieldErrors.isEmpty()) apiError.message else null,
                    )
                }
            }
        }
    }

    private fun validate(state: CreateCustomerUiState): Map<String, String> {
        val errors = mutableMapOf<String, String>()
        if (state.name.isBlank()) errors["name"] = "Name is required"
        if (!state.phone.matches(Regex("^[6-9]\\d{9}$"))) {
            errors["phone"] = "Enter a valid 10-digit mobile number"
        }
        if (state.bikeBrand.isBlank()) errors["bike.brand"] = "Bike brand is required"
        if (state.bikeRegistration.isBlank()) {
            errors["bike.registration_number"] = "Registration number is required"
        }
        if (state.addService) {
            if (state.serviceDate.isBlank()) errors["service_date"] = "Service date is required"
            if (state.items.isEmpty()) errors["items"] = "Add at least one bill item"
            state.items.forEachIndexed { index, item ->
                if (item.description.isBlank()) errors["items.$index.description"] = "Description required"
                if (item.quantity <= 0) errors["items.$index.quantity"] = "Invalid quantity"
                if (item.unitPrice < 0) errors["items.$index.unit_price"] = "Invalid price"
            }
        }
        return errors
    }

    private fun mapApiErrors(error: ApiException): Map<String, String> = error.fieldErrors
}
