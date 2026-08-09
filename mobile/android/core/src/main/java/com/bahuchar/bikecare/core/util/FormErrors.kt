package com.bahuchar.bikecare.core.util

object FormErrors {
    fun parseItemErrors(fieldErrors: Map<String, String>): Map<Int, Map<String, String>> {
        val result = mutableMapOf<Int, MutableMap<String, String>>()
        fieldErrors.forEach { (key, message) ->
            val match = Regex("^items\\.(\\d+)\\.(\\w+)$").find(key)
            if (match != null) {
                val index = match.groupValues[1].toInt()
                val field = match.groupValues[2]
                result.getOrPut(index) { mutableMapOf() }[field] = message
            }
        }
        return result
    }
}
