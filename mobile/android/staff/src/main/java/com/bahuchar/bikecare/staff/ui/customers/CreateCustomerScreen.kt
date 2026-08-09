package com.bahuchar.bikecare.staff.ui.customers

import androidx.compose.foundation.layout.*
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.text.KeyboardOptions
import androidx.compose.foundation.verticalScroll
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.automirrored.filled.ArrowBack
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.text.input.KeyboardType
import androidx.compose.ui.unit.dp
import androidx.hilt.navigation.compose.hiltViewModel
import com.bahuchar.bikecare.core.data.network.ApiErrorParser
import com.bahuchar.bikecare.core.ui.components.*
import com.bahuchar.bikecare.core.util.FormErrors

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun CreateCustomerScreen(
    onBack: () -> Unit,
    onSuccess: (CreateCustomerResult.Success) -> Unit,
    viewModel: CreateCustomerViewModel = hiltViewModel(),
) {
    val state by viewModel.state.collectAsState()
    val scrollState = rememberScrollState()

    Scaffold(
        topBar = {
            TopAppBar(
                title = { Text("New Customer") },
                navigationIcon = {
                    IconButton(onClick = onBack) {
                        Icon(Icons.AutoMirrored.Filled.ArrowBack, contentDescription = "Back")
                    }
                },
            )
        },
    ) { padding ->
        Box(
            modifier = Modifier
                .fillMaxSize()
                .padding(padding),
            contentAlignment = Alignment.TopCenter,
        ) {
            Column(
                modifier = Modifier
                    .widthIn(max = 600.dp)
                    .fillMaxWidth()
                    .verticalScroll(scrollState)
                    .imePadding()
                    .navigationBarsPadding()
                    .padding(16.dp),
                verticalArrangement = Arrangement.spacedBy(16.dp),
            ) {
                Text(
                    "Register customer, add their bike, and optionally start a service.",
                    style = MaterialTheme.typography.bodyMedium,
                    color = MaterialTheme.colorScheme.onSurface.copy(alpha = 0.7f),
                )

                FormSection(
                    step = 1,
                    title = "Customer Details",
                    subtitle = "Name and contact information",
                ) {
                    BahucharTextField(
                        value = state.name,
                        onValueChange = viewModel::updateName,
                        label = "Full Name *",
                        placeholder = "Customer name",
                        error = ApiErrorParser.fieldError(state.fieldErrors, "name"),
                    )
                    Spacer(Modifier.height(12.dp))
                    BahucharTextField(
                        value = state.phone,
                        onValueChange = viewModel::updatePhone,
                        label = "Mobile *",
                        placeholder = "9876543210",
                        keyboardOptions = KeyboardOptions(keyboardType = KeyboardType.Phone),
                        error = ApiErrorParser.fieldError(state.fieldErrors, "phone"),
                    )
                    Spacer(Modifier.height(12.dp))
                    BahucharTextField(
                        value = state.email,
                        onValueChange = viewModel::updateEmail,
                        label = "Email",
                        placeholder = "Optional",
                        keyboardOptions = KeyboardOptions(keyboardType = KeyboardType.Email),
                        error = ApiErrorParser.fieldError(state.fieldErrors, "email"),
                    )
                    Spacer(Modifier.height(12.dp))
                    BahucharTextField(
                        value = state.address,
                        onValueChange = viewModel::updateAddress,
                        label = "Address",
                        placeholder = "Optional",
                        minLines = 2,
                        singleLine = false,
                        error = ApiErrorParser.fieldError(state.fieldErrors, "address"),
                    )
                }

                FormSection(
                    step = 2,
                    title = "Bike Details",
                    subtitle = "Required — every customer needs at least one bike",
                ) {
                    BahucharTextField(
                        value = state.bikeBrand,
                        onValueChange = viewModel::updateBikeBrand,
                        label = "Brand *",
                        placeholder = "e.g. Hero, Honda",
                        error = ApiErrorParser.fieldError(state.fieldErrors, "bike.brand"),
                    )
                    Spacer(Modifier.height(12.dp))
                    Row(horizontalArrangement = Arrangement.spacedBy(8.dp)) {
                        BahucharTextField(
                            value = state.bikeModel,
                            onValueChange = viewModel::updateBikeModel,
                            label = "Model",
                            placeholder = "Optional",
                            modifier = Modifier.weight(1f),
                            error = ApiErrorParser.fieldError(state.fieldErrors, "bike.model"),
                        )
                        BahucharTextField(
                            value = state.bikeRegistration,
                            onValueChange = viewModel::updateBikeRegistration,
                            label = "Reg. No. *",
                            placeholder = "GJ XX XX XXXX",
                            modifier = Modifier.weight(1f),
                            error = ApiErrorParser.fieldError(state.fieldErrors, "bike.registration_number"),
                        )
                    }
                }

                FormSection(
                    step = 3,
                    title = "Service Record",
                    subtitle = "Optionally create a service in the same step",
                ) {
                    Row(
                        modifier = Modifier.fillMaxWidth(),
                        horizontalArrangement = Arrangement.SpaceBetween,
                        verticalAlignment = Alignment.CenterVertically,
                    ) {
                        Text("Create service now")
                        Switch(checked = state.addService, onCheckedChange = viewModel::updateAddService)
                    }

                    if (state.addService) {
                        Spacer(Modifier.height(12.dp))
                        BahucharTextField(
                            value = state.serviceDate,
                            onValueChange = viewModel::updateServiceDate,
                            label = "Service Date *",
                            error = ApiErrorParser.fieldError(state.fieldErrors, "service_date"),
                        )
                        Spacer(Modifier.height(12.dp))
                        BahucharTextField(
                            value = state.workDone,
                            onValueChange = viewModel::updateWorkDone,
                            label = "Work Done",
                            placeholder = "Optional notes",
                            minLines = 2,
                            singleLine = false,
                            error = ApiErrorParser.fieldError(state.fieldErrors, "work_done"),
                        )
                        Spacer(Modifier.height(12.dp))
                        ServiceItemsEditor(
                            items = state.items,
                            onItemsChange = viewModel::updateItems,
                            itemErrors = FormErrors.parseItemErrors(state.fieldErrors),
                            generalError = ApiErrorParser.fieldError(state.fieldErrors, "items"),
                        )
                    }
                }

                state.generalError?.let {
                    Text(it, color = MaterialTheme.colorScheme.error)
                }

                LoadingButton(
                    text = if (state.addService) "Save Customer & Create Service" else "Save Customer & Bike",
                    onClick = { viewModel.submit(onSuccess) },
                    isLoading = state.isLoading,
                    enabled = !state.isLoading,
                )
            }
        }
    }
}
