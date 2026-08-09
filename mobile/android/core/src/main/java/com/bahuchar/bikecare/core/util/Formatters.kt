package com.bahuchar.bikecare.core.util

import java.text.NumberFormat
import java.util.Currency
import java.util.Locale

object Formatters {
    private val inr = NumberFormat.getCurrencyInstance(Locale("en", "IN"))

    init {
        inr.currency = Currency.getInstance("INR")
    }

    fun currency(amount: Double): String = inr.format(amount)

    fun statusLabel(status: String): String = when (status) {
        "in_progress" -> "In Progress"
        "completed" -> "Completed"
        "paid" -> "Paid"
        "unpaid" -> "Unpaid"
        "partial" -> "Partial"
        else -> status.replaceFirstChar { it.uppercase() }
    }
}
