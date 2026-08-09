package com.bahuchar.bikecare.staff.di;

import com.bahuchar.bikecare.core.data.api.StaffApi;
import com.bahuchar.bikecare.core.data.network.ApiClient;
import com.bahuchar.bikecare.core.data.network.NetworkConfig;
import dagger.internal.DaggerGenerated;
import dagger.internal.Factory;
import dagger.internal.Preconditions;
import dagger.internal.QualifierMetadata;
import dagger.internal.ScopeMetadata;
import javax.annotation.processing.Generated;
import javax.inject.Provider;

@ScopeMetadata("javax.inject.Singleton")
@QualifierMetadata
@DaggerGenerated
@Generated(
    value = "dagger.internal.codegen.ComponentProcessor",
    comments = "https://dagger.dev"
)
@SuppressWarnings({
    "unchecked",
    "rawtypes",
    "KotlinInternal",
    "KotlinInternalInJava",
    "cast",
    "deprecation"
})
public final class StaffNetworkModule_ProvideStaffApiFactory implements Factory<StaffApi> {
  private final Provider<ApiClient> apiClientProvider;

  private final Provider<NetworkConfig> configProvider;

  public StaffNetworkModule_ProvideStaffApiFactory(Provider<ApiClient> apiClientProvider,
      Provider<NetworkConfig> configProvider) {
    this.apiClientProvider = apiClientProvider;
    this.configProvider = configProvider;
  }

  @Override
  public StaffApi get() {
    return provideStaffApi(apiClientProvider.get(), configProvider.get());
  }

  public static StaffNetworkModule_ProvideStaffApiFactory create(
      Provider<ApiClient> apiClientProvider, Provider<NetworkConfig> configProvider) {
    return new StaffNetworkModule_ProvideStaffApiFactory(apiClientProvider, configProvider);
  }

  public static StaffApi provideStaffApi(ApiClient apiClient, NetworkConfig config) {
    return Preconditions.checkNotNullFromProvides(StaffNetworkModule.INSTANCE.provideStaffApi(apiClient, config));
  }
}
