package com.bahuchar.bikecare.customer.ui.auth

import androidx.compose.foundation.layout.*
import androidx.compose.foundation.text.KeyboardOptions
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.text.input.KeyboardType
import androidx.compose.ui.unit.dp
import androidx.hilt.navigation.compose.hiltViewModel

@Composable
fun OtpLoginScreen(
    onLoggedIn: () -> Unit,
    viewModel: CustomerAuthViewModel = hiltViewModel(),
) {
    var state by remember { mutableStateOf(viewModel.uiState) }

    LaunchedEffect(viewModel.uiState) { state = viewModel.uiState }

    Column(
        modifier = Modifier.fillMaxSize().padding(24.dp),
        verticalArrangement = Arrangement.Center,
        horizontalAlignment = Alignment.CenterHorizontally,
    ) {
        Text("Bahuchar Bike Care", style = MaterialTheme.typography.headlineMedium)
        Text("Customer Login", color = MaterialTheme.colorScheme.primary)
        Spacer(Modifier.height(24.dp))

        OutlinedTextField(
            value = state.phone,
            onValueChange = { viewModel.updatePhone(it); state = viewModel.uiState },
            label = { Text("Mobile Number") },
            modifier = Modifier.fillMaxWidth(),
            keyboardOptions = KeyboardOptions(keyboardType = KeyboardType.Phone),
            singleLine = true,
            enabled = !state.otpSent,
        )

        if (state.otpSent) {
            Spacer(Modifier.height(12.dp))
            OutlinedTextField(
                value = state.code,
                onValueChange = { viewModel.updateCode(it); state = viewModel.uiState },
                label = { Text("OTP Code") },
                modifier = Modifier.fillMaxWidth(),
                keyboardOptions = KeyboardOptions(keyboardType = KeyboardType.Number),
                singleLine = true,
            )
        }

        state.error?.let {
            Spacer(Modifier.height(8.dp))
            Text(it, color = MaterialTheme.colorScheme.error)
        }

        Spacer(Modifier.height(24.dp))
        if (!state.otpSent) {
            Button(
                onClick = { viewModel.requestOtp() },
                enabled = state.phone.length == 10 && !state.isLoading,
                modifier = Modifier.fillMaxWidth(),
            ) { Text("Send OTP") }
        } else {
            Button(
                onClick = { viewModel.verifyOtp(onLoggedIn) },
                enabled = state.code.length == 6 && !state.isLoading,
                modifier = Modifier.fillMaxWidth(),
            ) { Text("Verify & Login") }
        }
    }
}
