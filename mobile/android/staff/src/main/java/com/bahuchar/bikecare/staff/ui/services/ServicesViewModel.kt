package com.bahuchar.bikecare.staff.ui.services

import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.bahuchar.bikecare.core.data.model.ServiceRecordDto
import com.bahuchar.bikecare.staff.data.StaffRepository
import dagger.hilt.android.lifecycle.HiltViewModel
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.asStateFlow
import kotlinx.coroutines.launch
import javax.inject.Inject

data class ServicesUiState(
    val isLoading: Boolean = true,
    val services: List<ServiceRecordDto> = emptyList(),
    val statusFilter: String? = null,
    val error: String? = null,
    val actionMessage: String? = null,
)

@HiltViewModel
class ServicesViewModel @Inject constructor(
    private val repository: StaffRepository,
) : ViewModel() {
    private val _state = MutableStateFlow(ServicesUiState())
    val state = _state.asStateFlow()

    init { load() }

    fun setStatusFilter(status: String?) {
        _state.value = _state.value.copy(statusFilter = status)
        load()
    }

    fun load() {
        viewModelScope.launch {
            _state.value = _state.value.copy(isLoading = true, error = null)
            runCatching {
                repository.services(status = _state.value.statusFilter).data
            }.onSuccess { services ->
                _state.value = _state.value.copy(isLoading = false, services = services)
            }.onFailure { error ->
                _state.value = _state.value.copy(
                    isLoading = false,
                    error = error.message ?: "Failed to load services",
                )
            }
        }
    }

    fun completeService(id: Long) {
        viewModelScope.launch {
            runCatching {
                repository.completeService(id, "paid", "cash")
            }.onSuccess {
                _state.value = _state.value.copy(actionMessage = "Service completed")
                load()
            }.onFailure { error ->
                _state.value = _state.value.copy(actionMessage = error.message)
            }
        }
    }
}

@HiltViewModel
class ServiceDetailViewModel @Inject constructor(
    private val repository: StaffRepository,
) : ViewModel() {
    private val _service = MutableStateFlow<ServiceRecordDto?>(null)
    val service = _service.asStateFlow()

    fun load(id: Long) {
        viewModelScope.launch {
            runCatching { repository.service(id) }.onSuccess { _service.value = it }
        }
    }

    fun complete(id: Long, onDone: () -> Unit) {
        viewModelScope.launch {
            runCatching { repository.completeService(id, "paid", "cash") }.onSuccess { onDone() }
        }
    }
}
