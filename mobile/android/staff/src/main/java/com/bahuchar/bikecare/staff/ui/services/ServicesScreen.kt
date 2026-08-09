package com.bahuchar.bikecare.staff.ui.services

import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.automirrored.filled.ArrowBack
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.unit.dp
import androidx.hilt.navigation.compose.hiltViewModel
import com.bahuchar.bikecare.core.util.Formatters

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun ServicesScreen(
    onServiceClick: (Long) -> Unit,
    onAddService: () -> Unit,
    viewModel: ServicesViewModel = hiltViewModel(),
) {
    val state by viewModel.state.collectAsState()

    Column(Modifier.fillMaxSize()) {
        TopAppBar(title = { Text("Services") })
        Row(
            Modifier.padding(horizontal = 16.dp),
            horizontalArrangement = Arrangement.spacedBy(8.dp),
        ) {
            FilterChip(selected = state.statusFilter == null, onClick = { viewModel.setStatusFilter(null) }, label = { Text("All") })
            FilterChip(selected = state.statusFilter == "in_progress", onClick = { viewModel.setStatusFilter("in_progress") }, label = { Text("In Progress") })
            FilterChip(selected = state.statusFilter == "completed", onClick = { viewModel.setStatusFilter("completed") }, label = { Text("Completed") })
        }
        state.actionMessage?.let {
            Text(it, modifier = Modifier.padding(16.dp), color = MaterialTheme.colorScheme.primary)
        }
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
            state.services.isEmpty() -> {
                Column(
                    Modifier.fillMaxSize().padding(32.dp),
                    horizontalAlignment = Alignment.CenterHorizontally,
                    verticalArrangement = Arrangement.Center,
                ) {
                    Text("No services found", style = MaterialTheme.typography.titleMedium)
                    Spacer(Modifier.height(16.dp))
                    Button(onClick = onAddService) { Text("Create Service") }
                }
            }
            else -> LazyColumn(
                contentPadding = PaddingValues(16.dp),
                verticalArrangement = Arrangement.spacedBy(8.dp),
            ) {
                items(state.services) { service ->
                    Card(Modifier.fillMaxWidth().clickable { onServiceClick(service.id) }) {
                        Column(Modifier.padding(16.dp)) {
                            Text(service.customer?.name ?: "Customer", style = MaterialTheme.typography.titleMedium)
                            Text("${service.bike?.displayName ?: "Bike"} · ${service.serviceDate}")
                            Text("${Formatters.statusLabel(service.status)} · ${Formatters.currency(service.totalAmount)}")
                        }
                    }
                }
            }
        }
    }
}

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun ServiceDetailScreen(
    serviceId: Long,
    onBack: () -> Unit,
    viewModel: ServiceDetailViewModel = hiltViewModel(),
) {
    val service by viewModel.service.collectAsState()

    LaunchedEffect(serviceId) { viewModel.load(serviceId) }

    Scaffold(
        topBar = {
            TopAppBar(
                title = { Text("Service #$serviceId") },
                navigationIcon = {
                    IconButton(onClick = onBack) {
                        Icon(Icons.AutoMirrored.Filled.ArrowBack, contentDescription = "Back")
                    }
                },
            )
        },
    ) { padding ->
        Column(Modifier.padding(padding).padding(16.dp)) {
            service?.let { s ->
                Text(s.customer?.name ?: "Customer", style = MaterialTheme.typography.headlineSmall)
                Text(s.bike?.displayName ?: "")
                Text("Status: ${Formatters.statusLabel(s.status)}")
                Text("Total: ${Formatters.currency(s.totalAmount)}")
                s.workDone?.let { Text(it) }
                s.items?.forEach { item ->
                    ListItem(
                        headlineContent = { Text(item.description) },
                        supportingContent = { Text("${item.quantity} x ${Formatters.currency(item.unitPrice)}") },
                    )
                }
                if (s.status == "in_progress") {
                    Spacer(Modifier.height(16.dp))
                    Button(onClick = { viewModel.complete(serviceId, onBack) }) {
                        Text("Complete Service")
                    }
                }
            } ?: CircularProgressIndicator()
        }
    }
}
