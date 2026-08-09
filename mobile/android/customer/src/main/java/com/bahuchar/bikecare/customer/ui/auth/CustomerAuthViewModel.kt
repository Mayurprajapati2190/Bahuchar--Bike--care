package com.bahuchar.bikecare.customer.ui.auth

import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.bahuchar.bikecare.core.data.local.TokenStore
import com.bahuchar.bikecare.customer.data.CustomerRepository
import dagger.hilt.android.lifecycle.HiltViewModel
import kotlinx.coroutines.flow.SharingStarted
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.flow.map
import kotlinx.coroutines.flow.stateIn
import kotlinx.coroutines.launch
import javax.inject.Inject

data class OtpUiState(
    val phone: String = "",
    val code: String = "",
    val otpSent: Boolean = false,
    val isLoading: Boolean = false,
    val error: String? = null,
)

@HiltViewModel
class CustomerAuthViewModel @Inject constructor(
    private val repository: CustomerRepository,
    tokenStore: TokenStore,
) : ViewModel() {
    val isLoggedIn: StateFlow<Boolean> = tokenStore.tokenFlow
        .map { !it.isNullOrBlank() }
        .stateIn(viewModelScope, SharingStarted.Eagerly, false)

    var uiState = OtpUiState()
        private set

    fun updatePhone(value: String) {
        uiState = uiState.copy(phone = value.filter { it.isDigit() }.take(10), error = null)
    }

    fun updateCode(value: String) {
        uiState = uiState.copy(code = value.filter { it.isDigit() }.take(6), error = null)
    }

    fun requestOtp() {
        viewModelScope.launch {
            uiState = uiState.copy(isLoading = true, error = null)
            runCatching { repository.requestOtp(uiState.phone) }
                .onSuccess { uiState = uiState.copy(isLoading = false, otpSent = true) }
                .onFailure { uiState = uiState.copy(isLoading = false, error = it.message) }
        }
    }

    fun verifyOtp(onSuccess: () -> Unit) {
        viewModelScope.launch {
            uiState = uiState.copy(isLoading = true, error = null)
            runCatching { repository.verifyOtp(uiState.phone, uiState.code) }
                .onSuccess { uiState = uiState.copy(isLoading = false); onSuccess() }
                .onFailure { uiState = uiState.copy(isLoading = false, error = it.message) }
        }
    }
}
