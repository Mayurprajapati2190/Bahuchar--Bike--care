package com.bahuchar.bikecare.staff

import android.os.Bundle
import androidx.activity.ComponentActivity
import androidx.activity.compose.setContent
import androidx.activity.enableEdgeToEdge
import androidx.compose.runtime.collectAsState
import androidx.compose.runtime.getValue
import androidx.hilt.navigation.compose.hiltViewModel
import com.bahuchar.bikecare.core.ui.theme.BahucharTheme
import com.bahuchar.bikecare.staff.navigation.StaffNavHost
import com.bahuchar.bikecare.staff.ui.auth.AuthViewModel
import dagger.hilt.android.AndroidEntryPoint

@AndroidEntryPoint
class MainActivity : ComponentActivity() {
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        enableEdgeToEdge()
        setContent {
            BahucharTheme {
                val authViewModel: AuthViewModel = hiltViewModel()
                val isLoggedIn by authViewModel.isLoggedIn.collectAsState()

                StaffNavHost(
                    isLoggedIn = isLoggedIn,
                    onLoggedIn = {},
                    onLogout = {},
                )
            }
        }
    }
}
