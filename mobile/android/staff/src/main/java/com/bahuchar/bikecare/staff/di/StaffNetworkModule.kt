package com.bahuchar.bikecare.staff.di

import com.bahuchar.bikecare.core.data.api.StaffApi
import com.bahuchar.bikecare.core.data.network.ApiClient
import com.bahuchar.bikecare.core.data.network.NetworkConfig
import com.bahuchar.bikecare.staff.BuildConfig
import dagger.Module
import dagger.Provides
import dagger.hilt.InstallIn
import dagger.hilt.components.SingletonComponent
import javax.inject.Singleton

@Module
@InstallIn(SingletonComponent::class)
object StaffNetworkModule {
    @Provides
    @Singleton
    fun provideNetworkConfig(): NetworkConfig = NetworkConfig(
        baseUrl = BuildConfig.API_BASE_URL,
        hostHeader = BuildConfig.API_HOST_HEADER,
    )

    @Provides
    @Singleton
    fun provideStaffApi(apiClient: ApiClient, config: NetworkConfig): StaffApi =
        apiClient.createService(config)
}
