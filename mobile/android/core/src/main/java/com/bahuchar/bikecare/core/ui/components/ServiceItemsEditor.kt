package com.bahuchar.bikecare.core.ui.components

import androidx.compose.foundation.layout.*
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.Add
import androidx.compose.material.icons.filled.Delete
import androidx.compose.material3.*
import androidx.compose.runtime.Composable
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.text.input.KeyboardType
import androidx.compose.foundation.text.KeyboardOptions
import androidx.compose.ui.unit.dp
import com.bahuchar.bikecare.core.data.model.ServiceItemInput
import com.bahuchar.bikecare.core.util.Formatters

@Composable
fun ServiceItemsEditor(
    items: List<ServiceItemInput>,
    onItemsChange: (List<ServiceItemInput>) -> Unit,
    modifier: Modifier = Modifier,
    itemErrors: Map<Int, Map<String, String>> = emptyMap(),
    generalError: String? = null,
) {
    Column(modifier = modifier, verticalArrangement = Arrangement.spacedBy(12.dp)) {
        Row(
            modifier = Modifier.fillMaxWidth(),
            horizontalArrangement = Arrangement.SpaceBetween,
            verticalAlignment = Alignment.CenterVertically,
        ) {
            Text("Bill Items", style = MaterialTheme.typography.labelLarge)
            TextButton(
                onClick = {
                    onItemsChange(items + ServiceItemInput(description = "", quantity = 1.0, unitPrice = 0.0))
                },
            ) {
                Icon(Icons.Default.Add, contentDescription = null, modifier = Modifier.size(18.dp))
                Spacer(Modifier.width(4.dp))
                Text("Add Item")
            }
        }

        generalError?.let {
            Text(it, color = MaterialTheme.colorScheme.error, style = MaterialTheme.typography.bodySmall)
        }

        items.forEachIndexed { index, item ->
            val errors = itemErrors[index] ?: emptyMap()
            Card(modifier = Modifier.fillMaxWidth()) {
                Column(
                    Modifier.padding(12.dp),
                    verticalArrangement = Arrangement.spacedBy(8.dp),
                ) {
                    Row(
                        modifier = Modifier.fillMaxWidth(),
                        horizontalArrangement = Arrangement.SpaceBetween,
                        verticalAlignment = Alignment.CenterVertically,
                    ) {
                        Text("Item ${index + 1}", style = MaterialTheme.typography.labelMedium)
                        if (items.size > 1) {
                            IconButton(
                                onClick = { onItemsChange(items.filterIndexed { i, _ -> i != index }) },
                            ) {
                                Icon(Icons.Default.Delete, contentDescription = "Remove item")
                            }
                        }
                    }
                    BahucharTextField(
                        value = item.description,
                        onValueChange = { value ->
                            onItemsChange(items.mapIndexed { i, existing ->
                                if (i == index) existing.copy(description = value) else existing
                            })
                        },
                        label = "Description",
                        error = errors["description"],
                    )
                    Row(horizontalArrangement = Arrangement.spacedBy(8.dp)) {
                        BahucharTextField(
                            value = if (item.quantity == 0.0) "" else item.quantity.toString(),
                            onValueChange = { value ->
                                val qty = value.toDoubleOrNull() ?: 0.0
                                onItemsChange(items.mapIndexed { i, existing ->
                                    if (i == index) existing.copy(quantity = qty) else existing
                                })
                            },
                            label = "Qty",
                            modifier = Modifier.weight(1f),
                            keyboardOptions = KeyboardOptions(keyboardType = KeyboardType.Decimal),
                            error = errors["quantity"],
                        )
                        BahucharTextField(
                            value = if (item.unitPrice == 0.0) "" else item.unitPrice.toString(),
                            onValueChange = { value ->
                                val price = value.toDoubleOrNull() ?: 0.0
                                onItemsChange(items.mapIndexed { i, existing ->
                                    if (i == index) existing.copy(unitPrice = price) else existing
                                })
                            },
                            label = "Unit Price",
                            modifier = Modifier.weight(1f),
                            keyboardOptions = KeyboardOptions(keyboardType = KeyboardType.Decimal),
                            error = errors["unit_price"],
                        )
                    }
                    Text(
                        "Line total: ${Formatters.currency(item.quantity * item.unitPrice)}",
                        style = MaterialTheme.typography.bodySmall,
                        color = MaterialTheme.colorScheme.primary,
                    )
                }
            }
        }

        val grandTotal = items.sumOf { it.quantity * it.unitPrice }
        Text(
            "Grand Total: ${Formatters.currency(grandTotal)}",
            style = MaterialTheme.typography.titleMedium,
            color = MaterialTheme.colorScheme.primary,
        )
    }
}
