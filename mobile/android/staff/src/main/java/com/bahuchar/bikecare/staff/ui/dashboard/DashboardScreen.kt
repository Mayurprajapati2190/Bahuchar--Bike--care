package com.bahuchar.bikecare.staff.ui.dashboard

import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.Add
import androidx.compose.material3.*
import androidx.compose.runtime.Composable
import androidx.compose.runtime.collectAsState
import androidx.compose.runtime.getValue
import androidx.compose.ui.Modifier
import androidx.compose.ui.unit.dp
import androidx.hilt.navigation.compose.hiltViewModel
import com.bahuchar.bikecare.core.util.Formatters

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun DashboardScreen(
    onNewCustomer: () -> Unit,
    viewModel: DashboardViewModel = hiltViewModel(),
) {
    val state by viewModel.state.collectAsState()

    Column(Modifier.fillMaxSize()) {
        TopAppBar(title = { Text("Dashboard") })
        when {
            state.isLoading && state.data == null -> {
                Box(Modifier.fillMaxSize()) {
                    CircularProgressIndicator(Modifier.padding(24.dp))
                }
            }
            state.error != null && state.data == null -> {
                Column(Modifier.padding(16.dp)) {
                    Text(state.error ?: "Error", color = MaterialTheme.colorScheme.error)
                    Button(onClick = viewModel::refresh) { Text("Retry") }
                }
            }
            else -> {
                val data = state.data ?: return
                LazyColumn(
                    modifier = Modifier.fillMaxSize(),
                    contentPadding = PaddingValues(16.dp),
                    verticalArrangement = Arrangement.spacedBy(12.dp),
                ) {
                    item {
                        data.shop?.name?.let { Text(it, color = MaterialTheme.colorScheme.primary) }
                    }
                    item {
                        Card(
                            modifier = Modifier
                                .fillMaxWidth()
                                .clickable(onClick = onNewCustomer),
                        ) {
                            Row(
                                Modifier.padding(16.dp),
                                horizontalArrangement = Arrangement.spacedBy(12.dp),
                            ) {
                                Icon(Icons.Default.Add, contentDescription = null, tint = MaterialTheme.colorScheme.primary)
                                Column {
                                    Text("New Customer", style = MaterialTheme.typography.titleMedium)
                                    Text(
                                        "Add customer, bike & service",
                                        style = MaterialTheme.typography.bodySmall,
                                    )
                                }
                            }
                        }
                    }
                    item {
                        Row(Modifier.fillMaxWidth(), horizontalArrangement = Arrangement.spacedBy(8.dp)) {
                            StatCard("Customers", data.stats.totalCustomers.toString(), Modifier.weight(1f))
                            StatCard("This Month", data.stats.servicesThisMonth.toString(), Modifier.weight(1f))
                        }
                    }
                    item {
                        Row(Modifier.fillMaxWidth(), horizontalArrangement = Arrangement.spacedBy(8.dp)) {
                            StatCard("In Progress", data.stats.inProgress.toString(), Modifier.weight(1f))
                            StatCard("Due Reminders", data.stats.dueReminders.toString(), Modifier.weight(1f))
                        }
                    }
                    item {
                        StatCard(
                            "Pending Payments",
                            "${data.stats.pendingPayments} · ${Formatters.currency(data.stats.pendingAmount)}",
                            Modifier.fillMaxWidth(),
                        )
                    }
                    if (data.completedToday.isNotEmpty()) {
                        item { SectionTitle("Completed Today") }
                        items(data.completedToday) { service ->
                            ListItem(
                                headlineContent = { Text(service.customer?.name ?: "Customer") },
                                supportingContent = {
                                    Text("${service.bike?.displayName ?: "Bike"} · ${Formatters.currency(service.totalAmount)}")
                                },
                            )
                        }
                    }
                    if (data.pendingPayments.isNotEmpty()) {
                        item { SectionTitle("Pending Payments") }
                        items(data.pendingPayments) { bill ->
                            ListItem(
                                headlineContent = { Text(bill.billNumber) },
                                supportingContent = {
                                    Text("${Formatters.currency(bill.balanceDue ?: bill.totalAmount)} · ${Formatters.statusLabel(bill.paymentStatus)}")
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
private fun StatCard(title: String, value: String, modifier: Modifier = Modifier) {
    Card(modifier) {
        Column(Modifier.padding(16.dp)) {
            Text(title, style = MaterialTheme.typography.labelMedium)
            Text(value, style = MaterialTheme.typography.titleLarge)
        }
    }
}

@Composable
private fun SectionTitle(title: String) {
    Text(title, style = MaterialTheme.typography.titleMedium, modifier = Modifier.padding(top = 8.dp))
}
