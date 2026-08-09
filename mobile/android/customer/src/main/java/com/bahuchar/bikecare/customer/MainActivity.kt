package com.bahuchar.bikecare.customer

import android.os.Bundle
import androidx.activity.ComponentActivity
import androidx.activity.compose.setContent
import androidx.activity.enableEdgeToEdge
import androidx.compose.runtime.collectAsState
import androidx.compose.runtime.getValue
import androidx.hilt.navigation.compose.hiltViewModel
import com.bahuchar.bikecare.core.ui.theme.BahucharTheme
import com.bahuchar.bikecare.customer.ui.auth.CustomerAuthViewModel
import com.bahuchar.bikecare.customer.ui.auth.OtpLoginScreen
import com.bahuchar.bikecare.customer.ui.home.CustomerHomeScreen
import dagger.hilt.android.AndroidEntryPoint

@AndroidEntryPoint
class MainActivity : ComponentActivity() {
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        enableEdgeToEdge()
        setContent {
            BahucharTheme {
                val authViewModel: CustomerAuthViewModel = hiltViewModel()
                val isLoggedIn by authViewModel.isLoggedIn.collectAsState()

                if (isLoggedIn) {
                    CustomerHomeScreen()
                } else {
                    OtpLoginScreen(onLoggedIn = {})
                }
            }
        }
    }
}
