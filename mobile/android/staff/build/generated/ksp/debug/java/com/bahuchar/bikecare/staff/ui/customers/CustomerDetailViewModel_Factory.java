package com.bahuchar.bikecare.staff.ui.customers;

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
public final class CustomerDetailViewModel_Factory implements Factory<CustomerDetailViewModel> {
  private final Provider<StaffRepository> repositoryProvider;

  public CustomerDetailViewModel_Factory(Provider<StaffRepository> repositoryProvider) {
    this.repositoryProvider = repositoryProvider;
  }

  @Override
  public CustomerDetailViewModel get() {
    return newInstance(repositoryProvider.get());
  }

  public static CustomerDetailViewModel_Factory create(
      Provider<StaffRepository> repositoryProvider) {
    return new CustomerDetailViewModel_Factory(repositoryProvider);
  }

  public static CustomerDetailViewModel newInstance(StaffRepository repository) {
    return new CustomerDetailViewModel(repository);
  }
}
