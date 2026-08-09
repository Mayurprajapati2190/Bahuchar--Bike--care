package com.bahuchar.bikecare.staff.navigation

import androidx.compose.foundation.layout.padding
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.AccountBox
import androidx.compose.material.icons.filled.Add
import androidx.compose.material.icons.filled.Build
import androidx.compose.material.icons.filled.Dashboard
import androidx.compose.material.icons.filled.Receipt
import androidx.compose.material3.*
import androidx.compose.runtime.Composable
import androidx.compose.runtime.getValue
import androidx.compose.ui.graphics.vector.ImageVector
import androidx.navigation.NavDestination.Companion.hierarchy
import androidx.navigation.NavGraph.Companion.findStartDestination
import androidx.navigation.NavType
import androidx.navigation.navArgument
import androidx.navigation.compose.*
import com.bahuchar.bikecare.staff.ui.auth.LoginScreen
import com.bahuchar.bikecare.staff.ui.bills.BillDetailScreen
import com.bahuchar.bikecare.staff.ui.bills.BillsScreen
import com.bahuchar.bikecare.staff.ui.customers.CreateCustomerScreen
import com.bahuchar.bikecare.staff.ui.customers.CustomerDetailScreen
import com.bahuchar.bikecare.staff.ui.customers.CustomersScreen
import com.bahuchar.bikecare.staff.ui.dashboard.DashboardScreen
import com.bahuchar.bikecare.staff.ui.services.CreateServiceScreen
import com.bahuchar.bikecare.staff.ui.services.ServiceDetailScreen
import com.bahuchar.bikecare.staff.ui.services.ServicesScreen

sealed class StaffRoute(val route: String, val label: String, val icon: ImageVector) {
    data object Dashboard : StaffRoute("dashboard", "Dashboard", Icons.Default.Dashboard)
    data object Customers : StaffRoute("customers", "Customers", Icons.Default.AccountBox)
    data object Services : StaffRoute("services", "Services", Icons.Default.Build)
    data object Bills : StaffRoute("bills", "Bills", Icons.Default.Receipt)
}

@Composable
fun StaffNavHost(isLoggedIn: Boolean, onLoggedIn: () -> Unit, onLogout: () -> Unit) {
    val navController = rememberNavController()

    if (!isLoggedIn) {
        LoginScreen(onLoggedIn = onLoggedIn)
        return
    }

    val bottomItems = listOf(
        StaffRoute.Dashboard,
        StaffRoute.Customers,
        StaffRoute.Services,
        StaffRoute.Bills,
    )

    val navBackStackEntry by navController.currentBackStackEntryAsState()
    val currentRoute = navBackStackEntry?.destination?.route
    val showFab = currentRoute == StaffRoute.Customers.route || currentRoute == StaffRoute.Services.route

    Scaffold(
        bottomBar = {
            if (currentRoute in bottomItems.map { it.route }) {
                NavigationBar {
                    val currentDestination = navBackStackEntry?.destination
                    bottomItems.forEach { item ->
                        NavigationBarItem(
                            selected = currentDestination?.hierarchy?.any { it.route == item.route } == true,
                            onClick = {
                                navController.navigate(item.route) {
                                    popUpTo(navController.graph.findStartDestination().id) { saveState = true }
                                    launchSingleTop = true
                                    restoreState = true
                                }
                            },
                            icon = { Icon(item.icon, contentDescription = item.label) },
                            label = { Text(item.label) },
                        )
                    }
                }
            }
        },
        floatingActionButton = {
            if (showFab) {
                FloatingActionButton(
                    onClick = {
                        when (currentRoute) {
                            StaffRoute.Customers.route -> navController.navigate("customers/create")
                            StaffRoute.Services.route -> navController.navigate("services/create?customerId=-1")
                        }
                    },
                ) {
                    Icon(Icons.Default.Add, contentDescription = "Add")
                }
            }
        },
    ) { padding ->
        NavHost(
            navController = navController,
            startDestination = StaffRoute.Dashboard.route,
            modifier = androidx.compose.ui.Modifier.padding(padding),
        ) {
            composable(StaffRoute.Dashboard.route) {
                DashboardScreen(
                    onNewCustomer = { navController.navigate("customers/create") },
                )
            }
            composable(StaffRoute.Customers.route) {
                CustomersScreen(
                    onCustomerClick = { id -> navController.navigate("customers/$id") },
                    onAddCustomer = { navController.navigate("customers/create") },
                )
            }
            composable("customers/create") {
                CreateCustomerScreen(
                    onBack = { navController.popBackStack() },
                    onSuccess = { result ->
                        navController.popBackStack()
                        val destination = if (result.serviceId != null) {
                            "services/${result.serviceId}"
                        } else {
                            "customers/${result.customerId}"
                        }
                        navController.navigate(destination)
                    },
                )
            }
            composable(
                route = "customers/{id}",
                arguments = listOf(navArgument("id") { type = NavType.LongType }),
            ) { entry ->
                CustomerDetailScreen(
                    customerId = entry.arguments?.getLong("id") ?: 0L,
                    onBack = { navController.popBackStack() },
                    onAddService = { customerId ->
                        navController.navigate("services/create?customerId=$customerId")
                    },
                )
            }
            composable(StaffRoute.Services.route) {
                ServicesScreen(
                    onServiceClick = { id -> navController.navigate("services/$id") },
                    onAddService = { navController.navigate("services/create?customerId=-1") },
                )
            }
            composable(
                route = "services/create?customerId={customerId}",
                arguments = listOf(
                    navArgument("customerId") {
                        type = NavType.LongType
                        defaultValue = -1L
                    },
                ),
            ) { entry ->
                val customerId = entry.arguments?.getLong("customerId")?.takeIf { it > 0 }
                CreateServiceScreen(
                    preselectedCustomerId = customerId,
                    onBack = { navController.popBackStack() },
                    onSuccess = { result ->
                        navController.popBackStack()
                        navController.navigate("services/${result.serviceId}")
                    },
                )
            }
            composable(
                route = "services/{id}",
                arguments = listOf(navArgument("id") { type = NavType.LongType }),
            ) { entry ->
                ServiceDetailScreen(
                    serviceId = entry.arguments?.getLong("id") ?: 0L,
                    onBack = { navController.popBackStack() },
                )
            }
            composable(StaffRoute.Bills.route) {
                BillsScreen(onBillClick = { id -> navController.navigate("bills/$id") })
            }
            composable(
                route = "bills/{id}",
                arguments = listOf(navArgument("id") { type = NavType.LongType }),
            ) { entry ->
                BillDetailScreen(
                    billId = entry.arguments?.getLong("id") ?: 0L,
                    onBack = { navController.popBackStack() },
                )
            }
        }
    }
}
