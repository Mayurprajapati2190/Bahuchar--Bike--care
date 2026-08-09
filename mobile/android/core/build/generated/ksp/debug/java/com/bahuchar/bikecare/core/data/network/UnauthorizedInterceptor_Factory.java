package com.bahuchar.bikecare.core.data.network;

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
public final class UnauthorizedInterceptor_Factory implements Factory<UnauthorizedInterceptor> {
  private final Provider<TokenStore> tokenStoreProvider;

  public UnauthorizedInterceptor_Factory(Provider<TokenStore> tokenStoreProvider) {
    this.tokenStoreProvider = tokenStoreProvider;
  }

  @Override
  public UnauthorizedInterceptor get() {
    return newInstance(tokenStoreProvider.get());
  }

  public static UnauthorizedInterceptor_Factory create(Provider<TokenStore> tokenStoreProvider) {
    return new UnauthorizedInterceptor_Factory(tokenStoreProvider);
  }

  public static UnauthorizedInterceptor newInstance(TokenStore tokenStore) {
    return new UnauthorizedInterceptor(tokenStore);
  }
}
