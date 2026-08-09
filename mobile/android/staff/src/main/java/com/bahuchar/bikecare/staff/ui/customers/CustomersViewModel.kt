package com.bahuchar.bikecare.staff.ui.customers

import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.bahuchar.bikecare.core.data.model.CustomerDto
import com.bahuchar.bikecare.staff.data.StaffRepository
import dagger.hilt.android.lifecycle.HiltViewModel
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.asStateFlow
import kotlinx.coroutines.launch
import javax.inject.Inject

data class CustomersUiState(
    val isLoading: Boolean = true,
    val customers: List<CustomerDto> = emptyList(),
    val search: String = "",
    val error: String? = null,
)

@HiltViewModel
class CustomersViewModel @Inject constructor(
    private val repository: StaffRepository,
) : ViewModel() {
    private val _state = MutableStateFlow(CustomersUiState())
    val state = _state.asStateFlow()

    init { load() }

    fun updateSearch(value: String) {
        _state.value = _state.value.copy(search = value)
        load()
    }

    fun load() {
        viewModelScope.launch {
            _state.value = _state.value.copy(isLoading = true, error = null)
            runCatching {
                repository.customers(search = _state.value.search.ifBlank { null }).data
            }.onSuccess { customers ->
                _state.value = _state.value.copy(isLoading = false, customers = customers)
            }.onFailure { error ->
                _state.value = _state.value.copy(
                    isLoading = false,
                    error = error.message ?: "Failed to load customers",
                )
            }
        }
    }
}

@HiltViewModel
class CustomerDetailViewModel @Inject constructor(
    private val repository: StaffRepository,
) : ViewModel() {
    private val _state = MutableStateFlow<CustomerDto?>(null)
    val customer = _state.asStateFlow()
    private val _error = MutableStateFlow<String?>(null)
    val error = _error.asStateFlow()

    fun load(id: Long) {
        viewModelScope.launch {
            runCatching { repository.customer(id) }
                .onSuccess { _state.value = it }
                .onFailure { _error.value = it.message }
        }
    }
}
