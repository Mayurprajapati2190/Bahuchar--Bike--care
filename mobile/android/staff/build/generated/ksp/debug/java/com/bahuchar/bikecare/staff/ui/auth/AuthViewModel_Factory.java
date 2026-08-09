package com.bahuchar.bikecare.staff.ui.auth;

import com.bahuchar.bikecare.core.data.local.TokenStore;
import com.bahuchar.bikecare.staff.data.StaffRepository;
import dagger.internal.DaggerGenerated;
import dagger.internal.Factory;
import dagger.internal.QualifierMetadata;
import dagger.internal.ScopeMetadata;
import javax.annotation.processing.Generated;
import javax.inject.Provider;

@ScopeMetadata
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
public final class AuthViewModel_Factory implements Factory<AuthViewModel> {
  private final Provider<StaffRepository> repositoryProvider;

  private final Provider<TokenStore> tokenStoreProvider;

  public AuthViewModel_Factory(Provider<StaffRepository> repositoryProvider,
      Provider<TokenStore> tokenStoreProvider) {
    this.repositoryProvider = repositoryProvider;
    this.tokenStoreProvider = tokenStoreProvider;
  }

  @Override
  public AuthViewModel get() {
    return newInstance(repositoryProvider.get(), tokenStoreProvider.get());
  }

  public static AuthViewModel_Factory create(Provider<StaffRepository> repositoryProvider,
      Provider<TokenStore> tokenStoreProvider) {
    return new AuthViewModel_Factory(repositoryProvider, tokenStoreProvider);
  }

  public static AuthViewModel newInstance(StaffRepository repository, TokenStore tokenStore) {
    return new AuthViewModel(repository, tokenStore);
  }
}
