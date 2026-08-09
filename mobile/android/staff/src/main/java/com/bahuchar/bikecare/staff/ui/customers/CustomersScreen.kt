package com.bahuchar.bikecare.staff.ui.customers

import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.automirrored.filled.ArrowBack
import androidx.compose.material.icons.filled.Add
import androidx.compose.material.icons.filled.Search
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.unit.dp
import androidx.hilt.navigation.compose.hiltViewModel

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun CustomersScreen(
    onCustomerClick: (Long) -> Unit,
    onAddCustomer: () -> Unit,
    viewModel: CustomersViewModel = hiltViewModel(),
) {
    val state by viewModel.state.collectAsState()

    Column(Modifier.fillMaxSize()) {
        TopAppBar(title = { Text("Customers") })
        Column(Modifier.padding(horizontal = 16.dp)) {
            OutlinedTextField(
                value = state.search,
                onValueChange = viewModel::updateSearch,
                modifier = Modifier.fillMaxWidth(),
                placeholder = { Text("Search name or phone") },
                leadingIcon = { Icon(Icons.Default.Search, contentDescription = null) },
                singleLine = true,
            )
        }
        Spacer(Modifier.height(12.dp))

        when {
            state.isLoading -> {
                Box(Modifier.fillMaxSize(), contentAlignment = Alignment.Center) {
                    CircularProgressIndicator()
                }
            }
            state.error != null -> {
                Column(
                    Modifier.fillMaxSize().padding(16.dp),
                    verticalArrangement = Arrangement.spacedBy(12.dp),
                ) {
                    Text(state.error ?: "Error", color = MaterialTheme.colorScheme.error)
                    Button(onClick = viewModel::load) { Text("Retry") }
                }
            }
            state.customers.isEmpty() -> {
                EmptyState(
                    message = if (state.search.isBlank()) {
                        "No customers yet"
                    } else {
                        "No customers match your search"
                    },
                    actionLabel = if (state.search.isBlank()) "Add Customer" else null,
                    onAction = onAddCustomer,
                )
            }
            else -> LazyColumn(
                contentPadding = PaddingValues(horizontal = 16.dp, vertical = 8.dp),
                verticalArrangement = Arrangement.spacedBy(8.dp),
            ) {
                items(state.customers) { customer ->
                    Card(
                        modifier = Modifier
                            .fillMaxWidth()
                            .clickable { onCustomerClick(customer.id) },
                    ) {
                        Column(Modifier.padding(16.dp)) {
                            Text(customer.name, style = MaterialTheme.typography.titleMedium)
                            Text(customer.phone, style = MaterialTheme.typography.bodyMedium)
                            Text(
                                "${customer.bikesCount ?: 0} bikes · ${customer.serviceRecordsCount ?: 0} services",
                                style = MaterialTheme.typography.bodySmall,
                            )
                        }
                    }
                }
            }
        }
    }
}

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun CustomerDetailScreen(
    customerId: Long,
    onBack: () -> Unit,
    onAddService: (Long) -> Unit,
    viewModel: CustomerDetailViewModel = hiltViewModel(),
) {
    val customer by viewModel.customer.collectAsState()
    val error by viewModel.error.collectAsState()

    LaunchedEffect(customerId) { viewModel.load(customerId) }

    Scaffold(
        topBar = {
            TopAppBar(
                title = { Text(customer?.name ?: "Customer") },
                navigationIcon = {
                    IconButton(onClick = onBack) {
                        Icon(Icons.AutoMirrored.Filled.ArrowBack, contentDescription = "Back")
                    }
                },
            )
        },
        floatingActionButton = {
            if (customer != null) {
                ExtendedFloatingActionButton(
                    onClick = { onAddService(customerId) },
                    icon = { Icon(Icons.Default.Add, contentDescription = null) },
                    text = { Text("Add Service") },
                )
            }
        },
    ) { padding ->
        Column(
            Modifier
                .fillMaxSize()
                .padding(padding)
                .padding(16.dp),
        ) {
            when {
                error != null -> Text(error ?: "Error", color = MaterialTheme.colorScheme.error)
                customer == null -> CircularProgressIndicator()
                else -> {
                    val c = customer!!
                    Text(c.phone)
                    c.email?.let { Text(it) }
                    c.address?.let { Text(it) }
                    Spacer(Modifier.height(16.dp))
                    Text("Bikes", style = MaterialTheme.typography.titleMedium)
                    c.bikes?.forEach { bike ->
                        ListItem(
                            headlineContent = { Text(bike.displayName ?: bike.brand) },
                            supportingContent = { Text(bike.registrationNumber ?: "") },
                        )
                    }
                    Spacer(Modifier.height(16.dp))
                    Text("Recent Services", style = MaterialTheme.typography.titleMedium)
                    val services = c.serviceRecords
                    if (services.isNullOrEmpty()) {
                        Text(
                            "No services yet",
                            style = MaterialTheme.typography.bodyMedium,
                            color = MaterialTheme.colorScheme.onSurface.copy(alpha = 0.6f),
                        )
                    } else {
                        services.forEach { service ->
                            ListItem(
                                headlineContent = { Text(service.serviceDate) },
                                supportingContent = {
                                    Text("${service.status} · ${service.bike?.displayName ?: ""}")
                                },
                            )
                        }
                    }
                }
            }
        }
    }
}

@Composable
private fun EmptyState(
    message: String,
    actionLabel: String?,
    onAction: () -> Unit,
) {
    Column(
        modifier = Modifier
            .fillMaxSize()
            .padding(32.dp),
        horizontalAlignment = Alignment.CenterHorizontally,
        verticalArrangement = Arrangement.Center,
    ) {
        Text(message, style = MaterialTheme.typography.titleMedium)
        if (actionLabel != null) {
            Spacer(Modifier.height(16.dp))
            Button(onClick = onAction) { Text(actionLabel) }
        }
    }
}
