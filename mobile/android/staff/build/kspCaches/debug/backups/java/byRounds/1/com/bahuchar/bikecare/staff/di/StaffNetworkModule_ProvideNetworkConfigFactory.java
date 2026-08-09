package com.bahuchar.bikecare.staff.di;

import com.bahuchar.bikecare.core.data.network.NetworkConfig;
import dagger.internal.DaggerGenerated;
import dagger.internal.Factory;
import dagger.internal.Preconditions;
import dagger.internal.QualifierMetadata;
import dagger.internal.ScopeMetadata;
import javax.annotation.processing.Generated;

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
public final class StaffNetworkModule_ProvideNetworkConfigFactory implements Factory<NetworkConfig> {
  @Override
  public NetworkConfig get() {
    return provideNetworkConfig();
  }

  public static StaffNetworkModule_ProvideNetworkConfigFactory create() {
    return InstanceHolder.INSTANCE;
  }

  public static NetworkConfig provideNetworkConfig() {
    return Preconditions.checkNotNullFromProvides(StaffNetworkModule.INSTANCE.provideNetworkConfig());
  }

  private static final class InstanceHolder {
    private static final StaffNetworkModule_ProvideNetworkConfigFactory INSTANCE = new StaffNetworkModule_ProvideNetworkConfigFactory();
  }
}
