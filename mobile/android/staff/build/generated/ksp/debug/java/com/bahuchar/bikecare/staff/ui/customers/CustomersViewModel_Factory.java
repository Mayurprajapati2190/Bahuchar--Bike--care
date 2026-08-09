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
public final class CustomersViewModel_Factory implements Factory<CustomersViewModel> {
  private final Provider<StaffRepository> repositoryProvider;

  public CustomersViewModel_Factory(Provider<StaffRepository> repositoryProvider) {
    this.repositoryProvider = repositoryProvider;
  }

  @Override
  public CustomersViewModel get() {
    return newInstance(repositoryProvider.get());
  }

  public static CustomersViewModel_Factory create(Provider<StaffRepository> repositoryProvider) {
    return new CustomersViewModel_Factory(repositoryProvider);
  }

  public static CustomersViewModel newInstance(StaffRepository repository) {
    return new CustomersViewModel(repository);
  }
}
