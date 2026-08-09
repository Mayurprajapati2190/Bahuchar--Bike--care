package com.bahuchar.bikecare.staff.ui.bills

import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.bahuchar.bikecare.core.data.model.BillDto
import com.bahuchar.bikecare.core.data.model.UpdatePaymentRequest
import com.bahuchar.bikecare.staff.data.StaffRepository
import dagger.hilt.android.lifecycle.HiltViewModel
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.asStateFlow
import kotlinx.coroutines.launch
import javax.inject.Inject

data class BillsUiState(
    val isLoading: Boolean = true,
    val bills: List<BillDto> = emptyList(),
    val pendingOnly: Boolean = false,
    val error: String? = null,
)

@HiltViewModel
class BillsViewModel @Inject constructor(
    private val repository: StaffRepository,
) : ViewModel() {
    private val _state = MutableStateFlow(BillsUiState())
    val state = _state.asStateFlow()

    init { load() }

    fun togglePendingOnly(enabled: Boolean) {
        _state.value = _state.value.copy(pendingOnly = enabled)
        load()
    }

    fun load() {
        viewModelScope.launch {
            _state.value = _state.value.copy(isLoading = true, error = null)
            runCatching {
                repository.bills(payment = if (_state.value.pendingOnly) "pending" else null).data
            }.onSuccess { bills ->
                _state.value = _state.value.copy(isLoading = false, bills = bills)
            }.onFailure { error ->
                _state.value = _state.value.copy(
                    isLoading = false,
                    error = error.message ?: "Failed to load bills",
                )
            }
        }
    }
}

@HiltViewModel
class BillDetailViewModel @Inject constructor(
    private val repository: StaffRepository,
) : ViewModel() {
    private val _bill = MutableStateFlow<BillDto?>(null)
    val bill = _bill.asStateFlow()

    fun load(id: Long) {
        viewModelScope.launch {
            runCatching { repository.bill(id) }.onSuccess { _bill.value = it }
        }
    }

    fun markPaid(id: Long, onDone: () -> Unit) {
        viewModelScope.launch {
            runCatching {
                repository.updatePayment(id, UpdatePaymentRequest("paid", paymentMethod = "cash"))
            }.onSuccess {
                _bill.value = it
                onDone()
            }
        }
    }
}
