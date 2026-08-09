package com.bahuchar.bikecare.staff.ui.dashboard

import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.bahuchar.bikecare.core.data.model.DashboardResponse
import com.bahuchar.bikecare.staff.data.StaffRepository
import dagger.hilt.android.lifecycle.HiltViewModel
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.asStateFlow
import kotlinx.coroutines.launch
import javax.inject.Inject

data class DashboardUiState(
    val isLoading: Boolean = true,
    val data: DashboardResponse? = null,
    val error: String? = null,
)

@HiltViewModel
class DashboardViewModel @Inject constructor(
    private val repository: StaffRepository,
) : ViewModel() {
    private val _state = MutableStateFlow(DashboardUiState())
    val state = _state.asStateFlow()

    init {
        refresh()
    }

    fun refresh() {
        viewModelScope.launch {
            _state.value = _state.value.copy(isLoading = true, error = null)
            runCatching { repository.dashboard() }
                .onSuccess { data ->
                    _state.value = DashboardUiState(isLoading = false, data = data)
                }
                .onFailure { error ->
                    _state.value = DashboardUiState(
                        isLoading = false,
                        error = error.message ?: "Failed to load dashboard",
                    )
                }
        }
    }
}
