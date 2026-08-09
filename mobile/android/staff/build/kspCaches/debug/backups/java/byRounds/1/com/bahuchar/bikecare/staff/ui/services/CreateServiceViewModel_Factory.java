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
public final class CreateServiceViewModel_Factory implements Factory<CreateServiceViewModel> {
  private final Provider<StaffRepository> repositoryProvider;

  public CreateServiceViewModel_Factory(Provider<StaffRepository> repositoryProvider) {
    this.repositoryProvider = repositoryProvider;
  }

  @Override
  public CreateServiceViewModel get() {
    return newInstance(repositoryProvider.get());
  }

  public static CreateServiceViewModel_Factory create(
      Provider<StaffRepository> repositoryProvider) {
    return new CreateServiceViewModel_Factory(repositoryProvider);
  }

  public static CreateServiceViewModel newInstance(StaffRepository repository) {
    return new CreateServiceViewModel(repository);
  }
}
