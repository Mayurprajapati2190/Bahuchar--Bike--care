package com.bahuchar.bikecare.staff.data;

import com.bahuchar.bikecare.core.data.api.StaffApi;
import com.bahuchar.bikecare.core.data.local.TokenStore;
import dagger.internal.DaggerGenerated;
import dagger.internal.Factory;
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
public final class StaffRepository_Factory implements Factory<StaffRepository> {
  private final Provider<StaffApi> apiProvider;

  private final Provider<TokenStore> tokenStoreProvider;

  public StaffRepository_Factory(Provider<StaffApi> apiProvider,
      Provider<TokenStore> tokenStoreProvider) {
    this.apiProvider = apiProvider;
    this.tokenStoreProvider = tokenStoreProvider;
  }

  @Override
  public StaffRepository get() {
    return newInstance(apiProvider.get(), tokenStoreProvider.get());
  }

  public static StaffRepository_Factory create(Provider<StaffApi> apiProvider,
      Provider<TokenStore> tokenStoreProvider) {
    return new StaffRepository_Factory(apiProvider, tokenStoreProvider);
  }

  public static StaffRepository newInstance(StaffApi api, TokenStore tokenStore) {
    return new StaffRepository(api, tokenStore);
  }
}
