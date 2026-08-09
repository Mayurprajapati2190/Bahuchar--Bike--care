package com.bahuchar.bikecare.core.data.network;

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
public final class ApiClient_Factory implements Factory<ApiClient> {
  private final Provider<AuthInterceptor> authInterceptorProvider;

  private final Provider<UnauthorizedInterceptor> unauthorizedInterceptorProvider;

  public ApiClient_Factory(Provider<AuthInterceptor> authInterceptorProvider,
      Provider<UnauthorizedInterceptor> unauthorizedInterceptorProvider) {
    this.authInterceptorProvider = authInterceptorProvider;
    this.unauthorizedInterceptorProvider = unauthorizedInterceptorProvider;
  }

  @Override
  public ApiClient get() {
    return newInstance(authInterceptorProvider.get(), unauthorizedInterceptorProvider.get());
  }

  public static ApiClient_Factory create(Provider<AuthInterceptor> authInterceptorProvider,
      Provider<UnauthorizedInterceptor> unauthorizedInterceptorProvider) {
    return new ApiClient_Factory(authInterceptorProvider, unauthorizedInterceptorProvider);
  }

  public static ApiClient newInstance(AuthInterceptor authInterceptor,
      UnauthorizedInterceptor unauthorizedInterceptor) {
    return new ApiClient(authInterceptor, unauthorizedInterceptor);
  }
}
