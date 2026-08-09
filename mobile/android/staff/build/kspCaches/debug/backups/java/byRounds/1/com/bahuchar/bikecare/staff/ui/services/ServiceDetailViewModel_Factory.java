package com.bahuchar.bikecare.staff.ui.services;

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
public final class ServiceDetailViewModel_Factory implements Factory<ServiceDetailViewModel> {
  private final Provider<StaffRepository> repositoryProvider;

  public ServiceDetailViewModel_Factory(Provider<StaffRepository> repositoryProvider) {
    this.repositoryProvider = repositoryProvider;
  }

  @Override
  public ServiceDetailViewModel get() {
    return newInstance(repositoryProvider.get());
  }

  public static ServiceDetailViewModel_Factory create(
      Provider<StaffRepository> repositoryProvider) {
    return new ServiceDetailViewModel_Factory(repositoryProvider);
  }

  public static ServiceDetailViewModel newInstance(StaffRepository repository) {
    return new ServiceDetailViewModel(repository);
  }
}
