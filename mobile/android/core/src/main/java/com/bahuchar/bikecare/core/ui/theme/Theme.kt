package com.bahuchar.bikecare.core.ui.theme

import androidx.compose.material3.MaterialTheme
import androidx.compose.material3.darkColorScheme
import androidx.compose.material3.lightColorScheme
import androidx.compose.runtime.Composable
import androidx.compose.ui.graphics.Color

val Slate900 = Color(0xFF0F172A)
val Slate800 = Color(0xFF1E293B)
val Amber500 = Color(0xFFF59E0B)
val Amber400 = Color(0xFFFBBF24)

private val DarkColors = darkColorScheme(
    primary = Amber500,
    onPrimary = Slate900,
    secondary = Amber400,
    background = Slate900,
    surface = Slate800,
    onBackground = Color.White,
    onSurface = Color.White,
)

private val LightColors = lightColorScheme(
    primary = Amber500,
    onPrimary = Color.White,
    secondary = Slate900,
    background = Color(0xFFF8FAFC),
    surface = Color.White,
)

@Composable
fun BahucharTheme(
    darkTheme: Boolean = true,
    content: @Composable () -> Unit,
) {
    MaterialTheme(
        colorScheme = if (darkTheme) DarkColors else LightColors,
        content = content,
    )
}
