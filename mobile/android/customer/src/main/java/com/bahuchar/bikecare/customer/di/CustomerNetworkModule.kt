package com.bahuchar.bikecare.customer.di

import com.bahuchar.bikecare.core.data.api.CustomerApi
import com.bahuchar.bikecare.core.data.network.ApiClient
import com.bahuchar.bikecare.core.data.network.NetworkConfig
import com.bahuchar.bikecare.customer.BuildConfig
import dagger.Module
import dagger.Provides
import dagger.hilt.InstallIn
import dagger.hilt.components.SingletonComponent
import javax.inject.Singleton

@Module
@InstallIn(SingletonComponent::class)
object CustomerNetworkModule {
    @Provides
    @Singleton
    fun provideNetworkConfig(): NetworkConfig = NetworkConfig(
        baseUrl = BuildConfig.API_BASE_URL,
        hostHeader = BuildConfig.API_HOST_HEADER,
    )

    @Provides
    @Singleton
    fun provideCustomerApi(apiClient: ApiClient, config: NetworkConfig): CustomerApi =
        apiClient.createService(config)
}
