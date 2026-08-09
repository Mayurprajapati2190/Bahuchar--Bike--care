package com.bahuchar.bikecare.staff.ui.bills;

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
public final class BillsViewModel_Factory implements Factory<BillsViewModel> {
  private final Provider<StaffRepository> repositoryProvider;

  public BillsViewModel_Factory(Provider<StaffRepository> repositoryProvider) {
    this.repositoryProvider = repositoryProvider;
  }

  @Override
  public BillsViewModel get() {
    return newInstance(repositoryProvider.get());
  }

  public static BillsViewModel_Factory create(Provider<StaffRepository> repositoryProvider) {
    return new BillsViewModel_Factory(repositoryProvider);
  }

  public static BillsViewModel newInstance(StaffRepository repository) {
    return new BillsViewModel(repository);
  }
}
