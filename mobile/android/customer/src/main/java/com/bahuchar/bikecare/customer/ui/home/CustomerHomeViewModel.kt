package com.bahuchar.bikecare.customer.ui.home

import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.bahuchar.bikecare.core.data.model.*
import com.bahuchar.bikecare.customer.data.CustomerRepository
import dagger.hilt.android.lifecycle.HiltViewModel
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.asStateFlow
import kotlinx.coroutines.launch
import javax.inject.Inject

data class CustomerHomeState(
    val isLoading: Boolean = true,
    val profile: CustomerDto? = null,
    val nextService: ServiceRecordDto? = null,
    val shop: ShopDto? = null,
    val bikes: List<BikeDto> = emptyList(),
    val services: List<ServiceRecordDto> = emptyList(),
    val bills: List<BillDto> = emptyList(),
    val error: String? = null,
)

@HiltViewModel
class CustomerHomeViewModel @Inject constructor(
    private val repository: CustomerRepository,
) : ViewModel() {
    private val _state = MutableStateFlow(CustomerHomeState())
    val state = _state.asStateFlow()

    init { refresh() }

    fun refresh() {
        viewModelScope.launch {
            _state.value = _state.value.copy(isLoading = true, error = null)
            runCatching {
                val profile = repository.profile()
                val next = repository.nextServiceDue()
                val bikes = repository.bikes()
                val services = repository.services()
                val bills = repository.bills()
                CustomerHomeState(
                    isLoading = false,
                    profile = profile,
                    nextService = next.nextService,
                    shop = next.shop,
                    bikes = bikes,
                    services = services,
                    bills = bills,
                )
            }.onSuccess { _state.value = it }
                .onFailure { error ->
                    _state.value = _state.value.copy(
                        isLoading = false,
                        error = error.message ?: "Failed to load data",
                    )
                }
        }
    }
}
