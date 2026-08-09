package com.bahuchar.bikecare.staff.ui.services

import androidx.compose.foundation.layout.*
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.verticalScroll
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.automirrored.filled.ArrowBack
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.unit.dp
import androidx.hilt.navigation.compose.hiltViewModel
import com.bahuchar.bikecare.core.data.model.BikeOptionDto
import com.bahuchar.bikecare.core.data.model.CustomerOptionDto
import com.bahuchar.bikecare.core.data.network.ApiErrorParser
import com.bahuchar.bikecare.core.ui.components.*
import com.bahuchar.bikecare.core.util.FormErrors

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun CreateServiceScreen(
    preselectedCustomerId: Long?,
    onBack: () -> Unit,
    onSuccess: (CreateServiceResult.Success) -> Unit,
    viewModel: CreateServiceViewModel = hiltViewModel(),
) {
    val state by viewModel.state.collectAsState()

    LaunchedEffect(preselectedCustomerId) {
        viewModel.load(preselectedCustomerId)
    }

    Scaffold(
        topBar = {
            TopAppBar(
                title = { Text("New Service") },
                navigationIcon = {
                    IconButton(onClick = onBack) {
                        Icon(Icons.AutoMirrored.Filled.ArrowBack, contentDescription = "Back")
                    }
                },
            )
        },
    ) { padding ->
        when {
            state.isLoadingOptions -> {
                Box(
                    Modifier.fillMaxSize().padding(padding),
                    contentAlignment = Alignment.Center,
                ) {
                    CircularProgressIndicator()
                }
            }
            state.loadError != null -> {
                Column(
                    Modifier.fillMaxSize().padding(padding).padding(16.dp),
                    verticalArrangement = Arrangement.spacedBy(12.dp),
                ) {
                    Text(state.loadError ?: "Failed to load", color = MaterialTheme.colorScheme.error)
                    Button(onClick = { viewModel.load(preselectedCustomerId) }) { Text("Retry") }
                }
            }
            else -> {
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
                            .verticalScroll(rememberScrollState())
                            .imePadding()
                            .navigationBarsPadding()
                            .padding(16.dp),
                        verticalArrangement = Arrangement.spacedBy(16.dp),
                    ) {
                        Text(
                            "Add bill items — a tax invoice is generated when you complete the service.",
                            style = MaterialTheme.typography.bodyMedium,
                            color = MaterialTheme.colorScheme.onSurface.copy(alpha = 0.7f),
                        )

                        CustomerDropdown(
                            customers = state.customers,
                            selectedId = state.customerId,
                            onSelect = viewModel::selectCustomer,
                            error = ApiErrorParser.fieldError(state.fieldErrors, "customer_id"),
                        )

                        val bikes = viewModel.selectedCustomer?.bikes.orEmpty()
                        if (bikes.isNotEmpty()) {
                            BikeDropdown(
                                bikes = bikes,
                                selectedId = state.bikeId,
                                onSelect = viewModel::selectBike,
                                error = ApiErrorParser.fieldError(state.fieldErrors, "bike_id"),
                            )
                        }

                        BahucharTextField(
                            value = state.serviceDate,
                            onValueChange = viewModel::updateServiceDate,
                            label = "Service Date *",
                            error = ApiErrorParser.fieldError(state.fieldErrors, "service_date"),
                        )

                        BahucharTextField(
                            value = state.workDone,
                            onValueChange = viewModel::updateWorkDone,
                            label = "Work Done",
                            placeholder = "Optional notes",
                            minLines = 2,
                            singleLine = false,
                            error = ApiErrorParser.fieldError(state.fieldErrors, "work_done"),
                        )

                        ServiceItemsEditor(
                            items = state.items,
                            onItemsChange = viewModel::updateItems,
                            itemErrors = FormErrors.parseItemErrors(state.fieldErrors),
                            generalError = ApiErrorParser.fieldError(state.fieldErrors, "items"),
                        )

                        state.generalError?.let {
                            Text(it, color = MaterialTheme.colorScheme.error)
                        }

                        LoadingButton(
                            text = "Create Service",
                            onClick = { viewModel.submit(onSuccess) },
                            isLoading = state.isSubmitting,
                            enabled = !state.isSubmitting,
                        )
                    }
                }
            }
        }
    }
}

@OptIn(ExperimentalMaterial3Api::class)
@Composable
private fun CustomerDropdown(
    customers: List<CustomerOptionDto>,
    selectedId: Long?,
    onSelect: (Long) -> Unit,
    error: String?,
) {
    var expanded by remember { mutableStateOf(false) }
    val selected = customers.find { it.id == selectedId }

    ExposedDropdownMenuBox(
        expanded = expanded,
        onExpandedChange = { expanded = !expanded },
    ) {
        OutlinedTextField(
            value = selected?.let { "${it.name} (${it.phone})" } ?: "",
            onValueChange = {},
            readOnly = true,
            label = { Text("Customer *") },
            modifier = Modifier
                .menuAnchor(MenuAnchorType.PrimaryNotEditable)
                .fillMaxWidth(),
            isError = error != null,
            trailingIcon = { ExposedDropdownMenuDefaults.TrailingIcon(expanded = expanded) },
        )
        ExposedDropdownMenu(expanded = expanded, onDismissRequest = { expanded = false }) {
            customers.forEach { customer ->
                DropdownMenuItem(
                    text = { Text("${customer.name} (${customer.phone})") },
                    onClick = {
                        onSelect(customer.id)
                        expanded = false
                    },
                )
            }
        }
    }
    error?.let {
        Text(it, color = MaterialTheme.colorScheme.error, style = MaterialTheme.typography.bodySmall)
    }
}

@OptIn(ExperimentalMaterial3Api::class)
@Composable
private fun BikeDropdown(
    bikes: List<BikeOptionDto>,
    selectedId: Long?,
    onSelect: (Long) -> Unit,
    error: String?,
) {
    var expanded by remember { mutableStateOf(false) }
    val selected = bikes.find { it.id == selectedId }

    ExposedDropdownMenuBox(
        expanded = expanded,
        onExpandedChange = { expanded = !expanded },
    ) {
        OutlinedTextField(
            value = selected?.displayName ?: "",
            onValueChange = {},
            readOnly = true,
            label = { Text("Bike *") },
            modifier = Modifier
                .menuAnchor(MenuAnchorType.PrimaryNotEditable)
                .fillMaxWidth(),
            isError = error != null,
            trailingIcon = { ExposedDropdownMenuDefaults.TrailingIcon(expanded = expanded) },
        )
        ExposedDropdownMenu(expanded = expanded, onDismissRequest = { expanded = false }) {
            bikes.forEach { bike ->
                DropdownMenuItem(
                    text = { Text(bike.displayName) },
                    onClick = {
                        onSelect(bike.id)
                        expanded = false
                    },
                )
            }
        }
    }
    error?.let {
        Text(it, color = MaterialTheme.colorScheme.error, style = MaterialTheme.typography.bodySmall)
    }
}
