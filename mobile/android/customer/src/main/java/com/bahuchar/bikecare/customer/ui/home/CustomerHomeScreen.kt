package com.bahuchar.bikecare.customer.ui.home

import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.DirectionsBike
import androidx.compose.material.icons.filled.History
import androidx.compose.material.icons.filled.Home
import androidx.compose.material.icons.filled.Receipt
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Modifier
import androidx.compose.ui.unit.dp
import androidx.hilt.navigation.compose.hiltViewModel
import com.bahuchar.bikecare.core.util.Formatters

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun CustomerHomeScreen(viewModel: CustomerHomeViewModel = hiltViewModel()) {
    val state by viewModel.state.collectAsState()
    var tab by remember { mutableIntStateOf(0) }

    if (state.isLoading && state.profile == null) {
        Box(Modifier.fillMaxSize()) { CircularProgressIndicator(Modifier.padding(24.dp)) }
        return
    }

    Scaffold(
        topBar = {
            TopAppBar(
                title = {
                    Column {
                        Text(state.shop?.name ?: "Bahuchar Bike Care")
                        state.profile?.name?.let { Text(it, style = MaterialTheme.typography.bodySmall) }
                    }
                },
            )
        },
        bottomBar = {
            NavigationBar {
                NavigationBarItem(selected = tab == 0, onClick = { tab = 0 }, icon = { Icon(Icons.Default.Home, null) }, label = { Text("Home") })
                NavigationBarItem(selected = tab == 1, onClick = { tab = 1 }, icon = { Icon(Icons.Default.DirectionsBike, null) }, label = { Text("Bikes") })
                NavigationBarItem(selected = tab == 2, onClick = { tab = 2 }, icon = { Icon(Icons.Default.History, null) }, label = { Text("Services") })
                NavigationBarItem(selected = tab == 3, onClick = { tab = 3 }, icon = { Icon(Icons.Default.Receipt, null) }, label = { Text("Bills") })
            }
        },
    ) { padding ->
        LazyColumn(
            modifier = Modifier.padding(padding).fillMaxSize(),
            contentPadding = PaddingValues(16.dp),
            verticalArrangement = Arrangement.spacedBy(8.dp),
        ) {
            when (tab) {
                0 -> {
                    item {
                        Card(Modifier.fillMaxWidth()) {
                            Column(Modifier.padding(16.dp)) {
                                Text("Next Service Due", style = MaterialTheme.typography.titleMedium)
                                val due = state.nextService?.nextServiceDueAt
                                Text(due ?: "No upcoming service scheduled")
                                state.nextService?.bike?.displayName?.let { Text(it) }
                            }
                        }
                    }
                    state.shop?.let { shop ->
                        item {
                            Card(Modifier.fillMaxWidth()) {
                                Column(Modifier.padding(16.dp)) {
                                    Text("Contact Shop", style = MaterialTheme.typography.titleMedium)
                                    shop.phone?.let { Text(it) }
                                    shop.address?.let { Text(it) }
                                    shop.hours?.let { Text(it) }
                                }
                            }
                        }
                    }
                }
                1 -> items(state.bikes) { bike ->
                    ListItem(
                        headlineContent = { Text(bike.displayName ?: bike.brand) },
                        supportingContent = { Text(bike.registrationNumber ?: "") },
                    )
                }
                2 -> items(state.services) { service ->
                    ListItem(
                        headlineContent = { Text(service.serviceDate) },
                        supportingContent = {
                            Text("${service.bike?.displayName ?: ""} · ${Formatters.statusLabel(service.status)} · ${Formatters.currency(service.totalAmount)}")
                        },
                    )
                }
                3 -> items(state.bills) { bill ->
                    ListItem(
                        headlineContent = { Text(bill.billNumber) },
                        supportingContent = {
                            Text("${Formatters.currency(bill.totalAmount)} · ${Formatters.statusLabel(bill.paymentStatus)}")
                        },
                    )
                }
            }
        }
    }
}
