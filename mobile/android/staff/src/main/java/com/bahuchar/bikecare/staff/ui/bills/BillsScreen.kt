package com.bahuchar.bikecare.staff.ui.bills

import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.automirrored.filled.ArrowBack
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Modifier
import androidx.compose.ui.unit.dp
import androidx.hilt.navigation.compose.hiltViewModel
import com.bahuchar.bikecare.core.util.Formatters

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun BillsScreen(
    onBillClick: (Long) -> Unit,
    viewModel: BillsViewModel = hiltViewModel(),
) {
    val state by viewModel.state.collectAsState()

    Column(Modifier.fillMaxSize()) {
        TopAppBar(title = { Text("Bills") })
        Row(Modifier.padding(16.dp)) {
            FilterChip(
                selected = state.pendingOnly,
                onClick = { viewModel.togglePendingOnly(!state.pendingOnly) },
                label = { Text("Pending only") },
            )
        }
        when {
            state.isLoading -> CircularProgressIndicator(Modifier.padding(16.dp))
            state.error != null -> Text(state.error ?: "Error", Modifier.padding(16.dp), color = MaterialTheme.colorScheme.error)
            else -> LazyColumn(contentPadding = PaddingValues(16.dp), verticalArrangement = Arrangement.spacedBy(8.dp)) {
                items(state.bills) { bill ->
                    Card(Modifier.fillMaxWidth().clickable { onBillClick(bill.id) }) {
                        Column(Modifier.padding(16.dp)) {
                            Text(bill.billNumber, style = MaterialTheme.typography.titleMedium)
                            Text(
                                "${Formatters.currency(bill.totalAmount)} · ${Formatters.statusLabel(bill.paymentStatus)}",
                            )
                            bill.serviceRecord?.customer?.name?.let { Text(it) }
                        }
                    }
                }
            }
        }
    }
}

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun BillDetailScreen(
    billId: Long,
    onBack: () -> Unit,
    viewModel: BillDetailViewModel = hiltViewModel(),
) {
    val bill by viewModel.bill.collectAsState()

    LaunchedEffect(billId) { viewModel.load(billId) }

    Scaffold(
        topBar = {
            TopAppBar(
                title = { Text("Bill Detail") },
                navigationIcon = {
                    IconButton(onClick = onBack) {
                        Icon(Icons.AutoMirrored.Filled.ArrowBack, contentDescription = "Back")
                    }
                },
            )
        },
    ) { padding ->
        Column(Modifier.padding(padding).padding(16.dp)) {
            bill?.let { b ->
                Text(b.billNumber, style = MaterialTheme.typography.headlineSmall)
                Text("Date: ${b.billDate ?: ""}")
                Text("Total: ${Formatters.currency(b.totalAmount)}")
                Text("Status: ${Formatters.statusLabel(b.paymentStatus)}")
                b.balanceDue?.let { Text("Balance: ${Formatters.currency(it)}") }
                b.serviceRecord?.items?.forEach { item ->
                    ListItem(
                        headlineContent = { Text(item.description) },
                        supportingContent = { Text(Formatters.currency(item.unitPrice * item.quantity)) },
                    )
                }
                if (b.paymentStatus != "paid") {
                    Spacer(Modifier.height(16.dp))
                    Button(onClick = { viewModel.markPaid(billId) {} }) {
                        Text("Mark as Paid")
                    }
                }
            } ?: CircularProgressIndicator()
        }
    }
}
